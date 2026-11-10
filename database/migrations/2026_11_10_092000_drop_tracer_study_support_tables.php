<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MAIN = 'tracer_studies';

    public function up(): void
    {
        $legacyTables = [
            'tracer_study_periodes',
            'tracer_study_histories',
            'tracer_study_follow_ups',
            'tracer_study_historical_sources',
            'tracer_study_historical_records',
        ];
        foreach ($legacyTables as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Expected legacy table {$table} is missing; no tables were dropped.");
            }
        }

        $legacyHistorical = DB::table('tracer_study_historical_records')->count();
        $mainHistorical = DB::table(self::MAIN)->where('record_type', 'historical')->count();
        if ($legacyHistorical !== $mainHistorical) {
            throw new RuntimeException("Historical counts do not match ({$legacyHistorical} legacy, {$mainHistorical} consolidated). No tables were dropped.");
        }
        $sourceCount = DB::table('tracer_study_historical_sources')->count();
        $websiteCount = DB::table(self::MAIN)->where('record_type', 'website')->count();
        $periodCount = DB::table('tracer_study_periodes')->count();
        $historyCount = DB::table('tracer_study_histories')->count();
        $followUpCount = DB::table('tracer_study_follow_ups')->count();
        $hasAnyData = $legacyHistorical + $sourceCount + $websiteCount + $periodCount + $historyCount + $followUpCount > 0;

        $expectedYears = DB::table('tracer_study_historical_records as record')
            ->join('tracer_study_historical_sources as source', 'source.id', '=', 'record.source_id')
            ->select('source.source_year')->selectRaw('COUNT(*) as total')->groupBy('source.source_year')->pluck('total', 'source_year');
        $actualYears = DB::table(self::MAIN)->where('record_type', 'historical')
            ->select('tahun_pencatatan')->selectRaw('COUNT(*) as total')->groupBy('tahun_pencatatan')->pluck('total', 'tahun_pencatatan');
        foreach ($expectedYears as $year => $count) {
            if ((int) $count !== (int) ($actualYears[$year] ?? 0)) {
                throw new RuntimeException("Historical year {$year} count mismatch. No tables were dropped.");
            }
        }
        if ($hasAnyData && ((int) ($expectedYears[2025] ?? 0) !== 84 || (int) ($expectedYears[2026] ?? 0) !== 45)) {
            throw new RuntimeException('Expected historical baseline is 84 records for 2025 and 45 for 2026. No tables were dropped.');
        }

        $legacyResponses = $websiteCount;
        $allPeopleRecords = DB::table(self::MAIN)->whereIn('record_type', ['website', 'historical'])->count();
        if ($hasAnyData && ($legacyResponses !== 1 || $allPeopleRecords !== 130)) {
            throw new RuntimeException('Expected one website response and 130 alumni records in total. No tables were dropped.');
        }
        if ($hasAnyData && ($periodCount !== 1
            || (int) DB::table(self::MAIN)->where('record_type', 'period')->value('tahun') !== 2026)) {
            throw new RuntimeException('Expected the existing 2026 period record to be present. No tables were dropped.');
        }

        $copiedHistoryCount = DB::table(self::MAIN)->where('record_type', 'website')->pluck('riwayat_verifikasi')
            ->sum(fn (string $value) => count(json_decode(Crypt::decryptString($value), true, 512, JSON_THROW_ON_ERROR)));
        if ($historyCount !== $copiedHistoryCount) {
            throw new RuntimeException('Verification history validation failed. No tables were dropped.');
        }
        if ($hasAnyData && $historyCount !== 2) {
            throw new RuntimeException('Expected both audited verification history entries to be present. No tables were dropped.');
        }
        $copiedFollowUpCount = DB::table(self::MAIN)->where('record_type', 'website')->pluck('tindak_lanjut_data')
            ->sum(fn (string $value) => count(json_decode(Crypt::decryptString($value), true, 512, JSON_THROW_ON_ERROR)));
        if ($followUpCount !== $copiedFollowUpCount) {
            throw new RuntimeException('Follow-up validation failed. No tables were dropped.');
        }
        if ($hasAnyData && $followUpCount !== 0) {
            throw new RuntimeException('Unexpected follow-up data differs from the audited state. No tables were dropped.');
        }

        $unknownIncomingForeignKeys = DB::getDriverName() === 'mysql'
            ? DB::table('information_schema.KEY_COLUMN_USAGE')
                ->whereRaw('REFERENCED_TABLE_SCHEMA = DATABASE()')
                ->whereIn('REFERENCED_TABLE_NAME', $legacyTables)
                ->whereNotIn('TABLE_NAME', $legacyTables)
                ->exists()
            : false;
        if ($unknownIncomingForeignKeys) {
            throw new RuntimeException('A table outside the Tracer Study set references a legacy table. No tables were dropped.');
        }

        Schema::dropIfExists('tracer_study_historical_records');
        Schema::dropIfExists('tracer_study_historical_sources');
        Schema::dropIfExists('tracer_study_follow_ups');
        Schema::dropIfExists('tracer_study_histories');
        Schema::dropIfExists('tracer_study_periodes');
    }

    public function down(): void
    {
        if (Schema::hasTable('tracer_study_periodes')) {
            return;
        }

        Schema::create('tracer_study_periodes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('tahun')->unique('tracer_period_year_unique');
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->string('status', 20)->default('draft');
            $table->string('active_key', 20)->nullable()->unique('tracer_period_active_key_unique');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->string('nama', 120)->nullable();
            $table->text('keterangan')->nullable();
            $table->index(['status', 'tanggal_mulai', 'tanggal_berakhir'], 'tracer_period_status_dates_idx');
        });
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
            $table->text('nama_lengkap');
            $table->string('jurusan')->nullable();
            $table->string('status_asli')->nullable();
            $table->string('instansi')->nullable();
            $table->string('posisi')->nullable();
            $table->string('penempatan')->nullable();
            $table->longText('raw_values');
            $table->timestamp('imported_at');
            $table->timestamps();
            $table->unique(['source_id', 'source_row_number'], 'tracer_hist_source_row_unique');
            $table->index(['source_id', 'jurusan'], 'tracer_hist_source_major_idx');
            $table->index(['source_id', 'status_asli'], 'tracer_hist_source_status_idx');
            $table->index(['source_id', 'posisi'], 'tracer_hist_source_position_idx');
            $table->index(['source_id', 'penempatan'], 'tracer_hist_source_placement_idx');
        });
        Schema::create('tracer_study_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tracer_study_id')->constrained('tracer_studies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ringkasan_perubahan');
            $table->timestamps();
            $table->index(['tracer_study_id', 'created_at']);
        });
        Schema::create('tracer_study_follow_ups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tracer_study_id')->constrained('tracer_studies')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis', 30);
            $table->text('catatan_internal')->nullable();
            $table->text('pesan_alumni')->nullable();
            $table->timestamp('diberitahukan_pada')->nullable();
            $table->timestamps();
            $table->index(['tracer_study_id', 'jenis', 'created_at']);
        });

        DB::transaction(function (): void {
            $records = DB::table(self::MAIN)->where('record_type', 'historical')->orderBy('id')->get();
            foreach ($records->pluck('source_key')->unique() as $key) {
                $source = $records->firstWhere('source_key', $key);
                DB::table('tracer_study_historical_sources')->insert([
                    'id' => $source->legacy_source_id,
                    'source_key' => $source->source_key,
                    'source_label' => $source->source_label,
                    'source_year' => $source->tahun_pencatatan,
                    'source_period_label' => $source->source_period_label,
                    'source_date_range' => $source->source_date_range,
                    'file_name' => $source->source_file_name,
                    'file_sha256' => $source->source_sha256,
                    'imported_at' => $source->source_imported_at,
                    'created_at' => $source->source_imported_at,
                    'updated_at' => $source->source_imported_at,
                ]);
            }
            foreach ($records as $record) {
                DB::table('tracer_study_historical_records')->insert([
                    'id' => $record->legacy_record_id,
                    'source_id' => $record->legacy_source_id,
                    'source_row_number' => $record->source_row_number,
                    'nama_lengkap' => $record->nama_lengkap_terenkripsi,
                    'jurusan' => $record->jurusan,
                    'status_asli' => $record->status_asli,
                    'instansi' => $record->instansi_asli,
                    'posisi' => $record->posisi_asli,
                    'penempatan' => $record->penempatan_asli,
                    'raw_values' => $record->data_sumber,
                    'imported_at' => $record->source_imported_at,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            }
            foreach (DB::table(self::MAIN)->where('record_type', 'period')->orderBy('id')->get() as $period) {
                DB::table('tracer_study_periodes')->insert([
                    'id' => $period->legacy_periode_id,
                    'tahun' => $period->tahun,
                    'tanggal_mulai' => $period->tanggal_mulai,
                    'tanggal_berakhir' => $period->tanggal_berakhir,
                    'status' => $period->status,
                    'active_key' => $period->active_key,
                    'dibuat_oleh' => $period->dibuat_oleh,
                    'nama' => $period->nama,
                    'keterangan' => $period->keterangan,
                    'created_at' => $period->created_at,
                    'updated_at' => $period->updated_at,
                ]);
            }
            foreach (DB::table(self::MAIN)->where('record_type', 'website')->get() as $response) {
                $entries = json_decode(Crypt::decryptString($response->riwayat_verifikasi), true, 512, JSON_THROW_ON_ERROR);
                foreach ($entries as $entry) {
                    DB::table('tracer_study_histories')->insert([
                        'id' => $entry['id'], 'tracer_study_id' => $response->id, 'user_id' => $entry['user_id'],
                        'ringkasan_perubahan' => $entry['ringkasan_perubahan'], 'created_at' => $entry['created_at'], 'updated_at' => $entry['updated_at'],
                    ]);
                }
                $followUps = json_decode(Crypt::decryptString($response->tindak_lanjut_data), true, 512, JSON_THROW_ON_ERROR);
                foreach ($followUps as $entry) {
                    DB::table('tracer_study_follow_ups')->insert([
                        'id' => $entry['id'], 'tracer_study_id' => $response->id, 'admin_id' => $entry['admin_id'],
                        'jenis' => $entry['jenis'], 'catatan_internal' => $entry['catatan_internal'], 'pesan_alumni' => $entry['pesan_alumni'],
                        'diberitahukan_pada' => $entry['diberitahukan_pada'], 'created_at' => $entry['created_at'], 'updated_at' => $entry['updated_at'],
                    ]);
                }
            }
        });
    }
};
