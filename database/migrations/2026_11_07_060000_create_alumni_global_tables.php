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
        // 1. Tabel Direktori & Profil Mentor Alumni Global
        Schema::create('alumni_globals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_lengkap');
            $table->string('foto_profil')->nullable();
            $table->unsignedSmallInteger('tahun_kelulusan');
            $table->string('jurusan');
            $table->string('negara_bekerja')->default('Jepang');
            $table->string('kota_wilayah')->nullable(); // e.g. Ibaraki, Aichi, Tokyo, Osaka
            $table->string('bidang_pekerjaan'); // e.g. Manufaktur, Pengolahan Makanan, Pertanian, IT
            $table->string('nama_perusahaan')->nullable();
            $table->text('pengalaman_kerja_singkat');
            $table->text('deskripsi_pengalaman')->nullable();
            $table->text('tips_karier')->nullable();
            $table->string('bahasa_dikuasai')->nullable();
            $table->string('lama_bekerja')->nullable(); // e.g. '2 Tahun 6 Bulan'
            $table->boolean('is_mentor')->default(false);
            $table->boolean('kontak_disetujui')->default(false); // Apakah alumni menyetujui kontaknya ditampilkan
            $table->string('nomor_kontak')->nullable(); // Nomor WhatsApp/telepon resmi alumni
            $table->string('email_kontak')->nullable();
            $table->boolean('is_aktif')->default(true); // Status publikasi
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        // 2. Tabel Pengaturan Komunitas WhatsApp Alumni Global
        Schema::create('komunitas_alumni_globals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_komunitas')->default('Komunitas Alumni SMKN 7 Jember – Go Global Jepang');
            $table->text('deskripsi');
            $table->text('tujuan_komunitas');
            $table->text('aturan_komunitas');
            $table->string('link_whatsapp')->nullable();
            $table->boolean('is_aktif')->default(false); // Default false jika tautan belum tersedia resmi
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komunitas_alumni_globals');
        Schema::dropIfExists('alumni_globals');
    }
};
