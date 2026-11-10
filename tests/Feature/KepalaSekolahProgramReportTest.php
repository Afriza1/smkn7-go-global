<?php

namespace Tests\Feature;

use App\Http\Controllers\KepalaSekolahProgramReportController;
use App\Services\ProgramReportExcelExport;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KepalaSekolahProgramReportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('lpks', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_lpk');
            $table->string('wilayah_penempatan')->nullable();
            $table->string('bidang_program')->nullable();
            $table->boolean('is_demo')->default(false);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('pendaftaran_karier_jepangs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('lpk_id');
            $table->unsignedBigInteger('user_id');
            $table->string('jenis_peserta')->default('alumni');
            $table->string('status');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    public function test_progress_and_departure_previews_use_real_non_demo_applications(): void
    {
        $actualPartner = $this->createPartner('LPK Aktual', false, true);
        $demoPartner = $this->createPartner('LPK Demo', true, true);
        $this->insertApplication($actualPartner, 'menunggu_admin', '2025-03-10 09:00:00');
        $this->insertApplication($actualPartner, 'diberangkatkan', '2025-05-12 10:00:00');
        $this->insertApplication($demoPartner, 'diberangkatkan', '2025-06-01 10:00:00');

        $controller = app(KepalaSekolahProgramReportController::class);
        $progress = $controller->show('perkembangan')->getData()['reportData'];
        $departure = $controller->show('keberangkatan-penempatan')->getData()['reportData'];

        $this->assertSame(2, $progress['summary'][0][1]);
        $this->assertSame(1, $progress['summary'][1][1]);
        $this->assertSame(1, $progress['summary'][2][1]);
        $this->assertSame(['2025', 2], $progress['sections'][1]['rows'][0]);
        $this->assertSame(1, $departure['summary'][0][1]);
        $this->assertSame('Jepang', $departure['summary'][1][1]);
        $this->assertSame('LPK Aktual', $departure['sections'][1]['rows'][0][1]);
        $this->assertStringContainsString('tidak menyimpan tanggal keberangkatan', $departure['notice']);
    }

    public function test_partnership_preview_lists_only_confirmed_school_partners_and_does_not_claim_mou_validity(): void
    {
        $this->createPartner('LPK Aktif', false, true);
        $this->createPartner('LPK Nonaktif', false, false);
        $this->createPartner('LPK Demo', true, true);

        $report = app(KepalaSekolahProgramReportController::class)
            ->show('kemitraan-mou')
            ->getData()['reportData'];

        $this->assertSame(['Mitra resmi sekolah', 2], $report['summary'][0]);
        $this->assertSame([
            ['KANZO Indonesia', 'Mitra resmi sekolah'],
            ['Gakkou Nusantara', 'Mitra resmi sekolah'],
        ], $report['sections'][0]['rows']);
        $this->assertStringContainsString('bukan hasil query database LPK', $report['notice']);
        $this->assertStringContainsString('Informasi MoU, masa berlaku, kuota, dan perusahaan penerima belum tersedia', $report['notice']);
    }

    public function test_excel_export_creates_a_workbook_with_report_values(): void
    {
        $file = app(ProgramReportExcelExport::class)->create('Laporan Peserta', [[
            'title' => 'Ringkasan',
            'headers' => ['Indikator', 'Jumlah'],
            'rows' => [['Total peserta', 12]],
        ]]);

        $archive = new \ZipArchive;
        try {
            $this->assertTrue($archive->open($file) === true);
            $worksheet = $archive->getFromName('xl/worksheets/sheet1.xml');
            $this->assertIsString($worksheet);
            $this->assertStringContainsString('Laporan Peserta', $worksheet);
            $this->assertStringContainsString('Total peserta', $worksheet);
            $this->assertStringContainsString('12', $worksheet);
        } finally {
            $archive->close();
            @unlink($file);
        }
    }

    private function createPartner(string $name, bool $demo, bool $active): int
    {
        return (int) DB::table('lpks')->insertGetId([
            'nama_lpk' => $name,
            'wilayah_penempatan' => 'Aichi',
            'bidang_program' => 'Manufaktur',
            'is_demo' => $demo,
            'is_aktif' => $active,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertApplication(int $partnerId, string $status, string $createdAt, int $userId = 1, string $participantType = 'alumni'): void
    {
        DB::table('pendaftaran_karier_jepangs')->insert([
            'lpk_id' => $partnerId,
            'user_id' => $userId,
            'jenis_peserta' => $participantType,
            'status' => $status,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
