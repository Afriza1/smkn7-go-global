<?php

namespace Database\Seeders;

use App\Models\AlumniGlobal;
use App\Models\KomunitasAlumniGlobal;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlumniGlobalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin_sekolah')->first();

        // 1. Data Komunitas WhatsApp Alumni Global
        KomunitasAlumniGlobal::updateOrCreate(
            ['id' => 1],
            [
                'nama_komunitas' => 'Komunitas Alumni SMKN 7 Jember – Go Global Jepang',
                'deskripsi' => 'Wadah silaturahmi, pertukaran informasi lowongan kerja, dan pendampingan adaptasi kehidupan bagi seluruh alumni SMKN 7 Jember yang sedang meniti karier profesional di Jepang maupun yang sedang dalam persiapan keberangkatan.',
                'tujuan_komunitas' => "1. Menjalin jejaring komunikasi antargenerasi alumni SMKN 7 Jember di Jepang.\n2. Berbagi informasi praktis seputar kehidupan sehari-hari (seikatsu), administrasi pemda Jepang, dan tips lulus ujian keahlian.\n3. Memberikan mentoring langsung bagi adik-adik kelas dan calon peserta program magang/kerja luar negeri.",
                'aturan_komunitas' => "1. Gunakan bahasa yang sopan, menghargai sesama alumni, dan menjaga nama baik almamater SMKN 7 Jember.\n2. Dilarang menyebarkan berita bohong (hoaks), konten berbau SARA, politik praktis, dan promosi komersial ilegal.\n3. Saling mendukung dan berbagi pengalaman positif mengenai budaya kerja dan keselamatan kerja (K3/5S).\n4. Dilarang membagikan informasi rahasia perusahaan tempat bekerja di Jepang.",
                'link_whatsapp' => 'https://chat.whatsapp.com/demo-alumni-smkn7-jember',
                'is_aktif' => true,
                'updated_by' => $admin?->id,
            ]
        );

        // 2. Data Profil Direktori & Mentor Alumni Global (Bertanda DEMO)
        $alumniData = [
            [
                'nama_lengkap' => 'Rahmat Hidayat, A.Md.T.',
                'tahun_kelulusan' => 2021,
                'jurusan' => 'Teknik Komputer dan Jaringan',
                'negara_bekerja' => 'Jepang',
                'kota_wilayah' => 'Toyota-shi, Prefektur Aichi',
                'bidang_pekerjaan' => 'Manufaktur & Otomasi Pabrik',
                'nama_perusahaan' => 'Toyota Tsusho Precision Parts Co., Ltd.',
                'pengalaman_kerja_singkat' => 'Bekerja sebagai teknisi pemeliharaan sistem kontrol mesin industri PLC dan otomasi pabrik suku cadang otomotif selama lebih dari 3 tahun.',
                'deskripsi_pengalaman' => "Saya berangkat melalui program kerja sama BKK SMKN 7 Jember angkatan 2021. Di Jepang, ketelitian dan kedisiplinan waktu adalah kunci utama. Awalnya tantangan terbesar adalah bahasa teknis industri, namun dengan tekun belajar kaiwa setiap hari, dalam 1 tahun saya berhasil meraih sertifikat JLPT N2 dan dipercaya memegang tim pemeliharaan mesin presisi.",
                'tips_karier' => "Kuasai dasar-dasar elektro dan bahasa Jepang level percakapan praktis (kaiwa). Jangan hanya menghafal kanji untuk ujian, tapi biasakan mendengarkan intonasi instruksi atasan. Selalu terapkan 5S (Seiri, Seiton, Seiso, Seiketsu, Shitsuke) mulai dari sekolah.",
                'bahasa_dikuasai' => 'Bahasa Jepang (JLPT N2), Bahasa Inggris (Dasar)',
                'lama_bekerja' => '3 Tahun 4 Bulan',
                'is_mentor' => true,
                // Demo profiles never represent verified consent from a real alumnus.
                'kontak_disetujui' => false,
                'kontak_disetujui_pada' => null,
                'kontak_disetujui_oleh' => null,
                'nomor_kontak' => null,
                'email_kontak' => null,
                'is_aktif' => true,
                'is_demo' => true,
            ],
            [
                'nama_lengkap' => 'Dewi Anggraini',
                'tahun_kelulusan' => 2022,
                'jurusan' => 'Agribisnis Pengolahan Hasil Pertanian',
                'negara_bekerja' => 'Jepang',
                'kota_wilayah' => 'Mito-shi, Prefektur Ibaraki',
                'bidang_pekerjaan' => 'Pengolahan Makanan & Mutu Pangan',
                'nama_perusahaan' => 'Ibaraki Agricultural Processed Goods K.K.',
                'pengalaman_kerja_singkat' => 'Spesialis pengendalian mutu (Quality Control) produk olahan sayur dan buah beku ekspor pada fasilitas berstandar HACCP di Ibaraki.',
                'deskripsi_pengalaman' => "Karier di bidang food processing di Jepang sangat membutuhkan standar kebersihan dan sanitasi yang ketat. Semua SOP yang diajarkan di laboratorium APHP SMKN 7 Jember sangat relevan dan membuat saya cepat beradaptasi dengan lingkungan industri pangan modern di Jepang.",
                'tips_karier' => "Bagi adik-adik kelas, pelajari istilah kebersihan industri dan keamanan pangan Jepang. Ketahanan fisik saat bekerja di ruang pendingin juga perlu dilatih dengan pola hidup sehat.",
                'bahasa_dikuasai' => 'Bahasa Jepang (JLPT N3)',
                'lama_bekerja' => '2 Tahun 1 Bulan',
                'is_mentor' => true,
                'kontak_disetujui' => false, // Kontak privat sesuai permintaan alumni
                'kontak_disetujui_pada' => null,
                'kontak_disetujui_oleh' => null,
                'nomor_kontak' => null,
                'email_kontak' => null,
                'is_aktif' => true,
                'is_demo' => true,
            ],
            [
                'nama_lengkap' => 'Fajar Nugroho',
                'tahun_kelulusan' => 2020,
                'jurusan' => 'Teknik Pemesinan',
                'negara_bekerja' => 'Jepang',
                'kota_wilayah' => 'Sakai-shi, Prefektur Osaka',
                'bidang_pekerjaan' => 'Fabrikasi Logam & Pengelasan Presisi',
                'nama_perusahaan' => 'Kansai Steel Fabrication Co.',
                'pengalaman_kerja_singkat' => 'Operator mesin bubut CNC dan pengelasan busur listrik baja konstruksi bangunan tahan gempa.',
                'deskripsi_pengalaman' => "Telah menyelesaikan kontrak magang 3 tahun dan kini melanjutkan program Tokutei Ginou (Specified Skilled Worker) bidang permesinan. Gaji dan tunjangan sangat memadai untuk ditabung dan membantu perekonomian keluarga di Jember.",
                'tips_karier' => "Keterampilan membaca gambar teknik proyeksi Amerika dan Eropa wajib dikuasai dengan lancar. Jangan malu bertanya jika belum paham instruksi gambar kerja.",
                'bahasa_dikuasai' => 'Bahasa Jepang (JLPT N3), Dialek Kansai-ben',
                'lama_bekerja' => '4 Tahun 2 Bulan',
                'is_mentor' => true,
                // Demo profiles never represent verified consent from a real alumnus.
                'kontak_disetujui' => false,
                'kontak_disetujui_pada' => null,
                'kontak_disetujui_oleh' => null,
                'nomor_kontak' => null,
                'email_kontak' => null,
                'is_aktif' => true,
                'is_demo' => true,
            ],
            [
                'nama_lengkap' => 'Rizky Pratama',
                'tahun_kelulusan' => 2023,
                'jurusan' => 'Teknik Kendaraan Ringan Otomotif',
                'negara_bekerja' => 'Jepang',
                'kota_wilayah' => 'Hamamatsu, Prefektur Shizuoka',
                'bidang_pekerjaan' => 'Perakitan Komponen Otomotif',
                'nama_perusahaan' => 'Suzuki Parts Sub-assembly Corp.',
                'pengalaman_kerja_singkat' => 'Bekerja pada lini perakitan transmisi dan sistem pengereman kendaraan roda empat generasi terbaru.',
                'deskripsi_pengalaman' => "Baru menyelesaikan tahun pertama penempatan kerja di Hamamatsu. Lingkungan kerja sangat suportif dan banyak alumni SMKN 7 Jember angkatan sebelumnya yang membantu memberi arahan tempat tinggal dan belanja hemat.",
                'tips_karier' => "Jaga ketahanan mental dan fisik. Pembinaan FMD di sekolah sangat bermanfaat ketika kita berada di luar negeri jauh dari keluarga.",
                'bahasa_dikuasai' => 'Bahasa Jepang (JLPT N4)',
                'lama_bekerja' => '1 Tahun 3 Bulan',
                'is_mentor' => false,
                'kontak_disetujui' => false,
                'kontak_disetujui_pada' => null,
                'kontak_disetujui_oleh' => null,
                'nomor_kontak' => null,
                'email_kontak' => null,
                'is_aktif' => true,
                'is_demo' => true,
            ],
            [
                'nama_lengkap' => 'Siti Nurhaliza',
                'tahun_kelulusan' => 2022,
                'jurusan' => 'Akuntansi dan Keuangan Lembaga',
                'negara_bekerja' => 'Jepang',
                'kota_wilayah' => 'Koto-ku, Tokyo',
                'bidang_pekerjaan' => 'Logistik & Pergudangan Internasional',
                'nama_perusahaan' => 'Tokyo Bay Logistics Center Inc.',
                'pengalaman_kerja_singkat' => 'Staff operasional manajemen inventaris dan administrasi kargo ekspor-impor kawasan pelabuhan Tokyo.',
                'deskripsi_pengalaman' => "Membuktikan bahwa jurusan non-teknik dari SMK juga memiliki peluang besar berkarier di Jepang, khususnya di bidang supply chain dan data entry gudang modern berbasis barcode scanner.",
                'tips_karier' => "Kuasai software spreadsheet (Excel) dan terus tingkatkan kemampuan berbicara bahasa Jepang untuk komunikasi koordinasi tim pengiriman.",
                'bahasa_dikuasai' => 'Bahasa Jepang (JLPT N3), Bahasa Inggris (Menengah)',
                'lama_bekerja' => '1 Tahun 9 Bulan',
                'is_mentor' => false,
                'kontak_disetujui' => false,
                'kontak_disetujui_pada' => null,
                'kontak_disetujui_oleh' => null,
                'nomor_kontak' => null,
                'email_kontak' => null,
                'is_aktif' => true,
                'is_demo' => true,
            ],
        ];

        foreach ($alumniData as $data) {
            AlumniGlobal::updateOrCreate(
                ['nama_lengkap' => $data['nama_lengkap']],
                $data
            );
        }
    }
}
