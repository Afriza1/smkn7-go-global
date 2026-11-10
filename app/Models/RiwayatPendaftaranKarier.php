<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPendaftaranKarier extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'user_id',
        'action',
        'status_sebelumnya',
        'status_baru',
        'catatan',
    ];

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(PendaftaranKarierJepang::class, 'pendaftaran_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Human-readable action label.
     */
    public function getActionLabel(): string
    {
        return match ($this->action) {
            'pendaftaran_baru' => 'Pendaftaran Program Diajukan',
            'perbaikan_berkas' => 'Pengiriman Ulang Berkas Revisi',
            'verifikasi_admin_lolos' => 'Lolos Administrasi & Diteruskan ke BK',
            'verifikasi_admin_revisi' => 'Administrasi Perlu Perbaikan',
            'verifikasi_admin_tolak' => 'Administrasi Ditolak',
            'pertimbangan_bk' => 'Pertimbangan BK Ditambahkan & Diteruskan ke KS',
            'persetujuan_kepala_sekolah' => 'Disetujui Kepala Sekolah',
            'penolakan_kepala_sekolah' => 'Ditolak Kepala Sekolah',
            'update_status_lpk' => 'Pembaruan Status LPK oleh Admin',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }
}
