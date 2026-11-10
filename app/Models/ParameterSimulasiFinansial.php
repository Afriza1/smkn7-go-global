<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParameterSimulasiFinansial extends Model
{
    use HasFactory;

    protected $fillable = [
        'kurs_jpy_idr',
        'default_gaji_jpy',
        'default_biaya_keberangkatan',
        'default_biaya_hidup_jepang',
        'default_potongan_lainnya',
        'sumber_informasi',
        'keterangan_asumsi',
        'tanggal_pembaruan',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'kurs_jpy_idr' => 'decimal:2',
            'default_gaji_jpy' => 'integer',
            'default_biaya_keberangkatan' => 'decimal:2',
            'default_biaya_hidup_jepang' => 'decimal:2',
            'default_potongan_lainnya' => 'decimal:2',
            'tanggal_pembaruan' => 'datetime',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Dapatkan konfigurasi parameter aktif (singleton pattern).
     */
    public static function getActive(): self
    {
        return static::firstOrCreate([], [
            'kurs_jpy_idr' => 108.50,
            'default_gaji_jpy' => 180000,
            'default_biaya_keberangkatan' => 35000000,
            'default_biaya_hidup_jepang' => 8500000,
            'default_potongan_lainnya' => 1500000,
            'sumber_informasi' => 'Riset Rata-Rata Biaya BKK SMKN 7 Jember & Asosiasi Pemagangan Jepang 2026',
            'keterangan_asumsi' => 'Asumsi gaji kotor rata-rata peserta Tokutei Ginou/Ginou Jisshusei tahun pertama sebelum pajak daerah dan asuransi. Biaya hidup mencakup sewa apato subsidi, makan, dan utilitas.',
            'tanggal_pembaruan' => now(),
        ]);
    }
}
