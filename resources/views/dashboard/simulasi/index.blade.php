@extends('dashboard.layouts.dashboard')

@section('title', 'Simulasi Finansial Karier Jepang | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Simulasi Finansial')
@section('header_title', 'Simulasi Finansial Karier Jepang')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto" x-data="simulasiFinansial()">
    
    <!-- Top Header Banner -->
    <div class="bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0A1E3F] rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-56 h-56 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute right-20 bottom-0 w-32 h-32 rounded-full bg-[#FBBF24]/10 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-[#FBBF24] mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#FBBF24]"></span>
                    <span>Alat Bantu Perencanaan Finansial &bull; BKK SMKN 7 Jember</span>
                </div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold tracking-tight">
                    Kalkulator Simulasi Finansial Karier Jepang
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-200 leading-relaxed">
                    Perkirakan biaya keberangkatan, proyeksi penghasilan kotor, biaya hidup, serta kemampuan pengembalian modal atau cicilan pembiayaan secara realistis dan transparan.
                </p>
                <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-slate-300">
                    <span>Kurs Referensi: <strong>1 JPY = Rp {{ number_format($param->kurs_jpy_idr, 2, ',', '.') }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembaruan Terakhir: <strong>{{ $param->tanggal_pembaruan ? $param->tanggal_pembaruan->format('d M Y') : 'Terbaru' }}</strong></span>
                </div>
            </div>

            <!-- Shortcut ke Riwayat -->
            <div class="flex-shrink-0">
                <a href="{{ route('dashboard.simulasi.riwayat') }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white border border-white/20 text-xs font-bold transition-all shadow-xs">
                    <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat Tersimpan ({{ $user->riwayatSimulasis()->count() }})</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- ==============================================================
         1. MODE SIMULASI TOGGLE (MANDIRI VS MITRA)
         ============================================================== -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-3 rounded-2xl border border-slate-200/90 shadow-xs">
        <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl w-full sm:w-auto">
            <button type="button" 
                    @click="setMode('mandiri')"
                    :class="mode === 'mandiri' ? 'bg-[#0F2F5F] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="flex-1 sm:flex-none px-5 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>1. Simulasi Mandiri (Biaya Sendiri)</span>
            </button>

            <button type="button" 
                    @click="setMode('mitra')"
                    :class="mode === 'mitra' ? 'bg-[#0F2F5F] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                    class="flex-1 sm:flex-none px-5 py-2.5 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>2. Simulasi Mitra Pembiayaan</span>
            </button>
        </div>

        <div class="text-[11px] text-slate-500 px-3 py-1 bg-slate-50 rounded-lg border border-slate-200/60 hidden md:block">
            Mode Aktif: <strong class="text-slate-800" x-text="mode === 'mandiri' ? 'Biaya Pribadi / Keluarga' : 'Mitra Pembiayaan & Cicilan'"></strong>
        </div>
    </div>

    <!-- ==============================================================
         2. FORM SIMULASI & HASIL KALKULASI (2 KOLOM GRID)
         ============================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM KIRI: FORM INPUT PARAMETER -->
        <div class="lg:col-span-6 space-y-6">
            
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-[#0F2F5F] flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-[#155EAA] flex items-center justify-center font-bold text-xs">A</span>
                        <span x-text="mode === 'mandiri' ? 'Parameter Biaya Mandiri' : 'Parameter Pembiayaan Mitra'"></span>
                    </h2>
                    <button type="button" @click="resetToDefaults()" class="text-xs text-[#155EAA] hover:underline font-semibold cursor-pointer">
                        Reset Nilai Awal
                    </button>
                </div>

                <!-- PILIHAN MITRA JIKA MODE MITRA -->
                <div x-show="mode === 'mitra'" class="space-y-3 p-4 rounded-2xl bg-blue-50/50 border border-blue-100">
                    <label class="block text-xs font-bold text-slate-700">Pilih Mitra Pembiayaan Tersedia <span class="text-rose-500">*</span></label>
                    <select x-model="selectedMitraId" @change="onMitraChange()" class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white font-semibold text-slate-800 focus:outline-hidden focus:border-[#155EAA]">
                        @foreach($mitras as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->nama_mitra }} ({{ $m->jenis_skema }}) @if($m->is_demo) [Simulasi Lomba] @endif
                            </option>
                        @endforeach
                    </select>

                    <!-- Info Skema Mitra Terpilih -->
                    <template x-if="activeMitra">
                        <div class="text-[11px] space-y-1.5 text-slate-600 pt-2 border-t border-blue-100/70">
                            <div class="flex justify-between">
                                <span>Plafon Maksimal:</span>
                                <strong class="text-slate-800" x-text="formatRupiah(activeMitra.plafon_maksimal)"></strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Skema Perhitungan:</span>
                                <strong class="text-slate-800" x-text="activeMitra.metode_perhitungan_label"></strong>
                            </div>
                            <div class="flex justify-between">
                                <span>Biaya Administrasi:</span>
                                <strong class="text-slate-800" x-text="formatRupiah(activeMitra.biaya_administrasi_nilai)"></strong>
                            </div>
                            <div class="flex justify-between" x-show="activeMitra.keterangan_status">
                                <span class="text-amber-700 font-semibold" x-text="activeMitra.keterangan_status"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- 1. Estimasi Biaya Keberangkatan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        1. Estimasi Total Biaya Keberangkatan (IDR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                        <input type="number" 
                               x-model.number="form.biaya_keberangkatan" 
                               @input="kalkulasi()"
                               step="500000"
                               min="1000000"
                               class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white text-slate-800 focus:outline-hidden focus:border-[#155EAA] font-semibold">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Termasuk paspor, visa, medical check-up, diklat bahasa, & tiket penerbangan.</span>
                </div>

                <!-- PILIHAN TENOR KHUSUS MODE MITRA -->
                <div x-show="mode === 'mitra'">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Pilihan Jangka Waktu Cicilan (Tenor Bulan) <span class="text-rose-500">*</span>
                    </label>
                    <select x-model.number="form.tenor_bulan" @change="kalkulasi()" class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white font-semibold text-slate-800 focus:outline-hidden focus:border-[#155EAA]">
                        <template x-for="tenor in (activeMitra?.tenor_pilihan || [12, 18, 24])" :key="tenor">
                            <option :value="tenor" x-text="tenor + ' Bulan'"></option>
                        </template>
                    </select>
                </div>

                <!-- 2. Estimasi Gaji Bulanan di Jepang (JPY) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        2. Estimasi Gaji Bulanan di Jepang (Yen JPY) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs text-slate-400 font-bold">&yen;</span>
                        <input type="number" 
                               x-model.number="form.gaji_jpy" 
                               @input="kalkulasi()"
                               step="5000"
                               min="50000"
                               class="w-full pl-9 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white text-slate-800 focus:outline-hidden focus:border-[#155EAA] font-semibold">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Rata-rata gaji kotor awal SSW / Magang berkisar 160.000 s.d. 220.000 JPY.</span>
                </div>

                <!-- 3. Nilai Kurs JPY ke IDR -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        3. Nilai Kurs Yen ke Rupiah (JPY ke IDR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs text-slate-400 font-bold">1 JPY = Rp</span>
                        <input type="number" 
                               x-model.number="form.kurs_jpy" 
                               @input="kalkulasi()"
                               step="0.1"
                               min="1"
                               class="w-full pl-22 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white text-slate-800 focus:outline-hidden focus:border-[#155EAA] font-semibold">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Standar referensi resmi BKK SMKN 7 Jember saat ini: Rp {{ number_format($param->kurs_jpy_idr, 2) }}.</span>
                </div>

                <!-- 4. Estimasi Biaya Hidup Bulanan di Jepang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        4. Estimasi Biaya Hidup Bulanan di Jepang (IDR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                        <input type="number" 
                               x-model.number="form.biaya_hidup" 
                               @input="kalkulasi()"
                               step="500000"
                               min="0"
                               class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white text-slate-800 focus:outline-hidden focus:border-[#155EAA] font-semibold">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Mencakup sewa kamar subsidi, belanja pangan, listrik, gas, air, & internet (~70.000 - 85.000 JPY).</span>
                </div>

                <!-- 5. Potongan / Pengeluaran Lainnya (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        5. Estimasi Potongan / Pengeluaran Lainnya (IDR, Opsional)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs text-slate-400 font-bold">Rp</span>
                        <input type="number" 
                               x-model.number="form.pengeluaran_lain" 
                               @input="kalkulasi()"
                               step="100000"
                               min="0"
                               class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white text-slate-800 focus:outline-hidden focus:border-[#155EAA]">
                    </div>
                    <span class="text-[10px] text-slate-400 mt-1 block">Contoh: iuran paguyuban WNI, kirim uang saku orang tua, atau dana darurat.</span>
                </div>

                <!-- 6. Sumber Dana (Mode Mandiri) -->
                <div x-show="mode === 'mandiri'">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        6. Rencana Sumber Dana Keberangkatan
                    </label>
                    <input type="text" 
                           x-model="form.sumber_dana" 
                           placeholder="Contoh: Tabungan Keluarga, Bantuan Saudara, Dana Pribadi..."
                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white text-slate-800 focus:outline-hidden focus:border-[#155EAA]">
                </div>

            </div>

            <!-- Disclaimer Kotak Resmi -->
            <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-950 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="leading-relaxed">
                    <strong>Penting &bull; Pernyataan Estimasi:</strong> Hasil kalkulator ini adalah alat bantu perencanaan edukatif. Bukan jaminan nominal penghasilan pasti, bukan perjanjian pinjaman, dan bukan persetujuan kredit resmi dari bank/mitra.
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: HASIL KALKULASI & PENYIMPANAN -->
        <div class="lg:col-span-6 space-y-6">
            
            <!-- HASIL KALKULASI CARD -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-[#155EAA]/30 shadow-md space-y-6">
                
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Hasil Perhitungan Realtime</span>
                        <h2 class="text-lg font-extrabold text-[#0F2F5F]" x-text="mode === 'mandiri' ? 'Ringkasan Kemampuan Mandiri' : 'Ringkasan Pembiayaan Mitra'"></h2>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-[#155EAA] border border-blue-200">
                        Rumus Resmi
                    </span>
                </div>

                <!-- TAMPILAN JIKA SKEMA MITRA BELUM TERSEDIA -->
                <template x-if="mode === 'mitra' && hasil.status === 'belum_tersedia'">
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-2">
                        <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800" x-text="hasil.nama_mitra"></h3>
                        <p class="text-xs text-amber-800 font-semibold" x-text="hasil.pesan"></p>
                        <p class="text-[11px] text-slate-500">Silakan pilih mitra lain yang telah memiliki skema aktif atau gunakan mode Simulasi Mandiri.</p>
                    </div>
                </template>

                <!-- TAMPILAN JIKA PERHITUNGAN TERSEDIA -->
                <div x-show="mode === 'mandiri' || (mode === 'mitra' && hasil.status !== 'belum_tersedia')" class="space-y-4">
                    
                    <!-- KPI Card 1: Gaji Kotor Dikonversi ke IDR -->
                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs text-slate-500 font-semibold">Estimasi Gaji Kotor Bulanan</span>
                            <span class="text-xs font-mono font-bold text-slate-700" x-text="'&yen; ' + Number(form.gaji_jpy).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="text-2xl font-extrabold text-[#155EAA]" x-text="formatRupiah(hasil.pendapatan_kotor_idr)"></div>
                        <div class="text-[11px] text-slate-400 mt-1">Hasil konversi kurs 1 JPY = Rp <span x-text="Number(form.kurs_jpy).toLocaleString('id-ID')"></span></div>
                    </div>

                    <!-- KPI Card 2: Rincian Pengeluaran Bulanan -->
                    <div class="space-y-2 text-xs p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Estimasi Biaya Hidup di Jepang:</span>
                            <strong class="text-slate-800" x-text="formatRupiah(form.biaya_hidup)"></strong>
                        </div>
                        <div class="flex justify-between items-center text-slate-600" x-show="form.pengeluaran_lain > 0">
                            <span>Potongan / Pengeluaran Lain:</span>
                            <strong class="text-slate-800" x-text="formatRupiah(form.pengeluaran_lain)"></strong>
                        </div>
                        
                        <!-- Baris Tambahan Khusus Mitra (Cicilan) -->
                        <template x-if="mode === 'mitra' && hasil.cicilan_bulanan">
                            <div class="pt-2 border-t border-slate-200/80 space-y-1.5">
                                <div class="flex justify-between items-center text-slate-700 font-semibold">
                                    <span>Dana Dibiayai Mitra:</span>
                                    <strong class="text-[#155EAA]" x-text="formatRupiah(hasil.dana_dibiayai)"></strong>
                                </div>
                                <div class="flex justify-between items-center text-slate-700" x-show="hasil.total_uang_muka_awal > 0">
                                    <span>Uang Muka / Biaya Awal Sendiri:</span>
                                    <strong class="text-amber-800" x-text="formatRupiah(hasil.total_uang_muka_awal)"></strong>
                                </div>
                                <div class="flex justify-between items-center text-slate-700">
                                    <span>Biaya Administrasi:</span>
                                    <strong class="text-slate-800" x-text="formatRupiah(hasil.biaya_administrasi)"></strong>
                                </div>
                                <div class="flex justify-between items-center text-orange-700 font-bold bg-orange-50 p-2 rounded-lg">
                                    <span>Cicilan Pembiayaan per Bulan (<span x-text="form.tenor_bulan"></span> bln):</span>
                                    <span class="text-sm" x-text="formatRupiah(hasil.cicilan_bulanan)"></span>
                                </div>
                            </div>
                        </template>

                        <div class="pt-2 border-t border-slate-200 flex justify-between items-center font-bold text-slate-800">
                            <span>Total Beban Pengeluaran per Bulan:</span>
                            <span class="text-sm text-slate-900" x-text="formatRupiah(hasil.total_pengeluaran_bulanan)"></span>
                        </div>
                    </div>

                    <!-- KPI Card 3: Sisa Pendapatan Bersih (Net Monthly Surplus) -->
                    <div class="p-4 rounded-2xl border" :class="hasil.sisa_pendapatan_bulanan > 0 ? 'bg-emerald-50/70 border-emerald-200' : 'bg-rose-50/70 border-rose-200'">
                        <div class="text-xs font-bold" :class="hasil.sisa_pendapatan_bulanan > 0 ? 'text-emerald-800' : 'text-rose-800'">
                            Sisa Pendapatan Bersih per Bulan (Surplus Tabungan)
                        </div>
                        <div class="text-2xl font-extrabold mt-1" :class="hasil.sisa_pendapatan_bulanan > 0 ? 'text-[#166534]' : 'text-rose-700'" x-text="formatRupiah(hasil.sisa_pendapatan_bulanan)"></div>
                        <p class="text-[11px] mt-1 text-slate-600">
                            Uang bersih yang dapat ditabung atau dikirimkan ke keluarga di tanah air setiap bulan.
                        </p>
                    </div>

                    <!-- KPI Card 4: Waktu Pengembalian Modal (Mode Mandiri) -->
                    <template x-if="mode === 'mandiri'">
                        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200">
                            <span class="text-xs font-bold text-amber-900 block mb-1">Estimasi Waktu Pengembalian Modal Keberangkatan</span>
                            <template x-if="hasil.status_pengembalian === 'dapat_dihitung'">
                                <div>
                                    <div class="text-2xl font-extrabold text-amber-900">
                                        &plusmn; <span x-text="hasil.waktu_pengembalian_bulan"></span> Bulan
                                    </div>
                                    <p class="text-xs text-amber-800 mt-1" x-text="hasil.keterangan_pengembalian"></p>
                                </div>
                            </template>
                            <template x-if="hasil.status_pengembalian !== 'dapat_dihitung'">
                                <div class="text-xs text-rose-700 font-semibold" x-text="hasil.keterangan_pengembalian"></div>
                            </template>
                        </div>
                    </template>

                </div>

                <!-- FORM SIMPAN HASIL KE RIWAYAT AKUN -->
                <div class="pt-6 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Simpan Hasil Simulasi Ini</h3>

                    <form action="{{ route('dashboard.simulasi.simpan') }}" method="POST" class="space-y-3">
                        @csrf
                        <input type="hidden" name="mode" :value="mode">
                        <input type="hidden" name="biaya_keberangkatan" :value="form.biaya_keberangkatan">
                        <input type="hidden" name="gaji_jpy" :value="form.gaji_jpy">
                        <input type="hidden" name="kurs_jpy" :value="form.kurs_jpy">
                        <input type="hidden" name="biaya_hidup" :value="form.biaya_hidup">
                        <input type="hidden" name="pengeluaran_lain" :value="form.pengeluaran_lain">
                        <input type="hidden" name="sumber_dana" :value="form.sumber_dana">
                        <input type="hidden" name="mitra_id" :value="selectedMitraId">
                        <input type="hidden" name="tenor_bulan" :value="form.tenor_bulan">

                        <div>
                            <input type="text" 
                                   name="judul_simulasi" 
                                   required 
                                   placeholder="Beri nama simulasi, misal: Target SSW Nagoya 2026..."
                                   class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-white text-slate-800 focus:outline-hidden focus:border-[#155EAA]">
                        </div>

                        <div>
                            <textarea name="catatan_pengguna" 
                                      rows="2" 
                                      placeholder="Catatan pribadi rencana (opsional)..."
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-white text-slate-800 focus:outline-hidden focus:border-[#155EAA]"></textarea>
                        </div>

                        <button type="submit" 
                                class="w-full py-3 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span>Simpan ke Riwayat Simulasi Akun</span>
                        </button>
                    </form>
                </div>

            </div>

            <!-- RINGKASAN RIWAYAT TERBARU (WIDGET CEPAT) -->
            @if($riwayatSingkat->isNotEmpty())
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs">
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Simulasi Tersimpan Terkini</h3>
                        <a href="{{ route('dashboard.simulasi.riwayat') }}" class="text-xs font-bold text-[#155EAA] hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>
                    <div class="space-y-2 text-xs">
                        @foreach($riwayatSingkat as $rw)
                            <a href="{{ route('dashboard.simulasi.riwayat.show', $rw->id) }}" class="block p-3 rounded-xl bg-slate-50 hover:bg-blue-50/40 border border-slate-200/60 transition-colors">
                                <div class="flex items-center justify-between font-bold text-slate-800 mb-0.5">
                                    <span class="truncate">{{ $rw->judul_simulasi }}</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ $rw->jenis_simulasi === 'mandiri' ? 'bg-blue-100 text-[#155EAA]' : 'bg-purple-100 text-purple-800' }}">
                                        {{ ucfirst($rw->jenis_simulasi) }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 flex justify-between">
                                    <span>Sisa: {{ 'Rp ' . number_format($rw->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0, 0, ',', '.') }}/bln</span>
                                    <span class="font-mono text-slate-400">{{ $rw->created_at->format('d M Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>

<!-- Interactive Alpine.js / Vanilla JS Engine -->
<script>
function simulasiFinansial() {
    return {
        mode: '{{ $mode }}',
        selectedMitraId: {{ $selectedMitra ? $selectedMitra->id : ($mitras->first()->id ?? 'null') }},
        mitras: @json($mitras),
        defaultParam: {
            biaya_keberangkatan: {{ $param->default_biaya_keberangkatan }},
            gaji_jpy: {{ $param->default_gaji_jpy }},
            kurs_jpy: {{ $param->kurs_jpy_idr }},
            biaya_hidup: {{ $param->default_biaya_hidup_jepang }},
            pengeluaran_lain: {{ $param->default_potongan_lainnya }},
            sumber_dana: 'Dana Pribadi / Tabungan Keluarga',
        },
        form: {
            biaya_keberangkatan: {{ $param->default_biaya_keberangkatan }},
            gaji_jpy: {{ $param->default_gaji_jpy }},
            kurs_jpy: {{ $param->kurs_jpy_idr }},
            biaya_hidup: {{ $param->default_biaya_hidup_jepang }},
            pengeluaran_lain: {{ $param->default_potongan_lainnya }},
            sumber_dana: 'Dana Pribadi / Tabungan Keluarga',
            tenor_bulan: 12,
        },
        hasil: {},

        get activeMitra() {
            return this.mitras.find(m => m.id == this.selectedMitraId);
        },

        init() {
            if (this.activeMitra && this.activeMitra.tenor_pilihan && this.activeMitra.tenor_pilihan.length > 0) {
                this.form.tenor_bulan = this.activeMitra.tenor_pilihan[0];
            }
            this.kalkulasi();
        },

        setMode(newMode) {
            this.mode = newMode;
            this.kalkulasi();
        },

        onMitraChange() {
            if (this.activeMitra && this.activeMitra.tenor_pilihan && this.activeMitra.tenor_pilihan.length > 0) {
                this.form.tenor_bulan = this.activeMitra.tenor_pilihan[0];
            }
            this.kalkulasi();
        },

        resetToDefaults() {
            this.form.biaya_keberangkatan = this.defaultParam.biaya_keberangkatan;
            this.form.gaji_jpy = this.defaultParam.gaji_jpy;
            this.form.kurs_jpy = this.defaultParam.kurs_jpy;
            this.form.biaya_hidup = this.defaultParam.biaya_hidup;
            this.form.pengeluaran_lain = this.defaultParam.pengeluaran_lain;
            this.kalkulasi();
        },

        kalkulasi() {
            const gajiKotorIdr = Math.round((this.form.gaji_jpy || 0) * (this.form.kurs_jpy || 0));
            const biayaKeberangkatan = Number(this.form.biaya_keberangkatan) || 0;
            const biayaHidup = Number(this.form.biaya_hidup) || 0;
            const pengeluaranLain = Number(this.form.pengeluaran_lain) || 0;

            if (this.mode === 'mandiri') {
                const totalPengeluaran = Math.round(biayaHidup + pengeluaranLain);
                const sisaPendapatan = Math.round(gajiKotorIdr - totalPengeluaran);

                let waktuPengembalian = null;
                let statusPengembalian = 'belum_dapat_dihitung';
                let ketPengembalian = 'Pengembalian biaya belum dapat diperkirakan karena sisa pendapatan bulanan nihil atau negatif berdasarkan asumsi pengeluaran saat ini.';

                if (sisaPendapatan > 0) {
                    waktuPengembalian = Math.ceil(biayaKeberangkatan / sisaPendapatan);
                    statusPengembalian = 'dapat_dihitung';
                    ketPengembalian = 'Estimasi pengembalian modal biaya keberangkatan tercapai dalam waktu sekitar ' + waktuPengembalian + ' bulan setelah aktif bekerja.';
                }

                this.hasil = {
                    mode: 'mandiri',
                    pendapatan_kotor_idr: gajiKotorIdr,
                    total_pengeluaran_bulanan: totalPengeluaran,
                    sisa_pendapatan_bulanan: sisaPendapatan,
                    waktu_pengembalian_bulan: waktuPengembalian,
                    status_pengembalian: statusPengembalian,
                    keterangan_pengembalian: ketPengembalian,
                };
            } else {
                const mitra = this.activeMitra;
                if (!mitra || mitra.metode_perhitungan === 'belum_tersedia') {
                    this.hasil = {
                        mode: 'mitra',
                        status: 'belum_tersedia',
                        nama_mitra: mitra ? mitra.nama_mitra : 'Mitra Pembiayaan',
                        pesan: 'Informasi pembiayaan belum tersedia. Skema perhitungan resmi sedang dalam proses finalisasi nota kesepahaman (MoU) kemitraan dengan SMKN 7 Jember.',
                    };
                    return;
                }

                const plafon = Number(mitra.plafon_maksimal) || 0;
                const danaDibiayai = Math.min(biayaKeberangkatan, plafon);
                const dpPersen = Number(mitra.uang_muka_persen) || 0;
                const dpNominal = Math.round(danaDibiayai * (dpPersen / 100));
                const sisaBiayaSendiri = Math.max(0, biayaKeberangkatan - danaDibiayai);
                const totalUangMukaAwal = Math.round(dpNominal + sisaBiayaSendiri);
                const pokokCicilan = Math.max(0, danaDibiayai - dpNominal);

                let biayaAdmin = 0;
                if (mitra.biaya_administrasi_tipe === 'persentase') {
                    biayaAdmin = Math.round(pokokCicilan * (Number(mitra.biaya_administrasi_nilai) / 100));
                } else {
                    biayaAdmin = Number(mitra.biaya_administrasi_nilai) || 0;
                }

                const tenor = Number(this.form.tenor_bulan) || 12;
                let cicilanBulanan = 0;
                let totalPembayaran = 0;
                const bungaTahunan = Number(mitra.suku_bunga_tahunan) || 0;
                const marginBulanan = Number(mitra.margin_bulanan) || 0;

                if (mitra.metode_perhitungan === 'flat') {
                    const pokokPerBulan = pokokCicilan / tenor;
                    const totalBunga = pokokCicilan * (bungaTahunan / 100) * (tenor / 12);
                    cicilanBulanan = Math.round(pokokPerBulan + (totalBunga / tenor));
                    totalPembayaran = cicilanBulanan * tenor;
                } else if (mitra.metode_perhitungan === 'margin_syariah') {
                    const totalMargin = pokokCicilan * (marginBulanan / 100) * tenor;
                    cicilanBulanan = Math.round((pokokCicilan + totalMargin) / tenor);
                    totalPembayaran = cicilanBulanan * tenor;
                } else if (mitra.metode_perhitungan === 'anuitas') {
                    const r = (bungaTahunan / 100) / 12;
                    if (r > 0) {
                        cicilanBulanan = Math.round(pokokCicilan * (r * Math.pow(1 + r, tenor)) / (Math.pow(1 + r, tenor) - 1));
                    } else {
                        cicilanBulanan = Math.round(pokokCicilan / tenor);
                    }
                    totalPembayaran = cicilanBulanan * tenor;
                }

                const totalPengeluaran = Math.round(biayaHidup + pengeluaranLain + cicilanBulanan);
                const sisaPendapatan = Math.round(gajiKotorIdr - totalPengeluaran);

                this.hasil = {
                    mode: 'mitra',
                    status: 'tersedia',
                    pendapatan_kotor_idr: gajiKotorIdr,
                    dana_dibiayai: danaDibiayai,
                    total_uang_muka_awal: totalUangMukaAwal,
                    biaya_administrasi: biayaAdmin,
                    cicilan_bulanan: cicilanBulanan,
                    total_pembayaran_cicilan: totalPembayaran,
                    total_pengeluaran_bulanan: totalPengeluaran,
                    sisa_pendapatan_bulanan: sisaPendapatan,
                };
            }
        },

        formatRupiah(number) {
            if (isNaN(number) || number === null || number === undefined) return 'Rp 0';
            return 'Rp ' + Number(number).toLocaleString('id-ID');
        }
    }
}
</script>
@endsection
