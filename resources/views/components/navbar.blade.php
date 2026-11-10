@php
    $navbarHomeHref = Auth::check()
        ? route('dashboard')
        : (request()->routeIs('landing') ? '#beranda' : route('landing') . '#beranda');
    $navbarSectionHref = fn (string $section): string => Auth::check() || request()->routeIs('landing')
        ? '#' . $section
        : route('landing') . '#' . $section;
@endphp

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-shadow duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20 gap-4">
            
            <!-- Left: Logo & Brand Identity -->
            <a href="{{ $navbarHomeHref }}" class="flex items-center gap-3 group focus:outline-hidden" aria-label="Beranda SMKN 7 Jember Go Global">
                <img src="{{ asset('images/logo-smkn-7-jember-horizontal.png') }}" 
                     alt="Logo SMKN 7 Jember Go Global" 
                     class="h-10 w-40 sm:h-12 sm:w-48 xl:h-14 xl:w-56 shrink-0 object-contain transition-transform duration-200 group-hover:scale-102">
            </a>

            <!-- Center / Desktop Navigation Menu -->
            <nav class="hidden xl:flex min-w-0 flex-1 flex-nowrap translate-x-1 items-center justify-center gap-0.5 2xl:gap-1" aria-label="Navigasi Utama">
                
                <!-- Beranda -->
                <a href="{{ $navbarHomeHref }}" 
                   class="nav-link shrink-0 whitespace-nowrap px-1.5 py-2 text-sm 2xl:text-[15px] font-semibold text-slate-700 hover:text-[#155EAA] rounded-md transition-colors">
                    Beranda
                </a>

                <!-- Karir Jepang (Dropdown) -->
                <div class="relative group dropdown-container shrink-0">
                    <button type="button" 
                            class="dropdown-trigger flex items-center gap-1 whitespace-nowrap px-1.5 py-2 text-sm 2xl:text-[15px] font-semibold text-slate-700 hover:text-[#155EAA] rounded-md transition-colors focus:outline-hidden"
                            aria-expanded="false" 
                            aria-haspopup="true">
                        <span>Karir Jepang</span>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#155EAA] transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu absolute left-0 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-100 py-2 hidden opacity-0 translate-y-2 transition-all duration-200 z-50">
                        <a href="{{ route('karier-jepang.index') }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-blue-50 text-[#155EAA] mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Peluang Kerja & Magang</span>
                                <span class="text-[11px] text-slate-500">Tokutei Ginou & Ginou Jisshusei</span>
                            </div>
                        </a>
                        <a href="{{ $navbarSectionHref('karier') }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-emerald-50 text-[#166534] mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Pelatihan Bahasa & Budaya</span>
                                <span class="text-[11px] text-slate-500">Standar JLPT N4 & etika Hou-Ren-So</span>
                            </div>
                        </a>
                        <a href="{{ $navbarSectionHref('akses') }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-amber-50 text-[#d97706] mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Panduan Dokumen Keberangkatan</span>
                                <span class="text-[11px] text-slate-500">Paspor, COE, dan Visa Kerja</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Konseling & Edukasi (Dropdown) -->
                <div class="relative group dropdown-container shrink-0">
                    <button type="button" 
                            class="dropdown-trigger flex items-center gap-1 whitespace-nowrap px-1.5 py-2 text-sm 2xl:text-[15px] font-semibold text-slate-700 hover:text-[#155EAA] rounded-md transition-colors focus:outline-hidden"
                            aria-expanded="false" 
                            aria-haspopup="true">
                        <span>Konseling & Edukasi</span>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#155EAA] transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu absolute left-0 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-100 py-2 hidden opacity-0 translate-y-2 transition-all duration-200 z-50">
                        <a href="{{ $navbarSectionHref('konseling') }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-indigo-50 text-indigo-700 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Konseling Digital</span>
                                <span class="text-[11px] text-slate-500">Bimbingan kesiapan mental & karir</span>
                            </div>
                        </a>
                        <a href="{{ $navbarSectionHref('konseling') }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-rose-50 text-rose-700 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Modul Edukasi Masa Depan</span>
                                <span class="text-[11px] text-slate-500">Materi mandiri & wawasan global</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Alumni -->
                <div class="relative group dropdown-container shrink-0">
                    <button type="button" 
                            class="dropdown-trigger flex items-center gap-1 whitespace-nowrap px-1.5 py-2 text-sm 2xl:text-[15px] font-semibold text-slate-700 hover:text-[#155EAA] rounded-md transition-colors focus:outline-hidden"
                            aria-expanded="false" 
                            aria-haspopup="true">
                        <span>Alumni</span>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#155EAA] transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu absolute left-0 mt-1 w-60 bg-white rounded-xl shadow-xl border border-slate-100 py-2 hidden opacity-0 translate-y-2 transition-all duration-200 z-50">
                        <a href="{{ $navbarSectionHref('alumni') }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-cyan-50 text-cyan-700 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Inspirasi Alumni</span>
                                <span class="text-[11px] text-slate-500">Testimoni & pengalaman kerja nyata</span>
                            </div>
                        </a>
                        <a href="{{ Auth::check() && Auth::user()->hasRole('alumni') ? route('dashboard.tracer-study.index') : (Auth::check() && Auth::user()->hasRole('admin_sekolah') ? route('dashboard.admin.tracer-study.index') : (Auth::check() && Auth::user()->hasRole('kepala_sekolah') ? route('dashboard.kepala-sekolah.tracer-study') : route('login'))) }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-violet-50 text-violet-700 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Tracer Study Global</span>
                                <span class="text-[11px] text-slate-500">Sebaran alumni di kancah global</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Informasi (Dropdown) -->
                <div class="relative group dropdown-container shrink-0">
                    <button type="button" 
                            class="dropdown-trigger flex items-center gap-1 whitespace-nowrap px-1.5 py-2 text-sm 2xl:text-[15px] font-semibold text-slate-700 hover:text-[#155EAA] rounded-md transition-colors focus:outline-hidden"
                            aria-expanded="false" 
                            aria-haspopup="true">
                        <span>Informasi</span>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#155EAA] transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="dropdown-menu absolute left-0 mt-1 w-60 bg-white rounded-xl shadow-xl border border-slate-100 py-2 hidden opacity-0 translate-y-2 transition-all duration-200 z-50">
                        <a href="{{ $navbarSectionHref('pengumuman') }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-blue-50 text-[#155EAA] mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Pengumuman Terbaru</span>
                                <span class="text-[11px] text-slate-500">Agenda, jadwal & berita resmi</span>
                            </div>
                        </a>
                        <a href="{{ $navbarSectionHref('galeri') }}" class="flex items-start gap-3 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#155EAA] transition-colors">
                            <span class="p-1.5 rounded-lg bg-emerald-50 text-[#166534] mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <div>
                                <span class="font-bold block text-slate-800">Galeri Kegiatan</span>
                                <span class="text-[11px] text-slate-500">Dokumentasi pelatihan & keberangkatan</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Layanan -->
                <div class="relative group dropdown-container shrink-0">
                    <button type="button" class="dropdown-trigger flex items-center gap-1 whitespace-nowrap px-1.5 py-2 text-sm 2xl:text-[15px] font-semibold text-slate-700 hover:text-[#155EAA] rounded-md transition-colors focus:outline-hidden" aria-expanded="false" aria-haspopup="true">
                        Layanan
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#155EAA] transition-transform duration-200 chevron-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="dropdown-menu absolute right-0 mt-1 w-64 bg-white rounded-xl shadow-xl border border-slate-100 py-2 hidden opacity-0 translate-y-2 transition-all duration-200 z-50">
                        <button type="button" onclick="window.openSimulationModal()" class="w-full text-left px-4 py-3 text-[13px] font-bold text-slate-800 hover:bg-slate-50 hover:text-[#155EAA]">Simulasi Finansial</button>
                        <a href="{{ $navbarSectionHref('akses') }}" class="block px-4 py-3 text-[13px] font-bold text-slate-800 hover:bg-slate-50 hover:text-[#155EAA]">Kolaborasi & Bantuan Sosial</a>
                    </div>
                </div>
            </nav>

            <!-- Right Actions: Search Bar & Login Button -->
            <div class="hidden md:flex shrink-0 items-center gap-3">
                
                <!-- Quick Search Input -->
                <div class="relative w-48 xl:w-56" role="search">
                    <input type="search" 
                           id="site-search-input"
                           placeholder="Cari fitur & info..." 
                           class="w-full pl-9 pr-8 py-2 text-[13px] bg-slate-100 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-[#155EAA] rounded-full text-slate-800 placeholder-slate-400 focus:outline-hidden transition-all duration-200"
                           aria-label="Cari fitur dan informasi">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button type="button" 
                            id="search-clear-btn" 
                            class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 hidden text-xs font-bold"
                            aria-label="Hapus pencarian">
                        ✕
                    </button>
                </div>

                <!-- Login / Dashboard Button -->
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-[13px] font-bold tracking-wide uppercase shadow-sm hover:shadow-md transition-all duration-200 focus:outline-hidden">
                        <span>Dashboard</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-[13px] font-bold tracking-wide uppercase shadow-sm hover:shadow-md transition-all duration-200 focus:outline-hidden">
                        <span>Login</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Toggle Button -->
            <div class="flex items-center gap-2 xl:hidden">
                <button type="button" 
                        id="mobile-search-toggle" 
                        class="p-2 text-slate-600 hover:text-[#0F2F5F] focus:outline-hidden"
                        aria-label="Cari">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <button type="button" 
                        id="hamburger-btn" 
                        class="p-2 rounded-lg text-slate-700 hover:text-[#0F2F5F] hover:bg-slate-100 focus:outline-hidden focus:ring-2 focus:ring-[#155EAA]" 
                        aria-label="Buka menu navigasi"
                        aria-expanded="false"
                        aria-controls="mobile-drawer">
                    <svg id="hamburger-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="hamburger-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Search Dropdown Bar (Collapsible on mobile) -->
        <div id="mobile-search-bar" class="hidden pb-3 lg:hidden">
            <div class="relative w-full">
                <input type="search" 
                       id="mobile-site-search-input"
                       placeholder="Cari info & layanan SMKN 7 Jember..." 
                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-100 rounded-lg border border-slate-200 focus:border-[#155EAA] focus:bg-white text-slate-800 placeholder-slate-400 focus:outline-hidden">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Overlay & Menu -->
    <div id="mobile-drawer" class="hidden xl:hidden border-t border-slate-200 bg-white shadow-2xl transition-all duration-300">
        <div class="px-4 pt-3 pb-6 space-y-1 divide-y divide-slate-100">
            <div class="space-y-1 pb-2">
                <a href="{{ $navbarHomeHref }}" class="mobile-nav-link block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50 hover:text-[#155EAA]">
                    Beranda
                </a>

                <!-- Mobile Accordion: Karir Jepang -->
                <div class="mobile-accordion">
                    <button type="button" class="mobile-accordion-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        <span>Karir Jepang</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="mobile-accordion-content hidden pl-6 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('karier-jepang.index') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Peluang Kerja & Magang</a>
                        <a href="{{ $navbarSectionHref('karier') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Pelatihan Bahasa & Budaya</a>
                        <a href="{{ $navbarSectionHref('akses') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Panduan Dokumen Keberangkatan</a>
                    </div>
                </div>

                <!-- Mobile Accordion: Konseling & Edukasi -->
                <div class="mobile-accordion">
                    <button type="button" class="mobile-accordion-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        <span>Konseling & Edukasi</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="mobile-accordion-content hidden pl-6 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ $navbarSectionHref('konseling') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Konseling Digital</a>
                        <a href="{{ $navbarSectionHref('akses') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Modul Edukasi Masa Depan</a>
                    </div>
                </div>

                <!-- Mobile Accordion: Alumni -->
                <div class="mobile-accordion">
                    <button type="button" class="mobile-accordion-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        <span>Alumni</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="mobile-accordion-content hidden pl-6 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ $navbarSectionHref('alumni') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Inspirasi Alumni</a>
                        <a href="{{ Auth::check() && Auth::user()->hasRole('alumni') ? route('dashboard.tracer-study.index') : (Auth::check() && Auth::user()->hasRole('admin_sekolah') ? route('dashboard.admin.tracer-study.index') : (Auth::check() && Auth::user()->hasRole('kepala_sekolah') ? route('dashboard.kepala-sekolah.tracer-study') : route('login'))) }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Tracer Study Global</a>
                    </div>
                </div>

                <!-- Mobile Accordion: Informasi -->
                <div class="mobile-accordion">
                    <button type="button" class="mobile-accordion-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        <span>Informasi</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="mobile-accordion-content hidden pl-6 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ $navbarSectionHref('pengumuman') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Pengumuman Terbaru</a>
                        <a href="{{ $navbarSectionHref('galeri') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Galeri Kegiatan</a>
                    </div>
                </div>

                <div class="mobile-accordion">
                    <button type="button" class="mobile-accordion-btn w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-800 hover:bg-slate-50">
                        <span>Layanan</span>
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="mobile-accordion-content hidden pl-6 pr-2 py-1 space-y-1 text-xs">
                        <button type="button" onclick="window.openSimulationModal()" class="w-full text-left py-2 text-slate-600 hover:text-[#155EAA]">Simulasi Finansial</button>
                        <a href="{{ $navbarSectionHref('akses') }}" class="mobile-nav-link block py-2 text-slate-600 hover:text-[#155EAA]">Kolaborasi & Bantuan Sosial</a>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                @auth
                    <a href="{{ route('dashboard') }}" 
                       class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-[#0F2F5F] text-white text-xs font-bold uppercase tracking-wider shadow-sm">
                        <span>Buka Dashboard {{ Auth::user()->getRoleDisplayName() }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-[#0F2F5F] text-white text-xs font-bold uppercase tracking-wider shadow-sm">
                        <span>Masuk ke Portal Login</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
