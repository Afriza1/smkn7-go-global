<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AlumniGlobalDirectoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
        Schema::create('alumni_globals', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_lengkap');
            $table->string('foto_profil')->nullable();
            $table->unsignedSmallInteger('tahun_kelulusan');
            $table->string('jurusan');
            $table->string('negara_bekerja')->default('Jepang');
            $table->string('kota_wilayah')->nullable();
            $table->string('bidang_pekerjaan');
            $table->string('nama_perusahaan')->nullable();
            $table->text('pengalaman_kerja_singkat');
            $table->text('deskripsi_pengalaman')->nullable();
            $table->text('tips_karier')->nullable();
            $table->string('bahasa_dikuasai')->nullable();
            $table->string('lama_bekerja')->nullable();
            $table->boolean('is_mentor')->default(false);
            $table->boolean('kontak_disetujui')->default(false);
            $table->timestamp('kontak_disetujui_pada')->nullable();
            $table->unsignedBigInteger('kontak_disetujui_oleh')->nullable();
            $table->string('nomor_kontak')->nullable();
            $table->string('email_kontak')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });
        Schema::create('komunitas_alumni_globals', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_komunitas');
            $table->text('deskripsi');
            $table->text('tujuan_komunitas');
            $table->text('aturan_komunitas');
            $table->string('link_whatsapp')->nullable();
            $table->boolean('is_aktif')->default(false);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('tracer_studies', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('record_type');
            $table->string('status_verifikasi')->nullable();
            $table->string('status_kegiatan')->nullable();
            $table->string('alamat_tempat_kerja')->nullable();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    private function profile(string $name, bool $demo, bool $mentor = false, bool $active = true): void
    {
        DB::table('alumni_globals')->insert([
            'nama_lengkap' => $name,
            'tahun_kelulusan' => 2020,
            'jurusan' => 'TKJ',
            'negara_bekerja' => 'Jepang',
            'bidang_pekerjaan' => 'Scaffolding di Jepang',
            'pengalaman_kerja_singkat' => 'Profil uji.',
            'is_mentor' => $mentor,
            'is_aktif' => $active,
            'is_demo' => $demo,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function student(): User
    {
        return new User(['id' => 501, 'name' => 'Siswa Uji', 'email' => 'siswa@example.test', 'role' => 'siswa', 'status' => 'aktif']);
    }

    public function test_page_shows_shared_school_statistics_mentors_and_whatsapp_community_without_directory_ui(): void
    {
        $this->profile('Profil Simulasi', true, true);
        $this->profile('Alumni nyata lain', false);
        $this->profile('Profil mentor lain', false, true);
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Alumni Terverifikasi', 'email' => 'verified@example.test', 'role' => 'alumni', 'status' => 'aktif'],
            ['id' => 2, 'name' => 'Alumni Belum Terverifikasi', 'email' => 'unverified@example.test', 'role' => 'alumni', 'status' => 'aktif'],
            ['id' => 3, 'name' => 'Siswa Uji', 'email' => 'other@example.test', 'role' => 'siswa', 'status' => 'aktif'],
        ]);
        DB::table('tracer_studies')->insert([
            ['user_id' => 1, 'record_type' => 'website', 'status_verifikasi' => 'verified', 'status_kegiatan' => 'bekerja', 'alamat_tempat_kerja' => 'Jepang'],
            ['user_id' => 2, 'record_type' => 'website', 'status_verifikasi' => 'unverified', 'status_kegiatan' => 'bekerja', 'alamat_tempat_kerja' => 'Jepang'],
            ['user_id' => 3, 'record_type' => 'website', 'status_verifikasi' => 'verified', 'status_kegiatan' => 'bekerja', 'alamat_tempat_kerja' => 'Jepang'],
        ]);
        DB::table('komunitas_alumni_globals')->insert([
            'nama_komunitas' => 'Komunitas Alumni SMKN 7 Jember – Go Global Jepang',
            'deskripsi' => 'Komunitas alumni.',
            'tujuan_komunitas' => 'Berjejaring.',
            'aturan_komunitas' => 'Saling menghormati.',
            'link_whatsapp' => 'https://chat.whatsapp.com/official-group',
            'is_aktif' => true,
        ]);

        $html = $this->actingAs($this->student())->get(route('dashboard.alumni-global.index'))
            ->assertOk()
            ->assertSee('Alumni di Luar Negeri')->assertSee('Mentor Aktif')
            ->assertDontSee('Mentor Berpengalaman')->assertDontSee('Negara Penempatan Kerja')
            ->assertSee('Elbim')->assertSee('Scaffolding di Jepang')
            ->assertSee('Siti Rohma Jepang')->assertSee('Caregiver di Jepang')
            ->assertDontSee('Profil Simulasi')->assertDontSee('DEMO')
            ->assertDontSee('Alumni nyata lain')->assertDontSee('Profil mentor lain')
            ->assertSee('Komunitas Alumni SMKN 7 Jember')
            ->assertSee('https://chat.whatsapp.com/official-group')->assertSee('Etika Komunitas WhatsApp')
            ->assertSee('Saling menghormati.')
            ->assertDontSee('+62 856-0288-1235')->assertDontSee('+62 882-1709-2575')
            ->assertDontSee('name="search"', false)->assertDontSee('name="bidang"', false)
            ->assertDontSee('name="negara"', false)->assertDontSee('name="mentor"', false)
            ->assertDontSee('Filter Cepat:')->assertDontSee('Tidak ada data alumni yang cocok')
            ->getContent();

        $this->assertSame(2, substr_count($html, 'Hubungi Mentor'));
        $this->assertSame(1, substr_count($html, 'https://wa.me/6285602881235'));
        $this->assertSame(1, substr_count($html, 'https://wa.me/6288217092575'));
        $this->assertStringContainsString('<div class="text-2xl font-black text-white">14</div>', $html);
        $this->assertStringContainsString('<div class="text-2xl font-black text-[#FBBF24]">2</div>', $html);
        $this->assertStringContainsString('grid-cols-1 sm:grid-cols-2 gap-4', $html);
        $this->assertStringContainsString('grid-cols-1 md:grid-cols-2 gap-5', $html);
        $this->assertStringContainsString('target="_blank"', $html);
    }

    public function test_mentor_filter_does_not_promote_other_profiles_and_demo_detail_is_hidden(): void
    {
        $this->profile('Profil Simulasi', true, true);
        $this->profile('Profil mentor lain', false, true);
        $demo = DB::table('alumni_globals')->where('nama_lengkap', 'Profil Simulasi')->first();
        $this->profile('Elbim', false, true);

        $this->actingAs($this->student())->get(route('dashboard.alumni-global.index', ['mentor' => 1]))
            ->assertOk()->assertDontSee('Profil Simulasi')->assertDontSee('Profil mentor lain')
            ->assertSee('Elbim')->assertSee('Hubungi Mentor');

        $this->actingAs($this->student())->get(route('dashboard.alumni-global.show', $demo->id))->assertNotFound();
    }

    public function test_directory_search_remains_available_in_controller_even_though_its_ui_is_hidden(): void
    {
        $this->profile('Alumni nyata lain', false);
        $response = $this->actingAs($this->student())->get(route('dashboard.alumni-global.index', ['search' => 'Alumni nyata lain']))
            ->assertOk()
            ->assertDontSee('Alumni nyata lain');

        $this->assertSame(1, $response->viewData('alumnis')->total());
    }
}
