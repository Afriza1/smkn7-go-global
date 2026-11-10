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
        // 1. Tabel Program Bantuan & Pembiayaan Kerjasama
        Schema::create('program_bantuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program');
            $table->string('slug')->unique();
            $table->string('nama_mitra'); // e.g. BMT NU Tanggul, LPK Genzo, Goko, Bank BNT
            $table->string('jenis_program'); // e.g. dana_talangan, bantuan_pelatihan, subsidi_keberangkatan, pinjaman_lunak
            $table->text('deskripsi');
            $table->text('persyaratan');
            $table->json('dokumen_dibutuhkan'); // array berkas wajib e.g. ["KTP Siswa", "Kartu Keluarga", ...]
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->string('keterangan_status')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Pengajuan Permohonan Bantuan oleh Siswa / Alumni
        Schema::create('pengajuan_bantuans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique(); // e.g. BANSOS-202611-0001
            $table->foreignId('program_bantuan_id')->constrained('program_bantuans')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('alasan_pengajuan');
            $table->text('informasi_tambahan')->nullable();
            $table->string('status')->default('menunggu_pemeriksaan');
            // Status: 'menunggu_pemeriksaan', 'perlu_perbaikan', 'terverifikasi_sekolah',
            //         'diteruskan_mitra', 'sedang_diproses_mitra', 'disetujui', 'ditolak', 'selesai', 'dibatalkan'
            $table->text('keterangan_pemohon')->nullable(); // Penjelasan/pesan resmi untuk siswa/alumni
            $table->text('catatan_perbaikan')->nullable();  // Rincian berkas yang wajib diperbaiki
            $table->boolean('persetujuan_data')->default(true); // Persetujuan penerusan data ke mitra
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamp('diteruskan_pada')->nullable();
            $table->timestamp('diputuskan_pada')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Dokumen Persyaratan Pengajuan (Private Storage)
        Schema::create('dokumen_pengajuan_bantuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_bantuan_id')->constrained('pengajuan_bantuans')->cascadeOnDelete();
            $table->string('nama_dokumen'); // e.g. 'KTP Siswa', 'Kartu Keluarga'
            $table->string('file_path');    // Relative path di disk 'local' (storage/app/private/...)
            $table->string('file_name');    // Original client file name
            $table->unsignedInteger('file_size'); // Ukuran bytes
            $table->string('mime_type');
            $table->timestamps();
        });

        // 4. Tabel Catatan Internal Admin BKK (Privat, Tidak Pernah Tampil ke Pemohon)
        Schema::create('catatan_internal_bantuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_bantuan_id')->constrained('pengajuan_bantuans')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->date('tanggal_koordinasi');
            $table->string('pihak_mitra'); // e.g. 'Bpk. Ahmad (BMT NU Tanggul)'
            $table->text('catatan_koordinasi');
            $table->string('keputusan_mitra')->nullable();
            $table->timestamps();
        });

        // 5. Tabel Audit Trail Riwayat Status Pengajuan
        Schema::create('riwayat_status_bantuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_bantuan_id')->constrained('pengajuan_bantuans')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status_sebelumnya')->nullable();
            $table->string('status_baru');
            $table->text('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_bantuans');
        Schema::dropIfExists('catatan_internal_bantuans');
        Schema::dropIfExists('dokumen_pengajuan_bantuans');
        Schema::dropIfExists('pengajuan_bantuans');
        Schema::dropIfExists('program_bantuans');
    }
};
