<?php

namespace Tests\Feature;

use App\Models\TracerStudy;
use App\Models\TracerStudyPeriode;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TracerStudySingleTableWorkflowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('role');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        Schema::create('tracer_studies', function (Blueprint $table): void {
            $table->id();
            $table->string('record_type');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('periode_id')->nullable();
            $table->unsignedSmallInteger('tahun_pencatatan')->nullable();
            $table->string('nama_lengkap')->nullable();
            $table->unsignedSmallInteger('tahun_lulus')->nullable();
            $table->string('jurusan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('status_kegiatan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('nama_perusahaan')->nullable();
            $table->unsignedSmallInteger('tahun_masuk_perusahaan')->nullable();
            $table->text('alamat_tempat_kerja')->nullable();
            $table->string('status_verifikasi')->nullable();
            $table->boolean('persetujuan_penggunaan_data')->default(false);
            $table->timestamp('disetujui_pada')->nullable();
            $table->unsignedBigInteger('diverifikasi_oleh')->nullable();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->text('riwayat_verifikasi')->nullable();
            $table->text('tindak_lanjut_data')->nullable();
            $table->unsignedSmallInteger('tahun')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->string('status')->nullable();
            $table->string('active_key')->nullable();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->string('nama')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('sumber_data')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'periode_id']);
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        DB::disconnect('sqlite');
        parent::tearDown();
    }

    public function test_alumni_submission_is_year_stamped_and_guru_bk_verification_and_revision_stay_in_the_same_record(): void
    {
        Carbon::setTestNow('2027-02-10 10:00:00');
        $alumni = User::query()->create(['name' => 'Alumni test', 'email' => 'alumni@example.test', 'password' => 'secret', 'role' => 'alumni', 'status' => 'aktif']);
        $guru = User::query()->create(['name' => 'Guru BK test', 'email' => 'guru@example.test', 'password' => 'secret', 'role' => 'guru', 'status' => 'aktif']);
        $periodId = DB::table('tracer_studies')->insertGetId([
            'record_type' => 'period', 'sumber_data' => 'internal', 'tahun' => 2026,
            'tanggal_mulai' => '2027-01-01', 'tanggal_berakhir' => '2027-12-31',
            'status' => 'open', 'active_key' => 'tracer-study-open', 'nama' => 'Tracer Study 2026',
        ]);
        $payload = [
            'nama_lengkap' => 'Alumni Test', 'tahun_lulus' => 2024, 'jurusan' => 'Teknik A',
            'alamat' => 'Jember', 'status_kegiatan' => 'bekerja', 'pekerjaan' => 'Staf',
            'nama_perusahaan' => 'Perusahaan', 'tahun_masuk_perusahaan' => 2025,
            'alamat_tempat_kerja' => 'Jember', 'persetujuan_penggunaan_data' => '1',
        ];

        $this->actingAs($alumni)->post(route('dashboard.tracer-study.store'), $payload)->assertRedirect();
        $this->assertSame(1, DB::table('tracer_studies')->where('record_type', 'website')->count(), json_encode(DB::table('tracer_studies')->get(['id', 'record_type', 'user_id', 'periode_id', 'tahun_pencatatan', 'status_kegiatan', 'nama_lengkap'])->toArray()));
        $this->actingAs($alumni)->post(route('dashboard.tracer-study.store'), $payload)->assertRedirect();
        $this->assertSame(1, DB::table('tracer_studies')->where('record_type', 'website')->count(), json_encode(DB::table('tracer_studies')->get(['id', 'record_type', 'user_id', 'periode_id', 'tahun_pencatatan', 'status_kegiatan', 'nama_lengkap'])->toArray()));

        $responseId = (int) DB::table('tracer_studies')->where('record_type', 'website')->value('id');
        $response = TracerStudy::query()->findOrFail($responseId);
        $this->assertSame(2027, $response->tahun_pencatatan);
        $this->assertSame($alumni->id, $response->user_id);
        $this->assertSame(1, $response->historyEntries()->count());

        $this->actingAs($guru)->post(route('dashboard.guru.tracer-study.verify', [$periodId, $responseId]), ['catatan' => 'Data sesuai'])->assertRedirect();
        $this->assertSame(TracerStudy::STATUS_VERIFIED, $response->fresh()->status_verifikasi);
        $this->assertSame(2, $response->fresh()->historyEntries()->count());

        $this->actingAs($guru)->post(route('dashboard.guru.tracer-study.revision', [$periodId, $responseId]), ['catatan' => 'Mohon periksa kembali'])->assertRedirect();
        $updated = $response->fresh();
        $this->assertSame(TracerStudy::STATUS_NEEDS_UPDATE, $updated->status_verifikasi);
        $this->assertSame(1, $updated->followUpEntries()->count());
        $this->assertSame('Mohon periksa kembali', $updated->followUpEntries()->first()->pesan_alumni);
        $this->assertSame(3, $updated->historyEntries()->count());
        $this->assertSame(1, DB::table('tracer_studies')->where('record_type', 'website')->count());
        $this->assertSame(1, TracerStudyPeriode::query()->count());
    }
}
