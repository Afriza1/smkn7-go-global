<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_edukasi_materis', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 180);
            $table->text('deskripsi');
            $table->string('kategori', 120);
            $table->string('file_path', 255)->unique();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['is_published', 'kategori']);
        });

        $timestamp = now();

        DB::table('modul_edukasi_materis')->insert([
            [
                'judul' => 'Menjadi Pekerja Migran Indonesia (PMI) yang Cerdas, Legal, dan Terlindungi',
                'deskripsi' => 'Materi pengenalan jalur prosedural dan nonprosedural, persyaratan dokumen, tahapan resmi bekerja ke luar negeri, serta kewaspadaan terhadap penipuan PMI.',
                'kategori' => 'Dasar Pekerja Migran Indonesia',
                'file_path' => 'materi/Menjadi-Pekerja-Migran-Indonesia.pdf',
                'is_published' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'judul' => 'Panduan Pintar Pekerja Migran Indonesia',
                'deskripsi' => 'Materi mengenai jalur resmi, pengenalan calo dan P3MI, pemahaman budaya kerja, serta kontrak dan hak pekerja migran.',
                'kategori' => 'Perlindungan Pekerja Migran Indonesia',
                'file_path' => 'materi/Panduan-Pintar-Pekerja-Migran-Indonesia.pdf',
                'is_published' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'judul' => 'Panduan Sukses & Aman Karir di Jepang',
                'deskripsi' => 'Materi tentang jalur kerja dan pemagangan di Jepang, pemeriksaan legalitas lembaga, persiapan bahasa dan budaya, serta kontrak kerja.',
                'kategori' => 'Persiapan Karier Jepang',
                'file_path' => 'materi/Panduan-Sukses-Karier-di-Jepang.pdf',
                'is_published' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('modul_edukasi_materis');
    }
};
