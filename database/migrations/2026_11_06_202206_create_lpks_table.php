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
        Schema::create('lpks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lpk');
            $table->string('slug')->unique();
            $table->string('ringkasan');
            $table->text('deskripsi')->nullable();
            $table->string('bidang_program');
            $table->string('wilayah_penempatan')->nullable();
            $table->text('persyaratan_khusus')->nullable();
            $table->integer('kuota_tersedia')->default(25);
            $table->boolean('is_demo')->default(false);
            $table->boolean('is_aktif')->default(true);
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpks');
    }
};
