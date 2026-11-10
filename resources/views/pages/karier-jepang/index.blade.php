@extends(Auth::check() && (Auth::user()->isSiswa() || Auth::user()->isAlumni() || Auth::user()->isKepalaSekolah()) ? 'dashboard.layouts.dashboard' : 'layouts.app')

@section('title', 'Karier Jepang | SMKN 7 Jember – Go Global')

@section('breadcrumb', 'Karier Jepang • Informasi Program')
@section('header_title', 'Informasi Program Karier Jepang')

@section('content')
<div class="bg-[#F8FAFC]">

    <!-- ==============================================================
         1. HERO HEADER KARIER JEPANG
         ============================================================== -->
    <div class="relative bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0A1E3F] text-white py-16 md:py-24 overflow-hidden border-b border-blue-900/50">
        <!-- Visual Accent Circles -->
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute right-1/4 bottom-0 w-64 h-64 rounded-full bg-[#FBBF24]/10 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-[#FBBF24] mb-4">
                    <span class="w-2 h-2 rounded-full bg-[#FBBF24] animate-pulse"></span>
                    <span>Program Unggulan BKK & Go Global</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight leading-tight">
                    Karier & Magang Profesional ke Jepang
                </h1>
                <p class="mt-4 text-sm sm:text-base text-slate-200 leading-relaxed">
                    SMKN 7 Jember memfasilitasi siswa aktif tingkat akhir dan alumni untuk meraih masa depan global melalui skema resmi <strong>Tokutei Ginou (Specified Skilled Worker)</strong> dan <strong>Ginou Jisshusei (Magang Teknis Industri)</strong> bersama Lembaga Pelatihan Kerja (LPK) mitra kredibel.
                </p>

                <!-- Information Disclaimer Banner -->
                <div class="mt-6 p-4 rounded-xl bg-white/10 border border-white/20 backdrop-blur-xs text-xs text-slate-200 flex items-start gap-3">
                    <svg class="w-5 h-5 text-[#FBBF24] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <strong class="text-white block font-bold mb-0.5">Pendaftaran Khusus Warga SMKN 7 Jember</strong>
                        Pendaftaran terbuka eksklusif untuk Siswa Aktif (kelas XII) dan Alumni resmi SMKN 7 Jember. Seluruh proses seleksi awal, verifikasi berkas, dan rekomendasi dibina langsung oleh BKK, Guru BK, dan Manajemen Sekolah.
                    </div>
                </div>

                <!-- CTA Action Buttons -->
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    @auth
                        @if(Auth::user()->isSiswa() || Auth::user()->isAlumni())
                            <a href="{{ route('karier-jepang.daftar') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#FBBF24] hover:bg-amber-400 text-[#0F2F5F] text-sm font-extrabold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 cursor-pointer">
                                <span>Daftar Program Karier Jepang</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-sm font-bold border border-white/30 transition-all">
                                <span>Buka Dashboard {{ Auth::user()->getRoleDisplayName() }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#FBBF24] hover:bg-amber-400 text-[#0F2F5F] text-sm font-extrabold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            <span>Masuk / Login untuk Mendaftar</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-bold border border-white/20 transition-all">
                            <span>Registrasi Akun Siswa/Alumni</span>
                        </a>
                    @endauth

                    <a href="{{ route('karier-jepang.template-pernyataan') }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/5 hover:bg-white/15 text-slate-200 text-xs sm:text-sm font-semibold border border-white/15 transition-all">
                        <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Unduh Format Surat Pernyataan Wali</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         2. GAMBARAN 2 SKEMA PROGRAM: TOKUTEI GINOU & GINOU JISSHUSEI
         ============================================================== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-[#155EAA]">Jalur Karier Internasional</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] mt-1">Pilihan Program Magang & Bekerja di Jepang</h2>
            <p class="text-xs sm:text-sm text-slate-600 mt-2">Dua skema legalitas tenaga kerja berstandar regulasi ketenagakerjaan pemerintah Jepang dan Republik Indonesia.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Card 1: Tokutei Ginou -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs hover:border-[#155EAA]/50 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-[#155EAA] border border-blue-100">
                        Pekerja Berketerampilan Khusus (SSW)
                    </span>
                    <span class="text-xs font-semibold text-slate-400">Kontrak s.d. 5 Tahun</span>
                </div>
                <h3 class="text-xl font-bold text-[#0F2F5F] mb-3">Tokutei Ginou (SSW 1)</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
                    Skema kerja formal dengan status tenaga kerja ahli penuh. Memiliki hak dan perlindungan setara dengan pekerja warga negara Jepang, standar gaji regional UMR Jepang, asuransi ketenagakerjaan penuh, dan fasilitas tempat tinggal terjamin.
                </p>
                <div class="space-y-2.5 text-xs text-slate-700 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Syarat Bahasa:</strong> Lulus ujian JLPT N4 atau JFT-Basic A2</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Ujian Keahlian:</strong> Tes Prometric sesuai bidang (Manufaktur, Kaigo, Food Service)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Sasaran:</strong> Siswa Kelas XII & Alumni SMKN 7 Jember</span>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-[#155EAA]">
                    <span>Sektor: Industri, Teknologi, Perawatan Lansia</span>
                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Regulasi Resmi</span>
                </div>
            </div>

            <!-- Card 2: Ginou Jisshusei -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs hover:border-[#166534]/50 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-[#166534] border border-emerald-100">
                        Magang Industri Teknis
                    </span>
                    <span class="text-xs font-semibold text-slate-400">Durasi 3 Tahun</span>
                </div>
                <h3 class="text-xl font-bold text-[#0F2F5F] mb-3">Ginou Jisshusei (Technical Intern)</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
                    Program transfer teknologi dan kompetensi kerja industri Jepang. Peserta magang ditempatkan langsung pada lini produksi, perakitan mesin, konstruksi, atau pertanian presisi dengan bimbingan supervisor profesional Jepang.
                </p>
                <div class="space-y-2.5 text-xs text-slate-700 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Syarat Bahasa:</strong> Standar N5 / N4 (Diberikan pelatihan intensif)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Fokus:</strong> Disiplin kerja 5S, Kaizen, keselamatan kerja (K3)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Kelanjutan:</strong> Dapat beralih status langsung ke Tokutei Ginou setelah 3 tahun</span>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs font-bold text-[#166534]">
                    <span>Sektor: Manufaktur, Pertanian, Konstruksi Interior</span>
                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Transfer IPTEK</span>
                </div>
            </div>

        </div>
    </div>

    <!-- ==============================================================
         3. ALUR TAHAPAN PENDAFTARAN & SELEKSI SEKOLAH (4 TAHAP)
         ============================================================== -->
    <div class="bg-slate-100/70 border-y border-slate-200/80 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-[#155EAA]">Transparansi Alur</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] mt-1">4 Tahap Seleksi Pendaftaran Sekolah</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-2">Setiap pendaftar melalui rantai verifikasi multi-tingkat demi menjamin kesiapan fisik, mental, dan legalitas.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Tahap 1 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs relative">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#155EAA] flex items-center justify-center font-extrabold text-sm mb-4">
                        1
                    </div>
                    <h3 class="text-base font-bold text-[#0F2F5F] mb-1">Pengajuan Peserta</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Siswa aktif atau alumni mengisi formulir, memilih satu LPK mitra, dan mengunggah KTP, KK, Rapor/Ijazah, serta Surat Pernyataan Wali.
                    </p>
                </div>

                <!-- Tahap 2 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs relative">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#155EAA] flex items-center justify-center font-extrabold text-sm mb-4">
                        2
                    </div>
                    <h3 class="text-base font-bold text-[#0F2F5F] mb-1">Pemeriksaan Admin</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Petugas BKK memeriksa keaslian berkas dan identitas peserta. Berkas yang kurang dapat dikembalikan untuk perbaikan sebelum diteruskan.
                    </p>
                </div>

                <!-- Tahap 3 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs relative">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#166534] flex items-center justify-center font-extrabold text-sm mb-4">
                        3
                    </div>
                    <h3 class="text-base font-bold text-[#0F2F5F] mb-1">Pertimbangan Guru BK</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Guru BK / Konselor mengevaluasi kesiapan mental, motivasi, dan rekam jejak kedisiplinan siswa untuk memberikan rekomendasi resmi.
                    </p>
                </div>

                <!-- Tahap 4 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs relative">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-[#d97706] flex items-center justify-center font-extrabold text-sm mb-4">
                        4
                    </div>
                    <h3 class="text-base font-bold text-[#0F2F5F] mb-1">Persetujuan Kepala Sekolah</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Pimpinan sekolah meninjau usulan lengkap dan memberikan putusan akhir persetujuan delegasi peserta ke LPK mitra yang dituju.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- ==============================================================
         4. DAFTAR LEMBAGA PELATIHAN KERJA (LPK) MITRA
         ============================================================== -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-[#155EAA]">Mitra Penyalur & Lembaga Pelatihan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F] mt-1">Daftar LPK Mitra Kerja Sama</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">Peserta memilih 1 LPK saat pendaftaran. Pilihan LPK dikunci setelah formulir diajukan.</p>
            </div>
            
            <div class="text-xs text-slate-500 bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-xs">
                Total Tersedia: <strong>{{ $lpks->count() }} Lembaga</strong>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($lpks as $lpk)
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <!-- Header LPK Card -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-[#0F2F5F]">
                                    {{ $lpk->nama_lpk }}
                                </h3>
                                <span class="text-xs text-slate-500 block mt-0.5">Penempatan: {{ $lpk->wilayah_penempatan ?: 'Belum diinformasikan' }}</span>
                            </div>

                            @if($lpk->is_demo)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 flex-shrink-0">
                                    Simulasi Lomba
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex-shrink-0">
                                    Mitra Resmi BKK
                                </span>
                            @endif
                        </div>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                            {{ $lpk->deskripsi ?: $lpk->ringkasan }}
                        </p>

                        <div class="space-y-2 text-xs text-slate-700 bg-slate-50 p-3.5 rounded-xl border border-slate-200/60 mb-4">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Program Utama:</span>
                                <strong class="text-slate-800">{{ $lpk->bidang_program }}</strong>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Alokasi Kuota:</span>
                                <strong class="text-[#155EAA]">{{ $lpk->kuota_tersedia > 0 ? $lpk->kuota_tersedia . ' Kursi Terbuka' : 'Belum diinformasikan' }}</strong>
                            </div>
                            @if($lpk->persyaratan_khusus)
                                <div class="pt-2 border-t border-slate-200/60 text-[11px] text-slate-600">
                                    <span class="font-semibold text-slate-700">Persyaratan:</span> {{ $lpk->persyaratan_khusus }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[11px] text-slate-400">Terdaftar resmi di sistem sekolah</span>
                        @auth
                            @if(Auth::user()->isSiswa() || Auth::user()->isAlumni())
                                <a href="{{ route('karier-jepang.daftar') }}" class="text-xs font-bold text-[#155EAA] hover:underline flex items-center gap-1">
                                    <span>Pilih LPK Ini</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ==============================================================
         5. PERSYARATAN UMUM & UNDUH DOKUMEN PENDUKUNG
         ============================================================== -->
    <div class="bg-white border-t border-slate-200/80 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#155EAA]">Ketentuan Pendaftaran</span>
                    <h2 class="text-2xl font-extrabold text-[#0F2F5F] mt-1 mb-6">Persyaratan Umum Peserta</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs sm:text-sm text-slate-700">
                        <div class="space-y-3">
                            <h3 class="font-bold text-[#0F2F5F] text-sm flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                                Bagi Siswa Aktif SMKN 7 Jember
                            </h3>
                            <ul class="space-y-2 list-disc list-inside text-slate-600 text-xs leading-relaxed">
                                <li>Siswa aktif kelas XII semester ganjil atau genap.</li>
                                <li>Memiliki NISN aktif dan terdaftar di Dapodik sekolah.</li>
                                <li>Nilai rata-rata rapor kompetensi keahlian minimal Baik.</li>
                                <li>Tidak memiliki catatan pelanggaran disiplin berat.</li>
                                <li>Mendapat persetujuan tertulis dari Orang Tua/Wali bermaterai.</li>
                            </ul>
                        </div>

                        <div class="space-y-3">
                            <h3 class="font-bold text-[#0F2F5F] text-sm flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                                Bagi Alumni SMKN 7 Jember
                            </h3>
                            <ul class="space-y-2 list-disc list-inside text-slate-600 text-xs leading-relaxed">
                                <li>Lulusan resmi seluruh jurusan di SMKN 7 Jember.</li>
                                <li>Usia maksimal 28 tahun saat pendaftaran program.</li>
                                <li>Melampirkan salinan ijazah SMK terlegalisir.</li>
                                <li>Bersedia mengikuti pembekalan intensif bahasa Jepang di LPK.</li>
                                <li>Menandatangani komitmen integritas alumni Go Global.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Box Template Dokumen -->
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/80 flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-[#d97706] flex items-center justify-center font-bold mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-[#0F2F5F] mb-2">Template Surat Pernyataan Wali</h3>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Unduh dan cetak draf format surat pernyataan persetujuan orang tua / wali. Dokumen wajib ditandatangani dan diunggah pada formulir pendaftaran.
                        </p>
                    </div>

                    <a href="{{ route('karier-jepang.template-pernyataan') }}" 
                       target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-sm">
                        <span>Lihat & Cetak Format Surat</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
