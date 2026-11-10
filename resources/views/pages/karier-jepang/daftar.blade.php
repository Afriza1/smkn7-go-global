@extends(Auth::check() ? 'dashboard.layouts.dashboard' : 'layouts.app')

@php
    $dashboardRoute = Auth::user()->isSiswa()
        ? route('dashboard.siswa')
        : route('dashboard.alumni');
@endphp

@section('title', 'Formulir Pendaftaran Karier Jepang | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Karier Jepang • Formulir Pendaftaran')
@section('header_title', 'Formulir Pendaftaran Karier Jepang')

@section('content')
<div class="bg-[#F8FAFC] py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Header -->
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-3" aria-label="Breadcrumb">
                <a href="{{ $dashboardRoute }}" class="hover:text-[#155EAA]">Dashboard</a>
                <span>&rsaquo;</span>
                <a href="{{ route('karier-jepang.index') }}" class="hover:text-[#155EAA]">Karier Jepang</a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-bold">Formulir Pendaftaran</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                        Formulir Pendaftaran Karier Jepang
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">
                        Lengkapi identitas, tentukan pilihan satu LPK mitra, dan unggah dokumen persyaratan wajib.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 text-[#155EAA] border border-blue-200 text-xs font-bold self-start sm:self-auto">
                    <span>Peran: {{ Auth::user()->getRoleDisplayName() }}</span>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800">
                <strong class="font-bold block mb-1">Terdapat kesalahan pada formulir yang perlu diperbaiki:</strong>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card Container -->
        <form action="{{ route('karier-jepang.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- ==============================================================
                 BAGIAN 1: IDENTITAS PESERTA
                 ============================================================== -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#155EAA] flex items-center justify-center font-bold text-xs">
                        1
                    </span>
                    <h2 class="text-base sm:text-lg font-bold text-[#0F2F5F]">
                        Identitas Calon Peserta
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    
                    <!-- Status peserta ditentukan dari role akun -->
                    <div>
                        <span class="block text-xs font-bold text-slate-700 mb-1.5">Status Kepesertaan</span>
                        <div class="w-full px-3.5 py-2.5 rounded-xl border border-blue-200 bg-blue-50 text-xs sm:text-sm font-semibold text-[#155EAA]" role="status">
                            {{ $user->role === 'siswa' ? 'Siswa Aktif (Kelas XII)' : 'Alumni SMKN 7 Jember' }}
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama_lengkap" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Lengkap Sesuai KTP/Ijazah <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nama_lengkap" 
                               id="nama_lengkap" 
                               value="{{ old('nama_lengkap', Auth::user()->name) }}" 
                               required
                               placeholder="Nama lengkap..." 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">
                    </div>

                    <!-- NISN (Khusus Siswa) -->
                    @if($user->role === 'siswa')
                    <div id="field_nis_nisn">
                        <label for="nis_nisn" class="block text-xs font-bold text-slate-700 mb-1.5">
                            NIS / NISN Siswa <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nis_nisn" 
                               id="nis_nisn" 
                               value="{{ old('nis_nisn') }}" 
                               placeholder="Contoh: 0064829103" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">
                        <span class="text-[11px] text-slate-400 mt-1 block">Wajib bagi siswa aktif untuk sinkronisasi data Dapodik.</span>
                    </div>
                    @else
                    <div id="field_tahun_lulus">
                        <label for="tahun_lulus" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tahun Kelulusan Alumni <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" 
                               name="tahun_lulus" 
                               id="tahun_lulus" 
                               value="{{ old('tahun_lulus', '2024') }}" 
                               min="2010" 
                               max="2026"
                               placeholder="Contoh: 2024" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">
                        <span class="text-[11px] text-slate-400 mt-1 block">Wajib bagi alumni lulusan SMKN 7 Jember.</span>
                    </div>
                    @endif

                    <!-- Jurusan / Konsentrasi Keahlian -->
                    <div>
                        <label for="jurusan" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Jurusan / Konsentrasi Keahlian <span class="text-rose-500">*</span>
                        </label>
                        <select name="jurusan"
                                id="jurusan" 
                                required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">
                            <option value="">Pilih Jurusan...</option>
                            @foreach($jurusanOptions as $jurusan)
                                <option value="{{ $jurusan }}" @selected(old('jurusan') === $jurusan)>{{ $jurusan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nomor WhatsApp / HP -->
                    <div>
                        <label for="nomor_telepon" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="nomor_telepon" 
                               id="nomor_telepon" 
                               value="{{ old('nomor_telepon', Auth::user()->phone) }}" 
                               required
                               placeholder="081234567890" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">
                    </div>

                    <!-- Alamat Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Alamat Email Aktif <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', Auth::user()->email) }}" 
                               required
                               placeholder="email@example.com" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">
                    </div>

                    <!-- Alamat Domisili -->
                    <div class="sm:col-span-2">
                        <label for="alamat" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Alamat Lengkap Domisili <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alamat" 
                                  id="alamat" 
                                  rows="2" 
                                  required
                                  placeholder="Nama jalan, RT/RW, Dusun/Kelurahan, Kecamatan, Kabupaten..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">{{ old('alamat') }}</textarea>
                    </div>

                    <!-- Motivasi / Alasan Minat -->
                    <div class="sm:col-span-2">
                        <label for="alasan_minat" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Motivasi & Alasan Mengikuti Program Karier Jepang (Opsional)
                        </label>
                        <textarea name="alasan_minat" 
                                  id="alasan_minat" 
                                  rows="2" 
                                  placeholder="Ceritakan minat bidang industri, target pencapaian bahasa, atau aspirasi karier Anda..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">{{ old('alasan_minat') }}</textarea>
                    </div>

                </div>
            </div>

            <!-- ==============================================================
                 BAGIAN 2: PILIHAN SATU LPK MITRA (TERKUNCI SETELAH SUBMIT)
                 ============================================================== -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#155EAA] flex items-center justify-center font-bold text-xs">
                            2
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-[#0F2F5F]">
                            Pilihan Lembaga Pelatihan Kerja (LPK) Mitra
                        </h2>
                    </div>
                    <span class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">
                        Pilihan 1 LPK Terkunci
                    </span>
                </div>

                <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 mb-6 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong>Ketentuan Pemilihan LPK:</strong> Setiap peserta hanya diperkenankan memilih satu LPK mitra. Pilihan ini akan dikunci dan diteruskan ke meja pimpinan sekolah. Apabila nantinya tidak lolos seleksi di LPK ini, Anda dapat mengajukan pendaftaran baru ke LPK lain melalui riwayat sistem sekolah.
                    </div>
                </div>

                @php($hasSelectablePartner = collect($partnerOptions)->contains(fn ($option) => $option['selectable']))
                @if(! $hasSelectablePartner)
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800">
                        Record mitra resmi yang dapat dipilih belum tersedia. Pendaftaran baru belum dapat dikirim sampai data resmi mitra tersedia.
                    </div>
                @endif
                <div class="space-y-4">
                    @foreach($partnerOptions as $option)
                        @php($lpk = $option['record'])
                        @if($option['selectable'])
                        <label class="block p-4 sm:p-5 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white hover:border-[#155EAA] transition-all cursor-pointer relative has-[:checked]:border-[#155EAA] has-[:checked]:bg-blue-50/20 has-[:checked]:ring-1 has-[:checked]:ring-[#155EAA]">
                            <div class="flex items-start gap-3">
                                <input type="radio" 
                                       name="lpk_id" 
                                       value="{{ $lpk->id }}"
                                       required
                                       @checked(old('lpk_id') == $lpk->id || ($loop->first && !old('lpk_id')))>
                                <div class="flex-1">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                                            {{ $option['name'] }}
                                        </h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 w-fit">Mitra Rekanan Resmi</span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed mb-2">
                                        {{ $lpk->ringkasan }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500">
                                        <span>Program: <strong class="text-slate-700">{{ $lpk->bidang_program }}</strong></span>
                                        <span>&bull;</span>
                                        <span>Wilayah: <strong class="text-slate-700">{{ $lpk->wilayah_penempatan }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </label>
                        @else
                            <div class="block p-4 sm:p-5 rounded-xl border border-slate-200 bg-slate-100 text-slate-500" aria-disabled="true">
                                <h3 class="text-sm sm:text-base font-bold text-slate-700">{{ $option['name'] }}</h3>
                                @if($lpk)
                                    <p class="mt-1 text-xs leading-relaxed">{{ $lpk->ringkasan }}</p>
                                    <div class="mt-2 flex flex-wrap gap-3 text-[11px]">
                                        <span>Program: <strong>{{ $lpk->bidang_program }}</strong></span>
                                        <span>Wilayah: <strong>{{ $lpk->wilayah_penempatan }}</strong></span>
                                    </div>
                                @endif
                                <p class="mt-2 text-[11px] font-semibold">Belum dapat dipilih: record resmi aktif yang unik belum tersedia.</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- ==============================================================
                 BAGIAN 3: UNGGAH DOKUMEN PERSYARATAN (PRIVATE STORAGE)
                 ============================================================== -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#155EAA] flex items-center justify-center font-bold text-xs">
                            3
                        </span>
                        <h2 class="text-base sm:text-lg font-bold text-[#0F2F5F]">
                            Unggah Dokumen Persyaratan
                        </h2>
                    </div>
                    <span class="text-xs text-slate-400">Format: PDF, JPG, PNG (Maks 2MB)</span>
                </div>

                <div class="mb-6 p-4 rounded-xl bg-blue-50/50 border border-blue-100 text-xs text-slate-600 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#155EAA] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Belum memiliki format Surat Pernyataan Orang Tua / Wali?</span>
                    </div>
                    <a href="{{ route('karier-jepang.template-pernyataan') }}" 
                       target="_blank"
                       class="px-3.5 py-1.5 rounded-lg bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-colors w-fit flex items-center gap-1.5">
                        <span>Unduh / Cetak Draf Format</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    
                    <!-- 1. KTP / Kartu Pelajar -->
                    <div>
                        <label for="file_identitas" class="block text-xs font-bold text-slate-700 mb-1">
                            1. KTP atau Kartu Pelajar <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mb-2">Scan/foto KTP asli atau Kartu Pelajar SMKN 7 Jember.</p>
                        <input type="file" 
                               name="file_identitas" 
                               id="file_identitas" 
                               required
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#155EAA] hover:file:bg-blue-100">
                    </div>

                    <!-- 2. Kartu Keluarga -->
                    <div>
                        <label for="file_kk" class="block text-xs font-bold text-slate-700 mb-1">
                            2. Kartu Keluarga (KK) <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mb-2">Salinan Kartu Keluarga yang jelas dan terbaca.</p>
                        <input type="file" 
                               name="file_kk" 
                               id="file_kk" 
                               required
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#155EAA] hover:file:bg-blue-100">
                    </div>

                    <!-- 3. Rapor / Ijazah -->
                    <div>
                        <label for="file_rapor_ijazah" class="block text-xs font-bold text-slate-700 mb-1">
                            3. Rapor Terakhir / Ijazah SMK <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mb-2">Rapor semester 5 (bagi siswa) atau Ijazah SMK (alumni).</p>
                        <input type="file" 
                               name="file_rapor_ijazah" 
                               id="file_rapor_ijazah" 
                               required
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#155EAA] hover:file:bg-blue-100">
                    </div>

                    <!-- 4. Surat Pernyataan Orang Tua / Wali -->
                    <div>
                        <label for="file_surat_ortu" class="block text-xs font-bold text-slate-700 mb-1">
                            4. Surat Pernyataan Orang Tua / Wali <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mb-2">Surat persetujuan yang telah ditandatangani bermaterai.</p>
                        <input type="file" 
                               name="file_surat_ortu" 
                               id="file_surat_ortu" 
                               required
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#155EAA] hover:file:bg-blue-100">
                    </div>

                    <!-- 5. Dokumen Pendukung Tambahan (Opsional) -->
                    <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                        <label for="file_tambahan" class="block text-xs font-bold text-slate-700 mb-1">
                            5. Dokumen Pendukung Tambahan (Opsional)
                        </label>
                        <p class="text-[11px] text-slate-400 mb-2">Sertifikat JLPT/JFT, sertifikat UKK, atau sertifikat prestasi kompetensi bila ada (Maks 3MB).</p>
                        <input type="file" 
                               name="file_tambahan" 
                               id="file_tambahan" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>

                </div>
            </div>

            <!-- ==============================================================
                 BAGIAN 4: PERNYATAAN & PENGIRIMAN
                 ============================================================== -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                <label class="flex items-start gap-3 cursor-pointer mb-6">
                    <input type="checkbox" 
                           name="persetujuan_syarat" 
                           value="1" 
                           required 
                           class="mt-1 rounded text-[#155EAA] focus:ring-[#155EAA]">
                    <span class="text-xs text-slate-600 leading-relaxed">
                        Saya menyatakan dengan sebenar-benarnya bahwa seluruh data dan dokumen yang saya unggah adalah <strong>asli, sah, dan dapat dipertanggungjawabkan</strong>. Saya bersedia mematuhi seluruh tahapan seleksi BKK SMKN 7 Jember, pertimbangan Guru BK, putusan Kepala Sekolah, serta tata tertib pelatihan di LPK mitra.
                    </span>
                </label>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                    <a href="{{ $dashboardRoute }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                        &larr; Batalkan Pendaftaran
                    </a>

                    <button type="submit"
                            @disabled(! $hasSelectablePartner)
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs sm:text-sm font-extrabold shadow-md hover:shadow-lg transition-all cursor-pointer">
                        <span>Kirim Pendaftaran Program</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

@endsection
