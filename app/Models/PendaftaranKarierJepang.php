<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PendaftaranKarierJepang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pendaftaran',
        'user_id',
        'lpk_id',
        'jenis_peserta',
        'nama_lengkap',
        'nis_nisn',
        'tahun_lulus',
        'jurusan',
        'nomor_telepon',
        'email',
        'alamat',
        'alasan_minat',
        'status',
        'file_identitas',
        'file_kk',
        'file_rapor_ijazah',
        'file_surat_ortu',
        'file_tambahan',
        'catatan_admin',
        'admin_checked_by',
        'admin_checked_at',
        'catatan_bk',
        'bk_checked_by',
        'bk_checked_at',
        'catatan_kepala_sekolah',
        'kepala_sekolah_checked_by',
        'kepala_sekolah_checked_at',
        'catatan_lpk_admin',
        'lpk_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'admin_checked_at' => 'datetime',
            'bk_checked_at' => 'datetime',
            'kepala_sekolah_checked_at' => 'datetime',
            'lpk_updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lpk(): BelongsTo
    {
        return $this->belongsTo(Lpk::class);
    }

    public function adminChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_checked_by');
    }

    public function bkChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bk_checked_by');
    }

    public function kepalaSekolahChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kepala_sekolah_checked_by');
    }

    public function riwayats(): HasMany
    {
        return $this->hasMany(RiwayatPendaftaranKarier::class, 'pendaftaran_id')->latest();
    }

    /**
     * Human-readable Indonesian status label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'menunggu_admin' => 'Menunggu Pemeriksaan Admin',
            'perlu_perbaikan' => 'Perlu Perbaikan Administrasi',
            'ditolak_admin' => 'Ditolak Pemeriksaan Administrasi',
            'menunggu_bk' => 'Menunggu Pertimbangan Guru BK',
            'menunggu_kepala_sekolah' => 'Menunggu Persetujuan Kepala Sekolah',
            'ditolak_kepala_sekolah' => 'Tidak Disetujui Kepala Sekolah',
            'disetujui_sekolah' => 'Disetujui Sekolah & Diteruskan ke LPK',
            'menunggu_seleksi_lpk' => 'Menunggu Seleksi LPK',
            'seleksi_berlangsung' => 'Seleksi LPK Berlangsung',
            'lolos_seleksi' => 'Lolos Seleksi LPK',
            'tidak_lolos_seleksi' => 'Tidak Lolos Seleksi LPK',
            'pelatihan' => 'Pelatihan Bahasa & Budaya',
            'mcu' => 'Medical Check Up (MCU)',
            'pengurusan_dokumen' => 'Pengurusan Dokumen & Visa (COE)',
            'persiapan_keberangkatan' => 'Persiapan Keberangkatan',
            'diberangkatkan' => 'Diberangkatkan ke Jepang',
            'mengundurkan_diri' => 'Mengundurkan Diri',
            'proses_dihentikan' => 'Proses Dihentikan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Tailwind CSS badge styling based on status.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'menunggu_admin', 'menunggu_bk', 'menunggu_kepala_sekolah', 'menunggu_seleksi_lpk' 
                => 'bg-amber-50 text-amber-700 border-amber-200',
            'perlu_perbaikan' 
                => 'bg-orange-50 text-orange-700 border-orange-200 font-bold',
            'ditolak_admin', 'ditolak_kepala_sekolah', 'tidak_lolos_seleksi', 'proses_dihentikan' 
                => 'bg-rose-50 text-rose-700 border-rose-200',
            'mengundurkan_diri'
                => 'bg-slate-100 text-slate-700 border-slate-300',
            'seleksi_berlangsung', 'pelatihan', 'mcu', 'pengurusan_dokumen', 'persiapan_keberangkatan' 
                => 'bg-blue-50 text-[#155EAA] border-blue-200',
            'disetujui_sekolah', 'lolos_seleksi', 'diberangkatkan' 
                => 'bg-emerald-50 text-[#166534] border-emerald-200 font-bold',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    /**
     * Helper to verify if applicant can re-upload / edit documents.
     */
    public function canBeEditedByApplicant(): bool
    {
        return $this->status === 'perlu_perbaikan';
    }

    /**
     * Helper to check if candidate is eligible to submit a brand new application.
     * When rejected or failed selection, applicant can apply again to another LPK.
     */
    public function isFinalClosed(): bool
    {
        return in_array($this->status, [
            'ditolak_admin', 
            'ditolak_kepala_sekolah', 
            'tidak_lolos_seleksi', 
            'mengundurkan_diri', 
            'proses_dihentikan'
        ], true);
    }

    /**
     * Get 10-step Progress Tracker State:
     * Steps:
     * 1. Pendaftaran
     * 2. Pemeriksaan Admin
     * 3. Pertimbangan Guru BK
     * 4. Persetujuan Kepala Sekolah
     * 5. Seleksi LPK
     * 6. Pelatihan
     * 7. MCU
     * 8. Pengurusan Dokumen
     * 9. Persiapan Keberangkatan
     * 10. Keberangkatan
     *
     * Returns array for each step: ['status' => 'completed'|'current'|'pending'|'revision'|'rejected', 'label' => string, 'date' => ?string, 'actor' => ?string, 'note' => ?string]
     */
    public function getTrackerSteps(): array
    {
        $steps = [
            1 => [
                'name' => 'Pendaftaran Akun & Dokumen',
                'description' => 'Pengisian identitas dan unggah berkas awal',
                'status' => 'completed',
                'date' => $this->created_at->format('d M Y, H:i'),
                'actor' => $this->nama_lengkap,
                'note' => 'Berkas pendaftaran berhasil dikirimkan.',
            ],
            2 => [
                'name' => 'Pemeriksaan Administrasi',
                'description' => 'Verifikasi keaslian dan kelengkapan dokumen oleh Admin BKK',
                'status' => 'pending',
                'date' => $this->admin_checked_at?->format('d M Y, H:i'),
                'actor' => $this->adminChecker?->name ?? 'Admin Sekolah (BKK)',
                'note' => $this->catatan_admin,
            ],
            3 => [
                'name' => 'Pertimbangan Guru BK',
                'description' => 'Penilaian kesiapan mental, karakter, dan rekomendasi BK',
                'status' => 'pending',
                'date' => $this->bk_checked_at?->format('d M Y, H:i'),
                'actor' => $this->bkChecker?->name ?? 'Guru BK / Pembimbing',
                'note' => $this->catatan_bk,
            ],
            4 => [
                'name' => 'Persetujuan Kepala Sekolah',
                'description' => 'Keputusan akhir delegasi pendaftaran dari pimpinan sekolah',
                'status' => 'pending',
                'date' => $this->kepala_sekolah_checked_at?->format('d M Y, H:i'),
                'actor' => $this->kepalaSekolahChecker?->name ?? 'Abdul Hayyi, S.Pd.',
                'note' => $this->catatan_kepala_sekolah,
            ],
            5 => [
                'name' => 'Seleksi Mitra LPK',
                'description' => 'Tes kompetensi, wawancara, dan penyelarasan pihak LPK',
                'status' => 'pending',
                'date' => null,
                'actor' => $this->lpk?->nama_lpk ?? 'LPK Mitra',
                'note' => null,
            ],
            6 => [
                'name' => 'Pelatihan Bahasa & Budaya',
                'description' => 'Pembekalan intensif standar N5/N4 dan etika kerja 5S',
                'status' => 'pending',
                'date' => null,
                'actor' => 'Pusat Diklat LPK & BKK',
                'note' => null,
            ],
            7 => [
                'name' => 'Medical Check Up (MCU)',
                'description' => 'Pemeriksaan kesehatan standar ketenagakerjaan Jepang',
                'status' => 'pending',
                'date' => null,
                'actor' => 'Klinik / RS Rekanan Resmi',
                'note' => null,
            ],
            8 => [
                'name' => 'Pengurusan Dokumen & Visa',
                'description' => 'Penerbitan COE dari Imigrasi Jepang & Visa Kerja',
                'status' => 'pending',
                'date' => null,
                'actor' => 'Imigrasi Jepang & Kedubes',
                'note' => null,
            ],
            9 => [
                'name' => 'Persiapan Keberangkatan',
                'description' => 'Pelepasan resmi, tiket penerbangan & orientasi kedatangan',
                'status' => 'pending',
                'date' => null,
                'actor' => 'SMKN 7 Jember & LPK',
                'note' => null,
            ],
            10 => [
                'name' => 'Keberangkatan & Penempatan',
                'description' => 'Tiba di Jepang dan mulai aktif bekerja / magang industri',
                'status' => 'pending',
                'date' => null,
                'actor' => 'Perusahaan Penerima di Jepang',
                'note' => null,
            ],
        ];

        // Evaluate step 2 (Admin)
        if ($this->status === 'menunggu_admin') {
            $steps[2]['status'] = 'current';
        } elseif ($this->status === 'perlu_perbaikan') {
            $steps[2]['status'] = 'revision';
        } elseif ($this->status === 'ditolak_admin') {
            $steps[2]['status'] = 'rejected';
        } else {
            $steps[2]['status'] = 'completed';
        }

        // Evaluate step 3 (BK)
        if (in_array($this->status, ['menunggu_admin', 'perlu_perbaikan', 'ditolak_admin'])) {
            $steps[3]['status'] = 'pending';
        } elseif ($this->status === 'menunggu_bk') {
            $steps[3]['status'] = 'current';
        } else {
            $steps[3]['status'] = 'completed';
        }

        // Evaluate step 4 (Kepala Sekolah)
        if (in_array($this->status, ['menunggu_admin', 'perlu_perbaikan', 'ditolak_admin', 'menunggu_bk'])) {
            $steps[4]['status'] = 'pending';
        } elseif ($this->status === 'menunggu_kepala_sekolah') {
            $steps[4]['status'] = 'current';
        } elseif ($this->status === 'ditolak_kepala_sekolah') {
            $steps[4]['status'] = 'rejected';
        } else {
            $steps[4]['status'] = 'completed';
        }

        // Evaluate post-approval steps (5 - 10)
        $postApprovalOrder = [
            'disetujui_sekolah' => 5,
            'menunggu_seleksi_lpk' => 5,
            'seleksi_berlangsung' => 5,
            'tidak_lolos_seleksi' => 5,
            'lolos_seleksi' => 6,
            'pelatihan' => 6,
            'mcu' => 7,
            'pengurusan_dokumen' => 8,
            'persiapan_keberangkatan' => 9,
            'diberangkatkan' => 10,
        ];

        if (isset($postApprovalOrder[$this->status])) {
            $currentIdx = $postApprovalOrder[$this->status];
            for ($i = 5; $i <= 10; $i++) {
                if ($i < $currentIdx) {
                    $steps[$i]['status'] = 'completed';
                } elseif ($i === $currentIdx) {
                    if ($this->status === 'tidak_lolos_seleksi') {
                        $steps[$i]['status'] = 'rejected';
                        $steps[$i]['note'] = $this->catatan_lpk_admin ?? 'Peserta dinyatakan belum lolos tahap seleksi LPK.';
                    } elseif ($this->status === 'diberangkatkan') {
                        $steps[$i]['status'] = 'completed';
                        $steps[$i]['note'] = 'Selamat! Peserta telah resmi diberangkatkan ke Jepang.';
                    } else {
                        $steps[$i]['status'] = 'current';
                        $steps[$i]['note'] = $this->catatan_lpk_admin ?? 'Tahap sedang berjalan.';
                    }
                    $steps[$i]['date'] = $this->lpk_updated_at?->format('d M Y, H:i');
                } else {
                    $steps[$i]['status'] = 'pending';
                }
            }
        } elseif (in_array($this->status, ['mengundurkan_diri', 'proses_dihentikan'])) {
            $steps[5]['status'] = 'rejected';
            $steps[5]['note'] = 'Proses program dihentikan / peserta mengundurkan diri.';
        }

        return $steps;
    }
}
