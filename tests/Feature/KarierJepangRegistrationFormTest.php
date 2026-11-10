<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KarierJepangRegistrationFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('email')->unique(); $table->string('phone')->nullable();
            $table->string('password')->nullable(); $table->string('role'); $table->string('status')->default('aktif'); $table->timestamps();
        });
        Schema::create('lpks', function (Blueprint $table): void {
            $table->id(); $table->string('nama_lpk'); $table->string('slug')->unique(); $table->string('ringkasan')->nullable();
            $table->text('deskripsi')->nullable(); $table->string('bidang_program')->nullable(); $table->string('wilayah_penempatan')->nullable();
            $table->text('persyaratan_khusus')->nullable(); $table->integer('kuota_tersedia')->default(0); $table->boolean('is_demo')->default(false);
            $table->boolean('is_aktif')->default(true); $table->integer('urutan')->default(0); $table->timestamps();
        });
        Schema::create('pendaftaran_karier_jepangs', function (Blueprint $table): void {
            $table->id(); $table->string('nomor_pendaftaran')->unique(); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('lpk_id');
            $table->string('jenis_peserta'); $table->string('nama_lengkap'); $table->string('nis_nisn')->nullable(); $table->string('tahun_lulus')->nullable();
            $table->string('jurusan'); $table->string('nomor_telepon'); $table->string('email'); $table->text('alamat'); $table->text('alasan_minat')->nullable();
            $table->string('status'); $table->string('file_identitas'); $table->string('file_kk'); $table->string('file_rapor_ijazah');
            $table->string('file_surat_ortu'); $table->string('file_tambahan')->nullable(); $table->timestamps();
        });
        Schema::create('riwayat_pendaftaran_kariers', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('pendaftaran_id'); $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action'); $table->string('status_sebelumnya')->nullable(); $table->string('status_baru'); $table->text('catatan')->nullable(); $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Storage::forgetDisk('local');
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    private function account(string $role, string $name): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)).'@example.test',
            'password' => bcrypt('test-password'),
            'role' => $role,
            'status' => 'aktif',
        ]);
    }

    private function officialPartners(): void
    {
        foreach ([
            [1, 'LPK Mitra Kerja Sama Resmi BKK (Jawa Timur)', 'lpk-mitra-resmi-bkk'],
            [2, 'LPK Hinode Mulia Bersama [Data Simulasi Lomba]', 'lpk-hinode-mulia-bersama'],
        ] as [$id, $name, $slug]) {
            DB::table('lpks')->insert([
                'id' => $id, 'nama_lpk' => $name, 'slug' => $slug, 'ringkasan' => 'Arsip historis',
                'bidang_program' => 'Arsip', 'is_demo' => $id === 2, 'is_aktif' => false,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        foreach ([
            [41, 'KANZO Indonesia', 'kanzo', 'Program Kanzo', 'Wilayah Kanzo'],
            [73, 'Gakkou Nusantara', 'gakkou', 'Program Gakkou', 'Wilayah Gakkou'],
        ] as [$id, $name, $slug, $program, $region]) {
            DB::table('lpks')->insert([
                'id' => $id, 'nama_lpk' => $name, 'slug' => $slug, 'ringkasan' => 'Ringkasan resmi '.$name,
                'bidang_program' => $program, 'wilayah_penempatan' => $region, 'is_demo' => false,
                'is_aktif' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        DB::table('lpks')->insert([
            'id' => 99, 'nama_lpk' => 'LPK Simulasi', 'slug' => 'simulasi', 'ringkasan' => 'Bukan mitra formulir',
            'bidang_program' => 'Demo', 'wilayah_penempatan' => 'Demo', 'is_demo' => true,
            'is_aktif' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function registrationPayload(int $lpkId, string $major, string $claimedType, array $roleFields): array
    {
        return [
            'lpk_id' => $lpkId,
            'jenis_peserta' => $claimedType,
            'nama_lengkap' => 'Peserta Uji',
            'jurusan' => $major,
            'nomor_telepon' => '081234567890',
            'email' => 'peserta@example.test',
            'alamat' => 'Alamat peserta uji',
            'alasan_minat' => 'Ingin mengikuti program.',
            'persetujuan_syarat' => '1',
            ...$roleFields,
            'file_identitas' => UploadedFile::fake()->image('identitas.jpg'),
            'file_kk' => UploadedFile::fake()->image('kk.jpg'),
            'file_rapor_ijazah' => UploadedFile::fake()->image('dokumen.jpg'),
            'file_surat_ortu' => UploadedFile::fake()->image('surat.jpg'),
        ];
    }

    public function test_student_and_alumni_see_role_fixed_status_and_identical_allowed_choices(): void
    {
        $this->officialPartners();
        $student = $this->account('siswa', 'Siswa Demo');
        $this->actingAs($student)->get(route('karier-jepang.daftar'))->assertOk()
            ->assertSee('Siswa Aktif (Kelas XII)')->assertDontSee('name="jenis_peserta"', false)
            ->assertSee('Teknik Komputer dan Jaringan')->assertSee('Teknik Sepeda Motor')
            ->assertSee('Teknik Kendaraan Ringan')->assertSee('Desain Komunikasi Visual')
            ->assertSee('Kecantikan Kulit dan Rambut')->assertSee('KANZO Indonesia')->assertSee('Gakkou Nusantara')
            ->assertSee('Program Kanzo')->assertSee('Wilayah Gakkou')
            ->assertDontSee('LPK Simulasi')->assertDontSee('LPK Mitra Kerja Sama Resmi BKK');

        $alumni = $this->account('alumni', 'Alumni Demo');
        $this->actingAs($alumni)->get(route('karier-jepang.daftar'))->assertOk()
            ->assertSee('Alumni SMKN 7 Jember')->assertDontSee('name="jenis_peserta"', false)
            ->assertSee('name="tahun_lulus"', false)->assertDontSee('name="nis_nisn"', false);
    }

    public function test_information_page_lists_only_active_partners_and_does_not_invent_missing_details(): void
    {
        $this->officialPartners();
        DB::table('lpks')->where('id', 99)->update(['is_aktif' => false]);
        DB::table('lpks')->whereIn('id', [41, 73])->update([
            'deskripsi' => null,
            'wilayah_penempatan' => null,
            'kuota_tersedia' => 0,
        ]);

        $this->get(route('karier-jepang.index'))->assertOk()
            ->assertSee('KANZO Indonesia')->assertSee('Gakkou Nusantara')
            ->assertDontSee('LPK Simulasi')
            ->assertSee('Ringkasan resmi KANZO Indonesia')
            ->assertSee('Belum diinformasikan');
    }

    public function test_admin_lpk_filter_only_lists_active_official_partners(): void
    {
        $this->officialPartners();
        DB::table('lpks')->where('id', 99)->update(['is_aktif' => false]);
        $admin = $this->account('admin_sekolah', 'Admin Demo');

        $this->actingAs($admin)->get(route('dashboard.admin'))->assertOk()
            ->assertSee('KANZO Indonesia')->assertSee('Gakkou Nusantara')
            ->assertDontSee('LPK Simulasi')
            ->assertDontSee('LPK Mitra Kerja Sama Resmi BKK');
    }

    public function test_backend_uses_authenticated_role_and_preserves_existing_registration(): void
    {
        Storage::fake('local');
        $this->officialPartners();
        $student = $this->account('siswa', 'Siswa Demo');
        $alumni = $this->account('alumni', 'Alumni Demo');

        DB::table('pendaftaran_karier_jepangs')->insert([
            'nomor_pendaftaran' => 'OLD-001', 'user_id' => $student->id, 'lpk_id' => 99, 'jenis_peserta' => 'siswa',
            'nama_lengkap' => 'Pendaftar Lama', 'jurusan' => 'Rekayasa Perangkat Lunak', 'nomor_telepon' => '0800',
            'email' => 'lama@example.test', 'alamat' => 'Alamat lama', 'status' => 'ditolak_admin',
            'file_identitas' => 'old-id', 'file_kk' => 'old-kk', 'file_rapor_ijazah' => 'old-doc', 'file_surat_ortu' => 'old-letter',
            'created_at' => now()->subYear(), 'updated_at' => now()->subYear(),
        ]);

        $this->actingAs($student)->post(route('karier-jepang.store'), $this->registrationPayload(
            41, 'Teknik Komputer dan Jaringan', 'alumni', ['nis_nisn' => '0012345678']
        ))->assertRedirect();
        $this->assertDatabaseHas('pendaftaran_karier_jepangs', [
            'user_id' => $student->id, 'lpk_id' => 41, 'jenis_peserta' => 'siswa',
            'jurusan' => 'Teknik Komputer dan Jaringan', 'nis_nisn' => '0012345678', 'tahun_lulus' => null,
        ]);

        $this->actingAs($alumni)->post(route('karier-jepang.store'), $this->registrationPayload(
            73, 'Kecantikan Kulit dan Rambut', 'siswa', ['tahun_lulus' => '2024']
        ))->assertRedirect();
        $this->assertDatabaseHas('pendaftaran_karier_jepangs', [
            'user_id' => $alumni->id, 'lpk_id' => 73, 'jenis_peserta' => 'alumni',
            'jurusan' => 'Kecantikan Kulit dan Rambut', 'nis_nisn' => null, 'tahun_lulus' => '2024',
        ]);
        $this->assertDatabaseHas('pendaftaran_karier_jepangs', [
            'nomor_pendaftaran' => 'OLD-001', 'lpk_id' => 99, 'jurusan' => 'Rekayasa Perangkat Lunak',
        ]);
        $this->assertSame(3, DB::table('pendaftaran_karier_jepangs')->count());
    }

    public function test_backend_rejects_unlisted_major_and_unapproved_lpk_ids(): void
    {
        $this->officialPartners();
        Storage::fake('local');
        $student = $this->account('siswa', 'Siswa Demo');
        $payload = $this->registrationPayload(99, 'Rekayasa Perangkat Lunak', 'siswa', ['nis_nisn' => '0012345678']);

        $this->actingAs($student)->post(route('karier-jepang.store'), $payload)
            ->assertSessionHasErrors(['lpk_id', 'jurusan']);
        $this->assertSame(0, DB::table('pendaftaran_karier_jepangs')->count());
    }

    public function test_missing_official_lpk_records_are_visible_as_unavailable_and_cannot_be_guessed(): void
    {
        $student = $this->account('siswa', 'Siswa Demo');
        $this->actingAs($student)->get(route('karier-jepang.daftar'))->assertOk()
            ->assertSee('KANZO Indonesia')->assertSee('Gakkou Nusantara')
            ->assertSee('record resmi aktif yang unik belum tersedia')
            ->assertSee('Record mitra resmi yang dapat dipilih belum tersedia');

        $this->actingAs($student)->post(route('karier-jepang.store'), ['lpk_id' => 41])
            ->assertSessionHasErrors('lpk_id');
        $this->assertSame(0, DB::table('pendaftaran_karier_jepangs')->count());
    }
}
