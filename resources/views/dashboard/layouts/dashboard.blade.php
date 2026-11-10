<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8FAFC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0F2F5F">
    <title>@yield('title', 'Dashboard Kepala Sekolah | SMKN 7 Jember Go Global')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/smkn-7-go-global.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full flex overflow-hidden font-sans text-[#1E293B] antialiased bg-[#F8FAFC]">

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- ==============================================================
         SIDEBAR KIRI (Responsif, Collapsible)
         ============================================================== -->
    <aside id="dashboard-sidebar" 
           class="fixed lg:static inset-y-0 left-0 z-50 w-64 xl:w-72 bg-[#0F2F5F] text-slate-200 flex flex-col justify-between transition-transform duration-300 -translate-x-full lg:translate-x-0 shadow-xl border-r border-blue-950/40">
        
        <!-- Sidebar Brand Lockup -->
        <div>
            <div class="h-20 px-6 flex items-center justify-between border-b border-white/10 bg-[#0A1E3F]">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group focus:outline-hidden" title="Dashboard">
                    <img src="{{ asset('images/smkn-7-go-global.png') }}" 
                         alt="Logo SMKN 7 Jember Go Global" 
                         class="h-10 sm:h-11 w-auto object-contain filter brightness-110 drop-shadow-sm">
                </a>
                <button type="button" 
                        onclick="toggleSidebar()" 
                        class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 lg:hidden focus:outline-hidden"
                        aria-label="Tutup sidebar">
                    âœ•
                </button>
            </div>

            <!-- Role Badge in Sidebar -->
            <div class="px-6 py-4 border-b border-white/5 bg-white/5 flex items-center justify-between text-xs">
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Hak Akses Sistem</span>
                    <strong class="text-[#FBBF24] font-bold text-xs">
                        {{ Auth::user()->getRoleDisplayName() ?? 'Kepala Sekolah' }}
                    </strong>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-[10px] font-bold @if(Auth::user()->status === 'aktif') bg-emerald-500/20 text-emerald-300 border-emerald-500/30 @elseif(Auth::user()->status === 'menunggu_verifikasi') bg-amber-500/20 text-amber-200 border-amber-500/30 @else bg-rose-500/20 text-rose-200 border-rose-500/30 @endif">
                    {{ Auth::user()->getStatusDisplayName() }}
                </span>
            </div>

            <!-- Navigation Links Based on Role -->
            <nav class="p-4 space-y-1 overflow-y-auto max-h-[calc(100vh-280px)]" aria-label="Menu Dashboard">
                
                @if(Auth::user()->isKepalaSekolah())
                    <!-- MENU KEPALA SEKOLAH -->
                    <a href="{{ route('dashboard.kepala-sekolah') }}" data-kepala-sidebar-menu="home"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all @if(request()->path() === 'dashboard/kepala-sekolah') bg-[#155EAA] text-white shadow-sm @else text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard Utama</span>
                    </a>

                    <a href="{{ route('dashboard.kepala-sekolah.tracer-study') }}" data-kepala-sidebar-menu="tracer"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->path() === 'dashboard/kepala-sekolah/tracer-study') font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.kepala-sekolah.tracer-study')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414 0l4.999 5A2 2 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                        <span>Statistik Tracer Study</span>
                    </a>

                @elseif(Auth::user()->isAdminSekolah())
                    <!-- MENU ADMIN SEKOLAH -->
                    <a href="{{ route('dashboard.admin') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all @if(request()->routeIs('dashboard.admin', 'dashboard.admin.show', 'dashboard.admin.verifikasi', 'dashboard.admin.status-lpk')) bg-[#155EAA] text-white shadow-sm @else text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Kelola Pendaftaran Jepang</span>
                    </a>

                    <a href="{{ route('dashboard.admin.simulasi.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.admin.simulasi*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Kelola Simulasi Finansial</span>
                    </a>

                    <a href="{{ route('dashboard.admin.alumni.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.admin.alumni*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Kelola Alumni Global</span>
                    </a>

                    <a href="{{ route('dashboard.admin.tracer-study.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.admin.tracer-study*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.admin.tracer-study*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414 0l4.999 5A2 2 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                        <span>Tracer Study</span>
                    </a>

                    <a href="{{ route('dashboard.admin.modul-edukasi.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.admin.modul-edukasi*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.admin.modul-edukasi*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                        <span>Kelola Modul Edukasi</span>
                    </a>

                    <a href="{{ route('dashboard.admin.panduan-dokumen.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.admin.panduan-dokumen*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.admin.panduan-dokumen*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Kelola Dokumen Keberangkatan</span>
                    </a>

                    <a href="{{ route('dashboard.admin.bantuan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.admin.bantuan*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Kelola Bantuan & Mitra</span>
                    </a>

                    <a href="{{ route('karier-jepang.index') }}" 
                       target="_blank"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-slate-300 hover:bg-white/10 hover:text-white transition-all">
                        <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Lihat Portal Publik</span>
                    </a>

                @elseif(Auth::user()->isGuru())
                    <!-- MENU GURU BK -->
                    <a href="{{ route('dashboard.guru') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all @if(request()->routeIs('dashboard.guru') || request()->routeIs('dashboard.guru.show')) bg-[#155EAA] text-white shadow-sm @else text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Antrean Pertimbangan BK</span>
                    </a>

                    <a href="{{ route('dashboard.guru.verifikasi-akun-siswa.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.guru.verifikasi-akun-siswa*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.guru.verifikasi-akun-siswa*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Verifikasi Akun Siswa</span>
                    </a>

                    <a href="{{ route('dashboard.guru.orang-tua.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.guru.orang-tua*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.guru.orang-tua*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Verifikasi Orang Tua</span>
                    </a>

                    <a href="{{ route('dashboard.guru.konseling.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.guru.konseling*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Konseling Digital</span>
                    </a>

                    <a href="{{ route('dashboard.guru.tracer-study.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.guru.tracer-study*') && !request()->routeIs('dashboard.guru.tracer-study.statistics')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.guru.tracer-study*') && !request()->routeIs('dashboard.guru.tracer-study.statistics')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 19V5m0 14h16M8 15v-3m4 3V8m4 7v-5m4 5V4"/></svg>
                        <span>Kelola Tracer Study</span>
                    </a>

                    <a href="{{ route('dashboard.guru.tracer-study.statistics') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.guru.tracer-study.statistics')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.guru.tracer-study.statistics')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M8 16V9m4 7V5m4 11v-4m4 4V7"/></svg>
                        <span>Statistik Tracer Study</span>
                    </a>

                @elseif(Auth::user()->isSiswa())
                    <!-- MENU SISWA -->
                    <a href="{{ route('dashboard.siswa') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all @if(request()->routeIs('dashboard.siswa*')) bg-[#155EAA] text-white shadow-sm @else text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Dashboard Siswa</span>
                    </a>

                    @if(Auth::user()->status === 'aktif')

                    <a href="{{ route('dashboard.modul-edukasi.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.modul-edukasi*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.modul-edukasi*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                        <span>Modul Edukasi</span>
                    </a>

                    <a href="{{ route('dashboard.panduan-dokumen.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.panduan-dokumen*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.panduan-dokumen*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Panduan Dokumen Keberangkatan</span>
                    </a>

                    <a href="{{ route('karier-jepang.daftar') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('karier-jepang.daftar', 'karier-jepang.pendaftaran.show', 'karier-jepang.pendaftaran.perbaikan')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('karier-jepang.daftar', 'karier-jepang.pendaftaran.show', 'karier-jepang.pendaftaran.perbaikan')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Daftar Karier Jepang</span>
                    </a>

                    <a href="{{ route('dashboard.simulasi.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.simulasi.index')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Simulasi Finansial</span>
                    </a>

                    <a href="{{ route('dashboard.simulasi.riwayat') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.simulasi.riwayat*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat Simulasi Saya</span>
                    </a>

                    <a href="{{ route('dashboard.konseling.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.konseling*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Konseling Digital</span>
                    </a>

                    <a href="{{ route('dashboard.alumni-global.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.alumni-global*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Alumni Global & Mentor</span>
                    </a>

                    <a href="{{ route('dashboard.bantuan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.bantuan*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Bantuan & Kolaborasi</span>
                    </a>

                    <a href="{{ route('karier-jepang.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('karier-jepang.index')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Informasi Program</span>
                    </a>

                    @endif

                @elseif(Auth::user()->isAlumni())
                    <!-- MENU ALUMNI -->
                    <a href="{{ route('dashboard.alumni') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all @if(request()->routeIs('dashboard.alumni*')) bg-[#155EAA] text-white shadow-sm @else text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Dashboard Alumni</span>
                    </a>

                    <a href="{{ route('dashboard.modul-edukasi.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.modul-edukasi*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.modul-edukasi*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                        <span>Modul Edukasi</span>
                    </a>

                    <a href="{{ route('dashboard.panduan-dokumen.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.panduan-dokumen*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.panduan-dokumen*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Panduan Dokumen Keberangkatan</span>
                    </a>

                    <a href="{{ route('dashboard.tracer-study.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.tracer-study*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('dashboard.tracer-study*')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414 0l4.999 5A2 2 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                        <span>Tracer Study</span>
                    </a>

                    <a href="{{ route('karier-jepang.daftar') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('karier-jepang.daftar', 'karier-jepang.pendaftaran.show', 'karier-jepang.pendaftaran.perbaikan')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 @if(request()->routeIs('karier-jepang.daftar', 'karier-jepang.pendaftaran.show', 'karier-jepang.pendaftaran.perbaikan')) text-white @else text-[#FBBF24] @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Daftar Karier Jepang</span>
                    </a>

                    <a href="{{ route('dashboard.simulasi.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.simulasi.index')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Simulasi Finansial</span>
                    </a>

                    <a href="{{ route('dashboard.simulasi.riwayat') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.simulasi.riwayat*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat Simulasi Saya</span>
                    </a>

                    <a href="{{ route('dashboard.konseling.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.konseling*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Konseling Digital</span>
                    </a>

                    <a href="{{ route('dashboard.alumni-global.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.alumni-global*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Alumni Global & Mentor</span>
                    </a>

                    <a href="{{ route('dashboard.bantuan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('dashboard.bantuan*')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Bantuan & Kolaborasi</span>
                    </a>

                    <a href="{{ route('karier-jepang.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition-all @if(request()->routeIs('karier-jepang.index')) font-bold bg-[#155EAA] text-white shadow-sm @else font-semibold text-slate-300 hover:bg-white/10 hover:text-white @endif">
                        <svg class="w-4 h-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>Informasi Program</span>
                    </a>
                @elseif(Auth::user()->isOrangTua())
                    <a href="{{ route('dashboard.orang-tua') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all @if(request()->routeIs('dashboard.orang-tua*')) bg-[#155EAA] text-white shadow-sm @else text-slate-300 hover:bg-white/10 hover:text-white @endif"><span class="w-4 h-4 text-center text-emerald-400">●</span><span>Portal Orang Tua</span></a>
                @endif

            </nav>
        </div>

        <!-- Sidebar Bottom: Settings & Logout -->
        <div class="p-4 border-t border-white/10 bg-[#0A1E3F] space-y-2">
            <div class="flex items-center gap-3 px-3 py-2 text-xs text-slate-300">
                <div class="w-8 h-8 rounded-full bg-[#155EAA] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                    {{ substr(Auth::user()->name ?? 'KS', 0, 2) }}
                </div>
                <div class="truncate">
                    <span class="font-bold text-white block text-xs truncate">{{ Auth::user()->name ?? 'Kepala Sekolah' }}</span>
                    <span class="text-[10px] text-slate-400 block truncate">{{ Auth::user()->email ?? '' }}</span>
                </div>
            </div>

            <!-- Logout Form with CSRF Protection -->
            <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold text-rose-300 hover:bg-rose-500/10 hover:text-rose-200 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar dari Akun (Logout)</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ==============================================================
         MAIN LAYOUT: Header Atas & Konten Utama
         ============================================================== -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Header Atas Dashboard -->
        <header class="h-20 bg-white border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between flex-shrink-0 z-30">
            
            <!-- Left: Toggle Sidebar on Mobile & Breadcrumb -->
            <div class="flex items-center gap-4">
                <button type="button" 
                        onclick="toggleSidebar()" 
                        class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 lg:hidden focus:outline-hidden"
                        aria-label="Toggle sidebar menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div>
                    <!-- Breadcrumb -->
                    <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400">
                        <a href="{{ route('dashboard') }}" class="hover:text-[#155EAA]">Dashboard</a>
                        <span>/</span>
                        <span class="text-slate-600">@yield('breadcrumb', 'Dashboard')</span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-extrabold text-[#0F2F5F] tracking-tight">
                        @yield('header_title', 'Dashboard Monitoring Kepala Sekolah')
                    </h1>
                </div>
            </div>

            <!-- Right: Notifications, Quick Actions, Profile Dropdown -->
            <div class="flex items-center gap-3">
                
                <!-- Notification Bell with Dropdown -->
                <div class="relative dropdown-container">
                    <button type="button" 
                            class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-[#0F2F5F] border border-slate-200/80 relative focus:outline-hidden"
                            aria-label="Lihat notifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="w-2 h-2 rounded-full bg-rose-500 absolute top-2 right-2"></span>
                    </button>
                    <!-- Notification preview menu -->
                    <div class="dropdown-menu absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 p-4 hidden z-50">
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                            <strong class="text-xs font-bold text-slate-800">Notifikasi Program</strong>
                            <span class="text-[10px] font-bold text-[#155EAA]">3 Baru</span>
                        </div>
                        <div class="space-y-2.5 text-xs text-slate-600">
                            <div class="p-2 rounded-lg bg-blue-50/60 border border-blue-100">
                                <span class="font-bold text-[#155EAA] block text-[11px]">Wawancara Mitra Industri</span>
                                12 siswa dijadwalkan wawancara offline bersama delegasi Shizuoka besok.
                            </div>
                            <div class="p-2 rounded-lg bg-emerald-50/60 border border-emerald-100">
                                <span class="font-bold text-[#166534] block text-[11px]">Hasil Skrining MCU</span>
                                24 peserta seleksi gelombang 1 dinyatakan fit tanpa catatan.
                            </div>
                            <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="font-bold text-slate-700 block text-[11px]">Registrasi Akun Baru</span>
                                4 siswa baru mengajukan pendaftaran akun untuk verifikasi.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="relative dropdown-container">
                    <button type="button" 
                            class="flex items-center gap-3 p-1.5 sm:px-3 sm:py-2 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-colors focus:outline-hidden">
                        <div class="w-8 h-8 rounded-full bg-[#0F2F5F] text-[#FBBF24] font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-xs">
                            {{ substr(Auth::user()->name ?? 'KS', 0, 2) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <span class="font-bold text-xs text-slate-800 block leading-tight">{{ Auth::user()->name ?? 'Kepala Sekolah' }}</span>
                            <span class="text-[10px] text-slate-400 block">{{ Auth::user()->getRoleDisplayName() ?? 'Kepala Sekolah' }}</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <!-- Account Dropdown Menu -->
                    <div class="dropdown-menu absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 hidden z-50 text-xs">
                        <div class="px-3 py-2 border-b border-slate-100">
                            <span class="font-bold text-slate-800 block">{{ Auth::user()->name }}</span>
                            <span class="text-[11px] text-slate-400 block truncate">{{ Auth::user()->email }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-rose-50 text-rose-600 font-bold cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>Keluar (Logout)</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </header>

        <!-- Main Scrollable Dashboard Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-[#F8FAFC]">
            @yield('content')
        </main>

    </div>

    <!-- Script for Sidebar Toggle and Dropdown Toggles in Dashboard -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('dashboard-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
            if (backdrop) {
                backdrop.classList.toggle('hidden');
            }
        }

        // Initialize dashboard dropdowns
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.dropdown-container').forEach((container) => {
                const btn = container.querySelector('button');
                const menu = container.querySelector('.dropdown-menu');
                if (!btn || !menu) return;

                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isHidden = menu.classList.contains('hidden');
                    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
                    if (isHidden) {
                        menu.classList.remove('hidden');
                    }
                });
            });

            document.addEventListener('click', () => {
                document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
            });

            const sidebarLinks = document.querySelectorAll('[data-kepala-sidebar-menu]');
            const syncKepalaSidebarActiveMenu = () => {
                const currentPath = window.location.pathname.replace(/\/$/, '');
                const homeLink = document.querySelector('[data-kepala-sidebar-menu="home"]');
                const tracerLink = document.querySelector('[data-kepala-sidebar-menu="tracer"]');
                const portalLink = document.querySelector('[data-kepala-sidebar-menu="portal"]');
                const homePath = homeLink ? new URL(homeLink.href, window.location.origin).pathname.replace(/\/$/, '') : '';
                const tracerPath = tracerLink ? new URL(tracerLink.href, window.location.origin).pathname.replace(/\/$/, '') : '';
                const portalPath = portalLink ? new URL(portalLink.href, window.location.origin).pathname.replace(/\/$/, '') : '';
                const activeMenu = currentPath === homePath ? 'home'
                    : tracerPath && currentPath === tracerPath ? 'tracer'
                    : portalPath && currentPath === portalPath ? 'portal'
                    : null;

                sidebarLinks.forEach((link) => {
                    const active = link.dataset.kepalaSidebarMenu === activeMenu;
                    link.classList.toggle('bg-[#155EAA]', active);
                    link.classList.toggle('text-white', active);
                    link.classList.toggle('shadow-sm', active);
                    link.classList.toggle('text-slate-300', !active);
                    link.classList.toggle('font-bold', active);
                    link.classList.toggle('font-semibold', !active);
                    link.setAttribute('aria-current', active ? 'page' : 'false');
                    const icon = link.querySelector('svg');
                    if (icon && (link.dataset.kepalaSidebarMenu === 'tracer' || link.dataset.kepalaSidebarMenu === 'portal')) {
                        icon.classList.toggle('text-white', active);
                        icon.classList.toggle(link.dataset.kepalaSidebarMenu === 'portal' ? 'text-emerald-400' : 'text-[#FBBF24]', !active);
                    }
                });
            };

            syncKepalaSidebarActiveMenu();
        });
    </script>
    @livewireScripts
</body>
</html>
