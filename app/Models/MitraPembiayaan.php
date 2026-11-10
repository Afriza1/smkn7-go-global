<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MitraPembiayaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mitra',
        'slug',
        'jenis_skema',
        'plafon_maksimal',
        'suku_bunga_tahunan',
        'margin_bulanan',
        'metode_perhitungan',
        'tenor_pilihan',
        'biaya_administrasi_tipe',
        'biaya_administrasi_nilai',
        'uang_muka_persen',
        'metode_pembayaran',
        'persyaratan',
        'sumber_informasi',
        'tanggal_berlaku',
        'is_aktif',
        'is_demo',
        'keterangan_status',
    ];

    protected function casts(): array
    {
        return [
            'plafon_maksimal' => 'decimal:2',
            'suku_bunga_tahunan' => 'decimal:2',
            'margin_bulanan' => 'decimal:2',
            'biaya_administrasi_nilai' => 'decimal:2',
            'uang_muka_persen' => 'decimal:2',
            'tenor_pilihan' => 'array',
            'tanggal_berlaku' => 'date',
            'is_aktif' => 'boolean',
            'is_demo' => 'boolean',
        ];
    }

    public function riwayatSimulasis(): HasMany
    {
        return $this->hasMany(RiwayatSimulasiFinansial::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Hitung simulasi cicilan pembiayaan sesuai skema asli mitra.
     */
    public function hitungCicilan(float $pokokPembiayaan, int $tenorBulan): array
    {
        if ($this->metode_perhitungan === 'belum_tersedia' || empty($this->metode_perhitungan)) {
            return [
                'status' => 'belum_tersedia',
                'pesan' => 'Informasi pembiayaan belum tersedia atau masih dalam proses konfirmasi kerja sama resmi.',
                'cicilan_bulanan' => 0,
                'total_pembayaran' => 0,
                'total_bunga_margin' => 0,
            ];
        }

        $bungaTahunan = (float) ($this->suku_bunga_tahunan ?? 0);
        $marginBulan = (float) ($this->margin_bulanan ?? 0);

        if ($this->metode_perhitungan === 'flat') {
            $pokokPerBulan = $pokokPembiayaan / $tenorBulan;
            $totalBunga = $pokokPembiayaan * ($bungaTahunan / 100) * ($tenorBulan / 12);
            $bungaPerBulan = $totalBunga / $tenorBulan;
            $cicilanBulanan = round($pokokPerBulan + $bungaPerBulan);
            $totalBayar = round($cicilanBulanan * $tenorBulan);

            return [
                'status' => 'tersedia',
                'metode' => 'Bunga Flat Tahunan (' . $bungaTahunan . '% p.a.)',
                'cicilan_bulanan' => $cicilanBulanan,
                'total_pembayaran' => $totalBayar,
                'total_bunga_margin' => round($totalBunga),
            ];
        }

        if ($this->metode_perhitungan === 'margin_syariah') {
            $totalMargin = $pokokPembiayaan * ($marginBulan / 100) * $tenorBulan;
            $totalHutang = $pokokPembiayaan + $totalMargin;
            $cicilanBulanan = round($totalHutang / $tenorBulan);
            $totalBayar = round($cicilanBulanan * $tenorBulan);

            return [
                'status' => 'tersedia',
                'metode' => 'Akad Murabahah / Margin Syariah (' . $marginBulan . '%/bulan)',
                'cicilan_bulanan' => $cicilanBulanan,
                'total_pembayaran' => $totalBayar,
                'total_bunga_margin' => round($totalMargin),
            ];
        }

        if ($this->metode_perhitungan === 'anuitas') {
            $r = ($bungaTahunan / 100) / 12;
            if ($r > 0) {
                $cicilanBulanan = round($pokokPembiayaan * ($r * pow(1 + $r, $tenorBulan)) / (pow(1 + $r, $tenorBulan) - 1));
            } else {
                $cicilanBulanan = round($pokokPembiayaan / $tenorBulan);
            }
            $totalBayar = round($cicilanBulanan * $tenorBulan);
            $totalBunga = max(0, $totalBayar - $pokokPembiayaan);

            return [
                'status' => 'tersedia',
                'metode' => 'Anuitas Efektif (' . $bungaTahunan . '% p.a.)',
                'cicilan_bulanan' => $cicilanBulanan,
                'total_pembayaran' => $totalBayar,
                'total_bunga_margin' => round($totalBunga),
            ];
        }

        return [
            'status' => 'belum_tersedia',
            'pesan' => 'Metode perhitungan skema pembiayaan mitra belum dikonfirmasi.',
            'cicilan_bulanan' => 0,
            'total_pembayaran' => 0,
            'total_bunga_margin' => 0,
        ];
    }
}
