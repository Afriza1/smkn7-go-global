<?php

namespace Database\Seeders;

use App\Models\CatatanInternalBantuan;
use App\Models\DokumenPengajuanBantuan;
use App\Models\PengajuanBantuan;
use App\Models\ProgramBantuan;
use App\Models\RiwayatStatusBantuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BantuanSosialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin_sekolah')->first();
        $siswa = User::where('role', 'siswa')->first();
        $alumni = User::where('role', 'alumni')->first();

        // 1. Program Bantuan Resmi & Kemitraan Nyata SMKN 7 Jember
        $programs = [
            [
                'nama_program' => 'Program Dana Talangan Keberangkatan BMT NU Tanggul',
                'slug' => 'dana-talangan-bmt-nu-tanggul',
                'nama_mitra' => 'BMT NU Tanggul',
                'jenis_program' => 'dana_talangan',
                'deskripsi' => 'Fasilitas pembiayaan talangan persiapan dan keberangkatan kerja ke Jepang dengan prinsip syariah tanpa agunan fisik berlebihan bagi siswa dan alumni SMKN 7 Jember yang telah dinyatakan lulus seleksi wawancara kerja / user interview.',
                'persyaratan' => "1. Siswa aktif kelas XII atau alumni resmi SMKN 7 Jember.\n2. Telah dinyatakan lulus wawancara kerja (Job Matching / User Interview) atau memiliki bukti pra-kontrak.\n3. Mendapatkan surat rekomendasi resmi dari BKK SMKN 7 Jember.\n4. Memiliki persetujuan tertulis dari orang tua/wali murid.\n5. Berkomitmen melunasi pembiayaan secara tertib setelah menerima gaji di Jepang.",
                'dokumen_dibutuhkan' => [
                    'KTP Siswa / Alumni',
                    'Kartu Keluarga (KK)',
                    'Surat Rekomendasi BKK SMKN 7 Jember',
                    'Bukti Kelulusan Wawancara / Kontrak Kerja',
                    'Surat Persetujuan Orang Tua Bermaterai',
                    'Rincian Estimasi Kebutuhan Biaya Keberangkatan'
                ],
                'periode_mulai' => now()->subMonth(),
                'periode_selesai' => now()->addMonths(6),
                'is_aktif' => true,
                'is_demo' => true,
                'keterangan_status' => 'Program aktif dibuka untuk peserta yang telah lulus tahap interview LPK mitra.',
            ],
            [
                'nama_program' => 'Beasiswa Subsidi Pelatihan Bahasa & FMD LPK Genzo',
                'slug' => 'beasiswa-pelatihan-lpk-genzo',
                'nama_mitra' => 'LPK Genzo',
                'jenis_program' => 'beasiswa_pelatihan',
                'deskripsi' => 'Program beasiswa subsidi pemotongan biaya pendidikan bahasa Jepang intensif (N5-N4) dan pembinaan Fisik, Mental, dan Disiplin (FMD) bagi calon tenaga kerja dari keluarga pra-sejahtera berprestasi.',
                'persyaratan' => "1. Siswa kelas XII atau alumni SMKN 7 Jember maksimal 2 tahun kelulusan.\n2. Rata-rata nilai rapor kejuruan minimal 80.00.\n3. Berasal dari keluarga pra-sejahtera (dibuktikan dengan SKTM dari desa/kelurahan setempat).\n4. Lolos tes kesamaptaan fisik dasar yang diselenggarakan oleh BKK SMKN 7 Jember.",
                'dokumen_dibutuhkan' => [
                    'KTP / Kartu Pelajar',
                    'Kartu Keluarga (KK)',
                    'Surat Keterangan Tidak Mampu (SKTM)',
                    'Fotokopi Rapor Terakhir / Ijazah Legalitas',
                    'Surat Pernyataan Komitmen Mengikuti Pelatihan Hingga Tuntas'
                ],
                'periode_mulai' => now()->subMonths(2),
                'periode_selesai' => now()->addMonths(4),
                'is_aktif' => true,
                'is_demo' => true,
                'keterangan_status' => 'Kuota tersedia: 15 penerima beasiswa per gelombang keberangkatan.',
            ],
            [
                'nama_program' => 'Subsidi Biaya Medical Check-Up & Paspor Go Global',
                'slug' => 'subsidi-mcu-paspor-go-global',
                'nama_mitra' => 'BKK SMKN 7 Jember & Bank BNT Peduli',
                'jenis_program' => 'subsidi_keberangkatan',
                'deskripsi' => 'Bantuan dana stimulan langsung untuk meringankan biaya pemeriksaan kesehatan menyeluruh (MCU) di laboratorium rujukan resmi serta biaya penerbitan paspor elektronik 10 tahun bagi siswa yang lolos seleksi tahap 1.',
                'persyaratan' => "1. Siswa atau alumni yang telah dinyatakan lolos seleksi berkas dan tes fisik tahap 1 di LPK mitra.\n2. Memiliki catatan kehadiran dan kedisiplinan baik di sekolah.",
                'dokumen_dibutuhkan' => [
                    'KTP Siswa / Alumni',
                    'Kartu Keluarga (KK)',
                    'Bukti Tanda Kelulusan Seleksi Tahap 1',
                    'Kuitansi / Rincian Biaya Rumah Sakit Rujukan'
                ],
                'periode_mulai' => now()->subMonth(),
                'periode_selesai' => now()->addMonths(3),
                'is_aktif' => true,
                'is_demo' => true,
                'keterangan_status' => 'Dana bantuan stimulan langsung ditransfer ke rekening rumah sakit / vendor paspor.',
            ],
            [
                'nama_program' => 'Skema Pendampingan Finansial Mitra Goko [DEMO]',
                'slug' => 'pendampingan-finansial-goko',
                'nama_mitra' => 'Goko',
                'jenis_program' => 'pinjaman_lunak',
                'deskripsi' => 'Program eksplorasi skema bantuan pembiayaan kerja sama mitra internasional Goko (Status kuota menunggu pembaharuan resmi).',
                'persyaratan' => "1. Alumni SMKN 7 Jember bidang kejuruan otomotif dan pengelasan.",
                'dokumen_dibutuhkan' => ['KTP', 'Kartu Keluarga', 'Sertifikat Kompetensi Keahlian'],
                'periode_mulai' => null,
                'periode_selesai' => null,
                'is_aktif' => false, // Non-aktif / Kuota belum dibuka
                'is_demo' => true,
                'keterangan_status' => 'Informasi skema kerja sama sedang dalam tahap finalisasi nota kesepahaman (MoU). Pendaftaran belum dibuka.',
            ]
        ];

        $savedPrograms = [];
        foreach ($programs as $item) {
            $savedPrograms[$item['slug']] = ProgramBantuan::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

        // 2. Demo Pengajuan Permohonan oleh Siswa (Budi Santoso) - Status: menunggu_pemeriksaan
        if ($siswa && isset($savedPrograms['dana-talangan-bmt-nu-tanggul'])) {
            $p1 = $savedPrograms['dana-talangan-bmt-nu-tanggul'];
            $pengajuan1 = PengajuanBantuan::updateOrCreate(
                ['nomor_pengajuan' => 'BANSOS-202611-0001'],
                [
                    'program_bantuan_id' => $p1->id,
                    'user_id' => $siswa->id,
                    'alasan_pengajuan' => 'Keluarga saya memiliki keterbatasan dana tunai untuk biaya paspor dan visa keberangkatan, sementara saya sudah dinyatakan lulus user interview bidang manufaktur di Aichi.',
                    'informasi_tambahan' => 'Orang tua bekerja sebagai petani dan siap menjadi penjamin pelunasan pembiayaan setelah penempatan kerja resmi.',
                    'status' => PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN,
                    'keterangan_pemohon' => 'Berkas pendaftaran Anda telah berhasil terkirim dan saat ini sedang menunggu antrean pemeriksaan administrasi oleh staf BKK.',
                    'persetujuan_data' => true,
                ]
            );

            // Simpan mock file privat
            $mockDir = 'dokumen_bantuan/' . $pengajuan1->id;
            Storage::disk('local')->put($mockDir . '/ktp_budi.pdf', 'MOCK PDF DATA KTP BUDI SANTOSO');
            Storage::disk('local')->put($mockDir . '/kk_budi.pdf', 'MOCK PDF DATA KK BUDI SANTOSO');

            DokumenPengajuanBantuan::updateOrCreate(
                ['pengajuan_bantuan_id' => $pengajuan1->id, 'nama_dokumen' => 'KTP Siswa / Alumni'],
                [
                    'file_path' => $mockDir . '/ktp_budi.pdf',
                    'file_name' => 'ktp_budi_santoso.pdf',
                    'file_size' => 1024 * 350,
                    'mime_type' => 'application/pdf',
                ]
            );

            DokumenPengajuanBantuan::updateOrCreate(
                ['pengajuan_bantuan_id' => $pengajuan1->id, 'nama_dokumen' => 'Kartu Keluarga (KK)'],
                [
                    'file_path' => $mockDir . '/kk_budi.pdf',
                    'file_name' => 'kartu_keluarga_budi.pdf',
                    'file_size' => 1024 * 512,
                    'mime_type' => 'application/pdf',
                ]
            );

            RiwayatStatusBantuan::updateOrCreate(
                ['pengajuan_bantuan_id' => $pengajuan1->id, 'status_baru' => PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN],
                [
                    'user_id' => $siswa->id,
                    'status_sebelumnya' => null,
                    'keterangan' => 'Permohonan bantuan diajukan secara mandiri oleh siswa melalui website Go Global.',
                ]
            );
        }

        // 3. Demo Pengajuan Permohonan oleh Alumni (Bayu Pratama) - Status: terverifikasi_sekolah
        if ($alumni && isset($savedPrograms['beasiswa-pelatihan-lpk-genzo'])) {
            $p2 = $savedPrograms['beasiswa-pelatihan-lpk-genzo'];
            $pengajuan2 = PengajuanBantuan::updateOrCreate(
                ['nomor_pengajuan' => 'BANSOS-202611-0002'],
                [
                    'program_bantuan_id' => $p2->id,
                    'user_id' => $alumni->id,
                    'alasan_pengajuan' => 'Ingin mengikuti pelatihan bahasa Jepang intensif di LPK Genzo namun terkendala biaya kursus penuh.',
                    'informasi_tambahan' => 'Memiliki prestasi kejuaraan LKS tingkat kabupaten di bidang otomotif.',
                    'status' => PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH,
                    'keterangan_pemohon' => 'Seluruh berkas persyaratan telah lengkap dan divalidasi oleh BKK. Dokumen permohonan sedang disiapkan untuk diteruskan kepada pengurus beasiswa LPK Genzo.',
                    'persetujuan_data' => true,
                    'diverifikasi_oleh' => $admin?->id,
                    'diverifikasi_pada' => now()->subDays(2),
                ]
            );

            RiwayatStatusBantuan::updateOrCreate(
                ['pengajuan_bantuan_id' => $pengajuan2->id, 'status_baru' => PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH],
                [
                    'user_id' => $admin?->id ?? $alumni->id,
                    'status_sebelumnya' => PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN,
                    'keterangan' => 'Verifikasi berkas persyaratan dinyatakan lolos dan memenuhi kriteria beasiswa pra-sejahtera.',
                ]
            );
        }

        // 4. Demo Pengajuan Permohonan - Status: diteruskan_mitra dengan Catatan Internal Koordinasi
        if ($siswa && isset($savedPrograms['dana-talangan-bmt-nu-tanggul'])) {
            $pengajuan3 = PengajuanBantuan::updateOrCreate(
                ['nomor_pengajuan' => 'BANSOS-202611-0003'],
                [
                    'program_bantuan_id' => $savedPrograms['dana-talangan-bmt-nu-tanggul']->id,
                    'user_id' => $siswa->id,
                    'alasan_pengajuan' => 'Membutuhkan dana talangan tiket pesawat dan MCU tahap akhir program magang Jepang.',
                    'informasi_tambahan' => 'Sudah menerima Certificate of Eligibility (COE) dari imigrasi Jepang.',
                    'status' => PengajuanBantuan::STATUS_DITERUSKAN_MITRA,
                    'keterangan_pemohon' => 'Berkas Anda telah resmi diserahkan ke pihak analis pembiayaan BMT NU Tanggul untuk proses persetujuan akad pembiayaan.',
                    'persetujuan_data' => true,
                    'diverifikasi_oleh' => $admin?->id,
                    'diverifikasi_pada' => now()->subDays(5),
                    'diteruskan_pada' => now()->subDays(3),
                ]
            );

            // Catatan Internal Admin (Hanya dibaca Admin BKK)
            if ($admin) {
                CatatanInternalBantuan::updateOrCreate(
                    [
                        'pengajuan_bantuan_id' => $pengajuan3->id,
                        'pihak_mitra' => 'Bpk. Ahmad Fauzi (Manajer Operasional BMT NU Tanggul)',
                    ],
                    [
                        'admin_id' => $admin->id,
                        'tanggal_koordinasi' => now()->subDays(3)->toDateString(),
                        'catatan_koordinasi' => 'Telah menyerahkan berkas fisik siswa atas nama Budi Santoso di kantor BMT NU Tanggul. Pihak analis meminta konfirmasi nomor kontrak kerja di Jepang dan jadwal estimasi keberangkatan kloter.',
                        'keputusan_mitra' => 'Sedang dalam review komite pembiayaan mikro syariah.',
                    ]
                );
            }

            RiwayatStatusBantuan::updateOrCreate(
                ['pengajuan_bantuan_id' => $pengajuan3->id, 'status_baru' => PengajuanBantuan::STATUS_DITERUSKAN_MITRA],
                [
                    'user_id' => $admin?->id ?? $siswa->id,
                    'status_sebelumnya' => PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH,
                    'keterangan' => 'Berkas fisik dan data rekomendasi sekolah telah disampaikan ke pihak BMT NU Tanggul.',
                ]
            );
        }
    }
}
