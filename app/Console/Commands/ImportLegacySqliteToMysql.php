<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class ImportLegacySqliteToMysql extends Command
{
    protected $signature = 'data:import-legacy-sqlite
        {--execute : Import after explicit interactive confirmation}
        {--verify : Compare source and destination without writing}
        {--yes : Confirm an import in a non-interactive environment}';

    protected $description = 'Preview, import once, or verify legacy SQLite application data in MySQL';

    private const SOURCE = 'legacy_sqlite_import';

    /** Import allowlist in parent-before-child order. Column names are mapped by identity
     * only after source and target schemas have been checked in preflight(). */
    private const IMPORT_TABLES = [
        'users',
        'roles',
        'permissions',
        'lpks',
        'program_bantuans',
        'mitra_pembiayaans',
        'kontak_layanan_konselings',
        'parameter_simulasi_finansials',
        'alumni_globals',
        'komunitas_alumni_globals',
        'passkeys',
        'password_reset_tokens',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'konseling_digitals',
        'pendaftaran_karier_jepangs',
        'pengajuan_bantuans',
        'riwayat_simulasi_finansials',
        'pesan_konselings',
        'catatan_privat_konselings',
        'riwayat_status_konselings',
        'riwayat_pendaftaran_kariers',
        'dokumen_pengajuan_bantuans',
        'catatan_internal_bantuans',
        'riwayat_status_bantuans',
    ];

    private const NEVER_IMPORT = [
        'migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
        'jalur_keberangkatan', 'kategori_panduan_dokumen', 'panduan_dokumen_keberangkatan',
        'panduan_dokumen_jalur', 'checklist_dokumen_keberangkatan', 'modul_edukasi_materis',
        'tracer_studies',
    ];

    private const ALLOWED_DESTINATION_ONLY_COLUMNS = [
        'alumni_globals' => ['kontak_disetujui_pada', 'kontak_disetujui_oleh'],
    ];

    private const JSON_COLUMNS = [
        'passkeys' => ['credential'],
        'program_bantuans' => ['dokumen_dibutuhkan'],
        'mitra_pembiayaans' => ['tenor_pilihan'],
        'riwayat_simulasi_finansials' => ['snapshot_input', 'snapshot_parameter', 'snapshot_hasil'],
    ];

    private const DOCUMENT_COLUMNS = [
        'pendaftaran_karier_jepangs' => [
            'file_identitas', 'file_kk', 'file_rapor_ijazah', 'file_surat_ortu', 'file_tambahan',
        ],
        'dokumen_pengajuan_bantuans' => ['file_path'],
    ];

    public function handle(): int
    {
        try {
            $this->configureSource();
            $source = DB::connection(self::SOURCE);
            $target = DB::connection();
            $actualDatabase = $target->selectOne('SELECT DATABASE() AS database_name')->database_name ?? null;
            if ($target->getDriverName() !== 'mysql' || $actualDatabase !== 'smkn_go_global') {
                throw new RuntimeException('Koneksi aktif bukan MySQL database smkn_go_global. Proses dihentikan.');
            }

            if ($this->option('verify') && $this->option('execute')) {
                throw new RuntimeException('Pilih --verify atau --execute, jangan keduanya.');
            }

            $plan = $this->preflight($source, $target, (bool) $this->option('verify'));
            $this->renderPlan($plan);

            if ($this->option('verify')) {
                return $this->verify($source, $target, $plan);
            }

            if (! $this->option('execute')) {
                $this->info('Dry-run selesai. Tidak ada data yang ditulis.');

                return self::SUCCESS;
            }

            if ($this->option('yes')) {
                if ($this->input->isInteractive()) {
                    $this->warn('--yes hanya ditujukan untuk proses non-interaktif.');
                    return self::FAILURE;
                }
            } elseif (! $this->input->isInteractive() || ! $this->confirm(
                'Import seluruh tabel yang tercantum ke MySQL? Tindakan ini tidak dapat diulang setelah tabel terisi.',
                false
            )) {
                $this->warn('Import dibatalkan.');
                return self::FAILURE;
            }

            return $this->import($source, $target, $plan);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        } finally {
            DB::purge(self::SOURCE);
        }
    }

    private function configureSource(): void
    {
        $path = database_path('database.sqlite');
        if (! is_file($path)) {
            throw new RuntimeException('File sumber database/database.sqlite tidak ditemukan.');
        }

        $default = config('database.default');
        $targetConfig = config("database.connections.{$default}");
        if (($targetConfig['driver'] ?? null) !== 'mysql'
            || ($targetConfig['database'] ?? null) !== 'smkn_go_global') {
            throw new RuntimeException('Koneksi utama harus MySQL ke database smkn_go_global. Import dihentikan.');
        }

        config(['database.connections.'.self::SOURCE => [
            'driver' => 'sqlite',
            'database' => $path,
            'prefix' => '',
            'foreign_key_constraints' => true,
            'options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::SQLITE_ATTR_OPEN_FLAGS => \PDO::SQLITE_OPEN_READONLY,
            ],
        ]]);

        DB::purge(self::SOURCE);
        DB::connection(self::SOURCE)->statement('PRAGMA query_only = ON');
    }

    /** @return array<string, array{columns: array, count: int, keys: array}> */
    private function preflight(Connection $source, Connection $target, bool $verification): array
    {
        $sourceSchema = $source->getSchemaBuilder();
        $targetSchema = $target->getSchemaBuilder();
        $plan = [];

        foreach (self::IMPORT_TABLES as $table) {
            if (! $sourceSchema->hasTable($table) || ! $targetSchema->hasTable($table)) {
                throw new RuntimeException("Tabel import {$table} tidak tersedia di kedua database.");
            }

            $sourceColumns = $sourceSchema->getColumnListing($table);
            $targetColumns = $targetSchema->getColumnListing($table);
            $allowedExtra = self::ALLOWED_DESTINATION_ONLY_COLUMNS[$table] ?? [];
            $missing = array_values(array_diff($sourceColumns, $targetColumns));
            $unexpected = array_values(array_diff($targetColumns, $sourceColumns, $allowedExtra));
            if ($missing !== [] || $unexpected !== []) {
                throw new RuntimeException(sprintf(
                    'Struktur %s tidak cocok. Kolom tujuan yang hilang: [%s]; kolom tujuan tambahan tak terpetakan: [%s].',
                    $table,
                    implode(', ', $missing),
                    implode(', ', $unexpected)
                ));
            }

            $this->validateColumnCompatibility($source, $sourceSchema, $targetSchema, $table, $sourceColumns);

            foreach ($allowedExtra as $column) {
                $metadata = collect($targetSchema->getColumns($table))->firstWhere('name', $column);
                if ($metadata === null || ! $metadata['nullable']) {
                    throw new RuntimeException("Kolom tambahan {$table}.{$column} harus ada dan nullable.");
                }
            }

            $columns = $sourceColumns;
            foreach ($allowedExtra as $column) {
                if (in_array($column, $targetColumns, true)) {
                    $columns[] = $column;
                }
            }

            $count = $source->table($table)->count();
            $targetCount = $target->table($table)->count();
            if (! $verification && $targetCount !== 0) {
                throw new RuntimeException("Tabel tujuan {$table} berisi {$targetCount} baris; import ditolak agar data tidak tertimpa.");
            }

            $primary = collect($targetSchema->getIndexes($table))
                ->firstWhere('primary', true)['columns'] ?? [];
            if ($primary === []) {
                throw new RuntimeException("Primary key tabel {$table} tidak ditemukan.");
            }

            $engine = $target->selectOne(
                'SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
                [$table]
            )->engine ?? null;
            if (strtoupper((string) $engine) !== 'INNODB') {
                throw new RuntimeException("Tabel {$table} tidak menggunakan InnoDB; rollback transaksional tidak dapat dijamin.");
            }

            $plan[$table] = [
                'columns' => $columns,
                'source_columns' => $sourceColumns,
                'count' => $count,
                'keys' => $primary,
            ];
        }

        $foreignKeyViolations = $source->select('PRAGMA foreign_key_check');
        if ($foreignKeyViolations !== []) {
            throw new RuntimeException('SQLite sumber memiliki pelanggaran foreign key; import dihentikan.');
        }

        $this->validateJson($source);
        $this->validateNumericCapacity($source, $target);

        return $plan;
    }

    private function validateJson(Connection $source): void
    {
        foreach (self::JSON_COLUMNS as $table => $columns) {
            foreach ($columns as $column) {
                foreach ($source->table($table)->whereNotNull($column)->select($column)->cursor() as $row) {
                    json_decode($row->{$column});
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new RuntimeException("JSON tidak valid ditemukan pada {$table}.{$column}; import dihentikan.");
                    }
                }
            }
        }
    }

    private function validateNumericCapacity(Connection $source, Connection $target): void
    {
        foreach (self::IMPORT_TABLES as $table) {
            foreach ($target->getSchemaBuilder()->getColumns($table) as $column) {
                if (! preg_match('/^(?:decimal|numeric)\s*\(\s*(\d+)\s*,\s*(\d+)\s*\)/i', $column['type'], $matches)) {
                    continue;
                }
                $precision = (int) $matches[1];
                $scale = (int) $matches[2];
                foreach ($source->table($table)->whereNotNull($column['name'])->select($column['name'])->cursor() as $row) {
                    $value = ltrim((string) $row->{$column['name']}, '+-');
                    if (! preg_match('/^\d+(?:\.\d+)?$/', $value)) {
                        throw new RuntimeException("Nilai numerik {$table}.{$column['name']} tidak dapat dipetakan secara aman.");
                    }
                    [$integer, $fraction] = array_pad(explode('.', $value, 2), 2, '');
                    if (strlen(ltrim($integer, '0')) > $precision - $scale
                        || strlen(rtrim($fraction, '0')) > $scale) {
                        throw new RuntimeException("Presisi nilai {$table}.{$column['name']} melebihi kapasitas MySQL.");
                    }
                }
            }
        }
    }

    private function validateColumnCompatibility(
        Connection $source,
        \Illuminate\Database\Schema\Builder $sourceSchema,
        \Illuminate\Database\Schema\Builder $targetSchema,
        string $table,
        array $sourceColumns
    ): void {
        $sourceMetadata = collect($sourceSchema->getColumns($table))->keyBy('name');
        $targetMetadata = collect($targetSchema->getColumns($table))->keyBy('name');

        foreach ($sourceColumns as $name) {
            $targetColumn = $targetMetadata->get($name);
            if (! $targetColumn['nullable'] && $source->table($table)->whereNull($name)->exists()) {
                throw new RuntimeException("Kolom {$table}.{$name} memiliki NULL yang tidak diterima struktur MySQL.");
            }

            if (strtolower((string) $targetColumn['type_name']) === 'date') {
                foreach ($source->table($table)->whereNotNull($name)->select($name)->cursor() as $row) {
                    $this->normalizeDateValue($row->{$name}, $table, $name);
                }
            }

            if (strtolower((string) $targetColumn['type_name']) === 'time') {
                foreach ($source->table($table)->whereNotNull($name)->select($name)->cursor() as $row) {
                    $this->normalizeTimeValue($row->{$name}, $table, $name);
                }
            }

            if (preg_match('/^(?:var)?char\s*\(\s*(\d+)\s*\)/i', $targetColumn['type'], $matches)) {
                $quoted = $source->getQueryGrammar()->wrap($name);
                $maxLength = $source->table($table)->max($source->raw("length({$quoted})"));
                if ((int) $maxLength > (int) $matches[1]) {
                    throw new RuntimeException("Panjang data {$table}.{$name} melebihi batas kolom MySQL.");
                }
            }

            if (preg_match('/^enum\s*\((.*)\)$/i', $targetColumn['type'], $matches)) {
                preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $matches[1], $enumMatches);
                $allowedValues = array_map(fn ($value) => stripcslashes($value), $enumMatches[1]);
                foreach ($source->table($table)->whereNotNull($name)->distinct()->pluck($name) as $value) {
                    if (! in_array((string) $value, $allowedValues, true)) {
                        throw new RuntimeException("Nilai {$table}.{$name} tidak termasuk pilihan ENUM MySQL.");
                    }
                }
            }

            // Keep the explicit metadata lookup here so a missing source definition
            // cannot silently be treated as an identity mapping.
            if (! $sourceMetadata->has($name)) {
                throw new RuntimeException("Kolom sumber {$table}.{$name} tidak dapat diperiksa.");
            }
        }
    }

    private function renderPlan(array $plan): void
    {
        $this->table(['Tabel', 'Baris SQLite', 'Kolom dipetakan'], array_map(
            fn ($table, $item) => [$table, $item['count'], count($item['columns'])],
            array_keys($plan),
            array_values($plan)
        ));
        $this->line('Dikecualikan: '.implode(', ', self::NEVER_IMPORT).'.');
        $source = DB::connection(self::SOURCE);
        $sourceSchema = $source->getSchemaBuilder();
        $nonEmptyExcluded = [];
        foreach (self::NEVER_IMPORT as $table) {
            if ($sourceSchema->hasTable($table)) {
                $count = $source->table($table)->count();
                if ($count > 0) {
                    $nonEmptyExcluded[] = "{$table}={$count}";
                }
            }
        }
        if ($nonEmptyExcluded !== []) {
            $this->warn('Data sumber yang sengaja tidak dipindahkan: '.implode(', ', $nonEmptyExcluded).'.');
        }
        $target = DB::connection();
        $targetSchema = $target->getSchemaBuilder();
        $preserved = [];
        foreach (self::NEVER_IMPORT as $table) {
            if ($targetSchema->hasTable($table)) {
                $preserved[] = $table.'='.$target->table($table)->count();
            }
        }
        $this->line('Tabel tujuan yang dipertahankan (jumlah baris): '.implode(', ', $preserved).'.');
        $this->renderDocumentReport(DB::connection(self::SOURCE));
    }

    private function renderDocumentReport(Connection $source): void
    {
        $root = config('filesystems.disks.local.root', storage_path('app/private'));
        $this->line('Referensi dokumen:');
        $missingRows = [];
        foreach (self::DOCUMENT_COLUMNS as $table => $columns) {
            foreach ($columns as $column) {
                $references = $found = 0;
                foreach ($source->table($table)->whereNotNull($column)->where($column, '<>', '')->select(['id', $column])->cursor() as $row) {
                    $references++;
                    $candidate = $this->safeStoragePath($root, (string) $row->{$column});
                    if ($candidate !== null && is_file($candidate)) {
                        $found++;
                    } else {
                        $missingRows[] = [$table, (string) $row->id, $column, json_encode((string) $row->{$column}, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE)];
                    }
                }
                $this->line("- {$table}.{$column}: {$references} referensi; {$found} tersedia; ".($references - $found).' tidak ditemukan');
            }
        }
        if ($missingRows !== []) {
            $this->line('Laporan terpisah — referensi dokumen yang tidak ditemukan (tabel, ID, kolom, path):');
            $this->table(['Tabel', 'ID', 'Kolom', 'Path tersimpan'], $missingRows);
        } else {
            $this->line('Laporan referensi dokumen tidak ditemukan: tidak ada.');
        }
    }

    private function safeStoragePath(string $root, string $relative): ?string
    {
        $relative = str_replace('\\', '/', $relative);
        if ($relative === '' || str_starts_with($relative, '/') || preg_match('/^[A-Za-z]:/', $relative)) {
            return null;
        }
        $segments = explode('/', $relative);
        if (in_array('..', $segments, true)) {
            return null;
        }
        $candidate = rtrim($root, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $segments);
        $realpath = realpath($candidate);
        $realRoot = realpath($root);
        if ($realpath === false || $realRoot === false) {
            return null;
        }
        $prefix = rtrim($realRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        return str_starts_with($realpath, $prefix) ? $realpath : null;
    }

    private function import(Connection $source, Connection $target, array $plan): int
    {
        $activeTable = 'preflight';
        try {
            $target->beginTransaction();
            foreach (array_keys($plan) as $table) {
                if ($target->table($table)->exists()) {
                    throw new RuntimeException("Tabel tujuan {$table} terisi setelah preflight; import dihentikan.");
                }
            }
            foreach ($plan as $table => $item) {
                $activeTable = $table;
                $buffer = [];
                $dateColumns = $this->dateColumnNames($target, $table);
                $timeColumns = $this->timeColumnNames($target, $table);
                foreach ($source->table($table)->select($item['source_columns'])->cursor() as $row) {
                    $record = (array) $row;
                    foreach ($dateColumns as $column) {
                        if (array_key_exists($column, $record)) {
                            $record[$column] = $this->normalizeDateValue($record[$column], $table, $column);
                        }
                    }
                    foreach ($timeColumns as $column) {
                        if (array_key_exists($column, $record)) {
                            $record[$column] = $this->normalizeTimeValue($record[$column], $table, $column);
                        }
                    }
                    foreach (self::ALLOWED_DESTINATION_ONLY_COLUMNS[$table] ?? [] as $column) {
                        if (in_array($column, $item['columns'], true)) {
                            $record[$column] = null;
                        }
                    }
                    foreach (self::JSON_COLUMNS[$table] ?? [] as $column) {
                        if (array_key_exists($column, $record) && $record[$column] !== null) {
                            $record[$column] = json_encode(json_decode($record[$column], false, 512, JSON_THROW_ON_ERROR), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
                        }
                    }
                    $buffer[] = array_intersect_key($record, array_flip($item['columns']));
                    if (count($buffer) >= 500) {
                        $target->table($table)->insert($buffer);
                        $buffer = [];
                    }
                }
                if ($buffer !== []) {
                    $target->table($table)->insert($buffer);
                }
            }

            $activeTable = 'post-import verification';
            $this->verifyContents($source, $target, $plan);
            $target->commit();
            $this->info('Import berhasil dan verifikasi jumlah, primary key, konten, serta foreign key lulus.');
            return self::SUCCESS;
        } catch (Throwable $exception) {
            if ($target->transactionLevel() > 0) {
                $target->rollBack();
            }
            $this->error("Import gagal pada {$activeTable}; transaksi MySQL di-rollback.");
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }

    private function verify(Connection $source, Connection $target, array $plan): int
    {
        try {
            $this->verifyContents($source, $target, $plan);
            $this->info('Verifikasi lulus: jumlah baris, primary key, nilai kolom, JSON, numerik, dan foreign key cocok.');
            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }

    private function verifyContents(Connection $source, Connection $target, array $plan): void
    {
        foreach ($plan as $table => $item) {
            $sourceCount = $source->table($table)->count();
            $targetCount = $target->table($table)->count();
            if ($sourceCount !== $targetCount) {
                throw new RuntimeException("Verifikasi {$table} gagal: jumlah baris sumber dan tujuan berbeda.");
            }
            $sourceQuery = $source->table($table)->select($item['source_columns']);
            $targetQuery = $target->table($table)->select($item['columns']);
            foreach ($item['keys'] as $key) {
                $sourceQuery->orderBy($key);
                $targetQuery->orderBy($key);
            }
            // cursor() returns a LazyCollection (IteratorAggregate), not an
            // Iterator itself. Wrap its underlying Traversable so lockstep
            // comparison can safely use PHP's Iterator API.
            $sourceRows = new \IteratorIterator($sourceQuery->cursor()->getIterator());
            $targetRows = new \IteratorIterator($targetQuery->cursor()->getIterator());
            $sourceRows->rewind();
            $targetRows->rewind();
            while ($sourceRows->valid() && $targetRows->valid()) {
                $sourceRow = (array) $sourceRows->current();
                foreach (self::ALLOWED_DESTINATION_ONLY_COLUMNS[$table] ?? [] as $column) {
                    if (in_array($column, $item['columns'], true)) {
                        $sourceRow[$column] = null;
                    }
                }
                $targetRow = (array) $targetRows->current();
                if ($this->canonicalRow($sourceRow, $table, $target) !== $this->canonicalRow($targetRow, $table, $target)) {
                    $column = $this->firstMismatchColumn($sourceRow, $targetRow, $table, $target);
                    $rowId = $this->safeRowIdentifier($sourceRow, $item['keys']);
                    throw new RuntimeException("Verifikasi nilai atau ID gagal pada tabel {$table}, ID {$rowId}, kolom {$column}; nilai tidak ditampilkan.");
                }
                $sourceRows->next();
                $targetRows->next();
            }
            if ($sourceRows->valid() || $targetRows->valid()) {
                throw new RuntimeException("Verifikasi ID gagal pada tabel {$table}.");
            }
        }

        foreach (self::IMPORT_TABLES as $table) {
            foreach ($target->getSchemaBuilder()->getForeignKeys($table) as $foreignKey) {
                $columns = $foreignKey['columns'];
                $query = $target->table($table.' as child');
                foreach ($columns as $column) {
                    $query->whereNotNull('child.'.$column);
                }
                $query->leftJoin($foreignKey['foreign_table'].' as parent', function ($join) use ($foreignKey, $columns) {
                    foreach ($columns as $index => $column) {
                        $join->on('child.'.$column, '=', 'parent.'.$foreignKey['foreign_columns'][$index]);
                    }
                });
                if ($query->whereNull('parent.'.$foreignKey['foreign_columns'][0])->exists()) {
                    throw new RuntimeException("Foreign key tidak valid terdeteksi pada {$table}; nilai baris tidak ditampilkan.");
                }
            }
        }

        $sourceUsers = $source->table('users')
            ->select(['role', 'status'])
            ->selectRaw('COUNT(*) AS aggregate_count')
            ->groupBy('role', 'status')
            ->orderBy('role')
            ->orderBy('status')
            ->get()
            ->map(fn ($row) => [(string) $row->role, (string) $row->status, (int) $row->aggregate_count])
            ->all();
        $targetUsers = $target->table('users')
            ->select(['role', 'status'])
            ->selectRaw('COUNT(*) AS aggregate_count')
            ->groupBy('role', 'status')
            ->orderBy('role')
            ->orderBy('status')
            ->get()
            ->map(fn ($row) => [(string) $row->role, (string) $row->status, (int) $row->aggregate_count])
            ->all();
        if ($sourceUsers !== $targetUsers) {
            throw new RuntimeException('Verifikasi distribusi akun berdasarkan role dan status gagal.');
        }

        $this->validateJson($target);
        $this->validateNumericCapacity($source, $target);
    }

    private function canonicalRow(array $row, string $table, Connection $target): string
    {
        ksort($row);
        $metadata = $this->columnMetadata($target, $table);
        foreach ($row as $column => $value) {
            $row[$column] = $this->canonicalValue($value, $table, $column, $metadata);
        }
        return json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    private function firstMismatchColumn(array $source, array $targetRow, string $table, Connection $target): string
    {
        $metadata = $this->columnMetadata($target, $table);
        $columns = array_unique(array_merge(array_keys($source), array_keys($targetRow)));
        foreach ($columns as $column) {
            if (! array_key_exists($column, $source) || ! array_key_exists($column, $targetRow)
                || $this->canonicalValue($source[$column] ?? null, $table, $column, $metadata)
                    !== $this->canonicalValue($targetRow[$column] ?? null, $table, $column, $metadata)) {
                return $column;
            }
        }
        return 'unknown';
    }

    private function canonicalValue(mixed $value, string $table, string $column, array $metadata): mixed
    {
        if ($value === null) {
            return null;
        }

        $typeName = strtolower((string) ($metadata[$column]['type_name'] ?? ''));
        if ($typeName === 'date') {
            return $this->normalizeDateValue($value, $table, $column);
        }
        if ($typeName === 'time') {
            return $this->normalizeTimeValue($value, $table, $column);
        }

        if (is_string($value) && in_array($column, array_merge(...array_values(self::JSON_COLUMNS)), true)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->canonicalizeJson($decoded);
            }
        }

        if (preg_match('/^(?:decimal|numeric)\s*\(/i', (string) ($metadata[$column]['type'] ?? '')) && is_numeric($value)) {
            return $this->canonicalDecimal((string) $value);
        }

        return $value;
    }

    private function columnMetadata(Connection $target, string $table): array
    {
        return collect($target->getSchemaBuilder()->getColumns($table))
            ->keyBy('name')
            ->all();
    }

    /** Return target columns whose MySQL type is DATE exactly; DATETIME/TIMESTAMP stay untouched. */
    private function dateColumnNames(Connection $target, string $table): array
    {
        return collect($target->getSchemaBuilder()->getColumns($table))
            ->filter(fn ($column) => strtolower((string) $column['type_name']) === 'date')
            ->pluck('name')
            ->all();
    }

    /** Return target columns whose MySQL type is TIME exactly; datetime types are excluded. */
    private function timeColumnNames(Connection $target, string $table): array
    {
        return collect($target->getSchemaBuilder()->getColumns($table))
            ->filter(fn ($column) => strtolower((string) $column['type_name']) === 'time')
            ->pluck('name')
            ->all();
    }

    private function normalizeDateValue(mixed $value, string $table, string $column): ?string
    {
        if ($value === null) {
            return null;
        }
        if (! is_string($value)
            || ! preg_match('/^(\d{4})-(\d{2})-(\d{2})(?: (\d{2}):(\d{2}):(\d{2}))?$/D', $value, $parts)) {
            throw new RuntimeException("Format tanggal tidak valid pada {$table}.{$column}; nilai tidak ditampilkan.");
        }

        if (! checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])
            || (isset($parts[4]) && ((int) $parts[4] > 23 || (int) $parts[5] > 59 || (int) $parts[6] > 59))) {
            throw new RuntimeException("Tanggal tidak valid pada {$table}.{$column}; nilai tidak ditampilkan.");
        }

        return substr($value, 0, 10);
    }

    private function normalizeTimeValue(mixed $value, string $table, string $column): ?string
    {
        if ($value === null) {
            return null;
        }
        if (! is_string($value)
            || ! preg_match('/^(\d{2}):(\d{2})(?::(\d{2}))?$/D', $value, $parts)) {
            throw new RuntimeException("Format waktu tidak valid pada {$table}.{$column}; nilai tidak ditampilkan.");
        }

        $hour = (int) $parts[1];
        $minute = (int) $parts[2];
        $second = isset($parts[3]) && $parts[3] !== '' ? (int) $parts[3] : 0;
        if ($hour > 23 || $minute > 59 || $second > 59) {
            throw new RuntimeException("Waktu tidak valid pada {$table}.{$column}; nilai tidak ditampilkan.");
        }

        return sprintf('%02d:%02d:%02d', $hour, $minute, $second);
    }

    private function safeRowIdentifier(array $row, array $keys): string
    {
        if (array_key_exists('id', $row) && is_scalar($row['id']) && ctype_digit((string) $row['id'])) {
            return (string) $row['id'];
        }

        $parts = [];
        foreach ($keys as $key) {
            $value = $row[$key] ?? null;
            $parts[] = $key.'='.(is_scalar($value) && (is_int($value) || is_float($value) || ctype_digit((string) $value))
                ? (string) $value
                : '[disamarkan]');
        }
        return implode(',', $parts) ?: '[tidak tersedia]';
    }

    private function canonicalizeJson(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }
        if (! array_is_list($value)) {
            ksort($value);
        }
        foreach ($value as $key => $nested) {
            $value[$key] = $this->canonicalizeJson($nested);
        }
        return $value;
    }

    private function canonicalDecimal(string $value): string
    {
        $negative = str_starts_with($value, '-');
        $value = ltrim($value, '+-');
        [$integer, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        $integer = ltrim($integer, '0') ?: '0';
        $fraction = rtrim($fraction, '0');
        return ($negative && ($integer !== '0' || $fraction !== '') ? '-' : '').$integer.($fraction === '' ? '' : '.'.$fraction);
    }
}
