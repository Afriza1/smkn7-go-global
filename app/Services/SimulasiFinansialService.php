<?php

namespace App\Services;

use App\Models\MitraPembiayaan;
use App\Models\ParameterSimulasiFinansial;

class SimulasiFinansialService
{
    /**
     * Hitung Simulasi Mandiri (Biaya Pribadi / Tabungan).
     */
    public function hitungMandiri(array $data, ParameterSimulasiFinansial $param): array
    {
        $biayaKeberangkatan = (float) ($data['biaya_keberangkatan'] ?? $param->default_biaya_keberangkatan);
        $gajiJpy = (int) ($data['gaji_jpy'] ?? $param->default_gaji_jpy);
        $kursJpy = (float) ($data['kurs_jpy'] ?? $param->kurs_jpy_idr);
        $biayaHidup = (float) ($data['biaya_hidup'] ?? $param->default_biaya_hidup_jepang);
        $pengeluaranLain = (float) ($data['pengeluaran_lain'] ?? 0);
        $sumberDana = $data['sumber_dana'] ?? 'Dana Pribadi / Tabungan Keluarga';

        // 1. Pendapatan kotor dalam Rupiah
        $pendapatanKotorIdr = round($gajiJpy * $kursJpy);

        // 2. Total pengeluaran bulanan di Jepang
        $totalPengeluaranBulanan = round($biayaHidup + $pengeluaranLain);

        // 3. Sisa pendapatan bulanan
        $sisaPendapatanBulanan = round($pendapatanKotorIdr - $totalPengeluaranBulanan);

        // 4. Estimasi waktu pengembalian biaya keberangkatan
        if ($sisaPendapatanBulanan > 0) {
            $waktuPengembalianBulan = (int) ceil($biayaKeberangkatan / $sisaPendapatanBulanan);
            $statusPengembalian = 'dapat_dihitung';
            $keteranganPengembalian = sprintf(
                'Estimasi pengembalian modal biaya keberangkatan tercapai dalam waktu sekitar %d bulan setelah aktif bekerja.',
                $waktuPengembalianBulan
            );
        } else {
            $waktuPengembalianBulan = null;
            $statusPengembalian = 'belum_dapat_dihitung';
            $keteranganPengembalian = 'Pengembalian biaya belum dapat diperkirakan karena sisa pendapatan bulanan nihil atau negatif berdasarkan asumsi pengeluaran saat ini.';
        }

        return [
            'mode' => 'mandiri',
            'pendapatan_kotor_idr' => $pendapatanKotorIdr,
            'gaji_jpy' => $gajiJpy,
            'kurs_jpy' => $kursJpy,
            'total_biaya_keberangkatan' => $biayaKeberangkatan,
            'biaya_hidup_idr' => $biayaHidup,
            'pengeluaran_lain_idr' => $pengeluaranLain,
            'total_pengeluaran_bulanan' => $totalPengeluaranBulanan,
            'sisa_pendapatan_bulanan' => $sisaPendapatanBulanan,
            'waktu_pengembalian_bulan' => $waktuPengembalianBulan,
            'status_pengembalian' => $statusPengembalian,
            'keterangan_pengembalian' => $keteranganPengembalian,
            'sumber_dana' => $sumberDana,
        ];
    }

