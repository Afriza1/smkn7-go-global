<?php

namespace Database\Seeders;

use App\Models\CatatanPrivatKonseling;
use App\Models\KonselingDigital;
use App\Models\KontakLayananKonseling;
use App\Models\PesanKonseling;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class KonselingDigitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            Log::warning('KonselingDigitalSeeder dilewati: seeder demo hanya boleh dijalankan di local/testing.');
            return;
        }

        // Add demo contact data only when the service table is empty.
        if (! KontakLayananKonseling::query()->exists()) {
            KontakLayananKonseling::firstOrCreate(
                ['nama_layanan' => 'Pusat Bimbingan Konseling & Pendampingan Karier Global'],
                [
                    'nama_guru_bk' => 'Romi Afrilian, S.Pd. (Koordinator Bimbingan Konseling)',
                    'kontak_resmi' => '(0331) 487722 / WA Hotline BK: 0812-3456-7890',
                    'jam_layanan' => 'Senin - Jumat, Pukul 07.30 - 15.30 WIB',
                    'lokasi_ruangan' => 'Ruang Bimbingan & Konseling (Gedung Utama Lantai 1), SMKN 7 Jember',
                    'email_layanan' => 'bk@smkn7jember.sch.id',
                    'kontak_bantuan_eskalasi' => 'Sekretariat Pimpinan / Kepala Sekolah SMKN 7 Jember (0331) 487720',
                    'keterangan_tambahan' => 'Konsultasi bersifat rahasia dan profesional. Terbuka untuk seluruh siswa aktif dan alumni SMKN 7 Jember.',
                    'is_aktif' => true,
                ]
            );
        }

        $guru = User::where('role', 'guru')->first();
        $siswa = User::where('role', 'siswa')->first();
        $alumni = User::where('role', 'alumni')->first();

        if (! $guru || ! $siswa) {
            return;
        }

        // 2. Demo 1: Konsultasi Online Aktif (Status: berlangsung)
        $konselingOnline = KonselingDigital::firstOrCreate(
            ['nomor_konseling' => 'BK-202611-0001'],
            [
                'user_id' => $siswa->id,
                'guru_id' => $guru->id,
                'kategori' => KonselingDigital::KATEGORI_PERSIAPAN_JEPANG,
                'metode' => KonselingDigital::METODE_ONLINE,
                'alasan_konseling' => 'Ingin berkonsultasi mengenai kesiapan mental dan restu orang tua untuk mengikuti seleksi magang industri manufaktur di Prefektur Aichi, Jepang.',
                'tanggal_usulan' => now()->toDateString(),
                'waktu_usulan' => '10:00 WIB',
                'status' => KonselingDigital::STATUS_BERLANGSUNG,
                'tanggal_jadwal' => now()->toDateString(),
                'jam_mulai' => '10:00',
                'jam_selesai' => '11:00',
                'catatan_jadwal' => 'Ruang chat aktif. Silakan sampaikan hal-hal yang ingin didiskusikan dengan terbuka.',
                'sesi_dimulai_pada' => now()->subMinutes(25),
            ]
        );

        if ($konselingOnline->wasRecentlyCreated) {
            // Pesan demo hanya dibuat untuk konsultasi yang baru dibuat oleh seeder.
            PesanKonseling::create([
                'konseling_id' => $konselingOnline->id,
                'sender_id' => $guru->id,
                'pesan' => "Halo Budi, selamat datang di Ruang Konseling Digital SMKN 7 Jember. Ibu sudah membaca pengajuanmu mengenai kesiapan seleksi ke Jepang. Bagaimana kabarmu hari ini?",
                'is_read' => true,
                'created_at' => now()->subMinutes(20),
            ]);

            PesanKonseling::create([
                'konseling_id' => $konselingOnline->id,
                'sender_id' => $siswa->id,
                'pesan' => "Selamat pagi Ibu Sri. Alhamdulillah kabar baik Bu. Saya sedikit cemas karena orang tua masih ragu melepaskan saya kerja jauh ke Jepang setelah lulus nanti.",
                'is_read' => true,
                'created_at' => now()->subMinutes(16),
            ]);

            PesanKonseling::create([
                'konseling_id' => $konselingOnline->id,
                'sender_id' => $guru->id,
                'pesan' => "Kekhawatiran orang tua sangat wajar, Nak Budi. Nanti sekolah dan BKK siap memfasilitasi sesi sosialisasi khusus bagi orang tua/wali siswa. Yang terpenting kamu tunjukkan komitmen belajar bahasa Jepang dan kedisiplinan terlebih dahulu.",
                'is_read' => true,
                'created_at' => now()->subMinutes(10),
            ]);
        } else {
            Log::warning('KonselingDigitalSeeder konflik nomor demo; konsultasi dan pesannya tidak diubah.', [
                'nomor_konseling' => $konselingOnline->nomor_konseling,
                'konseling_id' => $konselingOnline->id,
            ]);
        }

        // 3. Demo 2: Konsultasi Tatap Muka Terjadwal (Status: dijadwalkan)
        $konselingTatapMuka = KonselingDigital::firstOrCreate(
            ['nomor_konseling' => 'BK-202611-0002'],
            [
                'user_id' => $siswa->id,
                'guru_id' => $guru->id,
                'kategori' => KonselingDigital::KATEGORI_PENDIDIKAN_KARIER,
                'metode' => KonselingDigital::METODE_TATAP_MUKA,
                'alasan_konseling' => 'Konsultasi pilihan peminatan bidang kerja keperawatan lansia (kaigo) vs manufaktur.',
                'tanggal_usulan' => now()->addDays(2)->toDateString(),
                'waktu_usulan' => '13:30 WIB',
                'status' => KonselingDigital::STATUS_DIJADWALKAN,
                'tanggal_jadwal' => now()->addDays(2)->toDateString(),
                'jam_mulai' => '13:30',
                'jam_selesai' => '14:30',
                'tempat_tatap_muka' => 'Ruang Bimbingan Konseling (BK) SMKN 7 Jember - Gedung A Lantai 1',
                'catatan_jadwal' => 'Harap membawa portofolio nilai rapot dan sertifikat ekstrakurikuler jika ada.',
            ]
        );
        $this->reportExistingDemoConsultation($konselingTatapMuka);

        // 4. Demo 3: Pengajuan Baru Masuk dari Alumni (Status: menunggu_konfirmasi)
        if ($alumni) {
            $konselingAlumni = KonselingDigital::firstOrCreate(
                ['nomor_konseling' => 'BK-202611-0003'],
                [
                    'user_id' => $alumni->id,
                    'guru_id' => null,
                    'kategori' => KonselingDigital::KATEGORI_PERSIAPAN_KEBERANGKATAN,
                    'metode' => KonselingDigital::METODE_ONLINE,
                    'alasan_konseling' => 'Konsultasi mengenai tahapan medical check-up tahap akhir dan tes COE (Certificate of Eligibility) untuk visa kerja SSW.',
                    'tanggal_usulan' => now()->addDays(1)->toDateString(),
                    'waktu_usulan' => '09:00 WIB',
                    'status' => KonselingDigital::STATUS_MENUNGGU_KONFIRMASI,
                ]
            );
            $this->reportExistingDemoConsultation($konselingAlumni);
        }

        // 5. Demo 4: Konsultasi Telah Selesai dengan Catatan Tindak Lanjut Privat (Status: selesai)
        $konselingSelesai = KonselingDigital::firstOrCreate(
            ['nomor_konseling' => 'BK-202611-0004'],
            [
                'user_id' => $siswa->id,
                'guru_id' => $guru->id,
                'kategori' => KonselingDigital::KATEGORI_PRIBADI_SOSIAL,
                'metode' => KonselingDigital::METODE_ONLINE,
                'alasan_konseling' => 'Manajemen stres menjelang ujian kemampuan bahasa Jepang (JLPT N4).',
                'tanggal_usulan' => now()->subDays(3)->toDateString(),
                'waktu_usulan' => '14:00 WIB',
                'status' => KonselingDigital::STATUS_SELESAI,
                'tanggal_jadwal' => now()->subDays(3)->toDateString(),
                'jam_mulai' => '14:00',
                'jam_selesai' => '15:00',
                'sesi_dimulai_pada' => now()->subDays(3)->setTime(14, 0),
                'sesi_berakhir_pada' => now()->subDays(3)->setTime(15, 0),
            ]
        );

        if ($konselingSelesai->wasRecentlyCreated) {
            // Catatan privat dibuat sekali dan tidak pernah menimpa catatan existing.
            CatatanPrivatKonseling::firstOrCreate(
                ['konseling_id' => $konselingSelesai->id],
                [
                    'guru_id' => $guru->id,
                    'ringkasan_masalah' => 'Siswa mengalami kecemasan akademis menghadapi tes JLPT N4 karena target kelulusan untuk pengajuan visa kerja.',
                    'tindak_lanjut' => 'Diberikan teknik relaksasi, jadwal belajar terstruktur 1 jam per malam, serta diikutsertakan dalam kelompok belajar tutor sebaya di laboratorium bahasa sekolah.',
                    'evaluasi_psikologis_perilaku' => 'Kondisi emosional stabil, motivasi belajar tinggi, komitmen tinggi untuk memperbaiki manajemen waktu.',
                    'status_urgensi' => 'sedang',
                    'perlu_eskalasi_kepala_sekolah' => false,
                ]
            );
        } else {
            $this->reportExistingDemoConsultation($konselingSelesai);
        }
    }

    private function reportExistingDemoConsultation(KonselingDigital $konseling): void
    {
        if (! $konseling->wasRecentlyCreated) {
            Log::warning('KonselingDigitalSeeder konflik nomor demo; konsultasi yang ada tidak diubah.', [
                'nomor_konseling' => $konseling->nomor_konseling,
                'konseling_id' => $konseling->id,
            ]);
        }
    }
}
