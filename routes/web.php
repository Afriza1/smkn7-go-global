<?php

use App\Http\Controllers\AdminAlumniGlobalController;
use App\Http\Controllers\AdminBantuanSosialController;
use App\Http\Controllers\AdminKarierJepangController;
use App\Http\Controllers\AdminModulEdukasiController;
use App\Http\Controllers\AdminPanduanDokumenController;
use App\Http\Controllers\AdminSimulasiFinansialController;
use App\Http\Controllers\AdminTracerStudyController;
use App\Http\Controllers\AlumniGlobalController;
use App\Http\Controllers\BantuanSosialController;
use App\Http\Controllers\GuruBkKarierJepangController;
use App\Http\Controllers\GuruBkKonselingController;
use App\Http\Controllers\GuruBkTracerStudyController;
use App\Http\Controllers\GuruBkVerifikasiAkunSiswaController;
use App\Http\Controllers\KarierJepangController;
use App\Http\Controllers\KepalaSekolahKarierJepangController;
use App\Http\Controllers\KepalaSekolahProgramReportController;
use App\Http\Controllers\KepalaSekolahTracerStudyController;
use App\Http\Controllers\KonselingDigitalController;
use App\Http\Controllers\ModulEdukasiController;
use App\Http\Controllers\PanduanDokumenKeberangkatanController;
use App\Http\Controllers\PesertaDashboardController;
use App\Http\Controllers\OrangTuaPortalController;
use App\Http\Controllers\SimulasiFinansialController;
use App\Http\Controllers\SiswaVerificationController;
use App\Http\Controllers\TracerStudyController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SMKN 7 Jember Go Global
|--------------------------------------------------------------------------
*/

// 1. Landing Page (Preserved & Intact)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('landing');

// 2. Fitur Informasi Karier Jepang (Publik)
Route::get('/karier-jepang', [KarierJepangController::class, 'index'])->name('karier-jepang.index');
Route::get('/karier-jepang/unduh-template-pernyataan', [KarierJepangController::class, 'unduhTemplatePernyataan'])->name('karier-jepang.template-pernyataan');

