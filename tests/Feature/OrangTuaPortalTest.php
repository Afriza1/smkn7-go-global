<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\StudentIdentity;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OrangTuaPortalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite'); DB::setDefaultConnection('sqlite');
        Schema::create('users', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('email'); $table->string('password')->nullable(); $table->string('role'); $table->string('status')->default('aktif');
            $table->string('student_identity_type', 10)->nullable(); $table->text('student_identity_encrypted')->nullable(); $table->string('student_identity_hash', 64)->nullable(); $table->timestamps();
        });
        Schema::create('parent_student_links', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('parent_id')->nullable(); $table->unsignedBigInteger('student_id')->nullable(); $table->string('parent_name_snapshot'); $table->string('student_name_snapshot'); $table->string('identity_type');
            $table->string('status')->default('pending'); $table->timestamp('submitted_at'); $table->unsignedBigInteger('reviewed_by')->nullable(); $table->string('reviewer_name_snapshot')->nullable(); $table->timestamp('reviewed_at')->nullable(); $table->text('review_note')->nullable(); $table->timestamps();
        });
        Schema::create('parent_student_active_links', function (Blueprint $table): void { $table->id(); $table->unsignedBigInteger('parent_id')->unique(); $table->unsignedBigInteger('student_id')->unique(); $table->unsignedBigInteger('parent_student_link_id')->unique(); $table->timestamps(); });
        Schema::create('parent_student_link_decisions', function (Blueprint $table): void {
            $table->id(); $table->unsignedBigInteger('parent_student_link_id'); $table->unsignedBigInteger('actor_id')->nullable(); $table->string('actor_name_snapshot'); $table->string('decision'); $table->text('note')->nullable(); $table->timestamp('decided_at'); $table->timestamps();
        });
        Schema::create('lpks', function (Blueprint $table): void { $table->id(); $table->string('nama_lpk')->nullable(); $table->string('wilayah_penempatan')->nullable(); $table->string('bidang_program')->nullable(); });
        Schema::create('pendaftaran_karier_jepangs', function (Blueprint $table): void {
            $table->id(); $table->string('nomor_pendaftaran'); $table->unsignedBigInteger('user_id'); $table->unsignedBigInteger('lpk_id')->nullable(); $table->string('jurusan')->nullable(); $table->string('status');
            $table->string('nama_lengkap')->nullable(); $table->text('catatan_admin')->nullable(); $table->unsignedBigInteger('admin_checked_by')->nullable(); $table->timestamp('admin_checked_at')->nullable();
            $table->text('catatan_bk')->nullable(); $table->unsignedBigInteger('bk_checked_by')->nullable(); $table->timestamp('bk_checked_at')->nullable();
            $table->text('catatan_kepala_sekolah')->nullable(); $table->unsignedBigInteger('kepala_sekolah_checked_by')->nullable(); $table->timestamp('kepala_sekolah_checked_at')->nullable();
            $table->text('catatan_lpk_admin')->nullable(); $table->timestamp('lpk_updated_at')->nullable(); $table->timestamps();
        });
        $verificationMigration = require database_path('migrations/2026_11_10_120000_create_user_account_verification_requests_table.php');
        $verificationMigration->up();
    }

    protected function tearDown(): void { DB::disconnect('sqlite'); parent::tearDown(); }

    private function account(string $role, string $name): User
    {
        return User::create(['name' => $name, 'email' => strtolower(str_replace(' ', '.', $name)).'@example.test', 'password' => bcrypt('password'), 'role' => $role, 'status' => 'aktif']);
    }

    public function test_parent_request_requires_student_identity_and_bk_approval_before_dashboard_access(): void
    {
        $student = $this->account('siswa', 'Siswa Demo');
        $parent = $this->account('orang_tua', 'Orang Tua');
        $bk = $this->account('guru', 'Guru BK');
        $this->actingAs($student)->get(route('dashboard.siswa'))->assertOk()
            ->assertSee('Masukkan 10 digit NISN siswa')->assertSee('Simpan Identitas');
        $this->actingAs($student)->post(route('dashboard.siswa.identity.store'), ['identity_number' => '0012345678'])->assertRedirect();
        $student->refresh();
        $this->assertSame('nisn', $student->student_identity_type);
        $this->assertSame('0012345678', decrypt($student->student_identity_encrypted));
        $this->assertNotSame('0012345678', $student->student_identity_encrypted);
        $this->assertSame(hash_hmac('sha256', 'nisn:0012345678', (string) config('app.key')), $student->student_identity_hash);
        $this->actingAs($student)->get(route('dashboard.siswa'))->assertOk()
            ->assertSee('Nomor Induk Siswa Nasional (NISN)')
            ->assertSee('0012345678')->assertSee('Tersimpan')->assertDontSee('**********')
            ->assertSee('NISN telah tersimpan secara aman')
            ->assertDontSee('Masukkan 10 digit NISN siswa')->assertDontSee('Simpan Identitas')
            ->assertDontSee('name="identity_number"', false)->assertDontSee('NIK')
            ->assertDontSee('identity_type', false);

        $storedCiphertext = $student->student_identity_encrypted;
        $storedHash = $student->student_identity_hash;
        $this->actingAs($student)->post(route('dashboard.siswa.identity.store'), ['identity_number' => '0098765432'])
            ->assertSessionHasErrors('identity_number');
        $student->refresh();
        $this->assertSame($storedCiphertext, $student->student_identity_encrypted);
        $this->assertSame($storedHash, $student->student_identity_hash);

        $this->actingAs($parent)->get(route('dashboard.orang-tua'))->assertOk()
            ->assertSee('Nomor Induk Siswa Nasional (NISN)')->assertSee('Masukkan 10 digit NISN siswa')
            ->assertDontSee('NIK')->assertDontSee('0012345678')->assertDontSee('identity_type', false);
        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => '0099999999'])->assertSessionHasErrors('identity_number');
        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => '0012345678'])->assertRedirect(route('dashboard.orang-tua'));
        $this->actingAs($parent)->get(route('dashboard.orang-tua'))->assertOk()->assertSee('Menunggu Verifikasi BK')->assertDontSee('Siswa Demo')->assertDontSee('Progress Tracker Perjalanan Karier Jepang');
        $linkId = DB::table('parent_student_links')->value('id');
        $this->assertDatabaseHas('parent_student_links', ['id' => $linkId, 'identity_type' => 'nisn', 'status' => 'pending']);
        $this->actingAs($student)->post(route('dashboard.guru.orang-tua.decide', ['link' => $linkId]), ['decision' => 'approved'])->assertRedirect();
        $this->assertDatabaseHas('parent_student_links', ['id' => $linkId, 'status' => 'pending']);
        $this->actingAs($bk)->post(route('dashboard.guru.orang-tua.decide', ['link' => $linkId]), ['decision' => 'approved'])->assertRedirect();
        $this->actingAs($parent)->get(route('dashboard.orang-tua'))->assertOk()->assertSee('Siswa Demo');
        $this->assertDatabaseHas('parent_student_link_decisions', ['parent_student_link_id' => $linkId, 'actor_id' => $bk->id, 'decision' => 'approved']);
        $otherParent = $this->account('orang_tua', 'Orang Tua Lain');
        $this->actingAs($otherParent)->get(route('dashboard.orang-tua'))->assertOk()->assertDontSee('Siswa Demo');
    }

    public function test_full_nisn_is_visible_only_to_its_owner_and_not_to_another_student_or_guru(): void
    {
        $owner = $this->account('siswa', 'Pemilik NISN');
        $otherStudent = $this->account('siswa', 'Siswa Lain');
        $legacyNik = '3174000000000001';
        $owner->forceFill([
            'student_identity_type' => 'nik',
            'student_identity_encrypted' => encrypt($legacyNik),
            'student_identity_hash' => hash_hmac('sha256', 'nik:'.$legacyNik, (string) config('app.key')),
        ])->save();
        $encryptedEnvelope = app(StudentIdentity::class)->encryptNisn($owner, '0012345678');
        $owner->forceFill([
            'student_identity_type' => 'nisn',
            'student_identity_encrypted' => $encryptedEnvelope,
            'student_identity_hash' => hash_hmac('sha256', 'nisn:0012345678', (string) config('app.key')),
        ])->save();
        $otherStudent->forceFill([
            'student_identity_type' => 'nisn',
            'student_identity_encrypted' => encrypt('0098765432'),
            'student_identity_hash' => hash_hmac('sha256', 'nisn:0098765432', (string) config('app.key')),
        ])->save();

        $this->actingAs($owner)->get(route('dashboard.siswa'))->assertOk()
            ->assertSee('0012345678')->assertDontSee('0098765432')->assertDontSee($legacyNik);
        $this->actingAs($otherStudent)->get(route('dashboard.siswa'))->assertOk()
            ->assertSee('0098765432')->assertDontSee('0012345678')->assertDontSee($legacyNik);

        $guru = $this->account('guru', 'Guru Privasi');
        $this->actingAs($guru)->get(route('dashboard.guru.verifikasi-akun-siswa.index'))->assertOk()
            ->assertDontSee('0012345678')->assertDontSee('0098765432')->assertDontSee($legacyNik);
    }

    public function test_approved_parent_sees_live_ten_step_tracker_without_internal_bk_note(): void
    {
        $student = $this->account('siswa', 'Siswa Tracker');
        $parent = $this->account('orang_tua', 'Orang Tua Tracker');
        $linkId = DB::table('parent_student_links')->insertGetId([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'parent_name_snapshot' => $parent->name,
            'student_name_snapshot' => $student->name,
            'identity_type' => 'nisn',
            'status' => 'approved',
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('parent_student_active_links')->insert([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'parent_student_link_id' => $linkId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $lpkId = DB::table('lpks')->insertGetId([
            'nama_lpk' => 'LPK Test Tracker',
            'wilayah_penempatan' => 'Jepang',
            'bidang_program' => 'Program Karier Jepang',
        ]);
        $registrationId = DB::table('pendaftaran_karier_jepangs')->insertGetId([
            'nomor_pendaftaran' => 'KJ-TRACKER-001',
            'user_id' => $student->id,
            'lpk_id' => $lpkId,
            'jurusan' => 'Teknik Komputer dan Jaringan',
            'status' => 'menunggu_bk',
            'catatan_bk' => 'CATATAN BK INTERNAL - JANGAN TAMPILKAN KE ORANG TUA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $parentResponse = $this->actingAs($parent)->get(route('dashboard.orang-tua'))->assertOk();
        $studentResponse = $this->actingAs($student)->get(route('dashboard.siswa'))->assertOk();
        foreach ([
            'Progress Tracker Perjalanan Karier Jepang (10 Tahapan)',
            'Pendaftaran Akun & Dokumen',
            'Pemeriksaan Administrasi',
            'Pertimbangan Guru BK',
            'Persetujuan Kepala Sekolah',
            'Seleksi Mitra LPK',
            'Pelatihan Bahasa & Budaya',
            'Medical Check Up (MCU)',
            'Pengurusan Dokumen & Visa',
            'Persiapan Keberangkatan',
            'Keberangkatan & Penempatan',
            'Sedang Berlangsung',
        ] as $trackerText) {
            $parentResponse->assertSee($trackerText);
            $studentResponse->assertSee($trackerText);
        }
        $parentResponse->assertDontSee('CATATAN BK INTERNAL - JANGAN TAMPILKAN KE ORANG TUA')
            ->assertDontSee('name="decision"', false);
        $studentResponse->assertSee('CATATAN BK INTERNAL - JANGAN TAMPILKAN KE ORANG TUA');

        DB::table('pendaftaran_karier_jepangs')->where('id', $registrationId)->update(['status' => 'lolos_seleksi']);
        $this->actingAs($parent)->get(route('dashboard.orang-tua'))->assertOk()
            ->assertSee('Lolos Seleksi LPK')
            ->assertDontSee('CATATAN BK INTERNAL - JANGAN TAMPILKAN KE ORANG TUA');
    }

    public function test_approved_parent_without_registration_sees_empty_state_and_no_tracker(): void
    {
        $student = $this->account('siswa', 'Siswa Belum Mendaftar');
        $parent = $this->account('orang_tua', 'Orang Tua Tanpa Pendaftaran');
        DB::table('parent_student_links')->insert([
            'parent_id' => $parent->id,
            'student_id' => $student->id,
            'parent_name_snapshot' => $parent->name,
            'student_name_snapshot' => $student->name,
            'identity_type' => 'nisn',
            'status' => 'approved',
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($parent)->get(route('dashboard.orang-tua'))->assertOk()
            ->assertSee('Belum ada data pendaftaran program yang tersedia untuk siswa ini.')
            ->assertDontSee('Progress Tracker Perjalanan Karier Jepang (10 Tahapan)');
    }

    public function test_guru_sidebar_queue_shows_samuel_and_allows_opening_and_approving_parent_request(): void
    {
        $student = $this->account('siswa', 'Siswa Samuel');
        $nisn = '0011223344';
        $student->forceFill([
            'student_identity_type' => 'nisn',
            'student_identity_hash' => hash_hmac('sha256', 'nisn:'.$nisn, (string) config('app.key')),
            'student_identity_encrypted' => encrypt($nisn),
        ])->save();
        $parent = $this->account('orang_tua', 'Samuel');
        $guru = $this->account('guru', 'Guru Penguji');

        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => $nisn])
            ->assertRedirect(route('dashboard.orang-tua'));
        $linkId = DB::table('parent_student_links')->value('id');

        $this->actingAs($guru)->get(route('dashboard.guru.orang-tua.index'))
            ->assertOk()
            ->assertSee('Samuel')
            ->assertSee('Menunggu Verifikasi BK')
            ->assertSeeInOrder([
                'Antrean Pertimbangan BK',
                'Verifikasi Akun Siswa',
                'Verifikasi Orang Tua',
                'Konseling Digital',
                'Kelola Tracer Study',
                'Statistik Tracer Study',
            ]);

        $this->actingAs($guru)->get(route('dashboard.guru.orang-tua.show', $linkId))
            ->assertOk()
            ->assertSee('Samuel mengajukan hubungan')
            ->assertSee($parent->email)
            ->assertSee('Setujui')
            ->assertSee('Tolak');

        $this->actingAs($guru)->post(route('dashboard.guru.orang-tua.decide', $linkId), ['decision' => 'approved'])
            ->assertRedirect(route('dashboard.guru.orang-tua.index'));
        $this->assertDatabaseHas('parent_student_links', ['id' => $linkId, 'status' => 'approved', 'reviewed_by' => $guru->id]);
        $this->assertDatabaseHas('parent_student_link_decisions', ['parent_student_link_id' => $linkId, 'actor_id' => $guru->id, 'decision' => 'approved']);
    }

    public function test_parent_and_student_each_have_at_most_one_link_and_rejection_requires_reason(): void
    {
        $students = [$this->account('siswa', 'Satu'), $this->account('siswa', 'Dua')];
        foreach ($students as $index => $student) {
            $number = '000000000'.($index + 1);
            $student->forceFill(['student_identity_type' => 'nisn', 'student_identity_hash' => hash_hmac('sha256', 'nisn:'.$number, (string) config('app.key')), 'student_identity_encrypted' => encrypt($number)])->save();
        }
        $parent = $this->account('orang_tua', 'Wali');
        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => '0000000001'])->assertRedirect();
        $otherParent = $this->account('orang_tua', 'Wali Lain');
        $this->actingAs($otherParent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => '0000000001'])->assertSessionHasErrors('identity_number');
        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => '0000000002'])->assertSessionHasErrors('identity_number');
        $linkId = DB::table('parent_student_links')->value('id');
        $bk = $this->account('guru', 'Reviewer');
        $this->actingAs($bk)->post(route('dashboard.guru.orang-tua.decide', ['link' => $linkId]), ['decision' => 'rejected'])->assertSessionHasErrors('note');
    }

    public function test_identity_is_never_flashed_when_validation_fails(): void
    {
        $parent = $this->account('orang_tua', 'Wali');
        $response = $this->actingAs($parent)->from(route('dashboard.orang-tua'))->post(route('dashboard.orang-tua.submit'), ['identity_type' => 'nik', 'identity_number' => '123']);
        $response->assertSessionHasErrors('identity_number')->assertSessionMissing('_old_input.identity_number');
        $response->assertSessionHasErrors('identity_type')->assertSessionMissing('_old_input.identity_type');
        $this->assertStringNotContainsString('123', json_encode(session()->all()));
    }

    public function test_rejected_request_can_be_resubmitted_as_a_new_immutable_history_record(): void
    {
        $student = $this->account('siswa', 'Satu');
        $number = '0000000001';
        $student->forceFill(['student_identity_type' => 'nisn', 'student_identity_hash' => hash_hmac('sha256', 'nisn:'.$number, (string) config('app.key')), 'student_identity_encrypted' => encrypt($number)])->save();
        $parent = $this->account('orang_tua', 'Wali'); $bk = $this->account('guru', 'BK');
        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => $number]);
        $first = DB::table('parent_student_links')->value('id');
        $this->actingAs($bk)->post(route('dashboard.guru.orang-tua.decide', ['link' => $first]), ['decision' => 'rejected', 'note' => 'Tidak sesuai'])->assertRedirect();
        $this->actingAs($parent)->get(route('dashboard.orang-tua'))->assertOk()
            ->assertSee('Nomor Induk Siswa Nasional (NISN)')->assertSee('Ajukan ulang')
            ->assertDontSee('NIK')->assertDontSee('identity_type', false);
        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => $number]);
        $this->actingAs($parent)->get(route('dashboard.orang-tua'))->assertOk()
            ->assertSee('Menunggu Verifikasi BK')->assertDontSee('NIK')->assertDontSee('identity_type', false);
        $this->assertSame(2, DB::table('parent_student_links')->count());
        $this->assertDatabaseHas('parent_student_links', ['id' => $first, 'student_name_snapshot' => 'Satu', 'status' => 'rejected']);
        $this->assertDatabaseHas('parent_student_active_links', ['parent_id' => $parent->id, 'student_id' => $student->id]);
    }

    public function test_approved_link_cannot_be_claimed_by_another_parent_and_decision_keeps_context(): void
    {
        $student = $this->account('siswa', 'Siswa Arsip'); $number = '0000000007';
        $student->forceFill(['student_identity_type' => 'nisn', 'student_identity_hash' => hash_hmac('sha256', 'nisn:'.$number, (string) config('app.key')), 'student_identity_encrypted' => encrypt($number)])->save();
        $parent = $this->account('orang_tua', 'Wali Utama'); $bk = $this->account('guru', 'Reviewer Arsip');
        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => $number]);
        $linkId = DB::table('parent_student_links')->value('id');
        $this->actingAs($bk)->post(route('dashboard.guru.orang-tua.decide', ['link' => $linkId]), ['decision' => 'approved'])->assertRedirect();
        $secondParent = $this->account('orang_tua', 'Wali Kedua');
        $this->actingAs($secondParent)->post(route('dashboard.orang-tua.submit'), ['identity_number' => $number])->assertSessionHasErrors('identity_number');
        $this->assertDatabaseHas('parent_student_link_decisions', ['parent_student_link_id' => $linkId, 'actor_name_snapshot' => 'Reviewer Arsip', 'decision' => 'approved']);
        $this->assertDatabaseHas('parent_student_links', ['id' => $linkId, 'student_name_snapshot' => 'Siswa Arsip', 'status' => 'approved']);
    }

    public function test_new_identity_requires_exactly_ten_numeric_digits_and_rejects_nik_selection(): void
    {
        $student = $this->account('siswa', 'NISN Uji');
        foreach (['123456789', '12345678901', '12345abc90', '12345-6789'] as $invalidNisn) {
            $this->actingAs($student)->post(route('dashboard.siswa.identity.store'), ['identity_number' => $invalidNisn])
                ->assertSessionHasErrors('identity_number');
        }

        $this->actingAs($student)->post(route('dashboard.siswa.identity.store'), [
            'identity_type' => 'nik', 'identity_number' => '1234567890123456',
        ])->assertSessionHasErrors(['identity_type', 'identity_number']);

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'student_identity_type' => null,
            'student_identity_encrypted' => null,
            'student_identity_hash' => null,
        ]);

        $parent = $this->account('orang_tua', 'Orang Tua NISN Uji');
        $this->actingAs($parent)->post(route('dashboard.orang-tua.submit'), [
            'identity_type' => 'nik', 'identity_number' => '1234567890123456',
        ])->assertSessionHasErrors(['identity_type', 'identity_number']);
        $this->assertSame(0, DB::table('parent_student_links')->count());
    }

    public function test_existing_nik_ciphertext_and_hash_are_preserved_when_student_adds_nisn(): void
    {
        $student = $this->account('siswa', 'NISN Dari NIK');
        $legacyNik = '1234567890123456';
        $legacyCiphertext = encrypt($legacyNik);
        $legacyHash = hash_hmac('sha256', 'nik:'.$legacyNik, (string) config('app.key'));
        $student->forceFill([
            'student_identity_type' => 'nik',
            'student_identity_encrypted' => $legacyCiphertext,
            'student_identity_hash' => $legacyHash,
        ])->save();

        $nisn = '0012345678';
        $this->actingAs($student)->post(route('dashboard.siswa.identity.store'), ['identity_number' => $nisn])->assertRedirect();
        $student->refresh();

        $this->assertSame('nisn', $student->student_identity_type);
        $this->assertSame(hash_hmac('sha256', 'nisn:'.$nisn, (string) config('app.key')), $student->student_identity_hash);
        $payload = json_decode(decrypt($student->student_identity_encrypted), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame($nisn, $payload['nisn']);
        $this->assertSame('nik', $payload['legacy_identity']['type']);
        $this->assertSame($legacyCiphertext, $payload['legacy_identity']['encrypted_value']);
        $this->assertSame($legacyHash, $payload['legacy_identity']['hash']);
        $this->assertSame($legacyNik, decrypt($payload['legacy_identity']['encrypted_value']));
        $this->assertSame($nisn, app(StudentIdentity::class)->decryptNisn($student->student_identity_encrypted, $student->student_identity_type));
        $this->assertStringNotContainsString($nisn, $student->student_identity_encrypted);
    }

    public function test_dashboard_displays_database_account_status_and_separates_parent_relationship_review(): void
    {
        $student = $this->account('siswa', 'Status Siswa');
        $student->update(['status' => 'menunggu_verifikasi']);

        $this->actingAs($student)->get(route('dashboard.siswa'))->assertOk()
            ->assertSee('Status Verifikasi Akun')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Akun sedang diperiksa Guru BK')
            ->assertDontSee('Daftar Karier Jepang');

        $student->update(['status' => 'aktif']);
        $this->actingAs($student)->get(route('dashboard.siswa'))->assertOk()
            ->assertSee('Akun Anda berstatus aktif menurut data akun.');

        $student->update(['status' => 'ditolak']);
        $this->actingAs($student)->get(route('dashboard.siswa'))->assertOk()
            ->assertSee('Pengajuan akun memerlukan perbaikan')
            ->assertSee('Perbarui data dan ajukan kembali')
            ->assertSee('Ajukan Kembali untuk Verifikasi');
    }
}
