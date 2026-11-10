@extends('dashboard.layouts.dashboard')

@section('title', 'Kelola Simulasi Finansial | Admin BKK SMKN 7 Jember')
@section('breadcrumb', 'Admin &bull; Simulasi Finansial')
@section('header_title', 'Pengelolaan Fitur Simulasi Finansial')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Top Bar Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#155EAA] border border-blue-200 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                <span>BKK SMKN 7 Jember &bull; Konfigurasi Simulasi Karier Jepang</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                Pusat Pengelolaan Parameter & Mitra Finansial
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola parameter acuan umum (kurs valuta asing, standar biaya), data lembaga mitra pembiayaan resmi sekolah, dan pantau statistik pemanfaatan fitur.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.simulasi.index') }}" 
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-[#155EAA] hover:border-[#155EAA] text-xs font-bold shadow-2xs transition-all">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Lihat Tampilan Siswa</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800 space-y-1 shadow-2xs">
            <strong class="font-bold block">Terdapat kesalahan validasi formulir:</strong>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ==============================================================
         1. MACRO KPI STATS
         ============================================================== -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        
        <!-- Total Simulasi -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Simulasi</span>
            <div class="text-2xl font-extrabold text-[#0F2F5F]">{{ $stats['total_simulasi'] }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Rencana Tersimpan</span>
        </div>

        <!-- Mandiri -->
        <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 shadow-xs">
            <span class="text-[11px] font-bold text-[#155EAA] uppercase tracking-wider block mb-1">Simulasi Mandiri</span>
            <div class="text-2xl font-extrabold text-[#155EAA]">{{ $stats['total_mandiri'] }}</div>
            <span class="text-[10px] text-blue-700 mt-1 block font-semibold">Perencanaan Mandiri</span>
        </div>

        <!-- Mitra -->
        <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 shadow-xs">
            <span class="text-[11px] font-bold text-purple-700 uppercase tracking-wider block mb-1">Simulasi Mitra</span>
            <div class="text-2xl font-extrabold text-purple-800">{{ $stats['total_mitra'] }}</div>
            <span class="text-[10px] text-purple-600 mt-1 block font-semibold">Skema Pembiayaan</span>
        </div>

        <!-- Siswa / Alumni Unik -->
        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Pengguna Unik</span>
            <div class="text-2xl font-extrabold text-emerald-800">{{ $stats['total_pengguna_unik'] }}</div>
            <span class="text-[10px] text-emerald-600 mt-1 block">Siswa &amp; Alumni Aktif</span>
        </div>

        <!-- Mitra Aktif -->
        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 shadow-xs col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block mb-1">Mitra Aktif</span>
            <div class="text-2xl font-extrabold text-amber-800">{{ $stats['total_mitra_aktif'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $mitras->count() }} Mitra</span></div>
            <span class="text-[10px] text-amber-700 mt-1 block font-semibold">Tampil di Siswa</span>
        </div>

    </div>

    <!-- ==============================================================
         2. TAB NAVIGATION
         ============================================================== -->
    <div class="border-b border-slate-200 flex items-center gap-2 overflow-x-auto">
        <button type="button" 
                onclick="switchTab('parameter')" 
                id="btn-tab-parameter"
                class="px-5 py-3 text-xs font-bold border-b-2 border-[#155EAA] text-[#155EAA] flex items-center gap-2 transition-colors cursor-pointer whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Parameter Acuan Global</span>
        </button>

        <button type="button" 
                onclick="switchTab('mitra')" 
                id="btn-tab-mitra"
                class="px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 transition-colors cursor-pointer whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span>Kelola Mitra Pembiayaan ({{ $mitras->count() }})</span>
        </button>

        <button type="button" 
                onclick="switchTab('aktivitas')" 
                id="btn-tab-aktivitas"
                class="px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 transition-colors cursor-pointer whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Statistik &amp; Aktivitas Agregat</span>
        </button>
    </div>

    <!-- ==============================================================
         TAB 1: PARAMETER GLOBAL FORM
         ============================================================== -->
    <div id="tab-content-parameter" class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
            <div class="max-w-3xl">
                <h2 class="text-lg font-bold text-[#0F2F5F]">Konfigurasi Parameter Acuan & Kurs Global</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Parameter ini digunakan sebagai acuan dasar perhitungan ketika siswa dan alumni membuka kalkulator simulasi. Siswa tetap dapat menyesuaikan input mereka sendiri.
                </p>

                <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 flex items-center justify-between">
                    <span>Terakhir diperbarui: <strong>{{ $param->tanggal_pembaruan ? $param->tanggal_pembaruan->translatedFormat('d F Y, H:i') . ' WIB' : 'Belum pernah diperbarui' }}</strong></span>
                    <span class="text-slate-400">ID Konfigurasi: #{{ $param->id }}</span>
                </div>

                <form action="{{ route('dashboard.admin.simulasi.parameter.update') }}" method="POST" class="mt-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        
                        <!-- Kurs JPY ke IDR -->
                        <div class="md:col-span-2 p-4 rounded-2xl bg-blue-50/50 border border-blue-200">
                            <label for="kurs_jpy_idr" class="block text-xs font-bold text-[#0F2F5F] mb-1">
                                Kurs Acuan Valuta JPY ke Rupiah (1 JPY = Rp ...) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative max-w-xs">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                <input type="number" step="0.01" name="kurs_jpy_idr" id="kurs_jpy_idr" 
                                       value="{{ old('kurs_jpy_idr', $param->kurs_jpy_idr) }}" 
                                       required
                                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-blue-300 bg-white text-sm font-extrabold text-[#0F2F5F] focus:ring-2 focus:ring-[#155EAA]">
                            </div>
                            <span class="text-[10px] text-slate-500 mt-1 block">Contoh: 108.50 (berdasarkan Bank Indonesia / pasar valas aktual).</span>
                        </div>

                        <!-- Default Gaji Pokok JPY -->
                        <div>
                            <label for="default_gaji_jpy" class="block text-xs font-bold text-slate-700 mb-1">
                                Default Gaji Pokok di Jepang (&yen;) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="default_gaji_jpy" id="default_gaji_jpy" 
                                   value="{{ old('default_gaji_jpy', $param->default_gaji_jpy) }}" 
                                   required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-[#155EAA]">
                            <span class="text-[10px] text-slate-400 mt-1 block">Standar industri magang / SSW: 160.000 - 220.000 JPY.</span>
                        </div>

                        <!-- Default Biaya Keberangkatan IDR -->
                        <div>
                            <label for="default_biaya_keberangkatan" class="block text-xs font-bold text-slate-700 mb-1">
                                Default Estimasi Biaya Keberangkatan (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="default_biaya_keberangkatan" id="default_biaya_keberangkatan" 
                                   value="{{ old('default_biaya_keberangkatan', $param->default_biaya_keberangkatan) }}" 
                                   required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-[#155EAA]">
                            <span class="text-[10px] text-slate-400 mt-1 block">Termasuk biaya pelatihan, paspor, visa, medical check-up, tiket.</span>
                        </div>

                        <!-- Default Biaya Hidup Jepang IDR -->
                        <div>
                            <label for="default_biaya_hidup_jepang" class="block text-xs font-bold text-slate-700 mb-1">
                                Default Biaya Hidup Bulanan di Jepang (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="default_biaya_hidup_jepang" id="default_biaya_hidup_jepang" 
                                   value="{{ old('default_biaya_hidup_jepang', $param->default_biaya_hidup_jepang) }}" 
                                   required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-[#155EAA]">
                            <span class="text-[10px] text-slate-400 mt-1 block">Sewa asrama/apartemen, makan, listrik, air, internet (est. Rp 7.000.000 - Rp 9.500.000).</span>
                        </div>

                        <!-- Default Potongan Lainnya IDR -->
                        <div>
                            <label for="default_potongan_lainnya" class="block text-xs font-bold text-slate-700 mb-1">
                                Default Potongan Wajib / Asuransi (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="default_potongan_lainnya" id="default_potongan_lainnya" 
                                   value="{{ old('default_potongan_lainnya', $param->default_potongan_lainnya) }}" 
                                   required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-[#155EAA]">
                            <span class="text-[10px] text-slate-400 mt-1 block">Pajak penghasilan Jepang, asuransi kesehatan nasional & jaminan pensiun nenkin.</span>
                        </div>

                        <!-- Sumber Informasi Referensi -->
                        <div class="md:col-span-2">
                            <label for="sumber_informasi" class="block text-xs font-bold text-slate-700 mb-1">
                                Sumber Informasi &amp; Dasar Penentuan Nilai <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="sumber_informasi" id="sumber_informasi" 
                                   value="{{ old('sumber_informasi', $param->sumber_informasi) }}" 
                                   required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-[#155EAA]">
                            <span class="text-[10px] text-slate-400 mt-1 block">Contoh: Riset Pasar LPK Mitra SMKN 7 Jember & Kurs Transaksi Bank Indonesia (2026).</span>
                        </div>

                        <!-- Keterangan Asumsi Tambahan -->
                        <div class="md:col-span-2">
                            <label for="keterangan_asumsi" class="block text-xs font-bold text-slate-700 mb-1">
                                Catatan Asumsi &amp; Penjelasan Tambahan (Opsional)
                            </label>
                            <textarea name="keterangan_asumsi" id="keterangan_asumsi" rows="3" 
                                      class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-[#155EAA]">{{ old('keterangan_asumsi', $param->keterangan_asumsi) }}</textarea>
                            <span class="text-[10px] text-slate-400 mt-1 block">Catatan ini akan ditampilkan sebagai informasi edukatif di bawah form kalkulator siswa.</span>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                            Simpan Perubahan Parameter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         TAB 2: KELOLA MITRA PEMBIAYAAN
         ============================================================== -->
    <div id="tab-content-mitra" class="space-y-6 hidden">
        
        <!-- Action Header & Add Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-[#0F2F5F]">Daftar Lembaga &amp; Mitra Pembiayaan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola skema pinjaman, plafon, suku bunga/margin, dan tenor pembiayaan keberangkatan.</p>
            </div>

            <button type="button" 
                    onclick="toggleAddMitraForm()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all cursor-pointer self-start sm:self-auto">
                <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Mitra Pembiayaan</span>
            </button>
        </div>

        <!-- Form Tambah Mitra Baru (Expandable) -->
        <div id="add-mitra-container" class="hidden bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h3 class="text-sm font-bold text-[#0F2F5F] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#155EAA]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Form Pendaftaran Mitra Pembiayaan Baru</span>
                </h3>
                <button type="button" onclick="toggleAddMitraForm()" class="text-xs text-slate-400 hover:text-slate-600 font-bold">Tutup &times;</button>
            </div>

            <form action="{{ route('dashboard.admin.simulasi.mitra.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Mitra Lembaga <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_mitra" required placeholder="Contoh: Koperasi Pegawai SMKN 7" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Skema Pembiayaan <span class="text-rose-500">*</span></label>
                        <input type="text" name="jenis_skema" required placeholder="Contoh: Dana Talangan Mandiri SMKN 7" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Plafon Maksimal (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="plafon_maksimal" required value="35000000" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metode Perhitungan <span class="text-rose-500">*</span></label>
                        <select name="metode_perhitungan" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold">
                            <option value="flat">Flat Rate</option>
                            <option value="anuitas">Anuitas (Bunga Efektif)</option>
                            <option value="margin_syariah">Margin Murabahah Syariah</option>
                            <option value="belum_tersedia">Belum Tersedia (Proses Kajian Resmi)</option>
                        </select>
                        <span class="text-[10px] text-slate-400 mt-1 block">Pilih "Belum Tersedia" bila skema resmi belum disepakati (menghindari bunga palsu).</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Suku Bunga Tahunan (% p.a)</label>
                        <input type="number" step="0.01" name="suku_bunga_tahunan" value="0.00" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Margin Bulanan (% per bulan)</label>
                        <input type="number" step="0.01" name="margin_bulanan" value="0.00" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Biaya Administrasi <span class="text-rose-500">*</span></label>
                        <select name="biaya_administrasi_tipe" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                            <option value="nominal">Nominal (Rupiah Tetap)</option>
                            <option value="persentase">Persentase (%)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Biaya Administrasi <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="biaya_administrasi_nilai" value="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Uang Muka / DP (%) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="uang_muka_persen" value="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pilihan Tenor Tersedia (Bulan) <span class="text-rose-500">*</span></label>
                        <div class="flex flex-wrap items-center gap-4 text-xs">
                            @foreach([3, 6, 12, 18, 24, 36, 48] as $t)
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="checkbox" name="tenor_pilihan[]" value="{{ $t }}" {{ in_array($t, [6, 12, 18, 24]) ? 'checked' : '' }} class="rounded border-slate-300 text-[#155EAA] focus:ring-[#155EAA]">
                                    <span>{{ $t }} Bulan</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sumber Informasi / Referensi Mitra</label>
                        <input type="text" name="sumber_informasi" placeholder="Contoh: MoU SMKN 7 Jember & Koperasi Pegawai Tahun 2026" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Syarat &amp; Ketentuan Khusus</label>
                        <textarea name="persyaratan" rows="2" placeholder="Siswa/Alumni terdaftar di BKK SMKN 7 Jember, rekomendasi orang tua..." class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs"></textarea>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3 flex items-center gap-6 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                            <input type="checkbox" name="is_aktif" value="1" checked class="rounded border-slate-300 text-[#155EAA] focus:ring-[#155EAA]">
                            <span>Aktifkan Mitra untuk Pilihan Siswa</span>
                        </label>

                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                            <input type="checkbox" name="is_demo" value="1" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                            <span>Tandai Sebagai Contoh Simulasi Lomba</span>
                        </label>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="toggleAddMitraForm()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                        Simpan Mitra Baru
                    </button>
                </div>
            </form>
        </div>

        <!-- Daftar Mitra Cards & Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#0F2F5F]">Daftar Lembaga Mitra Terdaftar ({{ $mitras->count() }})</h3>
                <span class="text-xs text-slate-400">Status aktif menentukan ketersediaan opsi bagi siswa</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-700">
                    <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Nama Lembaga &amp; Skema</th>
                            <th class="px-5 py-3.5">Plafon Maksimal</th>
                            <th class="px-5 py-3.5">Metode &amp; Bunga</th>
                            <th class="px-5 py-3.5">Pilihan Tenor</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mitras as $m)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <strong class="text-slate-900 text-sm font-bold">{{ $m->nama_mitra }}</strong>
                                        @if($m->is_demo)
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800">Demo Lomba</span>
                                        @endif
                                    </div>
                                    <span class="text-[11px] text-slate-500 block mt-0.5">{{ $m->jenis_skema }}</span>
                                    @if($m->sumber_informasi)
                                        <span class="text-[10px] text-slate-400 block mt-0.5 italic">{{ $m->sumber_informasi }}</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 font-bold text-slate-800 whitespace-nowrap">
                                    Rp {{ number_format($m->plafon_maksimal, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($m->metode_perhitungan === 'belum_tersedia')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Belum Tersedia
                                        </span>
                                    @elseif($m->metode_perhitungan === 'flat')
                                        <span class="font-semibold text-blue-900 block">Flat Rate</span>
                                        <span class="text-[10px] text-slate-400">{{ $m->suku_bunga_tahunan }}% / tahun</span>
                                    @elseif($m->metode_perhitungan === 'anuitas')
                                        <span class="font-semibold text-purple-900 block">Anuitas Efektif</span>
                                        <span class="text-[10px] text-slate-400">{{ $m->suku_bunga_tahunan }}% p.a.</span>
                                    @elseif($m->metode_perhitungan === 'margin_syariah')
                                        <span class="font-semibold text-emerald-900 block">Murabahah Syariah</span>
                                        <span class="text-[10px] text-slate-400">Margin {{ $m->margin_bulanan }}% / bln</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @if(is_array($m->tenor_pilihan))
                                            @foreach($m->tenor_pilihan as $tn)
                                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-[10px] font-mono text-slate-700">{{ $tn }} bln</span>
                                            @endforeach
                                        @else
                                            <span class="text-slate-400 text-[10px]">-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    @if($m->is_aktif)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Toggle Aktif/Nonaktif -->
                                        <form action="{{ route('dashboard.admin.simulasi.mitra.toggle', $m->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer {{ $m->is_aktif ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                                    title="{{ $m->is_aktif ? 'Nonaktifkan mitra' : 'Aktifkan mitra' }}">
                                                {{ $m->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                    Belum ada data mitra pembiayaan. Tambahkan mitra untuk mengaktifkan opsi simulasi pembiayaan bagi siswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ==============================================================
         TAB 3: STATISTIK & AKTIVITAS AGREGAT
         ============================================================== -->
    <div id="tab-content-aktivitas" class="space-y-6 hidden">
        
        <div>
            <h2 class="text-lg font-bold text-[#0F2F5F]">Log Aktivitas Simulasi Agregat</h2>
            <p class="text-xs text-slate-500 mt-0.5">Memantau pola penggunaan dan kebutuhan pembiayaan siswa secara agregat tanpa mengubah isi riwayat pribadi.</p>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#0F2F5F]">8 Simulasi Tersimpan Terbaru</h3>
                <span class="text-xs text-slate-400">Audit kepemilikan data &amp; transparansi estimasi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-slate-700">
                    <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Pengguna &amp; Peran</th>
                            <th class="px-5 py-3.5">Judul Rencana</th>
                            <th class="px-5 py-3.5">Mode</th>
                            <th class="px-5 py-3.5">Waktu Simpan</th>
                            <th class="px-5 py-3.5">Biaya Keberangkatan</th>
                            <th class="px-5 py-3.5">Surplus Bersih</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($aktivitasTerbaru as $act)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <strong class="text-slate-900 block font-bold">{{ $act->user->name }}</strong>
                                    <span class="text-[10px] text-slate-400 capitalize">{{ $act->user->role }} &bull; {{ $act->user->email }}</span>
                                </td>

                                <td class="px-5 py-4 font-semibold text-slate-800">
                                    {{ $act->judul_simulasi }}
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $act->jenis_simulasi === 'mandiri' ? 'bg-blue-50 text-[#155EAA]' : 'bg-purple-50 text-purple-700' }}">
                                        {{ $act->jenis_simulasi === 'mandiri' ? 'Mandiri' : 'Mitra: ' . ($act->nama_mitra_snapshot ?? 'Pembiayaan') }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap text-slate-500">
                                    <div>{{ $act->created_at->format('d/m/Y') }}</div>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $act->created_at->format('H:i') }} WIB</span>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-800">
                                    Rp {{ number_format($act->snapshot_hasil['total_biaya_keberangkatan'] ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap font-bold {{ ($act->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0) > 0 ? 'text-[#166534]' : 'text-rose-600' }}">
                                    Rp {{ number_format($act->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0, 0, ',', '.') }}
                                </td>

                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('dashboard.simulasi.riwayat.show', $act->id) }}" 
                                       target="_blank"
                                       class="px-3 py-1.5 rounded-lg bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-2xs">
                                        Lihat Snapshot
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                    Belum ada catatan aktivitas simulasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<script>
function switchTab(tab) {
    const tabs = ['parameter', 'mitra', 'aktivitas'];
    tabs.forEach(t => {
        const btn = document.getElementById('btn-tab-' + t);
        const content = document.getElementById('tab-content-' + t);
        if (t === tab) {
            btn.className = 'px-5 py-3 text-xs font-bold border-b-2 border-[#155EAA] text-[#155EAA] flex items-center gap-2 transition-colors cursor-pointer whitespace-nowrap';
            content.classList.remove('hidden');
        } else {
            btn.className = 'px-5 py-3 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 transition-colors cursor-pointer whitespace-nowrap';
            content.classList.add('hidden');
        }
    });
}

function toggleAddMitraForm() {
    const container = document.getElementById('add-mitra-container');
    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        container.scrollIntoView({ behavior: 'smooth' });
    } else {
        container.classList.add('hidden');
    }
}
</script>
@endsection