    /**
     * Hitung Simulasi Mitra Pembiayaan.
     */
    public function hitungMitra(array $data, MitraPembiayaan $mitra, ParameterSimulasiFinansial $param): array
    {
        $biayaKeberangkatan = (float) ($data['biaya_keberangkatan'] ?? $param->default_biaya_keberangkatan);
        $gajiJpy = (int) ($data['gaji_jpy'] ?? $param->default_gaji_jpy);
        $kursJpy = (float) ($data['kurs_jpy'] ?? $param->kurs_jpy_idr);
        $biayaHidup = (float) ($data['biaya_hidup'] ?? $param->default_biaya_hidup_jepang);
        $pengeluaranLain = (float) ($data['pengeluaran_lain'] ?? 0);
        $tenorBulan = (int) ($data['tenor_bulan'] ?? ($mitra->tenor_pilihan[0] ?? 12));

        // Jika skema mitra belum tersedia atau metode belum dikonfirmasi
        if ($mitra->metode_perhitungan === 'belum_tersedia') {
            return [
                'mode' => 'mitra',
                'status' => 'belum_tersedia',
                'nama_mitra' => $mitra->nama_mitra,
                'jenis_skema' => $mitra->jenis_skema,
                'pesan' => 'Informasi pembiayaan belum tersedia. Skema perhitungan resmi sedang dalam proses finalisasi nota kesepahaman (MoU) kemitraan dengan SMKN 7 Jember.',
                'total_biaya_keberangkatan' => $biayaKeberangkatan,
            ];
        }

        // Dana maksimal yang dapat dibiayai mitra
        $plafon = (float) $mitra->plafon_maksimal;
        $danaDibiayai = min($biayaKeberangkatan, $plafon);

        // Uang Muka (DP) jika berlaku
        $dpPersen = (float) $mitra->uang_muka_persen;
        $dpNominal = round($danaDibiayai * ($dpPersen / 100));
        $sisaBiayaSendiri = max(0, $biayaKeberangkatan - $danaDibiayai);
        $totalUangMukaAwal = round($dpNominal + $sisaBiayaSendiri);

        // Pokok Pinjaman yang dicicil
        $pokokCicilan = max(0, $danaDibiayai - $dpNominal);

        // Biaya Administrasi
        $biayaAdmin = 0;
        if ($mitra->biaya_administrasi_tipe === 'persentase') {
            $biayaAdmin = round($pokokCicilan * ((float) $mitra->biaya_administrasi_nilai / 100));
        } else {
            $biayaAdmin = (float) $mitra->biaya_administrasi_nilai;
        }

        // Perhitungan cicilan berdasarkan skema asli mitra
        $kalkulasiCicilan = $mitra->hitungCicilan($pokokCicilan, $tenorBulan);

        if ($kalkulasiCicilan['status'] !== 'tersedia') {
            return [
                'mode' => 'mitra',
                'status' => 'belum_tersedia',
                'nama_mitra' => $mitra->nama_mitra,
                'jenis_skema' => $mitra->jenis_skema,
                'pesan' => $kalkulasiCicilan['pesan'] ?? 'Perhitungan skema cicilan belum dapat dikonfirmasi.',
                'total_biaya_keberangkatan' => $biayaKeberangkatan,
            ];
        }

        $cicilanBulanan = (float) $kalkulasiCicilan['cicilan_bulanan'];
        $totalPembayaranCicilan = (float) $kalkulasiCicilan['total_pembayaran'];
        $totalBungaMargin = (float) $kalkulasiCicilan['total_bunga_margin'];

        // Analisis arus kas bulanan pekerja di Jepang
        $pendapatanKotorIdr = round($gajiJpy * $kursJpy);
        $totalPengeluaranLuarCicilan = round($biayaHidup + $pengeluaranLain);
        $totalPengeluaranDenganCicilan = round($totalPengeluaranLuarCicilan + $cicilanBulanan);
        $sisaPendapatanBulanan = round($pendapatanKotorIdr - $totalPengeluaranDenganCicilan);

        return [
            'mode' => 'mitra',
            'status' => 'tersedia',
            'nama_mitra' => $mitra->nama_mitra,
            'jenis_skema' => $mitra->jenis_skema,
            'metode_perhitungan_label' => $kalkulasiCicilan['metode'] ?? ucfirst($mitra->metode_perhitungan),
            'total_biaya_keberangkatan' => $biayaKeberangkatan,
            'dana_dibiayai' => $danaDibiayai,
            'total_uang_muka_awal' => $totalUangMukaAwal,
            'pokok_cicilan' => $pokokCicilan,
            'tenor_bulan' => $tenorBulan,
            'biaya_administrasi' => $biayaAdmin,
            'cicilan_bulanan' => $cicilanBulanan,
            'total_pembayaran_cicilan' => $totalPembayaranCicilan,
            'total_bunga_margin' => $totalBungaMargin,
            'gaji_jpy' => $gajiJpy,
            'kurs_jpy' => $kursJpy,
            'pendapatan_kotor_idr' => $pendapatanKotorIdr,
            'biaya_hidup_idr' => $biayaHidup,
            'pengeluaran_lain_idr' => $pengeluaranLain,
            'total_pengeluaran_bulanan' => $totalPengeluaranDenganCicilan,
            'sisa_pendapatan_bulanan' => $sisaPendapatanBulanan,
            'metode_pembayaran' => $mitra->metode_pembayaran,
            'persyaratan' => $mitra->persyaratan,
        ];
    }
}
