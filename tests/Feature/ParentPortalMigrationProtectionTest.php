<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class ParentPortalMigrationProtectionTest extends TestCase
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
            $table->string('nik')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    private function migration(): object
    {
        return require database_path('migrations/2026_11_10_000001_create_parent_student_links_table.php');
    }

    private function addUser(string $name, ?string $nik = null): int
    {
        return DB::table('users')->insertGetId([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)).'@example.test',
            'nik' => $nik,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function addLink(int $parentId, int $studentId): int
    {
        return DB::table('parent_student_links')->insertGetId([
            'parent_id' => $parentId,
            'student_id' => $studentId,
            'parent_name_snapshot' => 'Orang Tua',
            'student_name_snapshot' => 'Siswa',
            'identity_type' => 'nisn',
            'status' => 'pending',
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function assertRollbackProtected(string $messagePart): void
    {
        try {
            $this->migration()->down();
            $this->fail('Rollback seharusnya ditolak ketika data Portal Orang Tua masih digunakan.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('Rollback Portal Orang Tua ditolak', $exception->getMessage());
            $this->assertStringContainsString('Data Portal Orang Tua masih digunakan', $exception->getMessage());
            $this->assertStringContainsString($messagePart, $exception->getMessage());
        }
    }

    public function test_up_preserves_existing_users_and_adds_nullable_identity_columns_and_portal_constraints(): void
    {
        $userId = $this->addUser('Pengguna Lama', '3174000000000001');

        $this->migration()->up();

        $this->assertDatabaseHas('users', ['id' => $userId, 'name' => 'Pengguna Lama', 'nik' => '3174000000000001']);
        $this->assertSame(1, DB::table('users')->count());
        foreach (['student_identity_type', 'student_identity_encrypted', 'student_identity_hash'] as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column));
            $this->assertNull(DB::table('users')->value($column));
        }
        foreach (['parent_student_links', 'parent_student_link_decisions', 'parent_student_active_links'] as $table) {
            $this->assertTrue(Schema::hasTable($table));
        }
        $this->assertTrue(collect(Schema::getIndexes('users'))->contains(fn (array $index): bool => $index['unique'] && in_array('student_identity_hash', $index['columns'], true)));
        $this->assertSame(3, count(Schema::getForeignKeys('parent_student_links')));
        $this->assertSame(2, count(Schema::getForeignKeys('parent_student_link_decisions')));
        $this->assertSame(3, count(Schema::getForeignKeys('parent_student_active_links')));
    }

    public function test_down_is_blocked_when_request_decision_archive_or_active_link_exists(): void
    {
        $parentId = $this->addUser('Orang Tua');
        $studentId = $this->addUser('Siswa');
        $this->migration()->up();
        $linkId = $this->addLink($parentId, $studentId);
        DB::table('parent_student_link_decisions')->insert([
            'parent_student_link_id' => $linkId,
            'actor_id' => $parentId,
            'actor_name_snapshot' => 'Orang Tua',
            'decision' => 'submitted',
            'decided_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('parent_student_active_links')->insert([
            'parent_id' => $parentId,
            'student_id' => $studentId,
            'parent_student_link_id' => $linkId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertRollbackProtected('data pengajuan');
        $this->assertRollbackProtected('arsip keputusan Guru BK');
        $this->assertRollbackProtected('relasi aktif orang tua dan siswa');
    }

    public function test_down_is_blocked_when_nisn_encrypted_value_exists_without_a_parent_request(): void
    {
        $userId = $this->addUser('Siswa NISN');
        $this->migration()->up();
        DB::table('users')->where('id', $userId)->update([
            'student_identity_type' => 'nisn',
            'student_identity_encrypted' => encrypt('0012345678'),
        ]);

        $this->assertRollbackProtected('identitas terenkripsi');
        $this->assertTrue(Schema::hasColumn('users', 'student_identity_encrypted'));
    }

    public function test_down_is_blocked_when_identity_hash_exists_without_a_parent_request(): void
    {
        $userId = $this->addUser('Siswa Hash');
        $this->migration()->up();
        DB::table('users')->where('id', $userId)->update(['student_identity_hash' => hash('sha256', 'test')]);

        $this->assertRollbackProtected('hash identitas');
        $this->assertNotNull(DB::table('users')->where('id', $userId)->value('student_identity_hash'));
    }

    public function test_down_is_blocked_when_legacy_nik_identity_or_nik_column_has_data(): void
    {
        $userId = $this->addUser('Siswa NIK', '3174000000000001');
        $this->migration()->up();
        DB::table('users')->where('id', $userId)->update([
            'student_identity_type' => 'nik',
            'student_identity_encrypted' => encrypt('3174000000000001'),
        ]);

        $this->assertRollbackProtected('identitas NIK lama');
        $this->assertRollbackProtected('kolom NIK lama (nik)');
        $this->assertSame('3174000000000001', DB::table('users')->where('id', $userId)->value('nik'));
    }

    public function test_down_succeeds_only_when_no_portal_or_identity_data_exists(): void
    {
        $this->migration()->up();
        $this->migration()->down();

        $this->assertFalse(Schema::hasTable('parent_student_links'));
        $this->assertFalse(Schema::hasTable('parent_student_link_decisions'));
        $this->assertFalse(Schema::hasTable('parent_student_active_links'));
        $this->assertFalse(Schema::hasColumn('users', 'student_identity_encrypted'));
    }
}
