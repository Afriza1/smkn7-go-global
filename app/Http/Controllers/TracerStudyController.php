<?php

namespace App\Http\Controllers;

use App\Models\TracerStudy;
use App\Models\TracerStudyPeriode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TracerStudyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $periodeAktif = $this->periodePengisianTerbuka();
        $responsSaatIni = $periodeAktif
            ? $user->tracerStudies()->where('periode_id', $periodeAktif->id)->first()
            : null;

        $riwayat = $user->tracerStudies()
            ->with('periode')
            ->latest()
            ->get();
        $pesanPembaruan = $responsSaatIni && $responsSaatIni->status_verifikasi === TracerStudy::STATUS_NEEDS_UPDATE
            ? $responsSaatIni->followUpEntries()
                ->whereNotNull('pesan_alumni')
                ->where('pesan_alumni', '<>', '')
            : collect();

        $profil = $user->alumniGlobal()
            ->where('is_demo', false)
            ->first(['nama_lengkap', 'tahun_kelulusan', 'jurusan']);

        return view('dashboard.tracer-study.index', compact(
            'user', 'periodeAktif', 'responsSaatIni', 'riwayat', 'pesanPembaruan', 'profil'
        ));
    }

    public function store(Request $request)
    {
        $periode = $this->periodePengisianTerbuka();

        if (! $periode) {
            return redirect()->route('dashboard.tracer-study.index')
                ->with('error', 'Pengisian Tracer Study belum dibuka oleh Guru BK.');
        }

        $userId = (int) $request->user()->getAuthIdentifier();
        $periodId = (int) $periode->getKey();
        $existing = TracerStudy::where('user_id', $userId)
            ->where('periode_id', $periode->id)
            ->first();

        if ($existing) {
            return redirect()->route('dashboard.tracer-study.index')
                ->with('info', 'Respons untuk periode ini sudah tersimpan. Gunakan tombol Perbarui Data untuk mengubahnya.');
        }

        $validated = $this->validateResponse($request);

        DB::transaction(function () use ($validated, $periode, $userId, $periodId): void {
            $response = (new TracerStudy)->forceFill([
                ...$validated,
                'user_id' => $userId,
                'periode_id' => $periodId,
                'tahun_pencatatan' => now()->year,
                'status_verifikasi' => TracerStudy::STATUS_UNVERIFIED,
                'persetujuan_penggunaan_data' => true,
                'disetujui_pada' => now(),
            ]);
            $response->save();

            $response->appendHistory([
                'user_id' => $userId,
                'ringkasan_perubahan' => 'Mengirimkan respons Tracer Study periode ' . $periode->tahun . '.',
            ]);
        });

        return redirect()->route('dashboard.tracer-study.index')
            ->with('success', 'Respons Tracer Study berhasil dikirim. Data Anda dapat diperiksa oleh Guru BK dan petugas berwenang.');
    }

    public function update(Request $request, TracerStudy $tracerStudy)
    {
        $actorId = (int) $request->user()->getAuthIdentifier();
        $response = $request->user()->tracerStudies()->whereKey($tracerStudy->id)->firstOrFail();
        $periode = $this->periodePengisianTerbuka();

        if (! $periode || $response->periode_id !== $periode->id) {
            return redirect()->route('dashboard.tracer-study.index')
                ->with('error', 'Periode respons ini sudah ditutup sehingga data tidak dapat diperbarui.');
        }

        $validated = $this->validateResponse($request);

        DB::transaction(function () use ($response, $validated, $actorId): void {
            $changedFields = [];
            $previousStatus = $response->status_verifikasi;

            foreach ($validated as $field => $value) {
                if ((string) $response->{$field} !== (string) $value) {
                    $changedFields[] = $field;
                }
            }

            $response->fill($validated);
            $response->persetujuan_penggunaan_data = true;
            $response->disetujui_pada = now();
            $response->status_verifikasi = TracerStudy::STATUS_UNVERIFIED;
            $response->diverifikasi_oleh = null;
            $response->diverifikasi_pada = null;
            $response->save();

            $summary = $changedFields
                ? 'Memperbarui data: ' . implode(', ', $changedFields) . '.'
                : 'Mengonfirmasi kembali data dan persetujuan penggunaan data.';
            $summary .= ' Status pemeriksaan ' . $previousStatus . ' menjadi ' . TracerStudy::STATUS_UNVERIFIED . ' setelah pembaruan Alumni.';

            $response->appendHistory([
                'user_id' => $actorId,
                'ringkasan_perubahan' => $summary,
            ]);
        });

        return redirect()->route('dashboard.tracer-study.index')
            ->with('success', 'Respons Tracer Study berhasil diperbarui dan akan diperiksa kembali oleh Guru BK.');
    }

    private function validateResponse(Request $request): array
    {
        return $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'tahun_lulus' => 'required|integer|min:1950|max:' . now()->year,
            'jurusan' => 'required|string|max:255',
            'alamat' => 'required|string|max:2000',
            'status_kegiatan' => 'required|in:bekerja,magang',
            'pekerjaan' => 'required|string|max:255',
            'nama_perusahaan' => 'required|string|max:255',
            'tahun_masuk_perusahaan' => 'required|integer|min:1950|max:' . (now()->year + 1),
            'alamat_tempat_kerja' => 'required|string|max:2000',
            'persetujuan_penggunaan_data' => 'accepted',
        ], [
            'persetujuan_penggunaan_data.accepted' => 'Persetujuan penggunaan data untuk keperluan Tracer Study wajib diberikan sebelum mengirim respons.',
        ]);
    }

    private function periodePengisianTerbuka(): ?TracerStudyPeriode
    {
        return TracerStudyPeriode::query()
            ->where('status', TracerStudyPeriode::STATUS_OPEN)
            ->whereDate('tanggal_mulai', '<=', today())
            ->whereDate('tanggal_berakhir', '>=', today())
            ->orderByDesc('tahun')
            ->first();
    }
}
