<?php

namespace Tests\Feature;

use App\Http\Controllers\KepalaSekolahKarierJepangController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class KepalaSekolahDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('email'); $table->string('role')->default('siswa'); $table->string('status')->default('aktif'); $table->timestamps();
        });
        Schema::create('lpks', function (Blueprint $table): void {
            $table->id(); $table->string('nama_lpk'); $table->boolean('is_demo')->default(false); $table->timestamps();
        });
        Schema::create('pendaftaran_karier_jepangs', function (Blueprint $table): void {
            $table->id(); $table->string('nomor_pendaftaran'); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('lpk_id');
            $table->string('jenis_peserta')->default('alumni'); $table->string('nama_lengkap')->nullable(); $table->string('email')->nullable();
            $table->string('jurusan')->nullable(); $table->string('status'); $table->string('admin_checked_by')->nullable(); $table->string('bk_checked_by')->nullable(); $table->timestamps();
        });
        Schema::create('riwayat_pendaftaran_kariers', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('pendaftaran_id'); $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); $table->string('status_sebelumnya')->nullable(); $table->string('status_baru'); $table->text('catatan')->nullable(); $table->timestamps();
        });
        Schema::create('alumni_globals', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('user_id')->nullable(); $table->string('nama_lengkap'); $table->string('negara_bekerja')->default('Jepang');
            $table->boolean('is_aktif')->default(true); $table->boolean('is_demo')->default(false); $table->timestamps();
        });
        Schema::create('tracer_studies', function (Blueprint $table): void {
            $table->id(); $table->string('record_type'); $table->unsignedSmallInteger('tahun_pencatatan')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); $table->string('jurusan')->nullable(); $table->string('status_asli')->nullable();
            $table->string('status_kegiatan')->nullable(); $table->string('instansi_asli')->nullable(); $table->string('nama_perusahaan')->nullable();
            $table->string('nama_lengkap_terenkripsi')->nullable(); $table->string('posisi_asli')->nullable(); $table->string('pekerjaan')->nullable();
            $table->string('penempatan_asli')->nullable(); $table->string('alamat_tempat_kerja')->nullable();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    public function test_executive_dashboard_uses_live_sources_and_combines_historical_tracer_data(): void
    {
        foreach (range(1, 3) as $id) DB::table('users')->insert(['id' => $id, 'name' => 'Siswa ' . $id, 'email' => "siswa{$id}@example.test", 'role' => 'siswa', 'status' => 'aktif']);
        DB::table('users')->insert(['id' => 4, 'name' => 'Alumni', 'email' => 'alumni@example.test', 'role' => 'alumni', 'status' => 'aktif']);
        DB::table('alumni_globals')->insert([
            ['user_id' => 4, 'nama_lengkap' => 'Alumni Jepang', 'negara_bekerja' => 'Jepang', 'is_aktif' => true, 'is_demo' => false],
            ['user_id' => 4, 'nama_lengkap' => 'Profil demo', 'negara_bekerja' => 'Jepang', 'is_aktif' => true, 'is_demo' => true],
        ]);

        DB::table('lpks')->insert([['id' => 1, 'nama_lpk' => 'LPK Aktual', 'is_demo' => false], ['id' => 2, 'nama_lpk' => 'LPK Demo', 'is_demo' => true]]);
        foreach (range(1, 3) as $id) {
            DB::table('pendaftaran_karier_jepangs')->insert([
                'id' => $id, 'nomor_pendaftaran' => 'REG-' . $id, 'user_id' => $id, 'lpk_id' => 1,
                'status' => $id < 3 ? 'pelatihan' : 'menunggu_admin', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        DB::table('pendaftaran_karier_jepangs')->insert([
            'id' => 5, 'nomor_pendaftaran' => 'REG-WAITING', 'user_id' => 1, 'lpk_id' => 1,
            'status' => 'menunggu_kepala_sekolah', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('pendaftaran_karier_jepangs')->insert([
            'id' => 4, 'nomor_pendaftaran' => 'REG-DEMO', 'user_id' => 4, 'lpk_id' => 2,
            'status' => 'pelatihan', 'created_at' => now(), 'updated_at' => now(),
        ]);

        for ($index = 0; $index < 84; $index++) {
            $status = $index < 75 ? 'Bekerja' : ($index < 79 ? 'Bekerja_Jepang' : ($index < 83 ? 'Magang - Jepang' : 'Wiraswasta'));
            DB::table('tracer_studies')->insert([
                'record_type' => 'historical', 'tahun_pencatatan' => 2025, 'jurusan' => 'TKJ', 'status_asli' => $status,
                'instansi_asli' => 'Perusahaan ' . ($index % 4), 'posisi_asli' => null, 'penempatan_asli' => null,
            ]);
        }
        for ($index = 0; $index < 45; $index++) {
            DB::table('tracer_studies')->insert([
                'record_type' => 'historical', 'tahun_pencatatan' => 2026, 'jurusan' => 'Teknik Sepeda Motor',
                'instansi_asli' => 'Perusahaan 2026', 'status_asli' => null,
            ]);
        }
        DB::table('tracer_studies')->insert([
            'record_type' => 'website', 'tahun_pencatatan' => 2026, 'user_id' => 4, 'jurusan' => 'TKJ',
            'status_kegiatan' => 'bekerja', 'nama_perusahaan' => 'Perusahaan Website', 'pekerjaan' => 'Scaffolding', 'alamat_tempat_kerja' => 'Jepang',
        ]);
        DB::table('tracer_studies')->insert([
            'record_type' => 'period', 'tahun_pencatatan' => 2026, 'user_id' => null, 'jurusan' => null, 'status_asli' => null,
        ]);

        Auth::setUser(new User(['name' => 'Kepala Sekolah', 'email' => 'kepala@example.test', 'role' => 'kepala_sekolah']));
        $view = app(KepalaSekolahKarierJepangController::class)->index();

        $this->assertSame(874, $view->getData()['ringkasanUtama']['total_siswa']);
        $this->assertSame(14, $view->getData()['ringkasanUtama']['alumni_aktif_jepang']);
        $this->assertSame(3, $view->getData()['ringkasanUtama']['peserta_program_jepang']);
        $this->assertSame(16, $view->getData()['ringkasanUtama']['peserta_pelatihan']);
        $this->assertSame(2, $view->getData()['ringkasanUtama']['mitra_resmi']);
        $this->assertCount(7, $view->getData()['bidangKerja2025']);
        $this->assertSame(13, collect($view->getData()['bidangKerja2025'])->sum(fn ($item) => $item[1]));
        $this->assertCount(1, $view->getData()['menungguPersetujuan']);
        $this->assertSame(130, $view->getData()['tracerStudyStats']['total']);
        $this->assertSame(85, $view->getData()['tracerStudyStats']['status_total']);
        $this->assertCount(5, $view->getData()['tracerStudyStats']['status']);
        $this->assertCount(2, $view->getData()['tracerStudyStats']['years']);

        $view->with('errors', new ViewErrorBag);
        $html = view($view->name(), $view->getData())->render();
        $this->assertStringContainsString('Statistik Tracer Study Alumni', $html);
        $this->assertStringNotContainsString('Pipeline', $html);
        $this->assertStringNotContainsString('Tren Partisipasi Program Go Global', $html);
        $this->assertStringNotContainsString('Perlu Verifikasi', $html);
        $this->assertStringNotContainsString('Berdasarkan file alumni magang Jepang 2025', $html);
        $this->assertStringNotContainsString('Profil demo', $html);
        $this->assertStringContainsString('Laporan Perkembangan Peserta', $html);
        $this->assertStringContainsString('REG-WAITING', $html);
        $this->assertStringContainsString('>874</p>', $html);
        $this->assertStringContainsString('w-4/5 overflow-hidden rounded-full', $html);
        $this->assertStringContainsString('style="width: 100%" title="5 alumni"', $html);
        $this->assertStringContainsString('style="width: 60%" title="3 alumni"', $html);
        $this->assertStringContainsString('min-h-14 flex-col justify-between gap-2', $html);
        $this->assertStringContainsString('max-h-56 space-y-2 overflow-y-auto overscroll-contain', $html);
        $this->assertStringContainsString('Daftar Instansi/Perusahaan', $html);
        $this->assertStringContainsString('Daftar Posisi pekerjaan', $html);
    }
}
