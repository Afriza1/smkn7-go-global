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
        // 1. Tabel Utama Pengajuan & Sesi Konseling Digital
        Schema::create('konseling_digitals', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_konseling', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Kategori & Metode
            $table->string('kategori', 50); // persiapan_jepang, pendidikan_karier, persiapan_keberangkatan, pribadi_sosial, lainnya
            $table->string('metode', 30)->default('online'); // online, tatap_muka
            $table->text('alasan_konseling');
            
            // Usulan dari Siswa / Alumni
            $table->date('tanggal_usulan');
            $table->string('waktu_usulan', 50); // e.g. "09:00 WIB" atau "Jam Istirahat (12:00)"
            
            // Status Alur Konseling
            $table->string('status', 40)->default('menunggu_konfirmasi'); 
            // menunggu_konfirmasi, dijadwalkan, berlangsung, selesai, ditolak, dibatalkan
            
            // Penetapan Jadwal Pasti oleh Guru BK
            $table->date('tanggal_jadwal')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('tempat_tatap_muka')->nullable(); // Bila tatap muka: Ruang BK Gedung A Lantai 1
            $table->text('catatan_jadwal')->nullable(); // Pesan pengingat jadwal dari Guru BK
            
            // Catatan Penolakan / Pembatalan
            $table->text('alasan_penolakan')->nullable();
            $table->text('alasan_pembatalan')->nullable();
            
            // Timestamp Sesi Berlangsung
            $table->timestamp('sesi_dimulai_pada')->nullable();
            $table->timestamp('sesi_berakhir_pada')->nullable();

            $table->timestamps();

            // Index pencarian
            $table->index(['user_id', 'status']);
            $table->index(['guru_id', 'status']);
            $table->index('tanggal_jadwal');
        });

        // 2. Tabel Pesan Percakapan Chat Konseling
        Schema::create('pesan_konselings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konseling_id')->constrained('konseling_digitals')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('pesan');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['konseling_id', 'created_at']);
        });

        // 3. Tabel Catatan Tindak Lanjut Privat Guru BK (Terpisah dari Chat)
        Schema::create('catatan_privat_konselings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konseling_id')->constrained('konseling_digitals')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            
            // Konten Privat & Observasi Profesional
            $table->text('ringkasan_masalah')->nullable();
            $table->text('tindak_lanjut');
            $table->text('evaluasi_psikologis_perilaku')->nullable();
            $table->string('status_urgensi', 20)->default('sedang'); // rendah, sedang, tinggi
            
            // Mekanisme Eskalasi Terpilih ke Pimpinan (Tanpa membuka seluruh riwayat chat)
            $table->boolean('perlu_eskalasi_kepala_sekolah')->default(false);
            $table->text('catatan_eskalasi')->nullable();
            $table->timestamp('tanggal_eskalasi')->nullable();
            
            $table->timestamps();

            $table->index('konseling_id');
            $table->index('guru_id');
        });

        // 4. Log Riwayat Perubahan Status (Audit Trail)
        Schema::create('riwayat_status_konselings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konseling_id')->constrained('konseling_digitals')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status_sebelumnya', 40)->nullable();
            $table->string('status_baru', 40);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('konseling_id');
        });

        // 5. Tabel Informasi Kontak Layanan Konseling Resmi
        Schema::create('kontak_layanan_konselings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layanan');
            $table->string('nama_guru_bk');
            $table->string('kontak_resmi');
            $table->string('jam_layanan');
            $table->string('lokasi_ruangan');
            $table->string('email_layanan')->nullable();
            $table->string('kontak_bantuan_eskalasi')->nullable();
            $table->text('keterangan_tambahan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontak_layanan_konselings');
        Schema::dropIfExists('riwayat_status_konselings');
        Schema::dropIfExists('catatan_privat_konselings');
        Schema::dropIfExists('pesan_konselings');
        Schema::dropIfExists('konseling_digitals');
    }
};
