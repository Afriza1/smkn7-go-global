@extends('dashboard.layouts.dashboard')

@section('title', 'Dashboard Monitoring Kepala Sekolah | SMKN 7 JEMBER ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œ GO GLOBAL')
@section('breadcrumb', 'Dashboard Kepala Sekolah')
@section('header_title', 'Dashboard Monitoring Eksekutif Go Global')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- ==============================================================
         1. WELCOME BANNER EKSEKUTIF KEPALA SEKOLAH
         ============================================================== -->
    <div class="bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0A1E3F] rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <!-- Japanese Graphic Wave Accent -->
        <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute right-20 bottom-0 w-32 h-32 rounded-full bg-[#FBBF24]/10 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-[#FBBF24] mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#FBBF24]"></span>
                    <span>Monitoring Eksekutif &bull; SMKN 7 Jember</span>
                </div>
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight">
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>
                <p class="mt-2 text-xs sm:text-sm text-slate-200 leading-relaxed">
                    Pantau perkembangan akselerasi program kerja sama internasional, status seleksi ke Jepang, data serapan alumni, serta rekam jejak kemitraan industri dari satu dashboard terpadu.
                </p>
            </div>

            <div class="bg-white/10 border border-white/20 rounded-2xl p-4 backdrop-blur-xs text-xs text-slate-200 max-w-xs flex-shrink-0">
                <div class="flex items-center gap-2 font-bold text-[#FBBF24] mb-1">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Alur Persetujuan:</span>
                </div>
                <p class="text-[11px] leading-relaxed text-slate-300">
                    Sistem pendaftaran Karier Jepang terhubung langsung ke meja putusan pimpinan setelah diverifikasi Admin BKK & Guru BK.
                </p>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         1.5. USULAN PENDAFTARAN MENUNGGU PUTUSAN KEPALA SEKOLAH (INTERAKTIF)
         ============================================================== -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 @if($menungguPersetujuan->isNotEmpty()) border-amber-300 shadow-md @else border-slate-200/90 shadow-xs @endif">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl @if($menungguPersetujuan->isNotEmpty()) bg-amber-100 text-amber-800 @else bg-slate-100 text-slate-600 @endif flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-[#0F2F5F]">
                        Persetujuan Pendaftaran Program Karier Jepang
                    </h3>
                    <p class="text-xs text-slate-500">
                        Usulan peserta yang telah dinyatakan lengkap oleh Admin BKK dan direkomendasikan oleh Guru BK.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if($menungguPersetujuan->isNotEmpty())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        <span>{{ $menungguPersetujuan->count() }} Usulan Menunggu Putusan</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Seluruh Usulan Telah Diputuskan</span>
                    </span>
                @endif
            </div>
        </div>

        @if($menungguPersetujuan->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-700">
                    <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">No. Pendaftaran</th>
                            <th class="px-4 py-3">Nama Calon Peserta</th>
                            <th class="px-4 py-3">Jurusan / Status</th>
                            <th class="px-4 py-3">LPK Pilihan</th>
                            <th class="px-4 py-3">Pertimbangan Guru BK</th>
                            <th class="px-4 py-3 text-right">Aksi Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($menungguPersetujuan as $item)
                            <tr class="hover:bg-amber-50/40 transition-colors">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    {{ $item->nomor_pendaftaran }}
                                </td>
                                <td class="px-4 py-3">
                                    <strong class="text-slate-900 block font-bold">{{ $item->nama_lengkap }}</strong>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $item->email }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-medium text-slate-800 block">{{ $item->jurusan }}</span>
                                    <span class="text-[10px] text-slate-400 capitalize">{{ $item->jenis_peserta === 'siswa' ? 'Siswa Aktif' : 'Alumni' }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    {{ $item->lpk->nama_lpk }}
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="text-slate-600 truncate bg-emerald-50/70 p-1.5 rounded border border-emerald-200/60" title="{{ $item->catatan_bk }}">
                                        "{{ $item->catatan_bk }}"
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('dashboard.kepala-sekolah.show', $item->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-xs">
                                        <span>Tinjau & Putuskan Usulan</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-slate-500 py-3">
                Tidak ada berkas usulan pendaftaran baru yang sedang menunggu persetujuan Kepala Sekolah saat ini. Seluruh pengajuan yang masuk telah diproses.
            </p>
        @endif
    </div>

    <!-- 2. RINGKASAN CAPAIAN UTAMA -->
    <section aria-labelledby="metrics-heading">
        <h3 id="metrics-heading" class="mb-4 text-base font-bold text-[#0F2F5F]">Ringkasan Capaian Utama Program</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs"><p class="text-xs font-bold text-slate-500">Total Siswa</p><p class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($ringkasanUtama['total_siswa'], 0, ',', '.') }}</p></article>
            <article class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs"><p class="text-xs font-bold text-slate-500">Alumni Aktif di Jepang</p><p class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($ringkasanUtama['alumni_aktif_jepang'], 0, ',', '.') }}</p></article>
            <article class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs"><p class="text-xs font-bold text-slate-500">Peserta Program Jepang</p><p class="mt-2 text-2xl font-extrabold text-[#155EAA]">{{ number_format($ringkasanUtama['peserta_program_jepang'], 0, ',', '.') }}</p></article>
            <article class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs"><p class="text-xs font-bold text-slate-500">Mitra Resmi Sekolah</p><p class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($ringkasanUtama['mitra_resmi'], 0, ',', '.') }}</p></article>
        </div>
    </section>
    <!-- 3. DISTRIBUSI BIDANG KERJA ALUMNI MAGANG JEPANG 2025 -->
    <section class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs sm:p-8">
        <div class="mb-5"><span class="mb-1 block text-xs font-bold uppercase tracking-wider text-[#166534]">Penyerapan Kejuruan</span><h3 class="text-base font-extrabold text-[#0F2F5F]">Distribusi Bidang Kerja Alumni Magang Jepang 2025</h3></div>
        @php($maksJumlahBidangKerja = max(1, max(array_column($bidangKerja2025, 1))))
        <div class="grid grid-cols-1 gap-x-8 gap-y-4 md:grid-cols-2">
            @foreach ($bidangKerja2025 as [$bidang, $jumlah])
                <div class="flex min-h-14 flex-col justify-between gap-2">
                    <div class="flex items-start justify-between gap-3 text-xs leading-5">
                        <span class="min-w-0 break-words font-bold text-slate-800">{{ $bidang }}</span>
                        <span class="shrink-0 whitespace-nowrap font-extrabold text-[#0F2F5F]">{{ $jumlah }}</span>
                    </div>
                    <div class="h-2 w-4/5 overflow-hidden rounded-full bg-slate-100" role="img" aria-label="{{ $bidang }}: {{ $jumlah }} dari 13 alumni">
                        <div class="h-full rounded-full bg-[#155EAA]" style="width: {{ round($jumlah / $maksJumlahBidangKerja * 100) }}%" title="{{ $jumlah }} alumni"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <!-- 4. STATISTIK TRACER STUDY ALUMNI -->
    @if ($tracerStudyStats['total'] > 0)
    <section id="ringkasan-alumni" class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs sm:p-8">
        <div class="mb-5 flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
            <div><span class="mb-1 block text-xs font-bold uppercase tracking-wider text-purple-700">Rekam Jejak Lulusan</span><h3 class="text-lg font-extrabold text-[#0F2F5F]">Statistik Tracer Study Alumni</h3></div>
            <strong class="text-sm text-slate-600">{{ number_format($tracerStudyStats['total']) }} alumni terdata</strong>
        </div>
        @if ($tracerStudyStats['years']->count() > 1)
            <div class="mb-5 flex flex-wrap gap-2">@foreach ($tracerStudyStats['years'] as $year)<span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#155EAA]">{{ $year->label }}: {{ number_format($year->count) }}</span>@endforeach</div>
        @endif
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            @foreach ([['title' => 'Status setelah lulus', 'groups' => $tracerStudyStats['status']], ['title' => 'Program keahlian', 'groups' => $tracerStudyStats['majors']]] as $distribution)
                @if (count($distribution['groups']))
                    <div><h4 class="mb-3 text-sm font-extrabold text-[#0F2F5F]">{{ $distribution['title'] }}</h4><div class="space-y-2">
                        @foreach ($distribution['groups'] as $group)
                            <div class="flex items-center justify-between gap-3 text-xs"><span class="break-words font-medium text-slate-700">{{ $group->label }}</span><strong class="shrink-0 text-slate-900">{{ number_format($group->count) }}</strong></div>
                        @endforeach
                    </div>@if ($distribution['title'] === 'Status setelah lulus')<p class="mt-2 text-[11px] text-slate-500">{{ number_format($tracerStudyStats['status_total']) }} data mencantumkan status.</p>@endif</div>
                @endif
            @endforeach
            @foreach ([['title' => 'Instansi/Perusahaan', 'groups' => $tracerStudyStats['companies']], ['title' => 'Posisi pekerjaan', 'groups' => $tracerStudyStats['positions']], ['title' => 'Penempatan', 'groups' => $tracerStudyStats['placements']]] as $distribution)
                @if (count($distribution['groups']))
                    <div class="min-w-0">
                        <h4 class="mb-3 text-sm font-extrabold text-[#0F2F5F]">{{ $distribution['title'] }}</h4>
                        <div @class([
                            'max-h-56 space-y-2 overflow-y-auto overscroll-contain pr-2 [scrollbar-width:thin] [scrollbar-color:#cbd5e1_transparent] sm:max-h-64',
                        ]) role="region" aria-label="Daftar {{ $distribution['title'] }}" tabindex="0">
                        @foreach ($distribution['groups'] as $group)
                            <div class="flex items-start justify-between gap-3 text-xs"><span class="min-w-0 break-words font-medium text-slate-700">{{ $group->label }}</span><strong class="shrink-0 text-slate-900">{{ number_format($group->count) }}</strong></div>
                        @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </section>
    @endif
    <!-- 5. MITRA KERJA SAMA & ASOSIASI -->
    <section id="mitra-kerjasama" class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs sm:p-8">
        <div class="mb-5 flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div><span class="mb-1 block text-xs font-bold uppercase tracking-wider text-[#155EAA]">Jejaring Industri</span><h3 class="text-lg font-extrabold text-[#0F2F5F]">Mitra Industri & Asosiasi Penerima di Jepang</h3></div>
            <span class="shrink-0 text-xs font-bold text-slate-500">{{ number_format($ringkasanUtama['mitra_resmi']) }} mitra</span>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($mitraResmi as $mitra)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><h4 class="text-sm font-bold text-slate-900">{{ $mitra }}</h4></div>
            @endforeach
        </div>
        @if ($ringkasanUtama['peserta_pelatihan'] > 0)
            <p class="mt-5 rounded-xl border border-blue-100 bg-blue-50/60 p-4 text-sm font-bold text-[#0F2F5F]">{{ number_format($ringkasanUtama['peserta_pelatihan']) }} Peserta dalam Proses Pendidikan/Pelatihan</p>
        @endif
    </section>
    <!-- 6. AKTIVITAS PENTING DAN LAPORAN -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @if ($aktivitasTerkini->isNotEmpty())
            <section class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs sm:p-8" id="aktivitas-terbaru">
                <div class="mb-5 border-b border-slate-100 pb-4"><span class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Aktivitas</span><h3 class="text-base font-extrabold text-[#0F2F5F]">Aktivitas Penting Program</h3></div>
                <div class="space-y-4 text-xs">
                    @foreach ($aktivitasTerkini as $aktivitas)
                        <article class="border-b border-slate-100 pb-3 last:border-0">
                            <div class="flex flex-wrap items-center gap-2"><strong class="text-slate-900">{{ $aktivitas->getActionLabel() }}</strong><time class="text-[10px] text-slate-400" datetime="{{ $aktivitas->created_at?->toIso8601String() }}">{{ $aktivitas->created_at?->format('d M Y, H:i') }}</time></div>
                            <p class="mt-1 text-slate-600">{{ $aktivitas->user?->name ?? 'Pengguna tidak tersedia' }} · Pendaftaran {{ $aktivitas->pendaftaran?->nomor_pendaftaran ?? 'tidak tersedia' }} @if ($aktivitas->status_baru) · Status {{ $aktivitas->status_baru }} @endif</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="rounded-3xl border border-slate-200/90 bg-white p-6 shadow-xs sm:p-8" id="laporan-program">
            <div class="mb-5 border-b border-slate-100 pb-4"><span class="mb-1 block text-xs font-bold uppercase tracking-wider text-[#155EAA]">Laporan Program</span><h3 class="text-base font-extrabold text-[#0F2F5F]">Dokumen Eksekutif</h3></div>
            <div class="space-y-3 text-xs">
                <a href="{{ route('dashboard.kepala-sekolah.laporan.show', 'perkembangan') }}" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 hover:bg-slate-100"><strong class="text-slate-900">Laporan Perkembangan Peserta</strong><span class="text-[#155EAA]">Buka →</span></a>
                <a href="{{ route('dashboard.kepala-sekolah.laporan.show', 'keberangkatan-penempatan') }}" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 hover:bg-slate-100"><strong class="text-slate-900">Laporan Keberangkatan dan Penempatan</strong><span class="text-[#155EAA]">Buka →</span></a>
                <a href="{{ route('dashboard.kepala-sekolah.laporan.show', 'kemitraan-mou') }}" class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 hover:bg-slate-100"><strong class="text-slate-900">Daftar Mitra Resmi Sekolah</strong><span class="text-[#155EAA]">Buka →</span></a>
            </div>
        </section>
    </div>
</div>
@endsection
