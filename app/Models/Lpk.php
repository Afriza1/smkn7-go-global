<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lpk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lpk',
        'slug',
        'ringkasan',
        'deskripsi',
        'bidang_program',
        'wilayah_penempatan',
        'persyaratan_khusus',
        'kuota_tersedia',
        'is_demo',
        'is_aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
            'is_aktif' => 'boolean',
            'kuota_tersedia' => 'integer',
            'urutan' => 'integer',
        ];
    }

    /**
     * Relationship with Pendaftaran.
     */
    public function pendaftarans(): HasMany
    {
        return $this->hasMany(PendaftaranKarierJepang::class);
    }

    /**
     * Scope for active LPKs.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)->orderBy('urutan', 'asc');
    }
}
