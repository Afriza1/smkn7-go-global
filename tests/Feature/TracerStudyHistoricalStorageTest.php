<?php

namespace Tests\Feature;

use App\Models\TracerStudy;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TracerStudyHistoricalStorageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('tracer_studies', function (Blueprint $table): void {
            $table->id();
            $table->string('record_type');
            $table->string('sumber_data')->nullable();
            $table->unsignedSmallInteger('tahun_pencatatan')->nullable();
            $table->string('source_key')->nullable();
            $table->unsignedInteger('source_row_number')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->text('nama_lengkap_terenkripsi')->nullable();
            $table->text('data_sumber')->nullable();
            $table->string('jurusan')->nullable();
            $table->text('status_asli')->nullable();
            $table->string('posisi_asli')->nullable();
            $table->string('penempatan_asli')->nullable();
            $table->timestamps();
            $table->unique(['source_key', 'source_row_number']);
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    public function test_historical_names_and_full_source_rows_are_encrypted_in_the_main_table(): void
    {
        $record = TracerStudy::create([
            'record_type' => 'historical',
            'sumber_data' => 'historical',
            'tahun_pencatatan' => 2026,
            'source_key' => 'tracer-study-2026',
            'source_row_number' => 7,
            'nama_lengkap' => 'Nama Contoh',
            'jurusan' => 'Teknik A',
            'status_asli' => 'Pelatihan / Calon bekerja',
            'posisi_asli' => 'Pelatihan',
            'penempatan_asli' => 'Calon bekerja',
            'data_sumber' => [
                ['column_number' => 2, 'header' => 'NAMA', 'value' => 'Nama Contoh'],
                ['column_number' => 7, 'header' => 'NO WHATSAPP', 'value' => 'PRIVATE-CONTACT-TEST'],
            ],
        ]);

        $stored = DB::table('tracer_studies')->where('id', $record->id)->first();

        $this->assertSame('Nama Contoh', $record->nama_lengkap);
        $this->assertSame('Pelatihan / Calon bekerja', $record->status_asli);
        $this->assertNull($stored->user_id ?? null);
        $this->assertNull($stored->nama_lengkap);
        $this->assertStringNotContainsString('Nama Contoh', $stored->nama_lengkap_terenkripsi);
        $this->assertStringNotContainsString('PRIVATE-CONTACT-TEST', $stored->data_sumber);
        $this->assertSame('PRIVATE-CONTACT-TEST', $record->data_sumber[1]['value']);
        $this->assertSame(1, DB::table('tracer_studies')->count());
    }

    public function test_repeated_names_at_different_source_rows_remain_separate(): void
    {
        foreach ([10 => 'Bekerja', 11 => 'Wiraswasta'] as $rowNumber => $status) {
            TracerStudy::create([
                'record_type' => 'historical',
                'sumber_data' => 'historical',
                'tahun_pencatatan' => 2025,
                'source_key' => 'tracer-study-2025',
                'source_row_number' => $rowNumber,
                'nama_lengkap' => 'Nama Berulang',
                'status_asli' => $status,
                'data_sumber' => [['header' => 'Nama Alumni', 'value' => 'Nama Berulang']],
            ]);
        }

        $this->assertSame(2, DB::table('tracer_studies')->where('record_type', 'historical')->count());
        $this->assertSame(['Bekerja', 'Wiraswasta'], DB::table('tracer_studies')->orderBy('source_row_number')->pluck('status_asli')->all());
    }
}
