<section id="akses" class="py-16 md:py-20 bg-white border-b border-slate-200/60" aria-labelledby="quick-access-heading">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#155EAA] mb-2">
                    Layanan & Fitur Unggulan
                </span>
                <h2 id="quick-access-heading" class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] tracking-tight">
                    Akses Cepat Platform Go Global
                </h2>
                <p class="mt-2 text-sm text-slate-600 max-w-2xl">
                    Pusat navigasi terpadu untuk mempermudah siswa, alumni, dan orang tua dalam mengakses informasi karir internasional, bimbingan, dan layanan sekolah.
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>10 Layanan Terintegrasi</span>
            </div>
        </div>

        <!-- 10 Feature Cards Grid (Responsive: 1 col on mobile, 2 on sm, 3 on md, 5 on xl) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 sm:gap-5" id="feature-cards-grid">
            
            <!-- 1. Lowongan Kerja ke Jepang (Pastel Sky) -->
            <a href="#karier" 
               class="feature-card tone-sky p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="lowongan kerja jepang magang ginou jisshusei tokutei karir industri peluang">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-sky-100/90 text-sky-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-[#155EAA] transition-colors leading-snug">
                        Lowongan Kerja ke Jepang
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Peluang resmi program Tokutei Ginou dan Magang Industri di berbagai prefektur Jepang.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-sky-200/60 flex items-center justify-between text-[11px] font-semibold text-sky-800">
                    <span>Lihat Program</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

            <!-- 2. Panduan Dokumen Keberangkatan (Pastel Emerald) -->
            <a href="#karier" 
               class="feature-card tone-emerald p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="panduan dokumen paspor coe visa kontrak legalitas administrasi keberangkatan">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-100/90 text-emerald-800 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-[#166534] transition-colors leading-snug">
                        Panduan Dokumen Keberangkatan
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Standar penyiapan paspor, Certificate of Eligibility (COE), kontrak kerja, dan visa kedutaan.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-emerald-200/60 flex items-center justify-between text-[11px] font-semibold text-emerald-800">
                    <span>Panduan Alur</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

            <!-- 3. Simulasi Finansial (Pastel Amber) -->
            <button type="button" 
                    onclick="window.openSimulationModal()" 
                    class="feature-card tone-amber p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group text-left w-full cursor-pointer"
                    data-searchable="simulasi finansial kalkulator gaji yen rupiah tabungan biaya hidup estimasi pendapatan">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-amber-100/90 text-amber-800 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-amber-800 transition-colors leading-snug">
                        Simulasi Finansial
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Kalkulator interaktif estimasi pendapatan, biaya hidup bulanan di Jepang, serta potensi tabungan.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-amber-200/60 flex items-center justify-between text-[11px] font-semibold text-amber-800">
                    <span>Buka Kalkulator</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </button>

            <!-- 4. Konseling Digital (Pastel Indigo) -->
            <a href="#konseling" 
               class="feature-card tone-indigo p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="konseling digital bimbingan konseling bk psikologis konsultasi intervensi mental">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-100/90 text-indigo-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-800 transition-colors leading-snug">
                        Konseling Digital
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Layanan konsultasi online terpadu untuk pendampingan minat, kesiapan mental, dan adaptasi budaya.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-indigo-200/60 flex items-center justify-between text-[11px] font-semibold text-indigo-800">
                    <span>Mulai Konseling</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

            <!-- 5. Modul Edukasi Masa Depan Cerah (Pastel Rose) -->
            <a href="#karier" 
               class="feature-card tone-rose p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="modul edukasi materi masa depan cerah bahasa jepang etika 5s kaizen hou-ren-so">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-rose-100/90 text-rose-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-rose-800 transition-colors leading-snug">
                        Modul Edukasi Masa Depan Cerah
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Materi pembelajaran mandiri budaya industri Jepang, prinsip Kaizen, 5S, dan dasar bahasa.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-rose-200/60 flex items-center justify-between text-[11px] font-semibold text-rose-800">
                    <span>Akses Modul</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

            <!-- 6. Alumni Global (Pastel Cyan) -->
            <a href="#alumni" 
               class="feature-card tone-cyan p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="alumni global jejaring komunitas jepang inspirasi cerita testimoni">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-cyan-100/90 text-cyan-800 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-cyan-800 transition-colors leading-snug">
                        Alumni Global
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Jejaring komunikasi dan cerita inspiratif lulusan yang sukses meniti karir profesional di Jepang.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-cyan-200/60 flex items-center justify-between text-[11px] font-semibold text-cyan-800">
                    <span>Lihat Kisah</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

            <!-- 7. Tracer Study (Pastel Purple) -->
            <a href="{{ Auth::check() && Auth::user()->hasRole('alumni') ? route('dashboard.tracer-study.index') : (Auth::check() && Auth::user()->hasRole('admin_sekolah') ? route('dashboard.admin.tracer-study.index') : (Auth::check() && Auth::user()->hasRole('kepala_sekolah') ? route('dashboard.kepala-sekolah.tracer-study') : route('login'))) }}" 
               class="feature-card tone-purple p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="tracer study pendataan rekam jejak kuesioner lulusan evaluasi mutu industri">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-purple-100/90 text-purple-800 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-purple-800 transition-colors leading-snug">
                        Tracer Study
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Sistem pelacakan rekam jejak lulusan guna evaluasi kualitas dan relevansi kejuruan sekolah.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-purple-200/60 flex items-center justify-between text-[11px] font-semibold text-purple-800">
                    <span>Data Tracer</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

            <!-- 8. Portal Orang Tua (Pastel Orange) -->
            <a href="#konseling" 
               class="feature-card tone-orange p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="portal orang tua wali murid transparansi legalitas pendampingan biaya izin">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-orange-100/90 text-orange-800 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-orange-800 transition-colors leading-snug">
                        Portal Orang Tua
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Kanal keterbukaan informasi bagi wali murid mengenai keselamatan, legalitas, dan progres siswa.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-orange-200/60 flex items-center justify-between text-[11px] font-semibold text-orange-800">
                    <span>Ruang Informasi</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

            <!-- 9. Kolaborasi & Bantuan Sosial (Pastel Teal) -->
            <a href="#karier" 
               class="feature-card tone-teal p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="kolaborasi bantuan sosial beasiswa mitra lpk industri pembiayaan dukungan">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-teal-100/90 text-teal-800 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-teal-800 transition-colors leading-snug">
                        Kolaborasi & Bantuan Sosial
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Sinergi dengan lembaga mitra, asosiasi Jepang, dan skema kemudahan pembiayaan keberangkatan.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-teal-200/60 flex items-center justify-between text-[11px] font-semibold text-teal-800">
                    <span>Mitra & Skema</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

            <!-- 10. Informasi Sekolah (Pastel Slate) -->
            <a href="#kontak" 
               class="feature-card tone-slate p-5 rounded-2xl border card-hover-lift flex flex-col justify-between group"
               data-searchable="informasi sekolah profil smkn 7 jember bengkel jurusan alamat kontak">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-slate-200/80 text-slate-700 flex items-center justify-center mb-4 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 group-hover:text-slate-800 transition-colors leading-snug">
                        Informasi Sekolah
                    </h3>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Profil SMKN 7 Jember, sarana laboratorium, bengkel kejuruan, dan saluran kontak resmi.
                    </p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between text-[11px] font-semibold text-slate-700">
                    <span>Tentang Sekolah</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </div>
            </a>

        </div>

        <!-- No search results notification (hidden by default) -->
        <div id="no-feature-results" class="hidden text-center py-8 bg-slate-50 rounded-2xl border border-slate-200 mt-6">
            <svg class="w-10 h-10 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-semibold text-slate-700">Tidak ada kartu fitur yang cocok dengan kata kunci pencarian.</p>
            <button type="button" onclick="window.clearSiteSearch()" class="mt-2 text-xs font-bold text-[#155EAA] hover:underline">
                Reset Pencarian
            </button>
        </div>

    </div>
</section>
