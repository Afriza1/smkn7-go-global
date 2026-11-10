<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendaftaran_karier_jepangs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pendaftaran')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lpk_id')->constrained('lpks')->cascadeOnDelete();
            $table->enum('jenis_peserta', ['siswa', 'alumni']);
            $table->string('nama_lengkap');
            $table->string('nis_nisn')->nullable();
            $table->string('tahun_lulus')->nullable();
            $table->string('jurusan');
            $table->string('nomor_telepon');
            $table->string('email');
            $table->text('alamat');
            $table->text('alasan_minat')->nullable();

            // Main status
            $table->string('status')->default('menunggu_admin');

            // Secure private document file paths
            $table->string('file_identitas');
            $table->string('file_kk');
            $table->string('file_rapor_ijazah');
            $table->string('file_surat_ortu');
            $table->string('file_tambahan')->nullable();

            // Verification & approval trails
            $table->text('catatan_admin')->nullable();
            $table->foreignId('admin_checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('admin_checked_at')->nullable();

            $table->text('catatan_bk')->nullable();
            $table->foreignId('bk_checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('bk_checked_at')->nullable();

            $table->text('catatan_kepala_sekolah')->nullable();
            $table->foreignId('kepala_sekolah_checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('kepala_sekolah_checked_at')->nullable();

            // Post-approval LPK status updates by admin
            $table->text('catatan_lpk_admin')->nullable();
            $table->timestamp('lpk_updated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_karier_jepangs');
    }
};
