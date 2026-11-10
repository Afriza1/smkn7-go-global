<?php

namespace App\Http\Controllers;

use App\Models\AlumniGlobal;
use App\Models\KomunitasAlumniGlobal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAlumniGlobalController extends Controller
{
    /**
     * Halaman Manajemen Alumni Global & Pengaturan Komunitas oleh Admin BKK.
     */
    public function index(Request $request)
    {
        $query = AlumniGlobal::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('kota_wilayah', 'like', "%{$search}%")
                  ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('mentor')) {
            $query->where('is_mentor', $request->mentor === '1');
        }

        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === '1');
        }

        $alumnis = $query->orderBy('is_mentor', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $komunitas = KomunitasAlumniGlobal::first() ?? new KomunitasAlumniGlobal([
            'nama_komunitas' => 'Komunitas Alumni SMKN 7 Jember – Go Global Jepang',
            'deskripsi' => '',
            'tujuan_komunitas' => '',
            'aturan_komunitas' => '',
            'link_whatsapp' => '',
            'is_aktif' => false,
        ]);

        $stats = [
            'total' => AlumniGlobal::count(),
            'mentor' => AlumniGlobal::where('is_mentor', true)->count(),
            'kontak_publik' => AlumniGlobal::where('kontak_disetujui', true)
                ->where('is_demo', false)
                ->whereNotNull('kontak_disetujui_pada')
                ->whereNotNull('kontak_disetujui_oleh')
                ->count(),
            'aktif' => AlumniGlobal::where('is_aktif', true)->count(),
        ];

        return view('dashboard.admin.alumni-global.index', compact('alumnis', 'komunitas', 'stats'));
    }

    /**
     * Simpan Profil Alumni Baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validatedProfile($request, isNewProfile: true);

        AlumniGlobal::create($validated);

        return redirect()->route('dashboard.admin.alumni.index')
            ->with('success', 'Profil alumni ' . $validated['nama_lengkap'] . ' berhasil ditambahkan ke direktori.');
    }

    /**
     * Perbarui Profil Alumni.
     */
    public function update(Request $request, AlumniGlobal $alumni)
    {
        $validated = $this->validatedProfile($request, $alumni);

        $alumni->update($validated);

        return redirect()->route('dashboard.admin.alumni.index')
            ->with('success', 'Data profil alumni ' . $alumni->nama_lengkap . ' berhasil diperbarui.');
    }

    /**
     * Toggle Cepat Status Mentor.
     */
    public function toggleMentor(AlumniGlobal $alumni)
    {
        $alumni->is_mentor = ! $alumni->is_mentor;
        $alumni->save();

        $status = $alumni->is_mentor ? 'diaktifkan sebagai Mentor' : 'dinonaktifkan dari Mentor';
        return back()->with('success', "Status mentor untuk {$alumni->nama_lengkap} berhasil {$status}.");
    }

    /**
     * Toggle Cepat Persetujuan Publikasi Kontak.
     */
    public function toggleKontak(AlumniGlobal $alumni)
    {
        if (! $alumni->kontak_disetujui) {
            // Also scrub stale values on already-private records.
            $alumni->nomor_kontak = null;
            $alumni->email_kontak = null;
            $alumni->kontak_disetujui_pada = null;
            $alumni->kontak_disetujui_oleh = null;
            $alumni->save();

            return back()->with('error', 'Kontak tetap privat. Untuk mengaktifkan publikasi, gunakan Edit setelah memperoleh persetujuan baru dari alumni.');
        }

        $alumni->kontak_disetujui = false;
        $alumni->nomor_kontak = null;
        $alumni->email_kontak = null;
        $alumni->kontak_disetujui_pada = null;
        $alumni->kontak_disetujui_oleh = null;
        $alumni->save();

        return back()->with('success', "Persetujuan publikasi kontak {$alumni->nama_lengkap} dicabut; nomor dan email telah dihapus.");
    }

    /** Validate profile edits and require explicit fresh consent for activation. */
    private function validatedProfile(Request $request, ?AlumniGlobal $alumni = null, bool $isNewProfile = false): array
    {
        $isDemo = $request->boolean('is_demo');
        $publishingContact = ! $isDemo && $request->boolean('kontak_disetujui');
        $convertingDemo = $alumni?->is_demo && ! $isDemo;

        if ($convertingDemo && $publishingContact) {
            throw ValidationException::withMessages([
                'kontak_disetujui' => 'Data DEMO harus disimpan terlebih dahulu sebagai profil privat. Masukkan ulang kontak dan konfirmasi persetujuan baru pada penyuntingan berikutnya.',
            ]);
        }

        $needsFreshConsent = $publishingContact
            && ($isNewProfile
                || ! $alumni?->kontak_disetujui
                || ! $alumni?->kontak_disetujui_pada
                || ! $alumni?->kontak_disetujui_oleh
                || $alumni?->is_demo
                || ($alumni && (
                    $request->input('nomor_kontak') !== $alumni->nomor_kontak
                    || $request->input('email_kontak') !== $alumni->email_kontak
                )));

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'tahun_kelulusan' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'jurusan' => 'required|string|max:255',
            'negara_bekerja' => 'required|string|max:100',
            'kota_wilayah' => 'nullable|string|max:255',
            'bidang_pekerjaan' => 'required|string|max:255',
            'nama_perusahaan' => 'nullable|string|max:255',
            'pengalaman_kerja_singkat' => 'required|string|max:1000',
            'deskripsi_pengalaman' => 'nullable|string|max:3000',
            'tips_karier' => 'nullable|string|max:2000',
            'bahasa_dikuasai' => 'nullable|string|max:255',
            'lama_bekerja' => 'nullable|string|max:100',
            'is_mentor' => 'boolean',
            'kontak_disetujui' => 'nullable|boolean',
            'konfirmasi_persetujuan_alumni' => $needsFreshConsent ? 'required|accepted' : 'nullable',
            'nomor_kontak' => [
                'nullable', 'string', 'max:50',
                ...($publishingContact ? ['required_without:email_kontak'] : []),
            ],
            'email_kontak' => [
                'nullable', 'email', 'max:150',
                ...($publishingContact ? ['required_without:nomor_kontak'] : []),
            ],
            'is_aktif' => 'boolean',
            'is_demo' => 'boolean',
        ]);

        $validated['is_mentor'] = $request->boolean('is_mentor');
        $validated['is_aktif'] = $request->has('is_aktif') ? $request->boolean('is_aktif') : true;
        $validated['is_demo'] = $isDemo;
        $validated['kontak_disetujui'] = $publishingContact;
        unset($validated['konfirmasi_persetujuan_alumni']);

        if ($publishingContact && $needsFreshConsent) {
            $validated['kontak_disetujui_pada'] = now();
            $validated['kontak_disetujui_oleh'] = Auth::id();
        } elseif (! $publishingContact) {
            $validated['nomor_kontak'] = null;
            $validated['email_kontak'] = null;
            $validated['kontak_disetujui_pada'] = null;
            $validated['kontak_disetujui_oleh'] = null;
        }

        return $validated;
    }

    /**
     * Toggle Cepat Keaktifan / Publikasi Profil.
     */
    public function toggleAktif(AlumniGlobal $alumni)
    {
        $alumni->is_aktif = ! $alumni->is_aktif;
        $alumni->save();

        $status = $alumni->is_aktif ? 'dipublikasikan' : 'diarsipkan (non-aktif)';
        return back()->with('success', "Profil {$alumni->nama_lengkap} berhasil {$status}.");
    }

    /**
     * Hapus Profil Alumni.
     */
    public function destroy(AlumniGlobal $alumni)
    {
        $nama = $alumni->nama_lengkap;
        $alumni->delete();

        return redirect()->route('dashboard.admin.alumni.index')
            ->with('success', "Profil alumni {$nama} berhasil dihapus dari direktori.");
    }

    /**
     * Perbarui Pengaturan & Tautan Komunitas WhatsApp Alumni Global.
     */
    public function updateKomunitas(Request $request)
    {
        $validated = $request->validate([
            'nama_komunitas' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:2000',
            'tujuan_komunitas' => 'required|string|max:2000',
            'aturan_komunitas' => 'required|string|max:3000',
            'link_whatsapp' => 'nullable|url|max:255',
            'is_aktif' => 'boolean',
        ]);

        $validated['is_aktif'] = $request->boolean('is_aktif');
        $validated['updated_by'] = Auth::id();

        $komunitas = KomunitasAlumniGlobal::first();
        if ($komunitas) {
            $komunitas->update($validated);
        } else {
            KomunitasAlumniGlobal::create($validated);
        }

        return redirect()->route('dashboard.admin.alumni.index')
            ->with('success', 'Informasi dan pengaturan akses Komunitas WhatsApp Alumni Global berhasil diperbarui.');
    }
}
