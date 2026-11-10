<?php

namespace Database\Seeders;

use App\Models\Lpk;
use App\Models\PendaftaranKarierJepang;
use App\Models\RiwayatPendaftaranKarier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LpkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Mitra LPK
        $lpks = [
            [
                'nama_lpk' => 'LPK Mitra Kerja Sama Resmi BKK (Jawa Timur)',
                'slug' => 'lpk-mitra-resmi-bkk',
                'ringkasan' => 'Mitra penyalur resmi BKK SMKN 7 Jember untuk penempatan magang teknis industri di wilayah Kanto & Kansai.',
                'deskripsi' => 'Fasilitas kerja sama kelembagaan terpadu antara BKK SMKN 7 Jember dan lembaga pengirim resmi berizin Kemenaker. Fokus pada bidang manufaktur, permesinan, dan kelistrikan.',
                'bidang_program' => 'Ginou Jisshusei (Magang Industri) & Tokutei Ginou Manufaktur',
                'wilayah_penempatan' => 'Tokyo, Kanagawa, Saitama, & Osaka',
                'persyaratan_khusus' => 'Siswa kelas XII semester akhir atau alumni maksimal usia 25 tahun, sehat jasmani rohani, tidak bertato/tindik.',
                'kuota_tersedia' => 30,
                'is_demo' => false,
                'is_aktif' => true,
                'urutan' => 1,
            ],
            [
                'nama_lpk' => 'LPK Hinode Mulia Bersama [Data Simulasi Lomba]',
                'slug' => 'lpk-hinode-mulia-bersama',
                'ringkasan' => 'Lembaga pelatihan vokasi bahasa & budaya Jepang dengan fokus sektor pengolahan pangan dan otomotif.',
                'deskripsi' => '[DATA CONTOH DEMONSTRASI INOVASI LOMBA] Lembaga percontohan simulasi program kerja sama Tokutei Ginou bidang Food Processing dan Otomotif.',
                'bidang_program' => 'Tokutei Ginou Pengolahan Makanan & Perawatan Mesin',
                'wilayah_penempatan' => 'Aichi, Shizuoka, & Mie',
                'persyaratan_khusus' => 'Pria/Wanita lulusan SMK, bersedia mengikuti asrama diklat bahasa standar N4 selama 4 bulan.',
                'kuota_tersedia' => 25,
                'is_demo' => true,
                'is_aktif' => true,
                'urutan' => 2,
            ],
            [
                'nama_lpk' => 'LPK Sakura Global Nusantara [Data Simulasi Lomba]',
                'slug' => 'lpk-sakura-global-nusantara',
                'ringkasan' => 'Lembaga pembekalan kompetensi keperawatan lansia (Careworker) dan perhotelan di Jepang.',
                'deskripsi' => '[DATA CONTOH DEMONSTRASI INOVASI LOMBA] Lembaga percontohan simulasi pelatihan Kaigo (Careworker) dan Hospitality bersertifikat Prometric.',
                'bidang_program' => 'Tokutei Ginou Kaigo (Careworker) & Hospitality',
                'wilayah_penempatan' => 'Tokyo, Chiba, & Fukuoka',
                'persyaratan_khusus' => 'Peminatan keperawatan/layanan lansia, memiliki empati tinggi, lulus ujian bahasa JFT-Basic.',
                'kuota_tersedia' => 20,
                'is_demo' => true,
                'is_aktif' => true,
                'urutan' => 3,
            ],
            [
                'nama_lpk' => 'LPK Mirai Gakkou Mandiri [Data Simulasi Lomba]',
                'slug' => 'lpk-mirai-gakkou-mandiri',
                'ringkasan' => 'Pusat pelatihan magang konstruksi ringan dan agribisnis pertanian cerdas di Jepang barat.',
                'deskripsi' => '[DATA CONTOH DEMONSTRASI INOVASI LOMBA] Lembaga percontohan simulasi program pertanian cerdas dan konstruksi interior modern.',
                'bidang_program' => 'Ginou Jisshusei Pertanian Modern & Konstruksi Interior',
                'wilayah_penempatan' => 'Hiroshima, Okayama, & Hokkaido',
                'persyaratan_khusus' => 'Disiplin tinggi, ketahanan fisik prima, dan siap adaptasi cuaca 4 musim.',
                'kuota_tersedia' => 15,
                'is_demo' => true,
                'is_aktif' => true,
                'urutan' => 4,
            ],
        ];

        foreach ($lpks as $data) {
            Lpk::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // 2. Demonstration Applications for Competition Demo
        $siswaUser = User::where('email', 'siswa@smkn7jember.sch.id')->first();
        $alumniUser = User::where('email', 'alumni@smkn7jember.sch.id')->first();
        $adminUser = User::where('email', 'admin@smkn7jember.sch.id')->first();
        $guruUser = User::where('email', 'guru@smkn7jember.sch.id')->first();
        $kepalaUser = User::where('email', 'kepala.sekolah@smkn7jember.sch.id')->first();
        $lpkUtama = Lpk::first();
        $lpkDemo = Lpk::where('is_demo', true)->first();

        // 2A. Example Pendaftaran Siswa: Menunggu Persetujuan Kepala Sekolah (Ready for Principal demo click!)
        if ($siswaUser && $lpkUtama) {
            $pendaftaranSiswa = PendaftaranKarierJepang::updateOrCreate(
                ['nomor_pendaftaran' => 'KJ-202611-001'],
                [
                    'user_id' => $siswaUser->id,
                    'lpk_id' => $lpkUtama->id,
                    'jenis_peserta' => 'siswa',
                    'nama_lengkap' => $siswaUser->name,
                    'nis_nisn' => '2122.10.450 / 0064829103',
                    'tahun_lulus' => null,
                    'jurusan' => 'Teknik Komputer dan Jaringan',
                    'nomor_telepon' => $siswaUser->phone ?? '081234567892',
                    'email' => $siswaUser->email,
                    'alamat' => 'Jl. Kaliurang No. 45, Sumbersari, Jember',
                    'alasan_minat' => 'Ingin mengembangkan kompetensi teknis di bidang otomatisasi industri Jepang dan berkontribusi membanggakan SMKN 7 Jember.',
                    'status' => 'menunggu_kepala_sekolah',
                    'file_identitas' => 'dokumen_karier/identitas_siswa_dummy.pdf',
                    'file_kk' => 'dokumen_karier/kk_siswa_dummy.pdf',
                    'file_rapor_ijazah' => 'dokumen_karier/rapor_siswa_dummy.pdf',
                    'file_surat_ortu' => 'dokumen_karier/surat_ortu_siswa_dummy.pdf',
                    'catatan_admin' => 'Dokumen lengkap, KTP & KK terverifikasi valid. Persetujuan wali sah bermaterai.',
                    'admin_checked_by' => $adminUser?->id,
                    'admin_checked_at' => now()->subDays(2),
                    'catatan_bk' => 'Siswa memiliki rekam jejak kedisiplinan sangat baik, motivasi tinggi, dan direkomendasikan penuh mengikuti program Go Global.',
                    'bk_checked_by' => $guruUser?->id,
                    'bk_checked_at' => now()->subDay(),
                ]
            );

            RiwayatPendaftaranKarier::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaranSiswa->id,
                    'action' => 'pendaftaran_baru',
                ],
                [
                    'user_id' => $siswaUser->id,
                    'status_sebelumnya' => null,
                    'status_baru' => 'menunggu_admin',
                    'catatan' => 'Pendaftaran program Karier Jepang berhasil diajukan oleh siswa.',
                    'created_at' => now()->subDays(3),
                ]
            );

            RiwayatPendaftaranKarier::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaranSiswa->id,
                    'action' => 'verifikasi_admin_lolos',
                ],
                [
                    'user_id' => $adminUser?->id,
                    'status_sebelumnya' => 'menunggu_admin',
                    'status_baru' => 'menunggu_bk',
                    'catatan' => 'Pemeriksaan dokumen administrasi tuntas dan memenuhi syarat. Diteruskan ke Guru BK.',
                    'created_at' => now()->subDays(2),
                ]
            );

            RiwayatPendaftaranKarier::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaranSiswa->id,
                    'action' => 'pertimbangan_bk',
                ],
                [
                    'user_id' => $guruUser?->id,
                    'status_sebelumnya' => 'menunggu_bk',
                    'status_baru' => 'menunggu_kepala_sekolah',
                    'catatan' => 'Guru BK memberikan rekomendasi positif. Menunggu persetujuan akhir Kepala Sekolah.',
                    'created_at' => now()->subDay(),
                ]
            );
        }

        // 2B. Example Pendaftaran Alumni: Sedang Tahap Pelatihan Bahasa & Budaya (Ready for Progress Tracker demo!)
        if ($alumniUser && $lpkDemo) {
            $pendaftaranAlumni = PendaftaranKarierJepang::updateOrCreate(
                ['nomor_pendaftaran' => 'KJ-202611-002'],
                [
                    'user_id' => $alumniUser->id,
                    'lpk_id' => $lpkDemo->id,
                    'jenis_peserta' => 'alumni',
                    'nama_lengkap' => $alumniUser->name,
                    'nis_nisn' => null,
                    'tahun_lulus' => '2024',
                    'jurusan' => 'Rekayasa Perangkat Lunak',
                    'nomor_telepon' => $alumniUser->phone ?? '081234567893',
                    'email' => $alumniUser->email,
                    'alamat' => 'Jl. Mastrip Timur No. 12, Krajan Timur, Jember',
                    'alasan_minat' => 'Melanjutkan karir internasional di sektor teknologi dan otomasi manufaktur Jepang melalui skema Tokutei Ginou.',
                    'status' => 'pelatihan',
                    'file_identitas' => 'dokumen_karier/identitas_alumni_dummy.pdf',
                    'file_kk' => 'dokumen_karier/kk_alumni_dummy.pdf',
                    'file_rapor_ijazah' => 'dokumen_karier/ijazah_alumni_dummy.pdf',
                    'file_surat_ortu' => 'dokumen_karier/surat_ortu_alumni_dummy.pdf',
                    'catatan_admin' => 'Ijazah SMKN 7 Jember dan SKCK legalisir lengkap.',
                    'admin_checked_by' => $adminUser?->id,
                    'admin_checked_at' => now()->subMonths(2),
                    'catatan_bk' => 'Karakter mandiri, memiliki portofolio kejuruan yang solid.',
                    'bk_checked_by' => $guruUser?->id,
                    'bk_checked_at' => now()->subMonths(2)->addDay(),
                    'catatan_kepala_sekolah' => 'Disetujui. Semoga sukses membawa nama baik SMKN 7 Jember di kancah internasional.',
                    'kepala_sekolah_checked_by' => $kepalaUser?->id,
                    'kepala_sekolah_checked_at' => now()->subMonths(2)->addDays(2),
                    'catatan_lpk_admin' => 'Peserta saat ini menempuh modul intensif Bunpo & Kaiwa N4 di asrama diklat Surabaya.',
                    'lpk_updated_at' => now()->subWeeks(2),
                ]
            );

            RiwayatPendaftaranKarier::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaranAlumni->id,
                    'action' => 'persetujuan_kepala_sekolah',
                ],
                [
                    'user_id' => $kepalaUser?->id,
                    'status_sebelumnya' => 'menunggu_kepala_sekolah',
                    'status_baru' => 'disetujui_sekolah',
                    'catatan' => 'Pengajuan disetujui resmi oleh Kepala Sekolah.',
                    'created_at' => now()->subMonths(2)->addDays(2),
                ]
            );

            RiwayatPendaftaranKarier::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaranAlumni->id,
                    'action' => 'update_status_lpk',
                ],
                [
                    'user_id' => $adminUser?->id,
                    'status_sebelumnya' => 'lolos_seleksi',
                    'status_baru' => 'pelatihan',
                    'catatan' => 'Lolos wawancara LPK dan masuk ke tahap pembekalan intensif bahasa Jepang.',
                    'created_at' => now()->subWeeks(2),
                ]
            );
        }

        // 2C. Create dummy files so file download & preview does not fail with 404
        $dummyDir = storage_path('app/private/dokumen_karier');
        if (!is_dir($dummyDir)) {
            mkdir($dummyDir, 0755, true);
        }
        $dummyFiles = [
            'identitas_siswa_dummy.pdf',
            'kk_siswa_dummy.pdf',
            'rapor_siswa_dummy.pdf',
            'surat_ortu_siswa_dummy.pdf',
            'identitas_alumni_dummy.pdf',
            'kk_alumni_dummy.pdf',
            'ijazah_alumni_dummy.pdf',
            'surat_ortu_alumni_dummy.pdf',
        ];
        foreach ($dummyFiles as $f) {
            $filePath = $dummyDir . DIRECTORY_SEPARATOR . $f;
            if (!file_exists($filePath)) {
                file_put_contents($filePath, "%PDF-1.4\n%SMKN 7 JEMBER GO GLOBAL - DOKUMEN DEMO RESMI LOMBA\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000063 00000 n \n0000000110 00000 n \n0000000160 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n245\n%%EOF");
            }
        }
    }
}
