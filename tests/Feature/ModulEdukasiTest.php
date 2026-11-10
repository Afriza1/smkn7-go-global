<?php

namespace Tests\Feature;

use App\Models\ModulEdukasiMateri;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ModulEdukasiTest extends TestCase
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
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('role');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
        Schema::create('modul_edukasi_materis', function (Blueprint $table): void {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('kategori');
            $table->string('file_path')->unique();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        foreach ([
            ['Dasar Pekerja Migran Indonesia', 'Menjadi Pekerja Migran Indonesia (PMI) yang Cerdas, Legal, dan Terlindungi', 'materi/Menjadi-Pekerja-Migran-Indonesia.pdf'],
            ['Perlindungan Pekerja Migran Indonesia', 'Panduan Pintar Pekerja Migran Indonesia', 'materi/Panduan-Pintar-Pekerja-Migran-Indonesia.pdf'],
            ['Persiapan Karier Jepang', 'Panduan Sukses & Aman Karir di Jepang', 'materi/Panduan-Sukses-Karier-di-Jepang.pdf'],
        ] as [$category, $title, $file]) {
            ModulEdukasiMateri::create(['kategori' => $category, 'judul' => $title, 'deskripsi' => 'Ringkasan materi yang tersimpan.', 'file_path' => $file, 'is_published' => true]);
        }
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    private function account(string $role): User
    {
        return User::create(['name' => ucfirst($role), 'email' => $role.'@example.test', 'password' => bcrypt('password'), 'role' => $role, 'status' => 'aktif']);
    }

    public function test_siswa_and_alumni_share_three_category_cards_and_category_details(): void
    {
        foreach (['siswa', 'alumni'] as $role) {
            $user = $this->account($role);
            $list = $this->actingAs($user)->get(route('dashboard.modul-edukasi.index'))->assertOk();
            $list->assertSee('Dasar Pekerja Migran Indonesia')
                ->assertSee('Perlindungan Pekerja Migran Indonesia')
                ->assertSee('Persiapan Karier Jepang')
                ->assertSee('Lihat Detail')
                ->assertDontSee('Baca Materi')
                ->assertDontSee('Unduh PDF');

            foreach ([
                'dasar-pekerja-migran-indonesia' => 'Menjadi Pekerja Migran Indonesia (PMI) yang Cerdas, Legal, dan Terlindungi',
                'perlindungan-pekerja-migran-indonesia' => 'Panduan Pintar Pekerja Migran Indonesia',
                'persiapan-karier-jepang' => 'Panduan Sukses & Aman Karir di Jepang',
            ] as $slug => $title) {
                $this->get(route('dashboard.modul-edukasi.category', $slug))->assertOk()
                    ->assertSee('Dashboard')->assertSee('Modul Edukasi')
                    ->assertSee($title)->assertSee('Baca Materi')->assertSee('Unduh PDF')
                    ->assertSee('Kembali ke daftar Modul Edukasi');
            }
        }
    }

    public function test_read_and_download_keep_existing_pdf_file_routes(): void
    {
        $materi = ModulEdukasiMateri::firstOrFail();

        foreach (['siswa', 'alumni'] as $role) {
            $this->actingAs($this->account($role))->get(route('dashboard.modul-edukasi.read', $materi))
                ->assertOk()->assertSee('Pembaca PDF')->assertSee('Unduh PDF');
            $this->get(route('dashboard.modul-edukasi.file', $materi))->assertOk()->assertHeader('Content-Type', 'application/pdf');
            $this->get(route('dashboard.modul-edukasi.download', $materi))->assertOk()->assertHeader('Content-Disposition');
        }
    }

    public function test_unknown_category_and_other_roles_cannot_access_student_alumni_module_routes(): void
    {
        $this->actingAs($this->account('siswa'))
            ->get(route('dashboard.modul-edukasi.category', 'kategori-tidak-dikenal'))->assertNotFound();

        $this->actingAs($this->account('guru'))
            ->get(route('dashboard.modul-edukasi.index'))->assertRedirect();
    }

    public function test_category_without_published_material_shows_empty_state(): void
    {
        ModulEdukasiMateri::query()->where('kategori', 'Persiapan Karier Jepang')->update(['is_published' => false]);

        $this->actingAs($this->account('alumni'))
            ->get(route('dashboard.modul-edukasi.category', 'persiapan-karier-jepang'))
            ->assertOk()->assertSee('Materi belum tersedia');
    }
}
