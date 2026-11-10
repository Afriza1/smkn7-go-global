<section id="pengumuman" class="py-12 md:py-14 bg-[#F8FAFC]" aria-labelledby="announcements-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
            <div>
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#166534] mb-2">
                    Kanal Informasi Resmi
                </span>
                <h2 id="announcements-heading" class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] tracking-tight">
                    Pengumuman & Agenda Terbaru
                </h2>
                <p class="mt-2 text-sm text-slate-600 max-w-xl">
                    Informasi terpercaya seputar seleksi keberangkatan, jadwal diklat bahasa, sosialisasi program, dan kegiatan sekolah.
                </p>
            </div>
            <div class="hidden">
                <button type="button" disabled title="Halaman daftar pengumuman lengkap belum tersedia."
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-400 shadow-xs cursor-not-allowed">
                    <span>Lihat Semua Pengumuman</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5 items-stretch" id="announcements-grid">
            <!-- Pengumuman terbaru sebagai konten unggulan -->
            <article class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 flex flex-col group"
                     data-searchable="seleksi program magang industri jepang pendaftaran 2026 manufaktur pertanian">
                <div class="min-h-40 sm:min-h-48 bg-gradient-to-br from-[#0F2F5F] via-[#155EAA] to-[#166534] flex items-center justify-center relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-16 w-56 h-56 rounded-full border-[24px] border-white/10" aria-hidden="true"></div>
                    <div class="relative z-10 flex flex-col items-center gap-2 text-white text-center p-6">
                        <span class="w-14 h-14 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-[#FBBF24]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider text-white/90">Informasi Program Go Global</span>
                    </div>
                </div>
                <div class="flex flex-1 flex-col p-5 sm:p-6">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-[#155EAA]">Seleksi & Karir</span>
                        <time datetime="2026-11-04" class="text-xs font-medium text-slate-500">04 November 2026</time>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-[#155EAA] transition-colors leading-snug mb-2">
                        Pembukaan Seleksi Program Magang Industri Jepang Batch 2026/2027
                    </h3>
                    <p class="text-sm text-slate-600 leading-relaxed line-clamp-3">
                        SMKN 7 Jember membuka pendaftaran seleksi awal program magang kerja ke Jepang untuk bidang Teknik Manufaktur, Mekatronika, dan Pertanian Modern. Dapatkan kesempatan berkarier dengan fasilitas penuh dan pendampingan resmi sekolah.
                    </p>
                    <div class="mt-auto pt-4 flex items-center justify-between gap-3">
                        <span class="text-[11px] font-medium text-slate-500">Kuota Terbuka: 45 Peserta</span>
                        <button type="button" onclick="window.openAnnouncementModal(1)" class="shrink-0 text-xs font-bold text-[#155EAA] hover:text-[#0F2F5F] flex items-center gap-1 transition-colors cursor-pointer">
                            <span>Baca Selengkapnya</span>
                        </button>
                    </div>
                </div>
            </article>

            <!-- Tiga pengumuman pendukung mengikuti urutan sumber yang tersedia -->
            <div class="flex flex-col gap-3">
                <article class="min-h-0 bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 group"
                         data-searchable="pelatihan intensif bahasa jepang n4 n5 jlpt etika budaya kerja kaizen 5s">
                    <button type="button" onclick="window.openAnnouncementModal(2)" class="w-full h-full text-left flex items-center gap-3 sm:gap-4 p-3.5 sm:p-4 focus-visible:outline-2 focus-visible:outline-[#155EAA]" aria-label="Baca selengkapnya: Pelatihan Intensif Bahasa Jepang N4/N5 dan Etika Budaya Kerja Kaizen">
                        <span class="w-16 h-16 sm:w-[76px] sm:h-[76px] shrink-0 rounded-xl bg-emerald-50 text-[#166534] flex items-center justify-center border border-emerald-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-[#166534]">Diklat Vokasi</span>
                                <time datetime="2026-11-01" class="text-[11px] text-slate-500">01 November 2026</time>
                            </span>
                            <span class="block text-sm font-bold text-slate-900 group-hover:text-[#166534] transition-colors leading-snug line-clamp-2">Pelatihan Intensif Bahasa Jepang (N4/N5) & Etika Budaya Kerja Kaizen</span>
                            <span class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-bold text-[#166534]">Baca Selengkapnya</span>
                        </span>
                    </button>
                </article>

                <article class="min-h-0 bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 group"
                         data-searchable="sosialisasi standar kebugaran mcu medical check up kesehatan konseling bk">
                    <button type="button" onclick="window.openAnnouncementModal(3)" class="w-full h-full text-left flex items-center gap-3 sm:gap-4 p-3.5 sm:p-4 focus-visible:outline-2 focus-visible:outline-[#155EAA]" aria-label="Baca selengkapnya: Sosialisasi Standar Kebugaran Fisik dan Skrining MCU">
                        <span class="w-16 h-16 sm:w-[76px] sm:h-[76px] shrink-0 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center border border-amber-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-[#d97706]">Kesehatan & MCU</span>
                                <time datetime="2026-10-28" class="text-[11px] text-slate-500">28 Oktober 2026</time>
                            </span>
                            <span class="block text-sm font-bold text-slate-900 group-hover:text-amber-800 transition-colors leading-snug line-clamp-2">Sosialisasi Standar Kebugaran Fisik & Skrining Medical Check-Up (MCU)</span>
                            <span class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-bold text-amber-700">Baca Selengkapnya</span>
                        </span>
                    </button>
                </article>

                <article class="min-h-0 bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 group"
                         data-searchable="kunjungan delegasi mou jepang tokyo kerja sama industri kemitraan">
                    <button type="button" onclick="window.openAnnouncementModal(4)" class="w-full h-full text-left flex items-center gap-3 sm:gap-4 p-3.5 sm:p-4 focus-visible:outline-2 focus-visible:outline-[#155EAA]" aria-label="Baca selengkapnya: Kunjungan Delegasi Asosiasi Kerja Sama Industri Tokyo">
                        <span class="w-16 h-16 sm:w-[76px] sm:h-[76px] shrink-0 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center border border-purple-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex flex-wrap items-center gap-x-2 gap-y-1 mb-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">Kemitraan Global</span>
                                <time datetime="2026-10-24" class="text-[11px] text-slate-500">24 Oktober 2026</time>
                            </span>
                            <span class="block text-sm font-bold text-slate-900 group-hover:text-purple-700 transition-colors leading-snug line-clamp-2">Kunjungan Delegasi Asosiasi Kerja Sama Industri Tokyo ke SMKN 7 Jember</span>
                            <span class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-bold text-purple-700">Baca Selengkapnya</span>
                        </span>
                    </button>
                </article>
            </div>
        </div>
    </div>
</section>
