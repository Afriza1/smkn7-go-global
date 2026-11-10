<?php

namespace Database\Seeders;

use App\Models\MitraPembiayaan;
use App\Models\ParameterSimulasiFinansial;
use App\Models\RiwayatSimulasiFinansial;
use App\Models\User;
use Illuminate\Database\Seeder;

class SimulasiFinansialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Parameter Global
        $admin = User::where('role', 'admin_sekolah')->first();

        ParameterSimulasiFinansial::updateOrCreate(
            ['id' => 1],
            [
                'kurs_jpy_idr' => 108.50,
                'default_gaji_jpy' => 180000,
                'default_biaya_keberangkatan' => 35000000,
                'default_biaya_hidup_jepang' => 8500000,
                'default_potongan_lainnya' => 1500000,
                'sumber_informasi' => 'Riset Rata-Rata Biaya BKK SMKN 7 Jember & Asosiasi Pengirim Pemagangan Resmi 2026',
                'keterangan_asumsi' => 'Estimasi gaji kotor mengacu pada upah minimum regional (UMR) di wilayah industri Jepang (Kanto/Kansai/Chubu) rata-rata 180.000 JPY. Biaya hidup mencakup sewa apato bersubsidi, konsumsi makan bulanan, listrik/air/gas, dan paket internet.',
                'tanggal_pembaruan' => now(),
                'updated_by' => $admin?->id,
            ]
        );

        // 2. Data Mitra Pembiayaan
        $mitras = [
            [
                'nama_mitra' => 'Koperasi Pegawai & BKK SMKN 7 Jember',
                'slug' => 'koperasi-bkk-smkn7',
                'jenis_skema' => 'Dana Talangan Vokasi (Pinjaman Lunak Bergulir)',
                'plafon_maksimal' => 25000000,
                'suku_bunga_tahunan' => 0.00,
                'margin_bulanan' => 0.00,
                'metode_perhitungan' => 'flat',
                'tenor_pilihan' => [6, 12, 18, 24],
                'biaya_administrasi_tipe' => 'nominal',
                'biaya_administrasi_nilai' => 150000,
                'uang_muka_persen' => 0.00,
                'metode_pembayaran' => 'Potong Penghasilan Terjadwal via Koordinasi BKK & LPK',
                'persyaratan' => 'Siswa aktif kelas XII atau alumni terdata, telah lolos seleksi awal BKK, dan mendapat persetujuan tertulis orang tua/wali.',
                'sumber_informasi' => 'Keputusan Bersama Manajemen Sekolah & Pengurus Koperasi SMKN 7 Jember',
                'tanggal_berlaku' => '2026-01-01',
                'is_aktif' => true,
                'is_demo' => false,
                'keterangan_status' => 'Skema pinjaman lunak internal sekolah tanpa bunga untuk akselerasi keberangkatan siswa berprestasi.',
            ],
            [
                'nama_mitra' => 'Bank Penyalur KUR Penempatan PMI [Simulasi Lomba]',
                'slug' => 'bank-kur-penempatan-pmi',
                'jenis_skema' => 'Kredit Usaha Rakyat Penempatan Pekerja Migran Indonesia (KUR PMI)',
                'plafon_maksimal' => 35000000,
                'suku_bunga_tahunan' => 6.00,
                'margin_bulanan' => null,
                'metode_perhitungan' => 'anuitas',
                'tenor_pilihan' => [12, 24, 36],
                'biaya_administrasi_tipe' => 'nominal',
                'biaya_administrasi_nilai' => 0,
                'uang_muka_persen' => 0.00,
                'metode_pembayaran' => 'Autodebet Rekening Payroll Bank Penyalur di Jepang',
                'persyaratan' => 'Memiliki perjanjian kerja resmi, visa kerja yang telah disetujui imigrasi Jepang, SKCK, dan rekomendasi BKK.',
                'sumber_informasi' => 'Permenko Perekonomian RI No. 1 Tahun 2022 Pedoman Pelaksanaan KUR PMI [Data Simulasi Lomba]',
                'tanggal_berlaku' => '2026-01-15',
                'is_aktif' => true,
                'is_demo' => true,
                'keterangan_status' => 'Simulasi skema kredit subsidi pemerintah dengan bunga 6% efektif per tahun.',
            ],
            [
                'nama_mitra' => 'Lembaga Pembiayaan Syariah Vokasi [Dalam Penyelarasan]',
                'slug' => 'lembaga-syariah-vokasi',
                'jenis_skema' => 'Pembiayaan Multijasa Syariah',
                'plafon_maksimal' => 30000000,
                'suku_bunga_tahunan' => null,
                'margin_bulanan' => null,
                'metode_perhitungan' => 'belum_tersedia',
                'tenor_pilihan' => [12, 18, 24],
                'biaya_administrasi_tipe' => 'nominal',
                'biaya_administrasi_nilai' => 0,
                'uang_muka_persen' => 0.00,
                'metode_pembayaran' => 'Transfer Antar Bank Bulanan',
                'persyaratan' => 'Dalam proses perumusan nota kesepahaman resmi.',
                'sumber_informasi' => 'Biro Kemitraan SMKN 7 Jember',
                'tanggal_berlaku' => '2026-02-01',
                'is_aktif' => true,
                'is_demo' => true,
                'keterangan_status' => 'Informasi pembiayaan belum tersedia. Skema akad murabahah dan margin pembiayaan sedang dalam proses finalisasi kerja sama resmi.',
            ],
        ];

        foreach ($mitras as $mitra) {
            MitraPembiayaan::updateOrCreate(['slug' => $mitra['slug']], $mitra);
        }

        // 3. Demo Saved Simulation for Siswa Demo User
        $siswa = User::where('email', 'siswa@smkn7jember.sch.id')->first();
        if ($siswa) {
            RiwayatSimulasiFinansial::updateOrCreate(
                [
                    'user_id' => $siswa->id,
                    'judul_simulasi' => 'Estimasi Rencana Mandiri Tokutei Ginou TKJ',
                ],
                [
                    'jenis_simulasi' => 'mandiri',
                    'mitra_pembiayaan_id' => null,
                    'nama_mitra_snapshot' => null,
                    'snapshot_input' => [
                        'biaya_keberangkatan' => 35000000,
                        'gaji_jpy' => 185000,
                        'kurs_jpy' => 108.50,
                        'biaya_hidup' => 8500000,
                        'pengeluaran_lain' => 1500000,
                        'sumber_dana' => 'Tabungan Keluarga & Mandiri',
                    ],
                    'snapshot_parameter' => [
                        'kurs_jpy_idr' => 108.50,
                        'sumber_informasi' => 'Riset Rata-Rata Biaya BKK SMKN 7 Jember',
                    ],
                    'snapshot_hasil' => [
                        'pendapatan_kotor_idr' => 20072500,
                        'total_biaya_keberangkatan' => 35000000,
                        'biaya_hidup_idr' => 8500000,
                        'pengeluaran_lain_idr' => 1500000,
                        'total_pengeluaran_bulanan' => 10000000,
                        'sisa_pendapatan_bulanan' => 10072500,
                        'waktu_pengembalian_bulan' => 4,
                        'status_pengembalian' => 'dapat_dihitung',
                    ],
                    'catatan_pengguna' => 'Simulasi awal untuk target penempatan sektor manufaktur di Prefektur Aichi.',
                ]
            );
        }
    }
}
