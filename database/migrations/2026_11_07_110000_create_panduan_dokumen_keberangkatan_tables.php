<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jalur_keberangkatan', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique();
            $table->string('nama', 180);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('kategori_panduan_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120)->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        Schema::create('panduan_dokumen_keberangkatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_panduan_dokumen')->restrictOnDelete();
            $table->string('nama', 180);
            $table->text('penjelasan')->nullable();
            $table->text('petunjuk_pengurusan')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['kategori_id', 'is_published']);
        });

        Schema::create('panduan_dokumen_jalur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jalur_id')->constrained('jalur_keberangkatan')->cascadeOnDelete();
            $table->foreignId('panduan_dokumen_id')->constrained('panduan_dokumen_keberangkatan')->cascadeOnDelete();
            $table->string('penerapan', 24)->default('unconfigured'); // unconfigured, required, optional, not_applicable
            $table->timestamps();
            $table->unique(['jalur_id', 'panduan_dokumen_id']);
            $table->index(['jalur_id', 'penerapan']);
        });

        Schema::create('checklist_dokumen_keberangkatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jalur_id')->constrained('jalur_keberangkatan')->restrictOnDelete();
            $table->foreignId('panduan_dokumen_id')->constrained('panduan_dokumen_keberangkatan')->restrictOnDelete();
            $table->string('status', 24)->default('belum_disiapkan'); // belum_disiapkan, sedang_diproses, siap
            $table->timestamps();
            $table->unique(['user_id', 'jalur_id', 'panduan_dokumen_id'], 'checklist_user_jalur_dokumen_unique');
            $table->index(['user_id', 'jalur_id', 'status']);
        });

        $now = now();
        $jalurIds = [];
        foreach ([
            ['slug' => 'tokutei-ginou', 'nama' => 'Tokutei Ginou / Specified Skilled Worker (SSW)'],
            ['slug' => 'pemagangan-jepang', 'nama' => 'Program Pemagangan Jepang'],
        ] as $jalur) {
            $jalurIds[] = DB::table('jalur_keberangkatan')->insertGetId([
                ...$jalur,
                'deskripsi' => 'Jalur ini mengelompokkan panduan dokumen. Penerapan setiap dokumen perlu ditentukan oleh Admin BKK.',
                'is_published' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $kategoriIds = [];
        foreach ([
            'Identitas Diri',
            'Dokumen Keluarga',
            'Dokumen Perjalanan',
            'Dokumen Pekerjaan',
            'Sertifikasi Kompetensi dan Bahasa',
            'Dokumen Perlindungan Pekerja Migran',
        ] as $namaKategori) {
            $kategoriIds[$namaKategori] = DB::table('kategori_panduan_dokumen')->insertGetId([
                'nama' => $namaKategori,
                'deskripsi' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $dokumenAwal = [
            ['nama' => 'KTP', 'kategori' => 'Identitas Diri'],
            ['nama' => 'Kartu Keluarga', 'kategori' => 'Dokumen Keluarga'],
            ['nama' => 'Akta Kelahiran', 'kategori' => 'Dokumen Keluarga'],
            ['nama' => 'Surat Persetujuan Orang Tua/Wali atau Pasangan', 'kategori' => 'Dokumen Keluarga'],
            ['nama' => 'Paspor', 'kategori' => 'Dokumen Perjalanan'],
            ['nama' => 'Visa Kerja', 'kategori' => 'Dokumen Perjalanan'],
            ['nama' => 'Perjanjian Kerja', 'kategori' => 'Dokumen Pekerjaan'],
            ['nama' => 'Sertifikat Kompetensi', 'kategori' => 'Sertifikasi Kompetensi dan Bahasa'],
            ['nama' => 'Sertifikat Bahasa', 'kategori' => 'Sertifikasi Kompetensi dan Bahasa'],
            ['nama' => 'E-PMI', 'kategori' => 'Dokumen Perlindungan Pekerja Migran'],
            ['nama' => 'BPJS Ketenagakerjaan', 'kategori' => 'Dokumen Perlindungan Pekerja Migran'],
            ['nama' => 'Certificate of Eligibility (CoE)', 'kategori' => 'Dokumen Perjalanan'],
        ];

        foreach ($dokumenAwal as $dokumen) {
            $dokumenId = DB::table('panduan_dokumen_keberangkatan')->insertGetId([
                'kategori_id' => $kategoriIds[$dokumen['kategori']],
                'nama' => $dokumen['nama'],
                'penjelasan' => 'Dokumen ini berasal dari daftar referensi awal sekolah. Penerapan dan statusnya untuk jalur tertentu perlu ditentukan Admin BKK.',
                'petunjuk_pengurusan' => null,
                'keterangan' => null,
                'is_published' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($jalurIds as $jalurId) {
                DB::table('panduan_dokumen_jalur')->insert([
                    'jalur_id' => $jalurId,
                    'panduan_dokumen_id' => $dokumenId,
                    'penerapan' => 'unconfigured',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_dokumen_keberangkatan');
        Schema::dropIfExists('panduan_dokumen_jalur');
        Schema::dropIfExists('panduan_dokumen_keberangkatan');
        Schema::dropIfExists('kategori_panduan_dokumen');
        Schema::dropIfExists('jalur_keberangkatan');
    }
};
