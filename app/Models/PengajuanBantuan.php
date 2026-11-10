<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanBantuan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public const STATUS_MENUNGGU_PEMERIKSAAN = 'menunggu_pemeriksaan';
    public const STATUS_PERLU_PERBAIKAN = 'perlu_perbaikan';
    public const STATUS_TERVERIFIKASI_SEKOLAH = 'terverifikasi_sekolah';
    public const STATUS_DITERUSKAN_MITRA = 'diteruskan_mitra';
    public const STATUS_SEDANG_DIPROSES_MITRA = 'sedang_diproses_mitra';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    protected function casts(): array
    {
        return [
            'persetujuan_data' => 'boolean',
            'diverifikasi_pada' => 'datetime',
            'diteruskan_pada' => 'datetime',
            'diputuskan_pada' => 'datetime',
        ];
    }

    public function programBantuan(): BelongsTo
    {
        return $this->belongsTo(ProgramBantuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(DokumenPengajuanBantuan::class);
    }

    public function catatanInternals(): HasMany
    {
        return $this->hasMany(CatatanInternalBantuan::class);
    }

    public function riwayatStatuses(): HasMany
    {
        return $this->hasMany(RiwayatStatusBantuan::class)->latest('id');
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_MENUNGGU_PEMERIKSAAN, self::STATUS_PERLU_PERBAIKAN], true);
    }

    public function canBeFixed(): bool
    {
        return $this->status === self::STATUS_PERLU_PERBAIKAN;
    }

    public function badgeStatus(): array
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU_PEMERIKSAAN => [
                'label' => 'Menunggu Pemeriksaan',
                'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-400',
            ],
            self::STATUS_PERLU_PERBAIKAN => [
                'label' => 'Perlu Perbaikan Dokumen',
                'class' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot' => 'bg-rose-500 animate-pulse',
            ],
            self::STATUS_TERVERIFIKASI_SEKOLAH => [
                'label' => 'Terverifikasi Sekolah',
                'class' => 'bg-blue-50 text-blue-700 border-blue-200',
                'dot' => 'bg-blue-500',
            ],
            self::STATUS_DITERUSKAN_MITRA => [
                'label' => 'Diteruskan ke Mitra',
                'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'dot' => 'bg-indigo-500',
            ],
            self::STATUS_SEDANG_DIPROSES_MITRA => [
                'label' => 'Sedang Diproses Mitra',
                'class' => 'bg-purple-50 text-purple-700 border-purple-200',
                'dot' => 'bg-purple-500',
            ],
            self::STATUS_DISETUJUI => [
                'label' => 'Disetujui Mitra',
                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
            ],
            self::STATUS_DITOLAK => [
                'label' => 'Ditolak',
                'class' => 'bg-slate-100 text-slate-700 border-slate-300',
                'dot' => 'bg-slate-400',
            ],
            self::STATUS_SELESAI => [
                'label' => 'Selesai / Tersalurkan',
                'class' => 'bg-teal-50 text-teal-700 border-teal-200',
                'dot' => 'bg-teal-500',
            ],
            self::STATUS_DIBATALKAN => [
                'label' => 'Dibatalkan Pemohon',
                'class' => 'bg-slate-50 text-slate-500 border-slate-200',
                'dot' => 'bg-slate-300',
            ],
            default => [
                'label' => ucwords(str_replace('_', ' ', $this->status)),
                'class' => 'bg-slate-50 text-slate-700 border-slate-200',
                'dot' => 'bg-slate-400',
            ],
        };
    }
}
