<!-- ==========================================
     MODALS COMPONENT
     Interactive popups for frontend interactions
     ========================================== -->

<!-- 1. MODAL LOGIN (Placeholder Notification) -->
<div id="login-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="login-modal-title">
    <div class="modal-backdrop fixed inset-0" onclick="window.closeLoginModal()"></div>
    <div class="relative bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 modal-content-anim">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#155EAA] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </div>
                <div>
                    <h3 id="login-modal-title" class="text-base font-bold text-[#0F2F5F]">Portal Masuk Go Global</h3>
                    <p class="text-[11px] text-slate-500">SMKN 7 Jember</p>
                </div>
            </div>
            <button type="button" onclick="window.closeLoginModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Tutup modal">
                ✕
            </button>
        </div>

        <div class="py-2 space-y-4">
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs leading-relaxed flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong class="font-bold block mb-1">Tahap Pengembangan Frontend</strong>
                    Modul autentikasi, enkripsi password, dan hak akses multi-role (Siswa, Alumni, Guru BK, Orang Tua) akan dihubungkan secara menyeluruh pada fase backend selanjutnya.
                </div>
            </div>

            <!-- Role Selector Preview -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Simulasi Pilihan Peran:</label>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="p-3 rounded-xl border border-blue-200 bg-blue-50/50 text-[#155EAA] font-bold text-center">
                        🎓 Siswa Aktif
                    </div>
                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium text-center">
                        💼 Alumni Jepang
                    </div>
                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium text-center">
                        👨‍👩‍👧 Orang Tua / Wali
                    </div>
                    <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium text-center">
                        🏫 Guru & Pembina
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
            <a href="{{ route('register') }}" class="text-xs font-semibold text-[#155EAA] hover:underline">
                Daftar Akun Baru &rarr;
            </a>
            <div class="flex items-center gap-2">
                <button type="button" 
                        onclick="window.closeLoginModal()" 
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                    Tutup
                </button>
                <a href="{{ route('login') }}" 
                   class="px-5 py-2 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-sm">
                    Buka Halaman Login &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 2. MODAL KONSELING (Interactive Request Form) -->
