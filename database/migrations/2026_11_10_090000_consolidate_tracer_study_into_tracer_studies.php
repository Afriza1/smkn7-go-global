<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MAIN = 'tracer_studies';
    private const PERIODS = 'tracer_study_periodes';
    private const HISTORIES = 'tracer_study_histories';
    private const FOLLOW_UPS = 'tracer_study_follow_ups';
    private const HISTORICAL_RECORDS = 'tracer_study_historical_records';
    private const HISTORICAL_SOURCES = 'tracer_study_historical_sources';

    public function up(): void
    {
        foreach ([self::MAIN, self::PERIODS, self::HISTORIES, self::FOLLOW_UPS, self::HISTORICAL_RECORDS, self::HISTORICAL_SOURCES] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Required Tracer Study table {$table} is missing; no consolidation was performed.");
            }
        }

        if (Schema::hasColumn(self::MAIN, 'record_type')) {
            throw new RuntimeException('Tracer Study consolidation schema is already partly present; inspect before retrying.');
        }

        $periodForeignKey = collect(Schema::getForeignKeys(self::MAIN))
            ->first(fn (array $foreignKey) => $foreignKey['columns'] === ['periode_id']
                && $foreignKey['foreign_table'] === self::PERIODS);
        if (! $periodForeignKey) {
            throw new RuntimeException('The expected response-to-period foreign key was not found; inspect schema before migration.');
        }
        Schema::table(self::MAIN, function (Blueprint $table) use ($periodForeignKey): void {
            $table->dropForeign($periodForeignKey['name']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('periode_id')->nullable()->change();
            $table->text('nama_lengkap')->nullable()->change();
            $table->unsignedSmallInteger('tahun_lulus')->nullable()->change();
            $table->string('jurusan')->nullable()->change();
            $table->text('alamat')->nullable()->change();
            $table->string('status_kegiatan', 255)->nullable()->change();
            $table->string('pekerjaan')->nullable()->change();
            $table->string('nama_perusahaan')->nullable()->change();
            $table->unsignedSmallInteger('tahun_masuk_perusahaan')->nullable()->change();
            $table->text('alamat_tempat_kerja')->nullable()->change();
            $table->string('status_verifikasi', 30)->nullable()->change();
        });

        Schema::table(self::MAIN, function (Blueprint $table): void {
            $table->string('record_type', 20)->nullable()->after('id');
            $table->unsignedSmallInteger('tahun_pencatatan')->nullable();
            $table->string('sumber_data', 20)->nullable();
            $table->string('source_key', 80)->nullable();
            $table->unsignedInteger('source_row_number')->nullable();
            $table->unsignedBigInteger('legacy_record_id')->nullable();
            $table->unsignedBigInteger('legacy_source_id')->nullable();
            $table->unsignedBigInteger('legacy_periode_id')->nullable();
            $table->longText('nama_lengkap_terenkripsi')->nullable();
            $table->longText('status_asli')->nullable();
            $table->text('instansi_asli')->nullable();
            $table->text('posisi_asli')->nullable();
            $table->text('penempatan_asli')->nullable();
            $table->longText('data_sumber')->nullable();
            $table->longText('riwayat_verifikasi')->nullable();
            $table->longText('tindak_lanjut_data')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->string('status', 20)->nullable();
            $table->string('active_key', 20)->nullable();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->string('nama', 120)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('source_label', 180)->nullable();
            $table->string('source_period_label', 180)->nullable();
            $table->string('source_date_range', 180)->nullable();
            $table->string('source_file_name', 255)->nullable();
            $table->char('source_sha256', 64)->nullable();
            $table->timestamp('source_imported_at')->nullable();
            $table->unique(['source_key', 'source_row_number'], 'tracer_studies_source_row_unique');
            $table->unique('active_key', 'tracer_studies_active_key_unique');
            $table->unique(['record_type', 'tahun'], 'tracer_studies_type_period_year_unique');
            $table->index(['record_type', 'tahun_pencatatan'], 'tracer_studies_type_year_index');
            $table->index(['record_type', 'status', 'tanggal_mulai', 'tanggal_berakhir'], 'tracer_studies_period_window_index');
        });

        DB::transaction(function (): void {
            DB::table(self::MAIN)->whereNull('record_type')->update([
                'record_type' => 'website',
                'sumber_data' => 'website',
                'riwayat_verifikasi' => Crypt::encryptString('[]'),
                'tindak_lanjut_data' => Crypt::encryptString('[]'),
            ]);

            $periodIdMap = [];
            foreach (DB::table(self::PERIODS)->orderBy('id')->get() as $period) {
                $newPeriodId = DB::table(self::MAIN)->insertGetId([
                    'record_type' => 'period',
                    'sumber_data' => 'internal',
                    'tahun_pencatatan' => $period->tahun,
                    'tahun' => $period->tahun,
                    'tanggal_mulai' => $period->tanggal_mulai,
                    'tanggal_berakhir' => $period->tanggal_berakhir,
                    'status' => $period->status,
                    'active_key' => $period->active_key,
                    'dibuat_oleh' => $period->dibuat_oleh,
                    'nama' => $period->nama ?? null,
                    'keterangan' => $period->keterangan ?? null,
                    'legacy_periode_id' => $period->id,
                    'status_verifikasi' => null,
                    'created_at' => $period->created_at,
                    'updated_at' => $period->updated_at,
                ]);
                $periodIdMap[$period->id] = $newPeriodId;
            }

            foreach ($periodIdMap as $oldId => $newId) {
                DB::table(self::MAIN)->where('record_type', 'website')->where('periode_id', $oldId)->update([
                    'periode_id' => $newId,
                    'tahun_pencatatan' => DB::table(self::MAIN)->where('id', $newId)->value('tahun'),
                ]);
            }

            $sourceMap = DB::table(self::HISTORICAL_SOURCES)->get()->keyBy('id');
            $historicalRows = DB::table(self::HISTORICAL_RECORDS)->orderBy('id')->get();
            foreach ($historicalRows as $row) {
                $source = $sourceMap->get($row->source_id);
                if (! $source) {
                    throw new RuntimeException("Historical row {$row->id} references a missing source.");
                }

                $exists = DB::table(self::MAIN)->where('source_key', $source->source_key)
                    ->where('source_row_number', $row->source_row_number)->exists();
                if ($exists) {
                    throw new RuntimeException('A historical source row is already present in tracer_studies; aborting to avoid duplicates.');
                }

                DB::table(self::MAIN)->insert([
                    'record_type' => 'historical',
                    'sumber_data' => 'historical',
                    'tahun_pencatatan' => $source->source_year,
                    'user_id' => null,
                    'periode_id' => null,
                    'nama_lengkap' => null,
                    'nama_lengkap_terenkripsi' => $row->nama_lengkap,
                    'tahun_lulus' => null,
                    'jurusan' => $row->jurusan,
                    'status_kegiatan' => null,
                    'status_asli' => $row->status_asli,
                    'pekerjaan' => $row->posisi,
                    'nama_perusahaan' => $row->instansi,
                    'alamat_tempat_kerja' => null,
                    'instansi_asli' => $row->instansi,
                    'posisi_asli' => $row->posisi,
                    'penempatan_asli' => $row->penempatan,
                    'data_sumber' => $row->raw_values,
                    'source_key' => $source->source_key,
                    'source_row_number' => $row->source_row_number,
                    'legacy_record_id' => $row->id,
                    'legacy_source_id' => $row->source_id,
                    'source_label' => $source->source_label,
                    'source_period_label' => $source->source_period_label,
                    'source_date_range' => $source->source_date_range,
                    'source_file_name' => $source->file_name,
                    'source_sha256' => $source->file_sha256,
                    'source_imported_at' => $row->imported_at,
                    'riwayat_verifikasi' => Crypt::encryptString('[]'),
                    'tindak_lanjut_data' => Crypt::encryptString('[]'),
                    'status_verifikasi' => null,
                    'persetujuan_penggunaan_data' => false,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            foreach (DB::table(self::MAIN)->where('record_type', 'website')->orderBy('id')->get() as $response) {
                $history = DB::table(self::HISTORIES)->where('tracer_study_id', $response->id)->orderBy('created_at')->get()
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'user_id' => $item->user_id,
                        'ringkasan_perubahan' => $item->ringkasan_perubahan,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ])->all();
                $followUps = DB::table(self::FOLLOW_UPS)->where('tracer_study_id', $response->id)->orderBy('created_at')->get()
                    ->map(fn ($item) => [
                        'id' => $item->id,
                        'admin_id' => $item->admin_id,
                        'jenis' => $item->jenis,
                        'catatan_internal' => $item->catatan_internal,
                        'pesan_alumni' => $item->pesan_alumni,
                        'diberitahukan_pada' => $item->diberitahukan_pada,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ])->all();
                DB::table(self::MAIN)->where('id', $response->id)->update([
                    'riwayat_verifikasi' => Crypt::encryptString(json_encode($history, JSON_THROW_ON_ERROR)),
                    'tindak_lanjut_data' => Crypt::encryptString(json_encode($followUps, JSON_THROW_ON_ERROR)),
                ]);
            }

            $expectedHistorical = DB::table(self::HISTORICAL_RECORDS)->count();
            $actualHistorical = DB::table(self::MAIN)->where('record_type', 'historical')->count();
            $expectedWebsite = DB::table(self::HISTORIES)->distinct()->count('tracer_study_id');
            if ($expectedHistorical !== $actualHistorical) {
                throw new RuntimeException("Historical validation failed: expected {$expectedHistorical}, found {$actualHistorical}.");
            }
            if ($expectedWebsite > 0 && DB::table(self::MAIN)->where('record_type', 'website')->count() < $expectedWebsite) {
                throw new RuntimeException('Website response validation failed.');
            }
            if (DB::table(self::MAIN)->where('record_type', 'historical')->whereNotNull('user_id')->exists()) {
                throw new RuntimeException('Historical rows unexpectedly reference user accounts.');
            }

            foreach (DB::table(self::MAIN)->where('record_type', 'historical')->get(['nama_lengkap_terenkripsi', 'data_sumber']) as $record) {
                if (! is_string(Crypt::decryptString($record->nama_lengkap_terenkripsi))) {
                    throw new RuntimeException('A historical name could not be decrypted after copy.');
                }
                $payload = json_decode(Crypt::decryptString($record->data_sumber), true, 512, JSON_THROW_ON_ERROR);
                if (! is_array($payload)) {
                    throw new RuntimeException('A historical source row could not be verified after copy.');
                }
            }

            $expectedHistoryCount = DB::table(self::HISTORIES)->count();
            $actualHistoryCount = 0;
            foreach (DB::table(self::MAIN)->where('record_type', 'website')->pluck('riwayat_verifikasi') as $encryptedHistory) {
                $actualHistoryCount += count(json_decode(Crypt::decryptString($encryptedHistory), true, 512, JSON_THROW_ON_ERROR));
            }
            if ($expectedHistoryCount !== $actualHistoryCount) {
                throw new RuntimeException("Verification history validation failed: expected {$expectedHistoryCount}, found {$actualHistoryCount}.");
            }

            $expectedFollowUpCount = DB::table(self::FOLLOW_UPS)->count();
            $actualFollowUpCount = 0;
            foreach (DB::table(self::MAIN)->where('record_type', 'website')->pluck('tindak_lanjut_data') as $encryptedFollowUps) {
                $actualFollowUpCount += count(json_decode(Crypt::decryptString($encryptedFollowUps), true, 512, JSON_THROW_ON_ERROR));
            }
            if ($expectedFollowUpCount !== $actualFollowUpCount) {
                throw new RuntimeException("Follow-up validation failed: expected {$expectedFollowUpCount}, found {$actualFollowUpCount}.");
            }

            $invalidPeriodReference = DB::table(self::MAIN . ' as response')
                ->leftJoin(self::MAIN . ' as period', 'period.id', '=', 'response.periode_id')
                ->where('response.record_type', 'website')
                ->where(function ($query): void {
                    $query->whereNull('response.periode_id')
                        ->orWhereNull('period.record_type')
                        ->orWhere('period.record_type', '<>', 'period');
                })->exists();
            if ($invalidPeriodReference) {
                throw new RuntimeException('A website response does not reference a consolidated period record.');
            }
        });

        // The remaining legacy tables are dropped only after the application has been
        // switched and production counts are verified in the next migration.
    }

    public function down(): void
    {
        foreach ([self::PERIODS, self::HISTORIES, self::FOLLOW_UPS, self::HISTORICAL_SOURCES, self::HISTORICAL_RECORDS] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Legacy table {$table} must be restored before rolling back Tracer Study consolidation.");
            }
        }

        $foreignKeys = collect(Schema::getForeignKeys(self::MAIN));
        foreach (['tracer_studies_period_record_fk', 'tracer_studies_period_creator_fk'] as $name) {
            if ($foreignKeys->contains(fn (array $foreignKey) => $foreignKey['name'] === $name)) {
                Schema::table(self::MAIN, fn (Blueprint $table) => $table->dropForeign($name));
            }
        }

        foreach (DB::table(self::MAIN)->where('record_type', 'period')->get(['id', 'legacy_periode_id']) as $period) {
            DB::table(self::MAIN)->where('record_type', 'website')->where('periode_id', $period->id)->update([
                'periode_id' => $period->legacy_periode_id,
            ]);
        }
        DB::table(self::MAIN)->whereIn('record_type', ['historical', 'period'])->delete();

        Schema::table(self::MAIN, function (Blueprint $table): void {
            $table->dropUnique('tracer_studies_source_row_unique');
            $table->dropUnique('tracer_studies_active_key_unique');
            $table->dropUnique('tracer_studies_type_period_year_unique');
            $table->dropIndex('tracer_studies_type_year_index');
            $table->dropIndex('tracer_studies_period_window_index');
            $table->dropColumn([
                'record_type', 'tahun_pencatatan', 'sumber_data', 'source_key', 'source_row_number',
                'legacy_record_id', 'legacy_source_id', 'legacy_periode_id', 'nama_lengkap_terenkripsi',
                'status_asli', 'instansi_asli', 'posisi_asli', 'penempatan_asli', 'data_sumber',
                'riwayat_verifikasi', 'tindak_lanjut_data', 'tahun', 'tanggal_mulai', 'tanggal_berakhir',
                'status', 'active_key', 'dibuat_oleh', 'nama', 'keterangan', 'source_label',
                'source_period_label', 'source_date_range', 'source_file_name', 'source_sha256', 'source_imported_at',
            ]);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->unsignedBigInteger('periode_id')->nullable(false)->change();
            $table->string('nama_lengkap', 255)->nullable(false)->change();
            $table->unsignedSmallInteger('tahun_lulus')->nullable(false)->change();
            $table->string('jurusan')->nullable(false)->change();
            $table->text('alamat')->nullable(false)->change();
            $table->string('status_kegiatan', 20)->nullable(false)->change();
            $table->string('pekerjaan')->nullable(false)->change();
            $table->string('nama_perusahaan')->nullable(false)->change();
            $table->unsignedSmallInteger('tahun_masuk_perusahaan')->nullable(false)->change();
            $table->text('alamat_tempat_kerja')->nullable(false)->change();
            $table->string('status_verifikasi', 30)->nullable(false)->change();
            $table->foreign('periode_id', 'tracer_studies_periode_id_foreign')
                ->references('id')->on(self::PERIODS)->restrictOnDelete();
        });
    }
};
