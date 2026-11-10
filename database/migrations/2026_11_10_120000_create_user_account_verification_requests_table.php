<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_account_verification_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedInteger('submission_number');
            $table->string('status', 30)->index();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reviewer_name_snapshot')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'submission_number'], 'user_account_verification_submission_unique');
            $table->index(['status', 'created_at'], 'user_account_verification_status_created_index');
        });

        DB::table('users')
            ->select(['id', 'created_at'])
            ->where('role', 'siswa')
            ->where('status', 'menunggu_verifikasi')
            ->orderBy('id')
            ->chunkById(200, function ($users): void {
                $timestamp = now();
                $requests = $users->map(fn ($user): array => [
                    'user_id' => $user->id,
                    'submission_number' => 1,
                    'status' => 'menunggu_verifikasi',
                    'created_at' => $user->created_at ?? $timestamp,
                    'updated_at' => $user->created_at ?? $timestamp,
                ])->all();

                if ($requests !== []) {
                    DB::table('user_account_verification_requests')->insert($requests);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasTable('user_account_verification_requests')
            && DB::table('user_account_verification_requests')->exists()) {
            throw new RuntimeException(
                'Rollback ditolak: riwayat verifikasi akun masih tersimpan. Riwayat keputusan harus dipertahankan; tidak ada data yang dihapus.'
            );
        }

        Schema::dropIfExists('user_account_verification_requests');
    }
};
