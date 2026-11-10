<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('student_identity_type', 10)->nullable();
            $table->text('student_identity_encrypted')->nullable();
            $table->string('student_identity_hash', 64)->nullable()->unique();
        });

        Schema::create('parent_student_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('parent_name_snapshot');
            $table->string('student_name_snapshot');
            $table->string('identity_type', 10);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('submitted_at');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reviewer_name_snapshot')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();
        });

        Schema::create('parent_student_link_decisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_student_link_id')->constrained()->restrictOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_name_snapshot');
            $table->string('decision', 20);
            $table->text('note')->nullable();
            $table->timestamp('decided_at');
            $table->timestamps();
        });

        Schema::create('parent_student_active_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_student_link_id')->unique()->constrained('parent_student_links')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $protectedData = [];

        foreach ([
            'parent_student_links' => 'data pengajuan',
            'parent_student_link_decisions' => 'arsip keputusan Guru BK',
            'parent_student_active_links' => 'relasi aktif orang tua dan siswa',
        ] as $table => $description) {
            if (Schema::hasTable($table) && DB::table($table)->exists()) {
                $protectedData[] = $description;
            }
        }

        if (Schema::hasTable('users')) {
            foreach ([
                'student_identity_encrypted' => 'identitas terenkripsi',
                'student_identity_hash' => 'hash identitas',
            ] as $column => $description) {
                if (Schema::hasColumn('users', $column) && DB::table('users')->whereNotNull($column)->exists()) {
                    $protectedData[] = $description;
                }
            }

            if (Schema::hasColumn('users', 'student_identity_type')
                && DB::table('users')->where('student_identity_type', 'nik')->exists()) {
                $protectedData[] = 'identitas NIK lama';
            }

            foreach (['nik', 'student_nik', 'student_identity_nik'] as $column) {
                if (Schema::hasColumn('users', $column)
                    && DB::table('users')->whereNotNull($column)->where($column, '<>', '')->exists()) {
                    $protectedData[] = 'kolom NIK lama (' . $column . ')';
                }
            }
        }

        if ($protectedData !== []) {
            throw new RuntimeException(
                'Rollback Portal Orang Tua ditolak karena masih terdapat ' . implode(', ', array_unique($protectedData)) . '. '
                . 'Data Portal Orang Tua masih digunakan; arsipkan dan tangani data secara eksplisit sebelum rollback. '
                . 'Tidak ada data yang dihapus.'
            );
        }

        Schema::dropIfExists('parent_student_link_decisions');
        Schema::dropIfExists('parent_student_active_links');
        Schema::dropIfExists('parent_student_links');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['student_identity_hash']);
            $table->dropColumn(['student_identity_type', 'student_identity_encrypted', 'student_identity_hash']);
        });
    }
};
