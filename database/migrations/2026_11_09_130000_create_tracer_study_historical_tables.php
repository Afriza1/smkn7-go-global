<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracer_study_historical_sources', function (Blueprint $table): void {
            $table->id();
            $table->string('source_key', 80)->unique();
            $table->string('source_label', 180);
            $table->unsignedSmallInteger('source_year');
            $table->string('source_period_label', 180);
            $table->string('source_date_range', 180)->nullable();
            $table->string('file_name', 255);
            $table->char('file_sha256', 64);
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
            $table->index(['source_year', 'source_key'], 'tracer_hist_sources_year_key_idx');
        });

        Schema::create('tracer_study_historical_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('source_id')->constrained('tracer_study_historical_sources')->cascadeOnDelete();
            $table->unsignedInteger('source_row_number');
            $table->text('nama_lengkap'); // Laravel encrypted cast; never used in statistics.
            $table->string('jurusan')->nullable();
            $table->string('status_asli')->nullable();
            $table->string('instansi')->nullable();
            $table->string('posisi')->nullable();
            $table->string('penempatan')->nullable();
            $table->longText('raw_values'); // Encrypted full row, including WhatsApp where present.
            $table->timestamp('imported_at');
            $table->timestamps();

            $table->unique(['source_id', 'source_row_number'], 'tracer_hist_source_row_unique');
            $table->index(['source_id', 'jurusan'], 'tracer_hist_source_major_idx');
            $table->index(['source_id', 'status_asli'], 'tracer_hist_source_status_idx');
            $table->index(['source_id', 'posisi'], 'tracer_hist_source_position_idx');
            $table->index(['source_id', 'penempatan'], 'tracer_hist_source_placement_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracer_study_historical_records');
        Schema::dropIfExists('tracer_study_historical_sources');
    }
};