// 3. Rute Terautentikasi (Auth Middleware)
Route::middleware(['auth', 'account.status'])->group(function () {

    // General Dashboard Dispatcher
    Route::get('/dashboard', function () {
        $user = Auth::user();

        return match ($user->role ?? null) {
            'kepala_sekolah' => redirect()->route('dashboard.kepala-sekolah'),
            'admin_sekolah' => redirect()->route('dashboard.admin'),
            'siswa' => redirect()->route('dashboard.siswa'),
            'alumni' => redirect()->route('dashboard.alumni'),
            'orang_tua' => redirect()->route('dashboard.orang-tua'),
            'guru' => redirect()->route('dashboard.guru'),
            default => view('dashboard.role-placeholder', ['role' => $user->role ?? 'user']),
        };
    })->name('dashboard');

    // A. Fitur Pendaftaran & Tracking Karier Jepang (Siswa & Alumni)
    Route::get('/karier-jepang/daftar', [KarierJepangController::class, 'daftar'])
        ->middleware('role:siswa,alumni')
        ->name('karier-jepang.daftar');

    Route::post('/karier-jepang/daftar', [KarierJepangController::class, 'storeDaftar'])
        ->middleware('role:siswa,alumni')
        ->name('karier-jepang.store');

    Route::get('/karier-jepang/pendaftaran/{pendaftaran}', [KarierJepangController::class, 'showPendaftaran'])
        ->name('karier-jepang.pendaftaran.show');

    Route::get('/karier-jepang/pendaftaran/{pendaftaran}/perbaikan', [KarierJepangController::class, 'formPerbaikan'])
        ->middleware('role:siswa,alumni')
        ->name('karier-jepang.pendaftaran.perbaikan');

    Route::post('/karier-jepang/pendaftaran/{pendaftaran}/perbaikan', [KarierJepangController::class, 'storePerbaikan'])
        ->middleware('role:siswa,alumni')
        ->name('karier-jepang.pendaftaran.perbaikan.store');

    // Unduh Dokumen Aman (Private Storage)
    Route::get('/karier-jepang/dokumen/{pendaftaran}/{jenis}', [KarierJepangController::class, 'unduhDokumen'])
        ->name('karier-jepang.dokumen.unduh');

    // B. Dashboard Siswa & Alumni Terintegrasi
    Route::get('/dashboard/siswa', [PesertaDashboardController::class, 'siswa'])
        ->middleware('role:siswa')
        ->name('dashboard.siswa');

    Route::post('/dashboard/siswa/verifikasi/ajukan-ulang', [SiswaVerificationController::class, 'resubmit'])
        ->middleware('role:siswa')
        ->name('dashboard.siswa.verification.resubmit');

    Route::get('/dashboard/alumni', [PesertaDashboardController::class, 'alumni'])
        ->middleware('role:alumni')
        ->name('dashboard.alumni');

    // B.1. Tracer Study (Alumni)
    Route::middleware('role:alumni')->prefix('/dashboard/tracer-study')->name('dashboard.tracer-study.')->group(function () {
        Route::get('/', [TracerStudyController::class, 'index'])->name('index');
        Route::post('/', [TracerStudyController::class, 'store'])->name('store');
        Route::put('/{tracerStudy}', [TracerStudyController::class, 'update'])->name('update');
    });

    // B.2. Fitur Simulasi Finansial (Siswa & Alumni)
    Route::middleware('role:siswa,alumni')->group(function () {
        Route::get('/dashboard/panduan-dokumen', [PanduanDokumenKeberangkatanController::class, 'index'])
            ->name('dashboard.panduan-dokumen.index');
        Route::post('/dashboard/panduan-dokumen/{jalur}/{dokumen}/status', [PanduanDokumenKeberangkatanController::class, 'updateStatus'])
            ->name('dashboard.panduan-dokumen.status');
    });

    Route::middleware('role:admin_sekolah')->group(function () {
        Route::get('/dashboard/admin/panduan-dokumen', [AdminPanduanDokumenController::class, 'index'])
            ->name('dashboard.admin.panduan-dokumen.index');
        Route::post('/dashboard/admin/panduan-dokumen/jalur', [AdminPanduanDokumenController::class, 'storeJalur'])
            ->name('dashboard.admin.panduan-dokumen.jalur.store');
        Route::put('/dashboard/admin/panduan-dokumen/jalur/{jalur}', [AdminPanduanDokumenController::class, 'updateJalur'])
            ->name('dashboard.admin.panduan-dokumen.jalur.update');
        Route::post('/dashboard/admin/panduan-dokumen/kategori', [AdminPanduanDokumenController::class, 'storeKategori'])
            ->name('dashboard.admin.panduan-dokumen.kategori.store');
        Route::put('/dashboard/admin/panduan-dokumen/kategori/{kategori}', [AdminPanduanDokumenController::class, 'updateKategori'])
            ->name('dashboard.admin.panduan-dokumen.kategori.update');
        Route::post('/dashboard/admin/panduan-dokumen/dokumen', [AdminPanduanDokumenController::class, 'storeDokumen'])
            ->name('dashboard.admin.panduan-dokumen.dokumen.store');
        Route::put('/dashboard/admin/panduan-dokumen/dokumen/{dokumen}', [AdminPanduanDokumenController::class, 'updateDokumen'])
            ->name('dashboard.admin.panduan-dokumen.dokumen.update');
        Route::put('/dashboard/admin/panduan-dokumen/jalur/{jalur}/dokumen/{dokumen}', [AdminPanduanDokumenController::class, 'updatePenerapan'])
            ->name('dashboard.admin.panduan-dokumen.penerapan.update');
    });

    Route::middleware('role:siswa,alumni')->group(function () {
        Route::get('/dashboard/modul-edukasi', [ModulEdukasiController::class, 'index'])
            ->name('dashboard.modul-edukasi.index');
        Route::get('/dashboard/modul-edukasi/kategori/{kategori}', [ModulEdukasiController::class, 'category'])
            ->name('dashboard.modul-edukasi.category');
        Route::get('/dashboard/modul-edukasi/{materi}/baca', [ModulEdukasiController::class, 'read'])
            ->name('dashboard.modul-edukasi.read');
        Route::get('/dashboard/modul-edukasi/{materi}/file', [ModulEdukasiController::class, 'file'])
            ->name('dashboard.modul-edukasi.file');
        Route::get('/dashboard/modul-edukasi/{materi}/unduh', [ModulEdukasiController::class, 'download'])
            ->name('dashboard.modul-edukasi.download');
    });

    Route::middleware('role:admin_sekolah')->group(function () {
        Route::get('/dashboard/admin/modul-edukasi', [AdminModulEdukasiController::class, 'index'])
            ->name('dashboard.admin.modul-edukasi.index');
        Route::post('/dashboard/admin/modul-edukasi', [AdminModulEdukasiController::class, 'store'])
            ->name('dashboard.admin.modul-edukasi.store');
        Route::put('/dashboard/admin/modul-edukasi/{materi}', [AdminModulEdukasiController::class, 'update'])
            ->name('dashboard.admin.modul-edukasi.update');
        Route::delete('/dashboard/admin/modul-edukasi/{materi}', [AdminModulEdukasiController::class, 'destroy'])
            ->name('dashboard.admin.modul-edukasi.destroy');
        Route::get('/dashboard/admin/modul-edukasi/{materi}/baca', [ModulEdukasiController::class, 'read'])
            ->name('dashboard.admin.modul-edukasi.read');
        Route::get('/dashboard/admin/modul-edukasi/{materi}/file', [ModulEdukasiController::class, 'file'])
            ->name('dashboard.admin.modul-edukasi.file');
        Route::get('/dashboard/admin/modul-edukasi/{materi}/unduh', [ModulEdukasiController::class, 'download'])
            ->name('dashboard.admin.modul-edukasi.download');
    });

    Route::middleware('role:siswa,alumni')->group(function () {
        Route::get('/dashboard/simulasi-finansial', [SimulasiFinansialController::class, 'index'])
            ->name('dashboard.simulasi.index');

        Route::post('/dashboard/simulasi-finansial/hitung', [SimulasiFinansialController::class, 'hitung'])
            ->name('dashboard.simulasi.hitung');

        Route::post('/dashboard/simulasi-finansial/simpan', [SimulasiFinansialController::class, 'simpan'])
            ->name('dashboard.simulasi.simpan');

        Route::get('/dashboard/simulasi-finansial/riwayat', [SimulasiFinansialController::class, 'riwayat'])
            ->name('dashboard.simulasi.riwayat');

        // B.2. Fitur Konseling Digital (Siswa & Alumni)
        Route::get('/dashboard/konseling', [KonselingDigitalController::class, 'index'])
            ->name('dashboard.konseling.index');
        Route::get('/dashboard/konseling/buat', [KonselingDigitalController::class, 'create'])
            ->name('dashboard.konseling.create');
        Route::post('/dashboard/konseling', [KonselingDigitalController::class, 'store'])
            ->name('dashboard.konseling.store');
        Route::get('/dashboard/konseling/{konseling}', [KonselingDigitalController::class, 'show'])
            ->name('dashboard.konseling.show');
        Route::post('/dashboard/konseling/{konseling}/batal', [KonselingDigitalController::class, 'batal'])
            ->name('dashboard.konseling.batal');
        Route::get('/dashboard/konseling/{konseling}/chat', [KonselingDigitalController::class, 'chat'])
            ->name('dashboard.konseling.chat');

        // B.3. Fitur Alumni Global & Mentor (Siswa & Alumni)
        Route::get('/dashboard/alumni-global', [AlumniGlobalController::class, 'index'])
            ->name('dashboard.alumni-global.index');
        Route::get('/dashboard/alumni-global/{alumni}', [AlumniGlobalController::class, 'show'])
            ->name('dashboard.alumni-global.show');

        // B.4. Fitur Kolaborasi & Bantuan Sosial (Siswa & Alumni)
        Route::get('/dashboard/bantuan', [BantuanSosialController::class, 'index'])
            ->name('dashboard.bantuan.index');
        Route::get('/dashboard/bantuan/program/{program:slug}', [BantuanSosialController::class, 'showProgram'])
            ->name('dashboard.bantuan.program');
        Route::get('/dashboard/bantuan/ajukan/{program:slug}', [BantuanSosialController::class, 'create'])
            ->name('dashboard.bantuan.create');
        Route::post('/dashboard/bantuan/ajukan/{program:slug}', [BantuanSosialController::class, 'store'])
            ->name('dashboard.bantuan.store');
        Route::get('/dashboard/bantuan/pengajuan/{pengajuan}', [BantuanSosialController::class, 'show'])
            ->name('dashboard.bantuan.show');
        Route::get('/dashboard/bantuan/pengajuan/{pengajuan}/perbaikan', [BantuanSosialController::class, 'formPerbaikan'])
            ->name('dashboard.bantuan.perbaikan');
        Route::post('/dashboard/bantuan/pengajuan/{pengajuan}/perbaikan', [BantuanSosialController::class, 'storePerbaikan'])
            ->name('dashboard.bantuan.perbaikan.store');
        Route::post('/dashboard/bantuan/pengajuan/{pengajuan}/batal', [BantuanSosialController::class, 'batal'])
            ->name('dashboard.bantuan.batal');
    });

    // Akses Unduh Dokumen Privat Bantuan Sosial (Pemohon Siswa/Alumni & Admin BKK)
    Route::get('/dashboard/bantuan/dokumen/{dokumen}', [BantuanSosialController::class, 'unduhDokumen'])
        ->middleware('role:siswa,alumni,admin_sekolah')
        ->name('dashboard.bantuan.dokumen.unduh');

    Route::middleware('role:siswa,alumni,admin_sekolah')->group(function () {
        Route::get('/dashboard/simulasi-finansial/riwayat/{riwayat}', [SimulasiFinansialController::class, 'showRiwayat'])
            ->name('dashboard.simulasi.riwayat.show');

        Route::delete('/dashboard/simulasi-finansial/riwayat/{riwayat}', [SimulasiFinansialController::class, 'destroyRiwayat'])
            ->name('dashboard.simulasi.riwayat.destroy');
    });

    // C. Dashboard Admin Sekolah (BKK)
    Route::get('/dashboard/admin', [AdminKarierJepangController::class, 'index'])
        ->middleware('role:admin_sekolah')
        ->name('dashboard.admin');

    // C.1. Pengelolaan Simulasi Finansial & Parameter (Admin)
    Route::middleware('role:admin_sekolah')->group(function () {
        // C.1.a. Pengelolaan Tracer Study (Admin BKK)
        Route::get('/dashboard/admin/tracer-study', [AdminTracerStudyController::class, 'index'])
            ->name('dashboard.admin.tracer-study.index');
        Route::post('/dashboard/admin/tracer-study/periode', [AdminTracerStudyController::class, 'storePeriod'])
            ->name('dashboard.admin.tracer-study.periode.store');
        Route::put('/dashboard/admin/tracer-study/periode/{periode}', [AdminTracerStudyController::class, 'updatePeriod'])
            ->name('dashboard.admin.tracer-study.periode.update');
        Route::post('/dashboard/admin/tracer-study/periode/{periode}/buka', [AdminTracerStudyController::class, 'openPeriod'])
            ->name('dashboard.admin.tracer-study.periode.open');
        Route::post('/dashboard/admin/tracer-study/periode/{periode}/tutup', [AdminTracerStudyController::class, 'closePeriod'])
            ->name('dashboard.admin.tracer-study.periode.close');
        Route::get('/dashboard/admin/tracer-study/export', [AdminTracerStudyController::class, 'export'])
            ->name('dashboard.admin.tracer-study.export');
        Route::get('/dashboard/admin/tracer-study/respons/{tracerStudy}', [AdminTracerStudyController::class, 'show'])
            ->name('dashboard.admin.tracer-study.show');

        // C.1.b. Pengelolaan Simulasi Finansial & Parameter (Admin)
        Route::get('/dashboard/admin/simulasi-finansial', [AdminSimulasiFinansialController::class, 'index'])
            ->name('dashboard.admin.simulasi.index');

        Route::post('/dashboard/admin/simulasi-finansial/parameter', [AdminSimulasiFinansialController::class, 'updateParameter'])
            ->name('dashboard.admin.simulasi.parameter.update');

        Route::post('/dashboard/admin/simulasi-finansial/mitra', [AdminSimulasiFinansialController::class, 'storeMitra'])
            ->name('dashboard.admin.simulasi.mitra.store');

        Route::put('/dashboard/admin/simulasi-finansial/mitra/{mitra}', [AdminSimulasiFinansialController::class, 'updateMitra'])
            ->name('dashboard.admin.simulasi.mitra.update');

        Route::post('/dashboard/admin/simulasi-finansial/mitra/{mitra}/toggle', [AdminSimulasiFinansialController::class, 'toggleMitra'])
            ->name('dashboard.admin.simulasi.mitra.toggle');

        // C.2. Pengelolaan Alumni Global & Komunitas WhatsApp (Admin BKK)
        Route::get('/dashboard/admin/alumni-global', [AdminAlumniGlobalController::class, 'index'])
            ->name('dashboard.admin.alumni.index');
        Route::post('/dashboard/admin/alumni-global', [AdminAlumniGlobalController::class, 'store'])
            ->name('dashboard.admin.alumni.store');
        Route::put('/dashboard/admin/alumni-global/{alumni}', [AdminAlumniGlobalController::class, 'update'])
            ->name('dashboard.admin.alumni.update');
        Route::post('/dashboard/admin/alumni-global/{alumni}/toggle-mentor', [AdminAlumniGlobalController::class, 'toggleMentor'])
            ->name('dashboard.admin.alumni.toggle-mentor');
        Route::post('/dashboard/admin/alumni-global/{alumni}/toggle-kontak', [AdminAlumniGlobalController::class, 'toggleKontak'])
            ->name('dashboard.admin.alumni.toggle-kontak');
        Route::post('/dashboard/admin/alumni-global/{alumni}/toggle-aktif', [AdminAlumniGlobalController::class, 'toggleAktif'])
            ->name('dashboard.admin.alumni.toggle-aktif');
        Route::delete('/dashboard/admin/alumni-global/{alumni}', [AdminAlumniGlobalController::class, 'destroy'])
            ->name('dashboard.admin.alumni.destroy');
        Route::post('/dashboard/admin/alumni-global/komunitas', [AdminAlumniGlobalController::class, 'updateKomunitas'])
            ->name('dashboard.admin.alumni.komunitas');

        // C.3. Pengelolaan Kolaborasi & Bantuan Sosial (Admin BKK)
        Route::get('/dashboard/admin/bantuan', [AdminBantuanSosialController::class, 'index'])
            ->name('dashboard.admin.bantuan.index');
        Route::get('/dashboard/admin/bantuan/pengajuan/{pengajuan}', [AdminBantuanSosialController::class, 'show'])
            ->name('dashboard.admin.bantuan.show');
        Route::post('/dashboard/admin/bantuan/pengajuan/{pengajuan}/verifikasi', [AdminBantuanSosialController::class, 'verifikasi'])
            ->name('dashboard.admin.bantuan.verifikasi');
        Route::post('/dashboard/admin/bantuan/pengajuan/{pengajuan}/perbaikan', [AdminBantuanSosialController::class, 'mintaPerbaikan'])
            ->name('dashboard.admin.bantuan.perbaikan');
        Route::post('/dashboard/admin/bantuan/pengajuan/{pengajuan}/teruskan', [AdminBantuanSosialController::class, 'teruskanMitra'])
            ->name('dashboard.admin.bantuan.teruskan');
        Route::post('/dashboard/admin/bantuan/pengajuan/{pengajuan}/proses-mitra', [AdminBantuanSosialController::class, 'updateProsesMitra'])
            ->name('dashboard.admin.bantuan.proses-mitra');
        Route::post('/dashboard/admin/bantuan/pengajuan/{pengajuan}/putusan', [AdminBantuanSosialController::class, 'putusanMitra'])
            ->name('dashboard.admin.bantuan.putusan');
        Route::post('/dashboard/admin/bantuan/pengajuan/{pengajuan}/catatan-internal', [AdminBantuanSosialController::class, 'simpanCatatanInternal'])
            ->name('dashboard.admin.bantuan.catatan-internal');
        Route::post('/dashboard/admin/bantuan/program', [AdminBantuanSosialController::class, 'storeProgram'])
            ->name('dashboard.admin.bantuan.program.store');
        Route::put('/dashboard/admin/bantuan/program/{program}', [AdminBantuanSosialController::class, 'updateProgram'])
            ->name('dashboard.admin.bantuan.program.update');
        Route::post('/dashboard/admin/bantuan/program/{program}/toggle', [AdminBantuanSosialController::class, 'toggleProgram'])
            ->name('dashboard.admin.bantuan.program.toggle');
    });

    Route::get('/dashboard/admin/pendaftaran/{pendaftaran}', [AdminKarierJepangController::class, 'show'])
        ->middleware('role:admin_sekolah')
        ->name('dashboard.admin.show');

    Route::post('/dashboard/admin/pendaftaran/{pendaftaran}/verifikasi', [AdminKarierJepangController::class, 'verifikasi'])
        ->middleware('role:admin_sekolah')
        ->name('dashboard.admin.verifikasi');

    Route::post('/dashboard/admin/pendaftaran/{pendaftaran}/status-lpk', [AdminKarierJepangController::class, 'updateStatusLpk'])
        ->middleware('role:admin_sekolah')
        ->name('dashboard.admin.status-lpk');

    // D. Dashboard Guru BK / Pembimbing Karier
    Route::get('/dashboard/guru', [GuruBkKarierJepangController::class, 'index'])
        ->middleware('role:guru')
        ->name('dashboard.guru');

    Route::get('/dashboard/guru/pendaftaran/{pendaftaran}', [GuruBkKarierJepangController::class, 'show'])
        ->middleware('role:guru')
        ->name('dashboard.guru.show');

    Route::post('/dashboard/guru/pendaftaran/{pendaftaran}/pertimbangan', [GuruBkKarierJepangController::class, 'submitPertimbangan'])
        ->middleware('role:guru')
        ->name('dashboard.guru.pertimbangan');

    // D.1. Pengelolaan Konseling Digital oleh Guru BK
    Route::middleware('role:guru')->group(function () {
        Route::get('/dashboard/guru/verifikasi-akun-siswa', [GuruBkVerifikasiAkunSiswaController::class, 'index'])
            ->name('dashboard.guru.verifikasi-akun-siswa.index');
        Route::get('/dashboard/guru/verifikasi-akun-siswa/{verificationRequest}', [GuruBkVerifikasiAkunSiswaController::class, 'show'])
            ->name('dashboard.guru.verifikasi-akun-siswa.show');
        Route::post('/dashboard/guru/verifikasi-akun-siswa/{verificationRequest}/putusan', [GuruBkVerifikasiAkunSiswaController::class, 'decide'])
            ->name('dashboard.guru.verifikasi-akun-siswa.decide');
        Route::get('/dashboard/guru/statistik-tracer-study', [GuruBkTracerStudyController::class, 'statistics'])
            ->name('dashboard.guru.tracer-study.statistics');
        Route::get('/dashboard/guru/tracer-study', [GuruBkTracerStudyController::class, 'index'])
            ->name('dashboard.guru.tracer-study.index');
        Route::get('/dashboard/guru/tracer-study/periode/{periode}', [GuruBkTracerStudyController::class, 'show'])
            ->name('dashboard.guru.tracer-study.show');
        Route::get('/dashboard/guru/tracer-study/periode/{periode}/respons/{tracerStudy}', [GuruBkTracerStudyController::class, 'showResponse'])
            ->name('dashboard.guru.tracer-study.response');
        Route::post('/dashboard/guru/tracer-study/periode/{periode}/respons/{tracerStudy}/verifikasi', [GuruBkTracerStudyController::class, 'verify'])
            ->name('dashboard.guru.tracer-study.verify');
        Route::post('/dashboard/guru/tracer-study/periode/{periode}/respons/{tracerStudy}/minta-perbaikan', [GuruBkTracerStudyController::class, 'requestRevision'])
            ->name('dashboard.guru.tracer-study.revision');
        Route::post('/dashboard/guru/tracer-study/periode', [AdminTracerStudyController::class, 'storePeriod'])
            ->name('dashboard.guru.tracer-study.periode.store');
        Route::put('/dashboard/guru/tracer-study/periode/{periode}', [AdminTracerStudyController::class, 'updatePeriod'])
            ->name('dashboard.guru.tracer-study.periode.update');
        Route::post('/dashboard/guru/tracer-study/periode/{periode}/buka', [AdminTracerStudyController::class, 'openPeriod'])
            ->name('dashboard.guru.tracer-study.periode.open');
        Route::post('/dashboard/guru/tracer-study/periode/{periode}/tutup', [AdminTracerStudyController::class, 'closePeriod'])
            ->name('dashboard.guru.tracer-study.periode.close');

        Route::get('/dashboard/guru/konseling', [GuruBkKonselingController::class, 'index'])
            ->name('dashboard.guru.konseling.index');
        Route::post('/dashboard/guru/konseling/{konseling}/ambil', [GuruBkKonselingController::class, 'ambilKonseling'])
            ->name('dashboard.guru.konseling.ambil');
        Route::get('/dashboard/guru/konseling/{konseling}', [GuruBkKonselingController::class, 'show'])
            ->name('dashboard.guru.konseling.show');
        Route::post('/dashboard/guru/konseling/{konseling}/konfirmasi', [GuruBkKonselingController::class, 'konfirmasi'])
            ->name('dashboard.guru.konseling.konfirmasi');
        Route::post('/dashboard/guru/konseling/{konseling}/mulai', [GuruBkKonselingController::class, 'mulaiSesi'])
            ->name('dashboard.guru.konseling.mulai');
        Route::post('/dashboard/guru/konseling/{konseling}/selesai', [GuruBkKonselingController::class, 'akhiriSesi'])
            ->name('dashboard.guru.konseling.selesai');
        Route::post('/dashboard/guru/konseling/{konseling}/catatan', [GuruBkKonselingController::class, 'simpanCatatanPrivat'])
            ->name('dashboard.guru.konseling.catatan');
        Route::get('/dashboard/guru/konseling/{konseling}/chat', [GuruBkKonselingController::class, 'chat'])
            ->name('dashboard.guru.konseling.chat');
    });

    // E. Dashboard Kepala Sekolah Terintegrasi
    Route::get('/dashboard/kepala-sekolah', [KepalaSekolahKarierJepangController::class, 'index'])
        ->middleware('role:kepala_sekolah')
        ->name('dashboard.kepala-sekolah');

    Route::get('/dashboard/kepala-sekolah/tracer-study', [KepalaSekolahTracerStudyController::class, 'index'])
        ->middleware('role:kepala_sekolah')
        ->name('dashboard.kepala-sekolah.tracer-study');

    Route::middleware('role:kepala_sekolah')
        ->prefix('/dashboard/kepala-sekolah/laporan')
        ->name('dashboard.kepala-sekolah.laporan.')
        ->group(function () {
            Route::get('/{report}', [KepalaSekolahProgramReportController::class, 'show'])
                ->whereIn('report', ['perkembangan', 'keberangkatan-penempatan', 'kemitraan-mou'])
                ->name('show');
            Route::get('/{report}/excel', [KepalaSekolahProgramReportController::class, 'exportExcel'])
                ->whereIn('report', ['perkembangan', 'keberangkatan-penempatan', 'kemitraan-mou'])
                ->name('excel');
        });

    Route::get('/dashboard/kepala-sekolah/pendaftaran/{pendaftaran}', [KepalaSekolahKarierJepangController::class, 'show'])
        ->middleware('role:kepala_sekolah')
        ->name('dashboard.kepala-sekolah.show');

    Route::post('/dashboard/kepala-sekolah/pendaftaran/{pendaftaran}/putusan', [KepalaSekolahKarierJepangController::class, 'putusan'])
        ->middleware('role:kepala_sekolah')
        ->name('dashboard.kepala-sekolah.putusan');

    // F. Dashboard Orang Tua & Fallback
    Route::middleware('role:orang_tua')->group(function () {
        Route::get('/dashboard/orang-tua', [OrangTuaPortalController::class, 'index'])->name('dashboard.orang-tua');
        Route::post('/dashboard/orang-tua/pengajuan', [OrangTuaPortalController::class, 'submit'])->name('dashboard.orang-tua.submit');
    });

    Route::middleware('role:siswa')->post('/dashboard/siswa/identitas-orang-tua', [OrangTuaPortalController::class, 'storeIdentity'])->name('dashboard.siswa.identity.store');
    Route::middleware('role:guru')->prefix('/dashboard/guru/verifikasi-orang-tua')->name('dashboard.guru.orang-tua.')->group(function () {
        Route::get('/', [OrangTuaPortalController::class, 'reviewQueue'])->name('index');
        Route::get('/{link}', [OrangTuaPortalController::class, 'reviewShow'])->name('show');
        Route::post('/{link}/putusan', [OrangTuaPortalController::class, 'decide'])->name('decide');
    });

    Route::get('/dashboard/role/{role}', function ($role) {
        return view('dashboard.role-placeholder', ['role' => $role]);
    })->name('dashboard.role-placeholder');
});
