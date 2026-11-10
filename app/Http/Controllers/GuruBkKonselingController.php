<?php

namespace App\Http\Controllers;

use App\Models\CatatanPrivatKonseling;
use App\Models\KonselingDigital;
use App\Models\RiwayatStatusKonseling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruBkKonselingController extends Controller
{
    /**
     * Dashboard & Daftar Pengelolaan Konseling Digital oleh Guru BK.
     */
    public function index(Request $request)
    {
        $guru = Auth::user();
        $antreanKonselings = KonselingDigital::with('user')
            ->whereNull('guru_id')
            ->latest('id')
            ->paginate(8, ['*'], 'antrean_page');

        // Filter status, kategori, metode, & pencarian
        $status = $request->query('status');
        $kategori = $request->query('kategori');
        $metode = $request->query('metode');
        $search = $request->query('q');

        // Only assigned consultations are visible to this Guru BK.
        $query = KonselingDigital::with(['user', 'guru'])->where('guru_id', $guru->id);

        if ($status && in_array($status, [
            KonselingDigital::STATUS_MENUNGGU_KONFIRMASI,
            KonselingDigital::STATUS_DIJADWALKAN,
            KonselingDigital::STATUS_BERLANGSUNG,
            KonselingDigital::STATUS_SELESAI,
            KonselingDigital::STATUS_DITOLAK,
            KonselingDigital::STATUS_DIBATALKAN,
        ])) {
            $query->where('status', $status);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($metode) {
            $query->where('metode', $metode);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_konseling', 'like', "%{$search}%")
                  ->orWhere('alasan_konseling', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $konselings = $query->latest('id')->paginate(10)->withQueryString();

        // Macro KPI Guru BK
        $stats = [
            'total' => KonselingDigital::where('guru_id', $guru->id)->count(),
            'menunggu' => KonselingDigital::where('guru_id', $guru->id)->where('status', KonselingDigital::STATUS_MENUNGGU_KONFIRMASI)->count(),
            'dijadwalkan' => KonselingDigital::where('guru_id', $guru->id)->where('status', KonselingDigital::STATUS_DIJADWALKAN)->count(),
            'berlangsung' => KonselingDigital::where('guru_id', $guru->id)->where('status', KonselingDigital::STATUS_BERLANGSUNG)->count(),
            'selesai' => KonselingDigital::where('guru_id', $guru->id)->where('status', KonselingDigital::STATUS_SELESAI)->count(),
        ];

        return view('dashboard.guru.konseling.index', compact(
            'guru',
            'antreanKonselings',
            'konselings',
            'stats',
            'status',
            'kategori',
            'metode',
            'search'
        ));
    }

    /** Claim an unassigned consultation without exposing its detail first. */
    public function ambilKonseling(KonselingDigital $konseling)
    {
        $guru = Auth::user();

        // Conditional update is a single atomic statement: only one Guru can win.
        $claimed = KonselingDigital::whereKey($konseling->id)
            ->whereNull('guru_id')
            ->update(['guru_id' => $guru->id]);

        if ($claimed !== 1) {
            return redirect()->route('dashboard.guru.konseling.index')
                ->with('error', 'Konsultasi ini sudah ditugaskan kepada Guru BK lain.');
        }

        return redirect()->route('dashboard.guru.konseling.show', $konseling->id)
            ->with('success', 'Konsultasi berhasil diambil dan ditugaskan kepada Anda.');
    }

    /**
     * Detail Pengajuan & Manajemen Sesi oleh Guru BK.
     */
    public function show(KonselingDigital $konseling)
    {
        $guru = Auth::user();
        $this->ensureAssignedGuru($konseling, $guru->id);
        $konseling->load(['user', 'guru', 'catatanPrivat', 'riwayatStatuses.user']);

        return view('dashboard.guru.konseling.show', compact('konseling', 'guru'));
    }

    /**
     * Konfirmasi / Penjadwalan / Penolakan Pengajuan Konsultasi.
     */
    public function konfirmasi(Request $request, KonselingDigital $konseling)
    {
        $guru = Auth::user();
        $this->ensureAssignedGuru($konseling, $guru->id);
        $aksi = $request->input('aksi');

        if ($aksi === 'tolak') {
            $request->validate([
                'alasan_penolakan' => 'required|string|min:5|max:1000',
            ], [
                'alasan_penolakan.required' => 'Sertakan alasan penolakan secara jelas untuk siswa/alumni.',
            ]);

            $statusLama = $konseling->status;
            $konseling->update([
                'status' => KonselingDigital::STATUS_DITOLAK,
                'guru_id' => $guru->id,
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            RiwayatStatusKonseling::create([
                'konseling_id' => $konseling->id,
                'user_id' => $guru->id,
                'status_sebelumnya' => $statusLama,
                'status_baru' => KonselingDigital::STATUS_DITOLAK,
                'keterangan' => 'Pengajuan ditolak oleh Guru BK: ' . $request->alasan_penolakan,
            ]);

            return redirect()->route('dashboard.guru.konseling.show', $konseling->id)
                ->with('success', 'Pengajuan konseling telah ditolak dengan alasan yang tercatat.');
        }

        // Aksi Setujui / Jadwalkan
        $validated = $request->validate([
            'tanggal_jadwal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'metode' => 'required|in:online,tatap_muka',
            'tempat_tatap_muka' => 'nullable|string|max:255',
            'catatan_jadwal' => 'nullable|string|max:1000',
        ], [
            'tanggal_jadwal.required' => 'Tentukan tanggal pasti jadwal konsultasi.',
            'jam_mulai.required' => 'Tentukan jam mulai konsultasi.',
            'jam_selesai.required' => 'Tentukan jam selesai konsultasi.',
        ]);

        $tempatTatapMuka = $validated['tempat_tatap_muka'] ?? null;
        if ($validated['metode'] === 'tatap_muka' && empty($tempatTatapMuka)) {
            $tempatTatapMuka = 'Ruang Bimbingan Konseling (BK) SMKN 7 Jember, Gedung A Lt. 1';
        }

        $statusLama = $konseling->status;
        $konseling->update([
            'guru_id' => $guru->id,
            'status' => KonselingDigital::STATUS_DIJADWALKAN,
            'metode' => $validated['metode'],
            'tanggal_jadwal' => $validated['tanggal_jadwal'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'tempat_tatap_muka' => $tempatTatapMuka,
            'catatan_jadwal' => $validated['catatan_jadwal'] ?? null,
        ]);

        RiwayatStatusKonseling::create([
            'konseling_id' => $konseling->id,
            'user_id' => $guru->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => KonselingDigital::STATUS_DIJADWALKAN,
            'keterangan' => "Jadwal resmi ditetapkan pada {$validated['tanggal_jadwal']} ({$validated['jam_mulai']} - {$validated['jam_selesai']} WIB)",
        ]);

        return redirect()->route('dashboard.guru.konseling.show', $konseling->id)
            ->with('success', 'Jadwal konsultasi berhasil dikonfirmasi dan ditetapkan untuk peserta.');
    }

    /**
     * Aktifkan Ruang Konsultasi Online (Mulai Sesi).
     */
    public function mulaiSesi(KonselingDigital $konseling)
    {
        $guru = Auth::user();
        $this->ensureAssignedGuru($konseling, $guru->id);

        if (! in_array($konseling->status, [
            KonselingDigital::STATUS_DIJADWALKAN,
            KonselingDigital::STATUS_MENUNGGU_KONFIRMASI,
        ])) {
            return back()->with('error', 'Sesi tidak dapat dimulai untuk status konsultasi saat ini.');
        }

        $statusLama = $konseling->status;
        $konseling->update([
            'guru_id' => $guru->id,
            'status' => KonselingDigital::STATUS_BERLANGSUNG,
            'sesi_dimulai_pada' => now(),
        ]);

        RiwayatStatusKonseling::create([
            'konseling_id' => $konseling->id,
            'user_id' => $guru->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => KonselingDigital::STATUS_BERLANGSUNG,
            'keterangan' => 'Ruang konsultasi online telah dibuka dan sesi chat real-time aktif.',
        ]);

        return redirect()->route('dashboard.guru.konseling.chat', $konseling->id)
            ->with('success', 'Ruang konsultasi online berhasil diaktifkan. Anda kini berada di ruang chat real-time.');
    }

    /**
     * Akhiri Sesi Konsultasi (Tutup Sesi).
     */
    public function akhiriSesi(Request $request, KonselingDigital $konseling)
    {
        $guru = Auth::user();
        $this->ensureAssignedGuru($konseling, $guru->id);

        if ($konseling->status === KonselingDigital::STATUS_SELESAI) {
            return back()->with('error', 'Sesi konsultasi ini sudah berstatus selesai.');
        }

        $statusLama = $konseling->status;
        $konseling->update([
            'status' => KonselingDigital::STATUS_SELESAI,
            'sesi_berakhir_pada' => now(),
        ]);

        RiwayatStatusKonseling::create([
            'konseling_id' => $konseling->id,
            'user_id' => $guru->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => KonselingDigital::STATUS_SELESAI,
            'keterangan' => 'Sesi konsultasi resmi ditutup oleh Guru BK.',
        ]);

        return redirect()->route('dashboard.guru.konseling.show', $konseling->id)
            ->with('success', 'Sesi konsultasi telah berhasil diselesaikan. Silakan lengkapi catatan tindak lanjut privat di bawah ini.');
    }

    /**
     * Simpan / Perbarui Catatan Tindak Lanjut Privat Guru BK.
     */
    public function simpanCatatanPrivat(Request $request, KonselingDigital $konseling)
    {
        $guru = Auth::user();
        $this->ensureAssignedGuru($konseling, $guru->id);

        $validated = $request->validate([
            'ringkasan_masalah' => 'nullable|string|max:2000',
            'tindak_lanjut' => 'required|string|min:5|max:3000',
            'evaluasi_psikologis_perilaku' => 'nullable|string|max:2000',
            'status_urgensi' => 'required|in:rendah,sedang,tinggi',
            'perlu_eskalasi_kepala_sekolah' => 'nullable|boolean',
            'catatan_eskalasi' => 'nullable|string|max:2000',
        ], [
            'tindak_lanjut.required' => 'Rekomendasi dan tindak lanjut wajib diisi oleh Guru BK.',
        ]);

        $perluEskalasi = $request->boolean('perlu_eskalasi_kepala_sekolah');
        $catatanPrivat = $konseling->catatanPrivat;

        $tanggalEskalasi = $catatanPrivat?->tanggal_eskalasi;
        if ($perluEskalasi && ! $tanggalEskalasi) {
            $tanggalEskalasi = now();
        } elseif (! $perluEskalasi) {
            $tanggalEskalasi = null;
        }

        CatatanPrivatKonseling::updateOrCreate(
            ['konseling_id' => $konseling->id],
            [
                'guru_id' => $guru->id,
                'ringkasan_masalah' => $validated['ringkasan_masalah'],
                'tindak_lanjut' => $validated['tindak_lanjut'],
                'evaluasi_psikologis_perilaku' => $validated['evaluasi_psikologis_perilaku'],
                'status_urgensi' => $validated['status_urgensi'],
                'perlu_eskalasi_kepala_sekolah' => $perluEskalasi,
                'catatan_eskalasi' => $perluEskalasi ? $validated['catatan_eskalasi'] : null,
                'tanggal_eskalasi' => $tanggalEskalasi,
            ]
        );

        return redirect()->route('dashboard.guru.konseling.show', $konseling->id)
            ->with('success', 'Catatan tindak lanjut privat Guru BK berhasil disimpan secara aman.');
    }

    /**
     * Ruang Chat Konseling Digital bagi Guru BK.
     */
    public function chat(KonselingDigital $konseling)
    {
        $guru = Auth::user();
        $this->ensureAssignedGuru($konseling, $guru->id);

        if (! $konseling->isOnline()) {
            return redirect()->route('dashboard.guru.konseling.show', $konseling->id)
                ->with('error', 'Konsultasi ini menggunakan metode tatap muka di sekolah.');
        }

        return view('dashboard.guru.konseling.chat', compact('konseling', 'guru'));
    }

    private function ensureAssignedGuru(KonselingDigital $konseling, int $guruId): void
    {
        abort_unless(
            $konseling->guru_id !== null && (int) $konseling->guru_id === $guruId,
            403,
            'Konsultasi ini tidak ditugaskan kepada akun Guru BK Anda.'
        );
    }
}
