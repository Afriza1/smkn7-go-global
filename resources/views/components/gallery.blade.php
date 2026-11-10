<section id="galeri" class="py-12 md:py-14 bg-white border-b border-slate-200/80" aria-labelledby="gallery-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-5">
            <div>
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#166534] mb-2">Dokumentasi Visual</span>
                <h2 id="gallery-heading" class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] tracking-tight">Galeri Kegiatan & Momen Bersejarah</h2>
                <p class="mt-2 text-sm text-slate-600 max-w-2xl">Dokumentasi tahapan seleksi dan pembekalan Program Go Global SMKN 7 Jember.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2" id="gallery-filter-tabs">
                <button type="button" onclick="window.filterGallery('all')" class="gallery-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-[#0F2F5F] text-white active" data-filter="all">Semua Foto</button>
                <button type="button" onclick="window.filterGallery('seleksi')" class="gallery-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700" data-filter="seleksi">Seleksi</button>
                <button type="button" onclick="window.filterGallery('kesehatan')" class="gallery-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700" data-filter="kesehatan">Kesehatan</button>
                <button type="button" onclick="window.filterGallery('pembekalan')" class="gallery-filter-btn px-3.5 py-1.5 rounded-full text-xs font-bold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700" data-filter="pembekalan">Pembekalan</button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="gallery-grid">
            <div class="gallery-item group relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer bg-slate-100" data-category="seleksi" onclick="window.openGalleryLightbox(0)">
                <div class="h-40 sm:h-44 relative overflow-hidden bg-slate-100"><img src="{{ asset('images/test-fisik.jpg') }}" alt="Tes Fisik Peserta Program Go Global" class="h-full w-full object-contain" loading="lazy"><div class="gallery-overlay absolute inset-0 bg-[#0F2F5F]/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><span class="px-4 py-2 rounded-xl bg-white text-[#0F2F5F] text-xs font-bold shadow-lg">Pratinjau Foto</span></div></div>
                <div class="p-4 bg-white"><div class="flex items-center justify-between text-xs text-slate-500 mb-1"><span>Tahap Seleksi</span><span>Go Global</span></div><h3 class="text-sm font-bold text-slate-900 mb-1">Tes Fisik Peserta</h3><p class="text-xs leading-relaxed text-slate-600">Kegiatan tes fisik peserta sebagai bagian dari tahapan seleksi Program Go Global.</p></div>
            </div>

            <div class="gallery-item group relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer bg-slate-100" data-category="seleksi" onclick="window.openGalleryLightbox(1)">
                <div class="h-40 sm:h-44 relative overflow-hidden bg-slate-100"><img src="{{ asset('images/psikotes.jpg') }}" alt="Psikotes peserta Program Go Global" class="h-full w-full object-contain" loading="lazy"><div class="gallery-overlay absolute inset-0 bg-[#0F2F5F]/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><span class="px-4 py-2 rounded-xl bg-white text-[#0F2F5F] text-xs font-bold shadow-lg">Pratinjau Foto</span></div></div>
                <div class="p-4 bg-white"><div class="flex items-center justify-between text-xs text-slate-500 mb-1"><span>Tahap Seleksi</span><span>Go Global</span></div><h3 class="text-sm font-bold text-slate-900 mb-1">Psikotes Peserta</h3><p class="text-xs leading-relaxed text-slate-600">Pelaksanaan tes psikologi untuk mengenali kesiapan dan potensi peserta dalam mengikuti Program Go Global.</p></div>
            </div>

            <div class="gallery-item group relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer bg-slate-100" data-category="kesehatan" onclick="window.openGalleryLightbox(2)">
                <div class="h-40 sm:h-44 relative overflow-hidden bg-slate-100"><img src="{{ asset('images/mcu.jpg') }}" alt="Pemeriksaan kesehatan atau Medical Check Up peserta" class="h-full w-full object-contain" loading="lazy"><div class="gallery-overlay absolute inset-0 bg-[#0F2F5F]/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><span class="px-4 py-2 rounded-xl bg-white text-[#0F2F5F] text-xs font-bold shadow-lg">Pratinjau Foto</span></div></div>
                <div class="p-4 bg-white"><div class="flex items-center justify-between text-xs text-slate-500 mb-1"><span>Kesehatan</span><span>Go Global</span></div><h3 class="text-sm font-bold text-slate-900 mb-1">Pemeriksaan Kesehatan (MCU)</h3><p class="text-xs leading-relaxed text-slate-600">Pemeriksaan kesehatan atau Medical Check Up (MCU) sebagai bagian dari persiapan peserta mengikuti program.</p></div>
            </div>

            <div class="gallery-item group relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer bg-slate-100" data-category="pembekalan" onclick="window.openGalleryLightbox(3)">
                <div class="h-40 sm:h-44 relative overflow-hidden bg-slate-100"><img src="{{ asset('images/kewiraan.jpg') }}" alt="Pembekalan kewiraan peserta Program Go Global" class="h-full w-full object-contain" loading="lazy"><div class="gallery-overlay absolute inset-0 bg-[#0F2F5F]/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><span class="px-4 py-2 rounded-xl bg-white text-[#0F2F5F] text-xs font-bold shadow-lg">Pratinjau Foto</span></div></div>
                <div class="p-4 bg-white"><div class="flex items-center justify-between text-xs text-slate-500 mb-1"><span>Pembekalan</span><span>Go Global</span></div><h3 class="text-sm font-bold text-slate-900 mb-1">Pembekalan Kewiraan</h3><p class="text-xs leading-relaxed text-slate-600">Kegiatan penguatan kewiraan untuk membangun karakter, kedisiplinan, dan wawasan kebangsaan peserta.</p></div>
            </div>

            <div class="gallery-item group relative rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-lg transition-all duration-300 cursor-pointer bg-slate-100" data-category="pembekalan" onclick="window.openGalleryLightbox(4)">
                <div class="h-40 sm:h-44 relative overflow-hidden bg-slate-100"><img src="{{ asset('images/penguatan-bahasa-jepang.jpg') }}" alt="Penguatan Bahasa Jepang bagi peserta Program Go Global" class="h-full w-full object-contain" loading="lazy"><div class="gallery-overlay absolute inset-0 bg-[#0F2F5F]/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"><span class="px-4 py-2 rounded-xl bg-white text-[#0F2F5F] text-xs font-bold shadow-lg">Pratinjau Foto</span></div></div>
                <div class="p-4 bg-white"><div class="flex items-center justify-between text-xs text-slate-500 mb-1"><span>Pembekalan</span><span>Go Global</span></div><h3 class="text-sm font-bold text-slate-900 mb-1">Penguatan Bahasa Jepang</h3><p class="text-xs leading-relaxed text-slate-600">Kegiatan pembelajaran dan penguatan kemampuan bahasa Jepang sebagai bekal komunikasi peserta dalam kehidupan dan lingkungan kerja di Jepang.</p></div>
            </div>
        </div>
    </div>
</section>