<div id="counseling-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="counseling-modal-title">
    <div class="modal-backdrop fixed inset-0" onclick="window.closeCounselingModal()"></div>
    <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 modal-content-anim max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#166534] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div>
                    <h3 id="counseling-modal-title" class="text-base font-bold text-[#0F2F5F]">Formulir Bimbingan & Konseling</h3>
                    <p class="text-[11px] text-slate-500">Pendampingan Karir Go Global SMKN 7 Jember</p>
                </div>
            </div>
            <button type="button" onclick="window.closeCounselingModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Tutup modal">
                ✕
            </button>
        </div>

        <form id="counseling-form" onsubmit="window.handleCounselingSubmit(event)" class="space-y-4 py-2">
            <div>
                <label for="counseling-name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Siswa / Alumni *</label>
                <input type="text" id="counseling-name" required placeholder="Contoh: Muhammad Rizki" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="counseling-class" class="block text-xs font-bold text-slate-700 mb-1">Kelas / Angkatan *</label>
                    <input type="text" id="counseling-class" required placeholder="Contoh: XII TPM 1 / 2024" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden">
                </div>
                <div>
                    <label for="counseling-major" class="block text-xs font-bold text-slate-700 mb-1">Jurusan / Kompetensi</label>
                    <select id="counseling-major" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden bg-white">
                        <option value="Teknik Kendaraan Ringan">Teknik Kendaraan Ringan</option>
                        <option value="Teknik Mesin Sepeda Motor">Teknik Mesin Sepeda Motor</option>
                        <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan</option>
                        <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                        <option value="Kecantikan Kulit dan Rambut">Kecantikan Kulit dan Rambut</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="counseling-service" class="block text-xs font-bold text-slate-700 mb-1">Topik Bimbingan yang Diperlukan *</label>
                <select id="counseling-service" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden bg-white">
                    <option value="Konsultasi Minat & Program Karir">Konsultasi Minat & Pemilihan Program Karir</option>
                    <option value="Bimbingan Edukasi Persiapan MCU">Bimbingan Pola Hidup Sehat & Persiapan MCU</option>
                    <option value="Kesiapan Mental & Adaptasi Budaya">Kesiapan Mental & Mengatasi Stres Adaptasi</option>
                    <option value="Diskusi Bersama Orang Tua & BK">Konseling Bersama Orang Tua / Wali Murid</option>
                </select>
            </div>

            <div>
                <label for="counseling-message" class="block text-xs font-bold text-slate-700 mb-1">Pertanyaan atau Hal yang Ingin Didiskusikan</label>
                <textarea id="counseling-message" rows="3" placeholder="Tuliskan pertanyaan atau kendala yang dihadapi secara singkat..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden"></textarea>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-500 flex items-center gap-2">
                <span>🔒</span>
                <span>Data tersimpan privat dan hanya dapat diakses oleh tim Guru BK SMKN 7 Jember.</span>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="window.closeCounselingModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold shadow-sm transition-all cursor-pointer">
                    Kirim Permohonan Konseling
                </button>
            </div>
        </form>

        <!-- Success Feedback (hidden initially) -->
        <div id="counseling-success" class="hidden text-center py-6 space-y-3">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-[#166534] mx-auto flex items-center justify-center text-xl font-bold">
                ✓
            </div>
            <h4 class="text-base font-bold text-slate-900">Permohonan Berhasil Dikirim!</h4>
            <p class="text-xs text-slate-600 max-w-sm mx-auto leading-relaxed">
                Terima kasih. Permintaan bimbingan konseling Anda telah dicatat oleh sistem percontohan. Tim BK SMKN 7 Jember akan mengonfirmasi jadwal temu melalui pesan WhatsApp/Wali Kelas.
            </p>
            <button type="button" onclick="window.closeCounselingModal()" class="mt-4 px-5 py-2.5 rounded-xl bg-[#0F2F5F] text-white text-xs font-bold">
                Tutup Jendela
            </button>
        </div>
    </div>
</div>

<!-- 3. MODAL SIMULASI FINANSIAL (Interactive Japan Income Calculator) -->
<div id="simulation-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="simulation-modal-title">
    <div class="modal-backdrop fixed inset-0" onclick="window.closeSimulationModal()"></div>
    <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 modal-content-anim max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 id="simulation-modal-title" class="text-base font-bold text-[#0F2F5F]">Kalkulator Simulasi Finansial Jepang</h3>
                    <p class="text-[11px] text-slate-500">Estimasi Pendapatan, Pengeluaran & Potensi Tabungan</p>
                </div>
            </div>
            <button type="button" onclick="window.closeSimulationModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Tutup modal">
                ✕
            </button>
        </div>

        <div class="space-y-4 py-2">
            <!-- Controls -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="sim-program" class="block text-xs font-bold text-slate-700 mb-1">Skema Program</label>
                    <select id="sim-program" onchange="window.updateFinancialSimulation()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden bg-white">
                        <option value="tokutei">Tokutei Ginou (SSW) - Skill Penuh</option>
                        <option value="magang">Magang Kerja (Ginou Jisshusei)</option>
                    </select>
                </div>
                <div>
                    <label for="sim-region" class="block text-xs font-bold text-slate-700 mb-1">Prefektur / Wilayah Kerja</label>
                    <select id="sim-region" onchange="window.updateFinancialSimulation()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden bg-white">
                        <option value="shizuoka">Shizuoka / Chubu (Manufaktur & Mesin)</option>
                        <option value="aichi">Aichi / Nagoya (Otomotif & Robotik)</option>
                        <option value="tokyo">Tokyo / Kanto (Metropolitan)</option>
                        <option value="osaka">Osaka / Kansai (Industri & Pangan)</option>
                    </select>
                </div>
            </div>

            <!-- Calculation Output Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/80">
                    <span class="block text-[11px] font-semibold text-blue-900">Gaji Pokok Kotor (Gross)</span>
                    <strong class="block text-sm sm:text-base font-extrabold text-[#155EAA] mt-1" id="sim-out-gross">
                        ¥205,000
                    </strong>
                    <span class="text-[10px] text-blue-700" id="sim-out-gross-idr">&plusmn; Rp 21.500.000 / bln</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/80">
                    <span class="block text-[11px] font-semibold text-rose-900">Biaya Hidup & Pajak/Asuransi</span>
                    <strong class="block text-sm sm:text-base font-extrabold text-rose-700 mt-1" id="sim-out-expense">
                        ¥75,000
                    </strong>
                    <span class="text-[10px] text-rose-600" id="sim-out-expense-idr">&plusmn; Rp 7.800.000 / bln</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80">
                    <span class="block text-[11px] font-semibold text-emerald-900">Potensi Tabungan Bersih</span>
                    <strong class="block text-sm sm:text-base font-extrabold text-[#166534] mt-1" id="sim-out-net">
                        ¥130,000
                    </strong>
                    <span class="text-[10px] text-emerald-700 font-bold" id="sim-out-net-idr">&plusmn; Rp 13.700.000 / bln</span>
                </div>
            </div>

            <!-- Detailed Breakdown Accordion / List -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-2">
                <strong class="font-bold text-slate-800 block text-[11px] uppercase tracking-wider mb-2">Komponen Biaya Hidup di Jepang Termasuk:</strong>
                <div class="flex items-center justify-between text-slate-600">
                    <span>• Sewa Asrama / Apartemen Perusahaan (subsidi):</span>
                    <span class="font-semibold text-slate-800">¥25,000 - ¥35,000</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>• Asuransi Kesehatan Nasional & Pensiun (Kousei Nenkin):</span>
                    <span class="font-semibold text-slate-800">¥25,000 - ¥30,000</span>
                </div>
                <div class="flex items-center justify-between text-slate-600">
                    <span>• Biaya Makan, Listrik, Air & Internet Pribadi:</span>
                    <span class="font-semibold text-slate-800">¥20,000 - ¥25,000</span>
                </div>
                <div class="pt-2 border-t border-slate-200 text-[10px] text-slate-500 italic">
                    * Dana Pensiun (Nenkin) dapat dicairkan kembali (Lump-sum Withdrawal) saat selesai kontrak dan kembali ke Indonesia.
                </div>
            </div>

            <div class="text-[11px] text-slate-500">
                Kurs simulasi: 1 JPY &asymp; Rp 105 (dapat berubah mengikuti kurs valuta asing harian).
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
            <button type="button" onclick="window.openCounselingModal(); window.closeSimulationModal();" class="text-xs font-bold text-[#155EAA] hover:underline">
                Konsultasikan Skema Pembiayaan →
            </button>
            <button type="button" onclick="window.closeSimulationModal()" class="px-5 py-2.5 rounded-xl bg-[#0F2F5F] text-white text-xs font-bold">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- 4. MODAL DETAIL PENGUMUMAN (Dynamic Content Reader) -->
<div id="announcement-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="announcement-modal-title">
    <div class="modal-backdrop fixed inset-0" onclick="window.closeAnnouncementModal()"></div>
    <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 modal-content-anim max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-[#155EAA] mb-1" id="modal-announcement-category">
                    Pengumuman Resmi
                </span>
                <h3 id="announcement-modal-title" class="text-base sm:text-lg font-bold text-[#0F2F5F] leading-snug">
                    Judul Pengumuman
                </h3>
                <span class="text-[11px] text-slate-400" id="modal-announcement-date">Tanggal Publikasi</span>
            </div>
            <button type="button" onclick="window.closeAnnouncementModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Tutup modal">
                ✕
            </button>
        </div>

        <div class="py-2 text-xs sm:text-sm text-slate-600 leading-relaxed space-y-3" id="modal-announcement-body">
            <p>Memuat isi pengumuman...</p>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">SMKN 7 Jember Go Global</span>
            <button type="button" onclick="window.closeAnnouncementModal()" class="px-5 py-2.5 rounded-xl bg-[#0F2F5F] text-white text-xs font-bold">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- 5. MODAL GALLERY LIGHTBOX -->
<div id="gallery-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md" role="dialog" aria-modal="true" aria-labelledby="lightbox-title">
    <div class="relative max-w-3xl w-full bg-slate-900 rounded-3xl overflow-hidden border border-slate-800 shadow-2xl modal-content-anim">
        <button type="button" onclick="window.closeGalleryLightbox()" class="absolute top-4 right-4 z-20 text-white/70 hover:text-white p-2 rounded-full bg-black/40 hover:bg-black/60 transition-colors" aria-label="Tutup preview">
            ✕
        </button>

        <!-- Preview Image Container -->
        <div class="h-72 sm:h-96 bg-slate-800 flex items-center justify-center relative overflow-hidden" id="lightbox-image-container">
            <img id="lightbox-image" src="" alt="" class="h-full w-full object-contain">
        </div>

        <!-- Lightbox Caption & Details -->
        <div class="p-6 bg-slate-900 border-t border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-white">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-[#FBBF24] text-[#0F2F5F] inline-block mb-2" id="lightbox-badge">Kategori</span>
                <h3 class="text-base font-bold text-white mb-1" id="lightbox-title">Judul Foto Dokumentasi</h3>
                <p class="text-xs text-slate-300 max-w-lg leading-relaxed" id="lightbox-desc">Deskripsi foto dokumentasi...</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.prevGalleryImage()" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors">
                    ← Sebelumnya
                </button>
                <button type="button" onclick="window.nextGalleryImage()" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors">
                    Selanjutnya →
                </button>
            </div>
        </div>
    </div>
</div>

<!-- 6. MODAL PROGRAM KARIR DETAIL -->
<div id="career-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="career-modal-title">
    <div class="modal-backdrop fixed inset-0" onclick="window.closeCareerProgramModal()"></div>
    <div class="relative bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 modal-content-anim max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#155EAA] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h3 id="career-modal-title" class="text-base font-bold text-[#0F2F5F]">Skema Karir Internasional Jepang</h3>
                    <p class="text-[11px] text-slate-500">Program Kemitraan SMKN 7 Jember Go Global</p>
                </div>
            </div>
            <button type="button" onclick="window.closeCareerProgramModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Tutup modal">
                ✕
            </button>
        </div>

        <div class="space-y-4 py-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
            <!-- Comparison Table -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-100 text-[#0F2F5F] font-bold border-b border-slate-200">
                            <th class="p-3">Aspek</th>
                            <th class="p-3">Tokutei Ginou (SSW)</th>
                            <th class="p-3">Magang (Ginou Jisshusei)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold text-slate-800">Status Pekerja</td>
                            <td class="p-3">Pekerja dengan status tinggal Specified Skilled Worker (i)</td>
                            <td class="p-3">Peserta Pemagang Teknis</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-800">Standar Upah</td>
                            <td class="p-3 font-semibold text-[#166534]">Setara atau lebih tinggi daripada pekerja Jepang untuk pekerjaan sebanding</td>
                            <td class="p-3 font-semibold text-[#155EAA]">Setara atau lebih tinggi daripada pekerja Jepang untuk pekerjaan sebanding</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-800">Syarat Bahasa</td>
                            <td class="p-3">Umumnya JLPT N4 atau JFT-Basic serta tes keterampilan sesuai bidang</td>
                            <td class="p-3">Persyaratan mengikuti ketentuan program dan bidang terkait</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-800">Batas Masa Tinggal / Program</td>
                            <td class="p-3">Masa tinggal total maksimal 5 tahun untuk SSW (i)</td>
                            <td class="p-3">Tahap (i), (ii), dan (iii); total dapat mencapai maksimal 5 tahun sesuai persyaratan tiap tahap</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
            <button type="button" onclick="window.closeCareerProgramModal(); window.openCareerCounselingModal();" class="text-xs font-bold text-[#166534] hover:underline flex items-center gap-1">
                <span>Daftar Konseling Pemilihan Skema</span>
                <span>→</span>
            </button>
            <button type="button" onclick="window.closeCareerProgramModal()" class="px-5 py-2.5 rounded-xl bg-[#0F2F5F] text-white text-xs font-bold">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal konsultasi khusus pemilihan skema karir Jepang -->
<div id="career-counseling-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="career-counseling-modal-title">
    <div class="modal-backdrop fixed inset-0" onclick="window.closeCareerCounselingModal()"></div>
    <div class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 z-10 modal-content-anim max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#166534] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6m15 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 id="career-counseling-modal-title" class="text-base font-bold text-[#0F2F5F]">Konsultasi Pemilihan Skema Karir Jepang</h3>
                    <p class="text-[11px] text-slate-500">Konsultasi pemilihan jalur Tokutei Ginou (SSW) atau Magang (Ginou Jisshusei).</p>
                </div>
            </div>
            <button type="button" onclick="window.closeCareerCounselingModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Tutup modal">×</button>
        </div>

        <form id="career-counseling-form" onsubmit="window.handleCareerCounselingSubmit(event)" class="space-y-4 py-2">
            <div>
                <label for="career-counseling-name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Siswa / Alumni *</label>
                <input type="text" id="career-counseling-name" required autocomplete="name" placeholder="Masukkan nama lengkap" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="career-counseling-class" class="block text-xs font-bold text-slate-700 mb-1">Kelas / Angkatan *</label>
                    <input type="text" id="career-counseling-class" required placeholder="Contoh: XII TPM 1 / 2024" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden">
                </div>
                <div>
                    <label for="career-counseling-major" class="block text-xs font-bold text-slate-700 mb-1">Jurusan / Kompetensi *</label>
                    <select id="career-counseling-major" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden bg-white">
                        <option value="" disabled selected>Pilih jurusan</option>
                        <option value="Teknik Kendaraan Ringan">Teknik Kendaraan Ringan</option>
                        <option value="Teknik Mesin Sepeda Motor">Teknik Mesin Sepeda Motor</option>
                        <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan</option>
                        <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                        <option value="Kecantikan Kulit dan Rambut">Kecantikan Kulit dan Rambut</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="career-counseling-scheme" class="block text-xs font-bold text-slate-700 mb-1">Skema yang Ingin Dikonsultasikan *</label>
                <select id="career-counseling-scheme" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden bg-white">
                    <option value="" disabled selected>Pilih skema</option>
                    <option value="Tokutei Ginou (SSW)">Tokutei Ginou (SSW)</option>
                    <option value="Magang (Ginou Jisshusei)">Magang (Ginou Jisshusei)</option>
                    <option value="Belum Menentukan">Belum Menentukan</option>
                </select>
            </div>

            <div>
                <label for="career-counseling-topic" class="block text-xs font-bold text-slate-700 mb-1">Topik Konsultasi *</label>
                <select id="career-counseling-topic" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden bg-white">
                    <option value="" disabled selected>Pilih topik konsultasi</option>
                    <option value="Konsultasi pemilihan skema kerja Jepang">Konsultasi pemilihan skema kerja Jepang</option>
                    <option value="Persyaratan dan kualifikasi program">Persyaratan dan kualifikasi program</option>
                    <option value="Persiapan bahasa Jepang dan keterampilan">Persiapan bahasa Jepang dan keterampilan</option>
                    <option value="Informasi bidang pekerjaan dan penempatan">Informasi bidang pekerjaan dan penempatan</option>
                    <option value="Biaya dan tahapan keberangkatan">Biaya dan tahapan keberangkatan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div>
                <label for="career-counseling-message" class="block text-xs font-bold text-slate-700 mb-1">Pertanyaan atau Hal yang Ingin Dikonsultasikan *</label>
                <textarea id="career-counseling-message" rows="3" required placeholder="Tuliskan pertanyaan tentang pemilihan skema karir Jepang..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-[#155EAA] focus:outline-hidden"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="window.closeCareerCounselingModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold shadow-sm transition-all cursor-pointer">Kirim Konsultasi</button>
            </div>
        </form>

        <div id="career-counseling-success" class="hidden text-center py-6 space-y-3">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-[#166534] mx-auto flex items-center justify-center text-xl font-bold">✓</div>
            <h4 class="text-base font-bold text-slate-900">Konsultasi berhasil dikirim</h4>
            <p class="text-xs text-slate-600 max-w-sm mx-auto leading-relaxed">Terima kasih. Permintaan konsultasi pemilihan skema Anda telah dicatat oleh sistem percontohan.</p>
            <button type="button" onclick="window.closeCareerCounselingModal()" class="mt-4 px-5 py-2.5 rounded-xl bg-[#0F2F5F] text-white text-xs font-bold">Tutup Jendela</button>
        </div>
    </div>
</div>
