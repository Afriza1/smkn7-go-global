<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $periodTable = 'tracer_study_periodes';
        $periodTableExists = Schema::hasTable($periodTable);

        if ($periodTableExists) {
            $this->assertExistingPeriodsTableMatchesMigration();
        }

        foreach ([
            'tracer_studies',
            'tracer_study_histories',
            'tracer_study_follow_ups',
        ] as $tableName) {
            if (Schema::hasTable($tableName)) {
                throw new RuntimeException(
                    "Table {$tableName} already exists although this migration is pending. Inspect its structure before continuing."
                );
            }
        }

        if (! $periodTableExists) {
            Schema::create($periodTable, function (Blueprint $table) {
                $table->id();
                $table->unsignedSmallInteger('tahun');
                $table->date('tanggal_mulai');
                $table->date('tanggal_berakhir');
                $table->string('status', 20)->default('draft'); // draft, open, closed
                $table->string('active_key', 20)->nullable();
                $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->unique('tahun', 'tracer_period_year_unique');
                $table->unique('active_key', 'tracer_period_active_key_unique');
                $table->index(
                    ['status', 'tanggal_mulai', 'tanggal_berakhir'],
                    'tracer_period_status_dates_idx'
                );
            });
        }

        $this->ensurePeriodsTableIndexes();

        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('tracer_study_periodes')->restrictOnDelete();
            $table->string('nama_lengkap');
            $table->unsignedSmallInteger('tahun_lulus');
            $table->string('jurusan');
            $table->text('alamat');
            $table->string('status_kegiatan', 20); // bekerja, magang
            $table->string('pekerjaan');
            $table->string('nama_perusahaan');
            $table->unsignedSmallInteger('tahun_masuk_perusahaan');
            $table->text('alamat_tempat_kerja');
            $table->string('status_verifikasi', 30)->default('unverified');
            $table->boolean('persetujuan_penggunaan_data')->default(false);
            $table->timestamp('disetujui_pada')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'periode_id']);
            $table->index(['periode_id', 'status_verifikasi']);
            $table->index(['periode_id', 'tahun_lulus', 'jurusan']);
            $table->index(['periode_id', 'status_kegiatan']);
            $table->index(['periode_id', 'nama_perusahaan']);
        });

        Schema::create('tracer_study_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_study_id')->constrained('tracer_studies')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('ringkasan_perubahan');
            $table->timestamps();
            $table->index(['tracer_study_id', 'created_at']);
        });

        Schema::create('tracer_study_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_study_id')->constrained('tracer_studies')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis', 30); // request_update, internal_note
            $table->text('catatan_internal')->nullable();
            $table->text('pesan_alumni')->nullable();
            $table->timestamp('diberitahukan_pada')->nullable();
            $table->timestamps();
            $table->index(['tracer_study_id', 'jenis', 'created_at']);
        });
    }

    private function assertExistingPeriodsTableMatchesMigration(): void
    {
        $table = 'tracer_study_periodes';
        $expectedColumns = [
            'id' => ['type' => 'bigint unsigned', 'nullable' => false, 'default' => null, 'auto_increment' => true],
            'tahun' => ['type' => 'smallint unsigned', 'nullable' => false, 'default' => null, 'auto_increment' => false],
            'tanggal_mulai' => ['type' => 'date', 'nullable' => false, 'default' => null, 'auto_increment' => false],
            'tanggal_berakhir' => ['type' => 'date', 'nullable' => false, 'default' => null, 'auto_increment' => false],
            'status' => ['type' => 'varchar(20)', 'nullable' => false, 'default' => 'draft', 'auto_increment' => false],
            'active_key' => ['type' => 'varchar(20)', 'nullable' => true, 'default' => null, 'auto_increment' => false],
            'dibuat_oleh' => ['type' => 'bigint unsigned', 'nullable' => true, 'default' => null, 'auto_increment' => false],
            'created_at' => ['type' => 'timestamp', 'nullable' => true, 'default' => null, 'auto_increment' => false],
            'updated_at' => ['type' => 'timestamp', 'nullable' => true, 'default' => null, 'auto_increment' => false],
        ];

        $actualColumns = collect(Schema::getColumns($table))->keyBy('name');
        $missingColumns = array_diff(array_keys($expectedColumns), $actualColumns->keys()->all());
        $unexpectedColumns = array_diff($actualColumns->keys()->all(), array_keys($expectedColumns));

        if ($missingColumns !== [] || $unexpectedColumns !== []) {
            throw new RuntimeException(sprintf(
                'Existing table %s has a different column set. Missing: [%s]; unexpected: [%s]. Inspect and resolve before migration.',
                $table,
                implode(', ', $missingColumns),
                implode(', ', $unexpectedColumns)
            ));
        }

        foreach ($expectedColumns as $name => $expected) {
            $actual = $actualColumns->get($name);

            foreach ($expected as $attribute => $expectedValue) {
                $actualValue = $actual[$attribute];
                $matches = $attribute === 'type'
                    ? strtolower($actualValue) === strtolower($expectedValue)
                    : $actualValue === $expectedValue;

                if (! $matches) {
                    throw new RuntimeException(
                        "Existing table {$table}.{$name} has an unexpected {$attribute}; inspect and resolve before migration."
                    );
                }
            }
        }

        if (! Schema::hasIndex($table, ['id'], 'primary')) {
            throw new RuntimeException("Existing table {$table} is missing its primary key on id.");
        }

        $foreignKeys = Schema::getForeignKeys($table);
        $expectedForeignKeyExists = count($foreignKeys) === 1
            && $foreignKeys[0]['columns'] === ['dibuat_oleh']
            && $foreignKeys[0]['foreign_table'] === 'users'
            && $foreignKeys[0]['foreign_columns'] === ['id']
            && strtolower($foreignKeys[0]['on_update']) === 'no action'
            && strtolower($foreignKeys[0]['on_delete']) === 'set null';

        if (! $expectedForeignKeyExists) {
            throw new RuntimeException(
                "Existing table {$table} has a missing or different foreign key for dibuat_oleh. Inspect and resolve before migration."
            );
        }
    }

    private function ensurePeriodsTableIndexes(): void
    {
        $tableName = 'tracer_study_periodes';

        if (! Schema::hasIndex($tableName, ['tahun'], 'unique')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unique('tahun', 'tracer_period_year_unique');
            });
        }

        if (! Schema::hasIndex($tableName, ['active_key'], 'unique')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unique('active_key', 'tracer_period_active_key_unique');
            });
        }

        if (! $this->hasNonUniqueBtreeIndex(
            $tableName,
            ['status', 'tanggal_mulai', 'tanggal_berakhir']
        )) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->index(
                    ['status', 'tanggal_mulai', 'tanggal_berakhir'],
                    'tracer_period_status_dates_idx'
                );
            });
        }
    }

    private function hasNonUniqueBtreeIndex(string $tableName, array $columns): bool
    {
        if (! Schema::hasIndex($tableName, $columns, 'btree')) {
            return false;
        }

        foreach (Schema::getIndexes($tableName) as $index) {
            if ($index['columns'] === $columns
                && $index['type'] === 'btree'
                && ! $index['unique']
                && ! $index['primary']) {
                return true;
            }
        }

        return false;
    }

    public function down(): void
    {
        Schema::dropIfExists('tracer_study_follow_ups');
        Schema::dropIfExists('tracer_study_histories');
        Schema::dropIfExists('tracer_studies');
        Schema::dropIfExists('tracer_study_periodes');
    }
};
