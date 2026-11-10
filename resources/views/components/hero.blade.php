<section id="beranda" class="hero-wrapper min-h-[540px] lg:min-h-[600px] xl:min-h-[640px] flex items-center relative overflow-hidden" aria-label="Hero SMKN 7 Jember Go Global">
    
    <!-- Hero Background Image: 4 Students, Mt Fuji, Torii Gate, Pagoda, Sakura, Shinkansen -->
    <img src="{{ asset('images/hero-go-global-vivid.jpg') }}" 
         alt="Empat siswa berprestasi SMKN 7 Jember dengan latar Gunung Fuji, gerbang Torii, pagoda tradisional, bunga sakura, dan kereta Shinkansen Jepang" 
         class="hero-bg-img"
         fetchpriority="high">

    <!-- Gradient Overlay: Rich Vivid Forest Green Contrast on Left, Soft Fade for Bright Well-Lit Figures on Right -->
    <div class="hero-gradient-overlay" aria-hidden="true"></div>

    <!-- Hero Content Container -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-9 lg:py-10 w-full">
        <div class="max-w-lg lg:max-w-xl text-left">
            
            <!-- 1. Transparent Official Logo -->
            <div class="mb-2.5 sm:mb-3">
                <img src="{{ asset('images/smkn-7-go-global.png') }}" 
                     alt="Logo Resmi SMKN 7 Jember Go Global" 
                     class="h-11 sm:h-13 md:h-15 lg:h-17 w-auto object-contain filter drop-shadow-[0_4px_10px_rgba(0,0,0,0.35)]">
            </div>

            <!-- 2. Tagline Pill -->
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-black/35 border border-[#FDE047]/40 backdrop-blur-md mb-2 sm:mb-2.5 shadow-xs">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FDE047] animate-pulse"></span>
                <span class="text-[11px] sm:text-xs font-extrabold tracking-wider text-[#FDE047] uppercase drop-shadow-xs">
                    Berkarakter, Berkarya, Berprestasi
                </span>
            </div>

            <!-- 3. Headline with Poster-Inspired Typography & Dynamic Swoosh -->
            <div class="mb-2">
                <h1 class="tracking-tight">
                    <span class="block text-2xl sm:text-3xl lg:text-[2.6rem] font-black italic tracking-tight text-white drop-shadow-[0_3px_8px_rgba(0,0,0,0.6)] leading-none">
                        SMKN 7 JEMBER
                    </span>
                    <span class="block text-3xl sm:text-4xl lg:text-[3.2rem] font-black italic tracking-wide text-transparent bg-clip-text bg-gradient-to-r from-[#FEF08A] via-[#FDE047] to-[#F59E0B] drop-shadow-[0_4px_14px_rgba(0,0,0,0.7)] leading-tight mt-0.5">
                        GO GLOBAL
                    </span>
                </h1>
                <!-- Dynamic Curved Swoosh Underline matching the poster reference -->
                <div class="w-40 sm:w-48 lg:w-56 mt-0.5 mb-1.5">
                    <svg class="w-full h-2.5 sm:h-3 text-[#FDE047] drop-shadow-[0_2px_4px_rgba(0,0,0,0.4)]" viewBox="0 0 300 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 10C60 2 180 0 298 9C210 5 110 5 2 10Z" fill="currentColor"/>
                    </svg>
                </div>
            </div>

            <!-- 4. Supporting Slogan and Quote -->
            <p class="text-white text-sm sm:text-base lg:text-lg font-semibold leading-snug mb-1 max-w-xl drop-shadow-[0_2px_4px_rgba(0,0,0,0.5)]">
                Menuju Masa Depan Cerah dengan Karir Internasional Bersama SMKN 7 Jember
            </p>
            <p class="text-emerald-100/90 text-xs italic font-medium leading-tight mb-3.5 max-w-lg drop-shadow-xs">
                &ldquo;Menjunjung tinggi nilai-nilai karakter, berkarya untuk masa depan, meraih prestasi di tingkat global.&rdquo;
            </p>

            <!-- 5. Four Feature Pillars / Badges matching Poster -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 sm:gap-2 mb-4 max-w-lg">
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-black/25 border border-white/15 backdrop-blur-xs hover:border-[#FDE047]/40 transition-colors">
                    <svg class="w-3.5 h-3.5 text-[#FDE047] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-[11px] sm:text-xs font-bold text-white tracking-wide">Berkarakter</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-black/25 border border-white/15 backdrop-blur-xs hover:border-[#FDE047]/40 transition-colors">
                    <svg class="w-3.5 h-3.5 text-[#FDE047] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <span class="text-[11px] sm:text-xs font-bold text-white tracking-wide">Berkarya</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-black/25 border border-white/15 backdrop-blur-xs hover:border-[#FDE047]/40 transition-colors">
                    <svg class="w-3.5 h-3.5 text-[#FDE047] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                    <span class="text-[11px] sm:text-xs font-bold text-white tracking-wide">Berprestasi</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-black/25 border border-white/15 backdrop-blur-xs hover:border-[#FDE047]/40 transition-colors">
                    <svg class="w-3.5 h-3.5 text-[#FDE047] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-[11px] sm:text-xs font-bold text-[#FDE047] tracking-wide">Go Global</span>
                </div>
            </div>

            <!-- 6. Action CTA Buttons -->
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <!-- Button: Jelajahi Program -->
                <a href="#karier" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg bg-gradient-to-r from-[#FDE047] to-[#F59E0B] hover:from-[#FEF08A] hover:to-[#FBBF24] text-slate-900 text-xs sm:text-sm font-extrabold tracking-wide shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-hidden focus:ring-2 focus:ring-[#FDE047] focus:ring-offset-2 focus:ring-offset-[#14532d]">
                    <span>Jelajahi Program</span>
                    <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>

                <!-- Button: Pelajari Lebih Lanjut -->
                <a href="#akses" 
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 sm:px-6 sm:py-3 rounded-lg bg-white/15 hover:bg-white/25 text-white border border-white/30 backdrop-blur-xs text-xs sm:text-sm font-bold tracking-wide transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-hidden">
                    <span>Pelajari Lebih Lanjut</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </a>
            </div>

            <!-- 7. Key Trust Badges -->
            <div class="pt-2.5 border-t border-white/15 flex flex-wrap items-center gap-x-5 gap-y-1.5 text-[11px] sm:text-xs text-emerald-100/90 font-medium">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#FDE047] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Program Magang &amp; Tokutei Ginou Jepang
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#FDE047] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Diklat Bahasa &amp; Pendampingan Dokumen
                </span>
            </div>

        </div>
    </div>
</section>
