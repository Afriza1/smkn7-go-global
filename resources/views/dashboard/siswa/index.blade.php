@extends('dashboard.layouts.dashboard')

@section('title', 'Dashboard Siswa | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Dashboard Siswa')
@section('header_title', 'Portal Siswa SMKN 7 Jember – Go Global')

@section('content')
@php
    $accountStatusTone = match ($user->status) {
        'aktif' => 'text-emerald-300',
        'menunggu_verifikasi' => 'text-amber-300',
        'ditolak', 'dinonaktifkan' => 'text-rose-300',
        default => 'text-slate-200',
    };
@endphp
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Welcome Banner Siswa -->
    <div class="bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0A1E3F] rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute right-20 bottom-0 w-32 h-32 rounded-full bg-[#FBBF24]/10 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-[#FBBF24] mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#FBBF24]"></span>
                    <span>Portal Siswa &bull; SMKN 7 Jember</span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight">
                    Selamat Datang, {{ $user->name }}
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-200 max-w-2xl leading-relaxed">
                    Akses informasi peluang magang dan kerja di Jepang, pantau kemajuan berkas pendaftaran, serta ikuti bimbingan intensif dari BKK dan Guru BK.
                </p>
                <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-slate-300">
                    <span>Email: <strong>{{ $user->email }}</strong></span>
                    <span>&bull;</span>
                    <span>Status Akun: <strong class="{{ $accountStatusTone }}">{{ $user->getStatusDisplayName() }}</strong></span>
                </div>
            </div>

            @if(!$pendaftaranAktif)
                <a href="{{ route('karier-jepang.daftar') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#FBBF24] hover:bg-amber-400 text-[#0F2F5F] text-xs sm:text-sm font-extrabold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 flex-shrink-0">
                    <span>Daftar Karier Jepang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endif
        </div>
    </div>

    @if ($user->status === 'menunggu_verifikasi')
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950" aria-label="Informasi status akun">
            <h2 class="font-bold">Akun menunggu verifikasi</h2>
            <p class="mt-1 leading-relaxed">Status akun Anda tercatat menunggu verifikasi. Aplikasi saat ini belum menyediakan antrean atau tindakan untuk memverifikasi akun siswa, sehingga petugas dan lokasi prosesnya belum dapat dipastikan dari alur aplikasi. Ini terpisah dari verifikasi hubungan orang tua melalui NISN yang ditangani Guru BK.</p>
        </section>
    @elseif ($user->status === 'aktif')
        <p class="-mt-5 text-xs text-slate-600" aria-label="Informasi status akun">Akun Anda berstatus aktif menurut data akun.</p>
    @elseif ($user->status === 'ditolak')
        <section class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900" aria-label="Informasi status akun">
            <h2 class="font-bold">Pendaftaran akun ditolak atau memerlukan perbaikan</h2>
            <p class="mt-1">Silakan hubungi bagian tata usaha SMKN 7 Jember sesuai informasi pada proses login.</p>
        </section>
    @elseif ($user->status === 'dinonaktifkan')
        <section class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900" aria-label="Informasi status akun">
            <h2 class="font-bold">Akun dinonaktifkan</h2>
            <p class="mt-1">Akun ini tercatat dinonaktifkan oleh pihak sekolah.</p>
        </section>
    @endif

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <section class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div>
            <h2 class="text-lg font-extrabold text-[#0F2F5F]">Identitas untuk Portal Orang Tua</h2>
            <p class="text-xs text-slate-500 mt-1">NISN digunakan untuk mencocokkan pengajuan hubungan orang tua dengan data siswa. Pengisian tidak otomatis memberikan akses.</p>
        </div>
        @if ($parentIdentity['stored'])
            <div class="space-y-2">
                <p class="text-xs font-semibold text-slate-700">Nomor Induk Siswa Nasional (NISN)</p>
                <div class="flex flex-wrap items-center gap-3">
                    @if ($parentIdentity['nisn'])
                        <span class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 font-mono text-sm tracking-wider text-slate-700" aria-label="NISN tersimpan">{{ $parentIdentity['nisn'] }}</span>
                    @else
                        <span class="text-xs text-amber-800">NISN tersimpan, tetapi tidak dapat ditampilkan. Hubungi pihak sekolah melalui kanal resmi.</span>
                    @endif
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800">Tersimpan</span>
                </div>
                @if ($parentIdentity['match_ready'])
                    <p class="text-xs text-slate-600">NISN telah tersimpan secara aman dan digunakan untuk mencocokkan pengajuan hubungan orang tua.</p>
                @else
                    <p class="text-xs text-amber-800">Identitas tersimpan, tetapi data pencocok belum lengkap. Hubungi pihak sekolah melalui kanal resmi.</p>
                @endif
            </div>
        @else
            <form method="POST" action="{{ route('dashboard.siswa.identity.store') }}" class="grid gap-3 items-end sm:grid-cols-2">@csrf
                <label class="text-xs font-semibold text-slate-700">Nomor Induk Siswa Nasional (NISN)<input name="identity_number" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" placeholder="Masukkan 10 digit NISN siswa" required class="mt-1 block w-full rounded-xl border-slate-300 text-sm" autocomplete="off"></label>
                <button class="rounded-xl bg-[#166534] px-4 py-3 text-xs font-bold text-white hover:bg-green-800">Simpan Identitas</button>
            </form>
        @endif
        @error('identity_number')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
    </section>

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- ==============================================================
         STATUS PENDAFTARAN AKTIF KARIER JEPANG
         ============================================================== -->
    @if($pendaftaranAktif)
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pengajuan Aktif Anda</span>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#0F2F5F]">
                            {{ $pendaftaranAktif->nomor_pendaftaran }}
                        </h2>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $pendaftaranAktif->getStatusBadgeClass() }}">
                            {{ $pendaftaranAktif->getStatusLabel() }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if($pendaftaranAktif->status === 'perlu_perbaikan')
                        <a href="{{ route('karier-jepang.pendaftaran.perbaikan', $pendaftaranAktif->id) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-sm transition-all animate-bounce">
                            <span>Perbaiki Berkas</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endif

                    <a href="{{ route('karier-jepang.pendaftaran.show', $pendaftaranAktif->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all">
                        <span>Buka Detail & Progress Tracker</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Perlu Perbaikan Alert -->
            @if($pendaftaranAktif->status === 'perlu_perbaikan')
                <div class="p-5 rounded-2xl bg-orange-50 border-2 border-orange-300">
                    <strong class="text-xs sm:text-sm font-bold text-orange-950 block mb-1">Catatan Perbaikan dari Petugas Admin:</strong>
                    <p class="text-xs sm:text-sm text-orange-900 bg-white/80 p-3 rounded-xl border border-orange-200">
                        "{{ $pendaftaranAktif->catatan_admin }}"
                    </p>
                </div>
            @endif

            <!-- Ringkasan Info Pengajuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <span class="text-slate-400 block mb-1">Pilihan Lembaga Mitra</span>
                    <strong class="text-slate-800 text-sm block">{{ $pendaftaranAktif->lpk->nama_lpk }}</strong>
                    <span class="text-[11px] text-slate-500">{{ $pendaftaranAktif->lpk->wilayah_penempatan }}</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <span class="text-slate-400 block mb-1">Bidang & Program</span>
                    <strong class="text-[#155EAA] text-sm block">{{ $pendaftaranAktif->lpk->bidang_program }}</strong>
                    <span class="text-[11px] text-slate-500">Konsentrasi: {{ $pendaftaranAktif->jurusan }}</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <span class="text-slate-400 block mb-1">Tanggal Diajukan</span>
                    <strong class="text-slate-800 text-sm block">{{ $pendaftaranAktif->created_at->format('d M Y') }}</strong>
                    <span class="text-[11px] text-slate-500">{{ $pendaftaranAktif->created_at->format('H:i') }} WIB</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                    <span class="text-slate-400 block mb-1">Tahap Selanjutnya</span>
                    <strong class="text-emerald-700 text-sm block">
                        @if($pendaftaranAktif->status === 'menunggu_admin')
                            Verifikasi Dokumen BKK
                        @elseif($pendaftaranAktif->status === 'menunggu_bk')
                            Wawancara Kesiapan Guru BK
                        @elseif($pendaftaranAktif->status === 'menunggu_kepala_sekolah')
                            Persetujuan Kepala Sekolah
                        @else
                            Seleksi & Pembinaan LPK
                        @endif
                    </strong>
                    <span class="text-[11px] text-slate-500">Oleh Tim Manajemen Sekolah</span>
                </div>
            </div>

            <!-- Tracker Preview -->
            <div class="pt-4 border-t border-slate-100">
                <x-career-progress-tracker :steps="$trackerSteps" />
            </div>

        </div>
    @else
        <!-- Jika Belum Mendaftar -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-xs text-center max-w-2xl mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#155EAA] flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h2 class="text-xl font-bold text-[#0F2F5F]">Anda Belum Mengajukan Pendaftaran Program</h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">
                Pendaftaran program Karier Jepang bagi siswa aktif SMKN 7 Jember saat ini sedang dibuka. Pilih salah satu LPK mitra terverifikasi dan lengkapi berkas persyaratan.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('karier-jepang.daftar') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs sm:text-sm font-bold shadow-md transition-all">
                    <span>Mulai Isi Formulir Pendaftaran</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="{{ route('karier-jepang.index') }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition-all">
                    <span>Pelajari Informasi Program</span>
                </a>
            </div>
        </div>
    @endif

    <!-- ==============================================================
         RIWAYAT SELURUH PENGAJUAN
         ============================================================== -->
    @if($riwayatPendaftaran->count() > 1 || (!$pendaftaranAktif && $riwayatPendaftaran->isNotEmpty()))
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
            <h3 class="text-base font-bold text-[#0F2F5F] mb-4 pb-3 border-b border-slate-100">
                Riwayat Pengajuan Pendaftaran Karier Jepang Anda
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-700">
                    <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">No. Pendaftaran</th>
                            <th class="px-4 py-3">LPK Mitra</th>
                            <th class="px-4 py-3">Tanggal Pengajuan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($riwayatPendaftaran as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">{{ $item->nomor_pendaftaran }}</td>
                                <td class="px-4 py-3 font-medium">{{ $item->lpk->nama_lpk }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $item->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $item->getStatusBadgeClass() }}">
                                        {{ $item->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('karier-jepang.pendaftaran.show', $item->id) }}" class="text-[#155EAA] hover:underline font-bold">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Banner Fitur Simulasi Finansial -->
    <div class="bg-gradient-to-br from-[#0F2F5F] to-[#155EAA] rounded-3xl p-6 sm:p-7 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#FBBF24] text-xs font-bold">
                <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Perencanaan Finansial Mandiri &amp; Mitra</span>
            </div>
            <h3 class="text-lg sm:text-xl font-extrabold">Hitung Estimasi Biaya &amp; Kemampuan Pengembalian</h3>
            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                Rencanakan modal keberangkatan kerja ke Jepang dengan kalkulator simulasi resmi SMKN 7 Jember. Hitung konversi gaji yen ke rupiah, proyeksi tabungan bulanan, dan estimasi waktu balik modal.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto flex-shrink-0">
            <a href="{{ route('dashboard.simulasi.index') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#FBBF24] hover:bg-amber-400 text-[#0F2F5F] text-xs font-bold shadow-sm transition-all cursor-pointer">
                <span>Buka Kalkulator Simulasi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <a href="{{ route('dashboard.simulasi.riwayat') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold border border-white/20 transition-all cursor-pointer">
                <span>Riwayat Tersimpan</span>
            </a>
        </div>
    </div>

    <!-- Banner Akses Konseling Digital Siswa -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-1.5 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-[#166534] text-xs font-bold border border-emerald-200">
                <svg class="w-4 h-4 text-[#166534]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Bimbingan Konseling Digital</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold text-[#0F2F5F]">Butuh Bimbingan atau Diskusi Bersama Guru BK?</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Diskusikan persiapan magang kerja Jepang, keraguan karier, maupun permasalahan belajar secara rahasia langsung dengan Guru BK melalui ruang chat online atau tatap muka.
            </p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto flex-shrink-0">
            <a href="{{ route('dashboard.konseling.create') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                <span>Ajukan Konsultasi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <a href="{{ route('dashboard.konseling.index') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all cursor-pointer">
                <span>Daftar Sesi</span>
            </a>
        </div>
    </div>

    <!-- Quick Access Batch 3: Alumni Global & Bantuan Sosial -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Alumni Global & Komunitas -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#155EAA] text-xs font-bold border border-blue-200">
                    <svg class="w-4 h-4 text-[#155EAA]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Jejaring Alumni Jepang</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-[#0F2F5F]">Alumni Global &amp; Mentor Berpengalaman</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Jelajahi profil alumni SMKN 7 Jember yang bekerja di Jepang, dapatkan tips sukses adaptasi kerja dari para mentor, dan bergabunglah ke grup WhatsApp komunitas resmi.
                </p>
            </div>
            <div>
                <a href="{{ route('dashboard.alumni-global.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all">
                    <span>Buka Direktori Alumni</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Card 2: Kolaborasi & Bantuan Sosial -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xs flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-[#166534] text-xs font-bold border border-emerald-200">
                    <svg class="w-4 h-4 text-[#166534]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Dukungan Finansial &amp; Beasiswa</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-[#0F2F5F]">Kolaborasi &amp; Bantuan Sosial Kemitraan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Ajukan permohonan dana talangan keberangkatan (BMT NU Tanggul), subsidi kursus bahasa (LPK Genzo), atau subsidi MCU &amp; paspor resmi sekolah.
                </p>
            </div>
            <div>
                <a href="{{ route('dashboard.bantuan.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold shadow-xs transition-all">
                    <span>Lihat &amp; Ajukan Bantuan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </div>


</div>
@endsection
