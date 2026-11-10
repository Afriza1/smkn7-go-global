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
        // 1. Parameter Global Simulasi Finansial
        Schema::create('parameter_simulasi_finansials', function (Blueprint $table) {
            $table->id();
            $table->decimal('kurs_jpy_idr', 10, 2)->default(108.50);
            $table->unsignedInteger('default_gaji_jpy')->default(180000);
            $table->decimal('default_biaya_keberangkatan', 15, 2)->default(35000000);
            $table->decimal('default_biaya_hidup_jepang', 15, 2)->default(8500000);
            $table->decimal('default_potongan_lainnya', 15, 2)->default(1500000);
            $table->string('sumber_informasi')->default('Bursa Kerja Khusus (BKK) SMKN 7 Jember');
            $table->text('keterangan_asumsi')->nullable();
            $table->timestamp('tanggal_pembaruan')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Mitra Pembiayaan
        Schema::create('mitra_pembiayaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mitra');
            $table->string('slug')->unique();
            $table->string('jenis_skema');
            $table->decimal('plafon_maksimal', 15, 2)->default(30000000);
            $table->decimal('suku_bunga_tahunan', 5, 2)->nullable();
            $table->decimal('margin_bulanan', 5, 2)->nullable();
            $table->string('metode_perhitungan')->default('flat'); // 'flat', 'anuitas', 'margin_syariah', 'belum_tersedia'
            $table->json('tenor_pilihan')->nullable(); // e.g. [6, 12, 18, 24]
            $table->string('biaya_administrasi_tipe')->default('nominal'); // 'nominal', 'persentase'
            $table->decimal('biaya_administrasi_nilai', 15, 2)->default(0);
            $table->decimal('uang_muka_persen', 5, 2)->default(0);
            $table->string('metode_pembayaran')->nullable();
            $table->text('persyaratan')->nullable();
            $table->string('sumber_informasi')->nullable();
            $table->date('tanggal_berlaku')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->text('keterangan_status')->nullable();
            $table->timestamps();
        });

        // 3. Riwayat Hasil Simulasi Pengguna
        Schema::create('riwayat_simulasi_finansials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis_simulasi', ['mandiri', 'mitra']);
            $table->string('judul_simulasi')->default('Simulasi Finansial');
            $table->foreignId('mitra_pembiayaan_id')->nullable()->constrained('mitra_pembiayaans')->nullOnDelete();
            $table->string('nama_mitra_snapshot')->nullable();
            $table->json('snapshot_input');
            $table->json('snapshot_parameter');
            $table->json('snapshot_hasil');
            $table->text('catatan_pengguna')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_simulasi_finansials');
        Schema::dropIfExists('mitra_pembiayaans');
        Schema::dropIfExists('parameter_simulasi_finansials');
    }
};
