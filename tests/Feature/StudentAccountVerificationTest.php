<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentAccountVerificationTest extends TestCase
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
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('role');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        $migration = require database_path('migrations/2026_11_10_120000_create_user_account_verification_requests_table.php');
        $migration->up();
        Schema::create('lpks', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_lpk')->nullable();
        });
        Schema::create('pendaftaran_karier_jepangs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lpk_id')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });
        Schema::create('model_has_roles', function (Blueprint $table): void {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    private function account(string $role, string $status, string $name = 'Test User'): User
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)).'@example.test',
            'phone' => '08123456789',
            'password' => bcrypt('password'),
            'role' => $role,
            'status' => $status,
        ]);
    }

    private function request(User $student, string $status = 'menunggu_verifikasi', int $number = 1): int
    {
        return DB::table('user_account_verification_requests')->insertGetId([
            'user_id' => $student->id,
            'submission_number' => $number,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_pending_student_can_only_view_account_status_until_approved(): void
    {
        $student = $this->account('siswa', 'menunggu_verifikasi');
        $this->request($student);

        $this->actingAs($student)->get(route('dashboard.siswa'))
            ->assertOk()
            ->assertSee('Akun sedang diperiksa Guru BK')
            ->assertSee('Pengajuan ke-')
            ->assertDontSee('Daftar Karier Jepang');

        $this->actingAs($student)->get(route('dashboard.modul-edukasi.index'))
            ->assertRedirect(route('dashboard.siswa'));
    }

    public function test_registration_creates_first_verification_request_for_students_only(): void
    {
        $createUser = app(\App\Actions\Fortify\CreateNewUser::class);
        $student = $createUser->create([
            'name' => 'Siswa Terdaftar', 'email' => 'new.student@example.test', 'phone' => '08123456789',
            'role' => 'siswa', 'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!',
        ]);
        $alumni = $createUser->create([
            'name' => 'Alumni Terdaftar', 'email' => 'new.alumni@example.test', 'phone' => '08123456788',
            'role' => 'alumni', 'password' => 'StrongPass123!', 'password_confirmation' => 'StrongPass123!',
        ]);

        $this->assertSame('menunggu_verifikasi', $student->status);
        $this->assertDatabaseHas('user_account_verification_requests', [
            'user_id' => $student->id, 'submission_number' => 1, 'status' => 'menunggu_verifikasi',
        ]);
        $this->assertSame('menunggu_verifikasi', $alumni->status);
        $this->assertDatabaseMissing('user_account_verification_requests', ['user_id' => $alumni->id]);
    }

    public function test_guru_can_review_student_request_and_account_status_changes(): void
    {
        $student = $this->account('siswa', 'menunggu_verifikasi', 'Siswa Baru');
        $requestId = $this->request($student);
        $guru = $this->account('guru', 'aktif', 'Guru BK');

        $this->actingAs($guru)->get(route('dashboard.guru.verifikasi-akun-siswa.index'))
            ->assertOk()->assertSee('Siswa Baru')->assertSee('Verifikasi Akun Siswa');
        $this->actingAs($guru)->get(route('dashboard.guru.verifikasi-akun-siswa.show', $requestId))
            ->assertOk()->assertSee('Siswa Baru')->assertSee('Role')->assertSee('Riwayat Pengajuan')
            ->assertDontSee('password')->assertDontSee('student_identity_hash');
        $this->actingAs($guru)->post(route('dashboard.guru.verifikasi-akun-siswa.decide', $requestId), ['decision' => 'disetujui'])
            ->assertRedirect(route('dashboard.guru.verifikasi-akun-siswa.show', $requestId));

        $this->assertDatabaseHas('users', ['id' => $student->id, 'status' => 'aktif']);
        $this->assertDatabaseHas('user_account_verification_requests', [
            'id' => $requestId,
            'status' => 'disetujui',
            'reviewer_id' => $guru->id,
            'reviewer_name_snapshot' => 'Guru BK',
        ]);
        $this->actingAs($guru)->post(route('dashboard.guru.verifikasi-akun-siswa.decide', $requestId), ['decision' => 'disetujui'])
            ->assertStatus(409);
        $this->actingAs($student->fresh())->get(route('dashboard.siswa'))->assertOk()->assertSee('Identitas untuk Portal Orang Tua');
    }

    public function test_rejection_requires_reason_and_student_can_resubmit_same_account(): void
    {
        $student = $this->account('siswa', 'menunggu_verifikasi', 'Siswa Perbaikan');
        $requestId = $this->request($student);
        $guru = $this->account('guru', 'aktif', 'Guru Pemeriksa');

        $this->actingAs($guru)->post(route('dashboard.guru.verifikasi-akun-siswa.decide', $requestId), ['decision' => 'ditolak'])
            ->assertSessionHasErrors('rejection_reason');
        $this->actingAs($guru)->post(route('dashboard.guru.verifikasi-akun-siswa.decide', $requestId), [
            'decision' => 'ditolak', 'rejection_reason' => 'Mohon periksa kembali nama lengkap.',
        ])->assertRedirect();

        $student->refresh();
        $this->assertSame('ditolak', $student->status);
        $this->actingAs($student)->get(route('dashboard.siswa'))->assertOk()
            ->assertSee('Mohon periksa kembali nama lengkap.')
            ->assertSee('Perbarui data dan ajukan kembali');

        $this->actingAs($student)->post(route('dashboard.siswa.verification.resubmit'), [
            'name' => 'Siswa Perbaikan Baru',
            'email' => 'siswa.perbaikan.baru@example.test',
            'phone' => '081111111111',
        ])->assertRedirect(route('dashboard.siswa'));

        $this->assertDatabaseHas('users', ['id' => $student->id, 'name' => 'Siswa Perbaikan Baru', 'status' => 'menunggu_verifikasi']);
        $this->assertDatabaseHas('user_account_verification_requests', ['user_id' => $student->id, 'submission_number' => 2, 'status' => 'menunggu_verifikasi']);
        $this->assertSame(2, DB::table('user_account_verification_requests')->where('user_id', $student->id)->count());
    }

    public function test_status_changes_apply_to_existing_session_and_disabled_account_is_logged_out(): void
    {
        $student = $this->account('siswa', 'aktif', 'Siswa Status Berubah');
        $this->actingAs($student)->get(route('dashboard.siswa'))->assertOk();

        $student->update(['status' => 'menunggu_verifikasi']);
        $this->get(route('dashboard.siswa'))->assertOk()->assertSee('Akun sedang diperiksa Guru BK');
        $this->get(route('dashboard.modul-edukasi.index'))->assertRedirect(route('dashboard.siswa'));

        $student->update(['status' => 'dinonaktifkan']);
        $this->get(route('dashboard.siswa'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_other_roles_cannot_open_guru_verification_and_inactive_guru_is_blocked(): void
    {
        $alumni = $this->account('alumni', 'aktif', 'Alumni');
        $this->actingAs($alumni)->get(route('dashboard.guru.verifikasi-akun-siswa.index'))
            ->assertRedirect(route('dashboard.alumni'));

        $inactiveGuru = $this->account('guru', 'menunggu_verifikasi', 'Guru Menunggu');
        $this->actingAs($inactiveGuru)->get(route('dashboard.guru.verifikasi-akun-siswa.index'))->assertForbidden();
    }

    public function test_migration_backfills_existing_pending_students_without_changing_their_status(): void
    {
        // The request table is empty only if this isolated test does not contain new rows.
        $student = $this->account('siswa', 'menunggu_verifikasi', 'Siswa Lama');
        Schema::drop('user_account_verification_requests');
        $migration = require database_path('migrations/2026_11_10_120000_create_user_account_verification_requests_table.php');
        $migration->up();

        $this->assertSame('menunggu_verifikasi', $student->fresh()->status);
        $this->assertDatabaseHas('user_account_verification_requests', [
            'user_id' => $student->id,
            'submission_number' => 1,
            'status' => 'menunggu_verifikasi',
        ]);
    }

    public function test_migration_rollback_is_refused_when_verification_history_exists(): void
    {
        $student = $this->account('siswa', 'menunggu_verifikasi', 'Siswa Dengan Riwayat');
        $this->request($student);
        $migration = require database_path('migrations/2026_11_10_120000_create_user_account_verification_requests_table.php');

        try {
            $migration->down();
            $this->fail('Rollback harus ditolak ketika riwayat verifikasi tersimpan.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('riwayat verifikasi akun masih tersimpan', $exception->getMessage());
        }

        $this->assertTrue(Schema::hasTable('user_account_verification_requests'));
        $this->assertDatabaseHas('user_account_verification_requests', ['user_id' => $student->id]);
    }
}
