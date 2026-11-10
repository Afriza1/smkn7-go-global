<?php

namespace Tests\Feature;

use App\Http\Controllers\KepalaSekolahTracerStudyController;
use App\Http\Controllers\GuruBkTracerStudyController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class KepalaSekolahTracerStudyPrivacyTest extends TestCase
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
            $table->unsignedSmallInteger('tahun_pencatatan')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->string('nama')->nullable();
            $table->string('status')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->string('active_key')->nullable();
            $table->string('sumber_data')->nullable();
            $table->unsignedBigInteger('periode_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status_kegiatan')->nullable();
            $table->string('status_asli')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->string('posisi_asli')->nullable();
            $table->string('penempatan_asli')->nullable();
            $table->string('alamat_tempat_kerja')->nullable();
            $table->string('instansi_asli')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->string('nama_lengkap_terenkripsi')->nullable();
            $table->text('data_sumber')->nullable();
            $table->unsignedInteger('source_row_number')->nullable();
        });
    }

    protected function tearDown(): void
    {
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    public function test_summary_counts_alumni_by_recording_year_and_excludes_internal_periods(): void
    {
        $this->period(2025);
        for ($i = 1; $i <= 84; $i++) $this->historical(2025, 'TKJ', 'Bekerja');
        for ($i = 1; $i <= 45; $i++) $this->historical(2026, 'TSM', null);
        $this->website(2026, 'Teknik Sepeda Motor', 'bekerja');

        $data = $this->data();

        $this->assertSame(130, $data['totalRecords']);
        $this->assertSame([84, 46], $data['yearTotals']->sortBy('tahun')->pluck('total_records')->map(fn($count) => (int) $count)->all());
        $this->assertSame([84, 46], $data['progressGroups']->sortBy('label')->pluck('total_records')->map(fn($count) => (int) $count)->all());
        $this->assertSame(130, DB::table('tracer_studies')->whereIn('record_type', ['historical', 'website'])->count());
        $this->assertSame(1, DB::table('tracer_studies')->where('record_type', 'period')->count());

        Auth::setUser(new User(['name' => 'Kepala Sekolah', 'email' => 'kepala@example.test', 'role' => 'kepala_sekolah']));
        $view = app(KepalaSekolahTracerStudyController::class)->index(Request::create('/dashboard/kepala-sekolah/tracer-study'));
        $view->with('errors', new ViewErrorBag);
        $html = view($view->name(), $view->getData())->render();
        $this->assertStringContainsString('>84</text>', $html);
        $this->assertStringContainsString('>46</text>', $html);
    }

    public function test_guru_bk_statistics_route_reuses_identical_read_only_statistics_and_is_role_protected(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            $this->historical(2025, 'TKJ', 'Bekerja', 'Perusahaan Uji', 'Teknisi', 'Jember', 'Alumni ' . $i);
        }
        $this->historical(2026, 'TSM', 'Wiraswasta', 'Instansi Uji', 'Mandiri', 'Jember');
        $guru = new User(['name' => 'Guru BK', 'email' => 'guru@example.test', 'role' => 'guru', 'status' => 'aktif']);
        $response = $this->actingAs($guru)->get(route('dashboard.guru.tracer-study.statistics'))
            ->assertOk()->assertSee('Statistik Tracer Study')->assertSee('Distribusi Program Keahlian')
            ->assertSee('Distribusi Perusahaan / Instansi')->assertSee('Bidang Pekerjaan atau Posisi')
            ->assertSee('overflow-y-auto')->assertSee('Statistik Tracer Study')
            ->assertSee('Data Tracer Study 2025')->assertSee('Data Tracer Study 2026')->assertSee('Total 12')
            ->assertSee('Verifikasi Orang Tua')->assertSee('Kelola Tracer Study')
            ->assertSee('action="' . route('dashboard.guru.tracer-study.statistics') . '"', false);
        $html = $response->getContent();
        $sections = [
            'Statistik Tracer Study',
            'Ringkasan Data Alumni',
            'Perkembangan Data Tracer Study',
            'Status Alumni',
            'Distribusi Program Keahlian',
            'Distribusi Perusahaan / Instansi',
            'Bidang Pekerjaan atau Posisi',
            'Lokasi Penempatan',
            'Data Alumni Tracer Study',
        ];
        $previousPosition = -1;
        foreach ($sections as $section) {
            $position = strpos($html, $section);
            $this->assertNotFalse($position, "Bagian {$section} harus tampil pada halaman statistik Guru BK.");
            $this->assertGreaterThan($previousPosition, $position, "Bagian {$section} harus mengikuti urutan statistik yang ditentukan.");
            $previousPosition = $position;
        }
        $this->get(route('dashboard.guru.tracer-study.statistics', ['tahun_pencatatan' => '2025', 'per_page' => 10]))
            ->assertOk()->assertSee('dari 11 data')->assertSee('/dashboard/guru/statistik-tracer-study?tahun_pencatatan=2025', false);

        $guruData = app(GuruBkTracerStudyController::class)->statistics(Request::create('/dashboard/guru/statistik-tracer-study'))->getData();
        $kepalaData = app(KepalaSekolahTracerStudyController::class)->index(Request::create('/dashboard/kepala-sekolah/tracer-study'))->getData();
        foreach (['totalRecords', 'yearTotals', 'statusGroups', 'majorGroups', 'companyGroups', 'positionGroups', 'placementGroups'] as $key) {
            $this->assertEquals($kepalaData[$key], $guruData[$key], "Statistik {$key} harus identik untuk Guru BK dan Kepala Sekolah.");
        }

        $kepala = new User(['name' => 'Kepala', 'email' => 'kepala@example.test', 'role' => 'kepala_sekolah']);
        $this->actingAs($kepala)->get(route('dashboard.kepala-sekolah.tracer-study'))->assertOk();
        $otherRole = new User(['name' => 'Alumni', 'email' => 'alumni@example.test', 'role' => 'alumni']);
        $this->actingAs($otherRole)->get(route('dashboard.guru.tracer-study.statistics'))->assertRedirect();
    }

    public function test_statistics_show_singletons_and_apply_only_confirmed_normalization(): void
    {
        foreach (['TKJ', 'Teknik Komputer dan Jaringan', 'Teknik Komputer Dan Jaringan', 'Teknik Komputer Jaringan', 'TSM', 'Teknik Sepeda Motor'] as $major) {
            $this->historical(2025, $major, 'Bekerja');
        }
        foreach (['Bekerja', 'Bekerja_Jepang', 'Magang - Jepang', 'Wiraswasta'] as $status) {
            $this->historical(2025, 'TKJ', $status);
        }
        $this->historical(2026, 'TSM', null, 'Instansi tunggal', 'Pelatihan', 'Calon bekerja');

        $data = $this->data();

        $this->assertSame(7, $data['majorGroups']->firstWhere('jurusan', 'Teknik Komputer dan Jaringan (TKJ)')->total_records);
        $this->assertSame(1, $data['majorGroups']->firstWhere('jurusan', 'Teknik Komputer Jaringan')->total_records);
        $this->assertCount(4, $data['statusGroups']);
        $this->assertTrue($data['statusGroups']->contains(fn($group) => $group->status_label === 'Wiraswasta' && $group->total_records === 1));
        $this->assertTrue($data['companyGroups']->contains(fn($group) => $group->label === 'Instansi tunggal' && $group->total_records === 1));
        $this->assertTrue($data['positionGroups']->contains(fn($group) => $group->label === 'Pelatihan'));
        $this->assertTrue($data['placementGroups']->contains(fn($group) => $group->label === 'Calon bekerja'));
    }

    public function test_combined_filters_search_pagination_and_detail_work_without_exposing_contact(): void
    {
        $this->historical(2025, 'TKJ', 'Bekerja', 'Bengkel Contoh', 'Mekanik', 'Jember', 'Nama Alumni Uji', [
            ['header' => 'Kabupaten/Kota', 'value' => 'Jember'],
            ['header' => 'Jenjang', 'value' => 'SMK'],
            ['header' => 'Nama Sekolah', 'value' => 'SMKN 7 Jember'],
            ['header' => 'No Whatsapp Alumni', 'value' => 'PRIVATE-CONTACT-123'],
        ]);
        $this->historical(2026, 'TSM', 'Magang - Jepang', 'Lain', 'Peserta', 'Jepang');

        $data = $this->data(['tahun_pencatatan' => '2025', 'jurusan' => 'Teknik Komputer dan Jaringan (TKJ)', 'status' => 'Bekerja', 'q' => 'Nama Alumni']);
        $this->assertSame(1, $data['records']->total());
        $this->assertSame('Nama Alumni Uji', $data['records']->first()->nama);
        $this->assertSame('SMKN 7 Jember', $data['records']->first()->sekolah);

        Auth::setUser(new User(['name' => 'Kepala Sekolah', 'email' => 'kepala@example.test', 'role' => 'kepala_sekolah']));
        $view = app(KepalaSekolahTracerStudyController::class)->index(Request::create('/dashboard/kepala-sekolah/tracer-study'));
        $view->with('errors', new ViewErrorBag);
        $html = view($view->name(), $view->getData())->render();
        $this->assertStringContainsString('Data Alumni Tracer Study', $html);
        $this->assertStringContainsString('PRIVATE-CONTACT-123', Crypt::decryptString(DB::table('tracer_studies')->where('record_type', 'historical')->value('data_sumber')));
        $this->assertStringNotContainsString('PRIVATE-CONTACT-123', $html);
        $this->assertStringContainsString('Lokasi penempatan', $html);
        $this->assertStringContainsString('stroke-dasharray', $html);
        $this->assertStringContainsString('Perkembangan Data Tracer Study', $html);
        $this->assertStringContainsString('action="' . route('dashboard.kepala-sekolah.tracer-study') . '#data-alumni-tracer-study"', $html);
        $this->assertStringContainsString('id="data-alumni-tracer-study"', $html);
    }

    public function test_page_sizes_and_navigation_cover_the_full_filtered_result_set(): void
    {
        for ($i = 1; $i <= 101; $i++) $this->historical(2026, 'TKJ', 'Bekerja', 'Instansi ' . $i);

        foreach ([10, 25, 50, 100] as $size) {
            $view = $this->ajaxView(['per_page' => (string) $size]);
            $this->assertSame($size, $view->getData()['records']->perPage());
            $this->assertSame(101, $view->getData()['records']->total());
        }

        Paginator::currentPageResolver(fn() => 2);
        $pageTwo = $this->ajaxView(['per_page' => '10', 'page' => '2'])->getData()['records'];
        $this->assertSame(11, $pageTwo->firstItem());
        $this->assertNotNull($pageTwo->previousPageUrl());
        $this->assertNotNull($pageTwo->nextPageUrl());
        $this->assertStringEndsWith('#data-alumni-tracer-study', $pageTwo->nextPageUrl());
        Paginator::currentPageResolver(fn() => 1);
    }

    public function test_bekerja_filter_includes_website_status_case_variant(): void
    {
        $this->historical(2026, 'TKJ', 'Bekerja');
        $this->website(2026, 'TKJ', 'bekerja');

        $data = $this->data(['status' => 'Bekerja']);

        $this->assertSame(2, $data['records']->total());
    }

    public function test_ajax_request_returns_only_filtered_table_partial(): void
    {
        $this->historical(2025, 'DKV', 'Bekerja', 'Instansi target', 'Desainer', 'Jember', 'Nama target', [
            ['header' => 'Kabupaten/Kota', 'value' => 'Jember'],
            ['header' => 'No Whatsapp Alumni', 'value' => 'PRIVATE-AJAX-CONTACT'],
        ]);
        $this->historical(2026, 'DKV', 'Bekerja', 'Instansi lain', 'Desainer', 'Jember', 'Nama lainnya');
        $this->historical(2025, 'TKJ', 'Magang - Jepang', 'Perusahaan lain', 'Peserta', 'Jepang', 'Alumni lain');
        $this->period(2025);

        $query = [
            'tahun_pencatatan' => '2025',
            'jurusan' => 'Desain Komunikasi Visual (DKV)',
            'status' => 'Bekerja',
            'q' => 'Nama target',
            'per_page' => '10',
        ];
        $view = $this->ajaxView($query);

        $this->assertSame('dashboard.kepala-sekolah.partials.tracer-study-table', $view->name());
        $this->assertArrayNotHasKey('statusGroups', $view->getData());
        $this->assertSame(1, $view->getData()['records']->total());
        $html = view($view->name(), $view->getData())->render();
        $this->assertStringContainsString('Nama target', $html);
        $this->assertStringNotContainsString('Nama lainnya', $html);
        $this->assertStringNotContainsString('Perkembangan Data Tracer Study', $html);
        $this->assertStringNotContainsString('PRIVATE-AJAX-CONTACT', $html);
        $this->assertStringContainsString('data-tracer-filter-form', $html);
        $this->assertStringContainsString('data-tracer-pagination', $html);
        $this->assertStringContainsString('alumni-detail-', $html);
    }

    public function test_ajax_pagination_links_keep_all_filters_and_fragment(): void
    {
        for ($i = 1; $i <= 21; $i++) {
            $this->historical(2025, 'DKV', 'Bekerja', 'Instansi', 'Desainer', 'Jember', 'Alumni ' . $i);
        }

        Paginator::currentPageResolver(fn() => 2);
        $view = $this->ajaxView([
            'tahun_pencatatan' => '2025',
            'jurusan' => 'Desain Komunikasi Visual (DKV)',
            'status' => 'Bekerja',
            'q' => 'Alumni',
            'per_page' => '10',
            'page' => '2',
        ]);
        $nextUrl = $view->getData()['records']->nextPageUrl();
        $previousUrl = $view->getData()['records']->previousPageUrl();

        $this->assertSame(21, $view->getData()['records']->total());
        $this->assertStringContainsString('page=3', $nextUrl);
        $this->assertStringContainsString('tahun_pencatatan=2025', $nextUrl);
        $this->assertStringContainsString('jurusan=', $nextUrl);
        $this->assertStringContainsString('status=Bekerja', $nextUrl);
        $this->assertStringContainsString('q=Alumni', $nextUrl);
        $this->assertStringContainsString('per_page=10', $nextUrl);
        $this->assertStringEndsWith('#data-alumni-tracer-study', $nextUrl);
        $this->assertStringContainsString('page=1', $previousUrl);
        Paginator::currentPageResolver(fn() => 1);
    }

    public function test_authorized_route_returns_partial_only_for_ajax_and_keeps_role_protection(): void
    {
        $this->historical(2025, 'DKV', 'Bekerja');
        $this->actingAs(new User(['name' => 'Kepala Sekolah', 'email' => 'kepala@example.test', 'role' => 'kepala_sekolah']));

        $this->get(route('dashboard.kepala-sekolah.tracer-study'), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])->assertOk()->assertSee('tracer-study-table-content');

        $this->actingAs(new User(['name' => 'Guru', 'email' => 'guru@example.test', 'role' => 'guru', 'status' => 'aktif']));
        $this->get(route('dashboard.kepala-sekolah.tracer-study'), [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ])->assertRedirect();
    }

    private function data(array $query = []): array
    {
        return app(KepalaSekolahTracerStudyController::class)->index(Request::create('/dashboard/kepala-sekolah/tracer-study', 'GET', $query))->getData();
    }

    private function ajaxView(array $query = [])
    {
        $request = Request::create('/dashboard/kepala-sekolah/tracer-study', 'GET', $query);
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->headers->set('Accept', 'application/json');

        return app(KepalaSekolahTracerStudyController::class)->index($request)->getOriginalContent();
    }

    private function period(int $year): void
    {
        DB::table('tracer_studies')->insert(['record_type' => 'period', 'tahun' => $year, 'tahun_pencatatan' => $year, 'nama' => 'Periode internal']);
    }

    private function historical(int $year, string $major, ?string $status, string $company = 'Perusahaan', string $position = 'Posisi', string $placement = 'Lokasi', string $name = 'Nama Historis', array $payload = []): void
    {
        DB::table('tracer_studies')->insert([
            'record_type' => 'historical',
            'tahun_pencatatan' => $year,
            'jurusan' => $major,
            'status_asli' => $status,
            'instansi_asli' => $company,
            'posisi_asli' => $position,
            'penempatan_asli' => $placement,
            'nama_lengkap_terenkripsi' => Crypt::encryptString($name),
            'data_sumber' => $payload === [] ? null : Crypt::encryptString(json_encode($payload)),
        ]);
    }

    private function website(int $year, string $major, string $status): void
    {
        DB::table('tracer_studies')->insert(['record_type' => 'website', 'tahun_pencatatan' => $year, 'jurusan' => $major, 'status_kegiatan' => $status, 'nama_lengkap' => 'Alumni Website']);
    }
}
