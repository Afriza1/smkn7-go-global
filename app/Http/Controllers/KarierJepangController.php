<?php

namespace App\Http\Controllers;

use App\Models\Lpk;
use App\Models\PendaftaranKarierJepang;
use App\Models\RiwayatPendaftaranKarier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KarierJepangController extends Controller
{
    private const OFFICIAL_PARTNER_NAMES = ['KANZO Indonesia', 'Gakkou Nusantara'];

    private const ALLOWED_MAJORS = [
        'Teknik Komputer dan Jaringan',
        'Teknik Sepeda Motor',
        'Teknik Kendaraan Ringan',
        'Desain Komunikasi Visual',
        'Kecantikan Kulit dan Rambut',
    ];

    /**
     * 1. Halaman Informasi Utama Karier Jepang (Publik).
     */
    public function index()
    {
        abort_if(Auth::check() && Auth::user()->isGuru(), 403, 'Akses ke Portal Karier Jepang tidak tersedia untuk role Guru BK.');

        $lpks = Lpk::aktif()->get();
        $totalPeserta = PendaftaranKarierJepang::count();
        $totalBerangkat = PendaftaranKarierJepang::where('status', 'diberangkatkan')->count();

        return view('pages.karier-jepang.index', compact('lpks', 'totalPeserta', 'totalBerangkat'));
    }

    /**
     * 2. Formulir Pendaftaran Program (Siswa / Alumni).
     */
    public function daftar()
    {
        $user = Auth::user();

        // Validasi tipe role yang berhak
        if (! in_array($user->role, ['siswa', 'alumni'], true)) {
            return redirect()->route('dashboard')->with('error', 'Hanya akun Siswa Aktif dan Alumni yang dapat mendaftar program Karier Jepang.');
        }

        // Cek apakah ada pendaftaran aktif yang belum tuntas/ditutup
        $pendaftaranAktif = PendaftaranKarierJepang::where('user_id', $user->id)
            ->whereNotIn('status', ['ditolak_admin', 'ditolak_kepala_sekolah', 'tidak_lolos_seleksi', 'mengundurkan_diri', 'proses_dihentikan'])
            ->latest()
            ->first();

        if ($pendaftaranAktif) {
            return redirect()->route('karier-jepang.pendaftaran.show', $pendaftaranAktif->id)
                ->with('info', 'Anda telah memiliki pengajuan pendaftaran aktif (' . $pendaftaranAktif->nomor_pendaftaran . '). Pilihan LPK dikunci selama proses seleksi berlangsung.');
        }

        $partnerOptions = $this->officialPartnerOptions();
        $jurusanOptions = self::ALLOWED_MAJORS;

        return view('pages.karier-jepang.daftar', compact('user', 'partnerOptions', 'jurusanOptions'));
    }

    /**
     * 3. Simpan Pendaftaran Baru.
     */
    public function storeDaftar(Request $request)
    {
        $user = Auth::user();

        if (! in_array($user->role, ['siswa', 'alumni'], true)) {
            abort(403, 'Akses tidak diizinkan untuk peran ini.');
        }

        // Cek pendaftaran aktif ganda
        $hasActive = PendaftaranKarierJepang::where('user_id', $user->id)
            ->whereNotIn('status', ['ditolak_admin', 'ditolak_kepala_sekolah', 'tidak_lolos_seleksi', 'mengundurkan_diri', 'proses_dihentikan'])
            ->exists();

        if ($hasActive) {
            return redirect()->route('dashboard')->with('error', 'Anda masih memiliki pendaftaran yang sedang berjalan.');
        }

        $partnerOptions = $this->officialPartnerOptions();
        $allowedLpkIds = collect($partnerOptions)
            ->filter(fn (array $option): bool => $option['selectable'])
            ->map(fn (array $option): int => (int) $option['record']->id)
            ->all();

        if ($allowedLpkIds === []) {
            return back()->withInput()->withErrors([
                'lpk_id' => 'Pilihan mitra resmi belum tersedia pada data aplikasi. Pendaftaran belum dapat dikirim.',
            ]);
        }

        // Validasi input form
        $rules = [
            'lpk_id' => ['required', 'integer', Rule::in($allowedLpkIds)],
            'nama_lengkap' => 'required|string|max:255',
            'jurusan' => ['required', Rule::in(self::ALLOWED_MAJORS)],
            'nomor_telepon' => 'required|string|max:25',
            'email' => 'required|email|max:255',
            'alamat' => 'required|string|max:1000',
            'alasan_minat' => 'nullable|string|max:1000',
            'persetujuan_syarat' => 'accepted',

            // Dokumen persyaratan (PDF / JPG / PNG, max 2MB)
            'file_identitas' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_rapor_ijazah' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_surat_ortu' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_tambahan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ];

        if ($user->role === 'siswa') {
            $rules['nis_nisn'] = 'required|string|max:50';
        } else {
            $rules['tahun_lulus'] = 'required|numeric|min:2010|max:2026';
        }

        $validated = $request->validate($rules, [
            'file_identitas.required' => 'Dokumen KTP atau Kartu Pelajar wajib diunggah.',
            'file_kk.required' => 'Dokumen Kartu Keluarga (KK) wajib diunggah.',
            'file_rapor_ijazah.required' => 'Dokumen Rapor terakhir atau Ijazah wajib diunggah.',
            'file_surat_ortu.required' => 'Surat Pernyataan Orang Tua / Wali wajib diunggah.',
            'file_identitas.max' => 'Ukuran file identitas tidak boleh melebihi 2MB.',
            'file_kk.max' => 'Ukuran file KK tidak boleh melebihi 2MB.',
            'file_rapor_ijazah.max' => 'Ukuran file rapor/ijazah tidak boleh melebihi 2MB.',
            'file_surat_ortu.max' => 'Ukuran file surat pernyataan tidak boleh melebihi 2MB.',
            'persetujuan_syarat.accepted' => 'Anda wajib menyetujui pernyataan keaslian dokumen dan komitmen seleksi.',
            'lpk_id.in' => 'Pilih mitra resmi yang tersedia pada formulir.',
            'jurusan.in' => 'Pilih salah satu dari lima jurusan yang tersedia.',
        ]);

        // Simpan dokumen di private storage (bukan direktori public demi privasi)
        $dir = 'dokumen_karier/' . $user->id;
        $pathIdentitas = $request->file('file_identitas')->store($dir, 'local');
        $pathKk = $request->file('file_kk')->store($dir, 'local');
        $pathRapor = $request->file('file_rapor_ijazah')->store($dir, 'local');
        $pathSuratOrtu = $request->file('file_surat_ortu')->store($dir, 'local');
        $pathTambahan = $request->hasFile('file_tambahan') 
            ? $request->file('file_tambahan')->store($dir, 'local') 
            : null;

        // Generate nomor pendaftaran unik: KJ-YYYYMM-XXXX
        $nomorUrut = PendaftaranKarierJepang::count() + 1;
        $nomorPendaftaran = sprintf('KJ-%s-%04d', now()->format('Ym'), $nomorUrut);

        // Buat record pendaftaran
        $pendaftaran = PendaftaranKarierJepang::create([
            'nomor_pendaftaran' => $nomorPendaftaran,
            'user_id' => $user->id,
            'lpk_id' => $validated['lpk_id'],
            'jenis_peserta' => $user->role,
            'nama_lengkap' => $validated['nama_lengkap'],
            'nis_nisn' => $validated['nis_nisn'] ?? null,
            'tahun_lulus' => $validated['tahun_lulus'] ?? null,
            'jurusan' => $validated['jurusan'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'email' => $validated['email'],
            'alamat' => $validated['alamat'],
            'alasan_minat' => $validated['alasan_minat'] ?? null,
            'status' => 'menunggu_admin',
            'file_identitas' => $pathIdentitas,
            'file_kk' => $pathKk,
            'file_rapor_ijazah' => $pathRapor,
            'file_surat_ortu' => $pathSuratOrtu,
            'file_tambahan' => $pathTambahan,
        ]);

        // Catat di timeline riwayat
        RiwayatPendaftaranKarier::create([
            'pendaftaran_id' => $pendaftaran->id,
            'user_id' => $user->id,
            'action' => 'pendaftaran_baru',
            'status_sebelumnya' => null,
            'status_baru' => 'menunggu_admin',
            'catatan' => 'Pendaftaran program Karier Jepang berhasil dikirimkan oleh calon peserta.',
        ]);

        return redirect()->route('karier-jepang.pendaftaran.show', $pendaftaran->id)
            ->with('success', 'Pendaftaran program Karier Jepang berhasil diajukan! Berkas Anda saat ini berada dalam antrean pemeriksaan Admin Sekolah.');
    }

    /**
     * Resolve official partners from existing records only; never infer or synthesize LPK IDs.
     * Duplicate, inactive, or demo records are shown as unavailable and cannot receive applications.
     *
     * @return array<int, array{name: string, record: ?Lpk, selectable: bool}>
     */
    private function officialPartnerOptions(): array
    {
        $records = Lpk::query()
            ->whereIn('nama_lpk', self::OFFICIAL_PARTNER_NAMES)
            ->get()
            ->groupBy('nama_lpk');

        return collect(self::OFFICIAL_PARTNER_NAMES)->map(function (string $name) use ($records): array {
            $matches = $records->get($name, collect());
            $record = $matches->count() === 1 ? $matches->first() : null;

            return [
                'name' => $name,
                'record' => $record && ! $record->is_demo ? $record : null,
                'selectable' => $record !== null && ! $record->is_demo && $record->is_aktif,
            ];
        })->all();
    }

    /**
     * 4. Halaman Detail Pendaftaran & Progress Tracker 10 Tahapan.
     */
    public function showPendaftaran(PendaftaranKarierJepang $pendaftaran)
    {
        $user = Auth::user();

        // Pemeriksaan Hak Akses: Pemilik pengajuan, Admin, Guru/BK, atau Kepala Sekolah
        $isOwner = $user->id === $pendaftaran->user_id;
        $isStaff = in_array($user->role, ['admin_sekolah', 'guru', 'kepala_sekolah'], true);

        if (! $isOwner && ! $isStaff) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk memeriksa data pendaftaran ini.');
        }

        $pendaftaran->load(['lpk', 'user', 'riwayats.user', 'adminChecker', 'bkChecker', 'kepalaSekolahChecker']);
        $trackerSteps = $pendaftaran->getTrackerSteps();

        return view('pages.karier-jepang.show', compact('pendaftaran', 'trackerSteps', 'isOwner', 'isStaff'));
    }

    /**
     * 5. Form Perbaikan Berkas (Jika Dikembalikan Admin).
     */
    public function formPerbaikan(PendaftaranKarierJepang $pendaftaran)
    {
        $user = Auth::user();

        if ($user->id !== $pendaftaran->user_id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (! $pendaftaran->canBeEditedByApplicant()) {
            return redirect()->route('karier-jepang.pendaftaran.show', $pendaftaran->id)
                ->with('error', 'Status pengajuan Anda saat ini tidak memerlukan perbaikan dokumen.');
        }

        return view('pages.karier-jepang.perbaikan', compact('pendaftaran'));
    }

    /**
     * 6. Simpan Unggah Ulang Berkas Revisi.
     */
    public function storePerbaikan(Request $request, PendaftaranKarierJepang $pendaftaran)
    {
        $user = Auth::user();

        if ($user->id !== $pendaftaran->user_id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        if (! $pendaftaran->canBeEditedByApplicant()) {
            return redirect()->route('karier-jepang.pendaftaran.show', $pendaftaran->id)
                ->with('error', 'Pengajuan tidak dapat disunting pada status saat ini.');
        }

        $request->validate([
            'file_identitas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_rapor_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_surat_ortu' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'catatan_peserta' => 'nullable|string|max:500',
        ]);

        $dir = 'dokumen_karier/' . $user->id;
        $updatedFiles = [];

        if ($request->hasFile('file_identitas')) {
            $pendaftaran->file_identitas = $request->file('file_identitas')->store($dir, 'local');
            $updatedFiles[] = 'KTP / Kartu Pelajar';
        }
        if ($request->hasFile('file_kk')) {
            $pendaftaran->file_kk = $request->file('file_kk')->store($dir, 'local');
            $updatedFiles[] = 'Kartu Keluarga';
        }
        if ($request->hasFile('file_rapor_ijazah')) {
            $pendaftaran->file_rapor_ijazah = $request->file('file_rapor_ijazah')->store($dir, 'local');
            $updatedFiles[] = 'Rapor / Ijazah';
        }
        if ($request->hasFile('file_surat_ortu')) {
            $pendaftaran->file_surat_ortu = $request->file('file_surat_ortu')->store($dir, 'local');
            $updatedFiles[] = 'Surat Pernyataan Orang Tua';
        }

        $statusSebelumnya = $pendaftaran->status;
        $pendaftaran->status = 'menunggu_admin';
        $pendaftaran->save();

        $keteranganPerbaikan = count($updatedFiles) > 0 
            ? 'Memperbarui berkas: ' . implode(', ', $updatedFiles) . '.'
            : 'Mengirimkan konfirmasi ulang dokumen.';

        if ($request->filled('catatan_peserta')) {
            $keteranganPerbaikan .= ' Catatan peserta: ' . $request->catatan_peserta;
        }

        RiwayatPendaftaranKarier::create([
            'pendaftaran_id' => $pendaftaran->id,
            'user_id' => $user->id,
            'action' => 'perbaikan_berkas',
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru' => 'menunggu_admin',
            'catatan' => $keteranganPerbaikan,
        ]);

        return redirect()->route('karier-jepang.pendaftaran.show', $pendaftaran->id)
            ->with('success', 'Dokumen perbaikan berhasil diunggah! Berkas Anda kembali dialihkan ke antrean verifikasi Admin.');
    }

    /**
     * 7. Unduh Dokumen Aman (Private Storage Protection).
     */
    public function unduhDokumen(PendaftaranKarierJepang $pendaftaran, string $jenis)
    {
        $user = Auth::user();

        // Keamanan: Hanya pemilik dan staf sekolah berwenang
        $isOwner = $user->id === $pendaftaran->user_id;
        $isStaff = in_array($user->role, ['admin_sekolah', 'guru', 'kepala_sekolah'], true);

        if (! $isOwner && ! $isStaff) {
            abort(403, 'Akses dokumen ditolak: Anda tidak memiliki wewenang untuk melihat berkas ini.');
        }

        $filePath = match ($jenis) {
            'identitas' => $pendaftaran->file_identitas,
            'kk' => $pendaftaran->file_kk,
            'rapor_ijazah' => $pendaftaran->file_rapor_ijazah,
            'surat_ortu' => $pendaftaran->file_surat_ortu,
            'tambahan' => $pendaftaran->file_tambahan,
            default => null,
        };

        if (! $filePath || ! Storage::disk('local')->exists($filePath)) {
            abort(404, 'Berkas dokumen yang diminta tidak ditemukan pada penyimpanan aman server.');
        }

        return Storage::disk('local')->response($filePath);
    }

    /**
     * 8. Unduh / Lihat Format Draf Template Surat Pernyataan Orang Tua.
     */
    public function unduhTemplatePernyataan()
    {
        return view('pages.karier-jepang.template-pernyataan');
    }
}
