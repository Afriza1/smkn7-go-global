@extends('dashboard.layouts.dashboard')

@section('title', 'Detail Riwayat Simulasi | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Detail Riwayat Simulasi')
@section('header_title', 'Rincian Hasil Simulasi Finansial')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto print:max-w-full print:space-y-4">
    
    <!-- Top Action Bar (Hidden on print) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard.simulasi.riwayat') }}" class="hover:text-[#155EAA] flex items-center gap-1 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Riwayat</span>
                </a>
                <span>&rsaquo;</span>
                <span class="text-slate-700 font-bold">Detail Snapshot #{{ $riwayat->id }}</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                {{ $riwayat->judul_simulasi }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Disimpan pada {{ $riwayat->created_at->translatedFormat('d F Y') }} pukul {{ $riwayat->created_at->format('H:i') }} WIB
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-2xs transition-all cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / PDF</span>
            </button>

            <a href="{{ route('dashboard.simulasi.index', ['mode' => $riwayat->jenis_simulasi]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span>Simulasi Baru</span>
            </a>
        </div>
    </div>

    <!-- Snapshot Immutable Notice -->
    <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200/80 text-amber-900 flex items-start gap-3.5 shadow-2xs">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        <div class="text-xs leading-relaxed">
            <strong class="font-bold block text-amber-950 mb-0.5">Catatan Snapshot Terkunci (Immutable Snapshot)</strong>
            <span>Data perhitungan di bawah ini disimpan secara permanen berdasarkan parameter, kurs valuta asing, dan formula yang berlaku saat simulasi dibuat. Perubahan kurs atau kebijakan di masa mendatang tidak akan memodifikasi angka pada dokumen ini.</span>
        </div>
    </div>

    <!-- Header Card Ringkasan Utama -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg {{ $riwayat->jenis_simulasi === 'mandiri' ? 'bg-blue-100 text-[#155EAA]' : 'bg-purple-100 text-purple-700' }}">
                    {{ $riwayat->jenis_simulasi === 'mandiri' ? 'M' : 'P' }}
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Jenis Rencana Finansial</span>
                    <h2 class="text-lg sm:text-xl font-bold text-[#0F2F5F]">
                        {{ $riwayat->jenis_simulasi === 'mandiri' ? 'Simulasi Keberangkatan Mandiri' : 'Simulasi Mitra Pembiayaan: ' . ($riwayat->nama_mitra_snapshot ?? 'Mitra Terdaftar') }}
                    </h2>
                </div>
            </div>

            <div class="text-right">
                <span class="text-[11px] text-slate-400 block">Pemilik Simulasi</span>
                <span class="text-xs font-bold text-slate-800">{{ $riwayat->user->name }}</span>
                <span class="text-[10px] text-slate-500 block">NISN / Identitas: {{ $riwayat->user->nisn ?? 'Alumni' }}</span>
            </div>
        </div>

        <!-- 3 Highlight Metric Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-1">Biaya Keberangkatan</span>
                <div class="text-xl sm:text-2xl font-extrabold text-[#0F2F5F]">
                    Rp {{ number_format($riwayat->snapshot_hasil['total_biaya_keberangkatan'] ?? 0, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">
                    {{ $riwayat->jenis_simulasi === 'mandiri' ? 'Sumber: ' . ($riwayat->snapshot_input['sumber_dana'] ?? 'Tabungan Pribadi') : 'Estimasi Pengajuan Mitra' }}
                </span>
            </div>

            <div class="p-5 rounded-2xl bg-blue-50/70 border border-blue-100">
                <span class="text-[11px] font-semibold text-[#155EAA] uppercase tracking-wider block mb-1">Gaji Kotor Dikonversi</span>
                <div class="text-xl sm:text-2xl font-extrabold text-[#155EAA]">
                    Rp {{ number_format($riwayat->snapshot_hasil['pendapatan_kotor_idr'] ?? 0, 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-blue-900/70 mt-1 block font-mono">
                    &yen; {{ number_format($riwayat->snapshot_input['gaji_jpy'] ?? 0, 0, ',', '.') }} @ Rp {{ number_format($riwayat->snapshot_parameter['kurs_jpy_idr'] ?? 108.5, 2, ',', '.') }}
                </span>
            </div>

            <div class="p-5 rounded-2xl {{ ($riwayat->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0) > 0 ? 'bg-emerald-50/80 border-emerald-100 text-emerald-950' : 'bg-rose-50 border-rose-100 text-rose-950' }}">
                <span class="text-[11px] font-semibold uppercase tracking-wider block mb-1 {{ ($riwayat->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0) > 0 ? 'text-[#166534]' : 'text-rose-700' }}">
                    {{ $riwayat->jenis_simulasi === 'mandiri' ? 'Sisa Bersih / Tabungan Bulanan' : 'Sisa Pendapatan Setelah Cicilan' }}
                </span>
                <div class="text-xl sm:text-2xl font-extrabold {{ ($riwayat->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0) > 0 ? 'text-[#166534]' : 'text-rose-600' }}">
                    Rp {{ number_format($riwayat->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0, 0, ',', '.') }}
                </div>
                <span class="text-[11px] mt-1 block {{ ($riwayat->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0) > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                    {{ ($riwayat->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0) > 0 ? 'Surplus finansial positif' : 'Defisit (pengeluaran melampaui pendapatan)' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Rincian Detail 2 Kolom: Asumsi Snapshot & Rincian Arus Kas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Kolom Kiri: Parameter & Rincian Input (Snapshot) -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-6">
            <div>
                <h3 class="text-sm font-bold text-[#0F2F5F] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#155EAA]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Parameter & Asumsi yang Digunakan</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Konfigurasi yang tercatat saat pembuatan riwayat ini</p>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Nilai Kurs JPY ke IDR</span>
                    <strong class="text-slate-800 font-mono font-bold">1 JPY = Rp {{ number_format($riwayat->snapshot_parameter['kurs_jpy_idr'] ?? 108.5, 2, ',', '.') }}</strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Gaji Pokok di Jepang (Input)</span>
                    <strong class="text-slate-800 font-bold">&yen; {{ number_format($riwayat->snapshot_input['gaji_jpy'] ?? 0, 0, ',', '.') }}</strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Biaya Hidup Bulanan di Jepang</span>
                    <strong class="text-slate-800 font-bold">Rp {{ number_format($riwayat->snapshot_hasil['biaya_hidup_bulanan'] ?? 0, 0, ',', '.') }}</strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Potongan & Pengeluaran Lainnya</span>
                    <strong class="text-slate-800 font-bold">Rp {{ number_format($riwayat->snapshot_hasil['pengeluaran_lainnya'] ?? 0, 0, ',', '.') }}</strong>
                </div>

                @if($riwayat->jenis_simulasi === 'mandiri')
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Rencana Sumber Dana</span>
                        <strong class="text-slate-800 font-bold">{{ $riwayat->snapshot_input['sumber_dana'] ?? 'Tabungan Mandiri / Keluarga' }}</strong>
                    </div>
                @else
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Mitra Lembaga Pembiayaan</span>
                        <strong class="text-slate-800 font-bold">{{ $riwayat->snapshot_parameter['mitra_nama'] ?? $riwayat->nama_mitra_snapshot }}</strong>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Skema & Metode Bunga</span>
                        <strong class="text-slate-800 font-bold uppercase">{{ $riwayat->snapshot_parameter['mitra_skema'] ?? '-' }} ({{ $riwayat->snapshot_parameter['mitra_metode'] ?? '-' }})</strong>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Tenor Pinjaman Dipilih</span>
                        <strong class="text-slate-800 font-bold">{{ $riwayat->snapshot_input['tenor_bulan'] ?? '-' }} Bulan</strong>
                    </div>
                @endif

                <div class="pt-2 text-[11px] text-slate-400">
                    <span class="font-semibold block text-slate-500">Sumber Referensi Kurs:</span>
                    <span>{{ $riwayat->snapshot_parameter['sumber_informasi'] ?? $riwayat->snapshot_parameter['sumber_informasi_param'] ?? 'Standar Estimasi SMKN 7 Jember' }}</span>
                </div>
            </div>

            @if($riwayat->catatan_pengguna)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <strong class="text-xs font-bold text-slate-700 block mb-1">Catatan Pribadi Anda:</strong>
                    <p class="text-xs text-slate-600 leading-relaxed italic">"{{ $riwayat->catatan_pengguna }}"</p>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Rincian Hasil Kalkulasi -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-6">
            <div>
                <h3 class="text-sm font-bold text-[#0F2F5F] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#166534]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Analisis Kelayakan Finansial</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Penjabaran kemampuan pengembalian dan sisa dana tabungan</p>
            </div>

            @if($riwayat->jenis_simulasi === 'mandiri')
                <!-- Hasil Mode Mandiri -->
                <div class="space-y-4">
                    <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200">
                        <span class="text-[11px] font-bold text-amber-900 block mb-1 uppercase tracking-wide">
                            Estimasi Waktu Balik Modal (Payback Period)
                        </span>

                        @if(isset($riwayat->snapshot_hasil['waktu_pengembalian_bulan']) && $riwayat->snapshot_hasil['waktu_pengembalian_bulan'])
                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl sm:text-4xl font-extrabold text-amber-900">
                                    {{ $riwayat->snapshot_hasil['waktu_pengembalian_bulan'] }}
                                </span>
                                <span class="text-sm font-bold text-amber-800">Bulan Kerja</span>
                                <span class="text-xs text-amber-700">(&plusmn; {{ round($riwayat->snapshot_hasil['waktu_pengembalian_bulan'] / 12, 1) }} Tahun)</span>
                            </div>
                            <p class="text-xs text-amber-800/90 mt-2 leading-relaxed">
                                {{ $riwayat->snapshot_hasil['keterangan_pengembalian'] ?? 'Biaya keberangkatan mandiri diperkirakan impas dalam jangka waktu tersebut.' }}
                            </p>
                        @else
                            <div class="text-rose-600 font-bold text-sm">
                                Tidak Dapat Dihitung (Surplus &le; 0)
                            </div>
                            <p class="text-xs text-rose-700 mt-1">
                                {{ $riwayat->snapshot_hasil['keterangan_pengembalian'] ?? 'Pendapatan tidak mencukupi untuk menutup pengeluaran bulanan sehingga pengembalian modal tidak dapat diproyeksikan.' }}
                            </p>
                        @endif
                    </div>

                    <div class="space-y-2.5 text-xs text-slate-700">
                        <div class="flex justify-between py-1.5 border-b border-slate-100">
                            <span>Pendapatan Kotor Bulanan</span>
                            <strong class="font-bold">Rp {{ number_format($riwayat->snapshot_hasil['pendapatan_kotor_idr'] ?? 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 text-rose-600">
                            <span>Total Pengeluaran & Biaya Hidup</span>
                            <strong>- Rp {{ number_format($riwayat->snapshot_hasil['total_pengeluaran_bulanan'] ?? 0, 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex justify-between py-2 border-t-2 border-slate-200 font-bold text-sm text-[#166534]">
                            <span>Surplus Bersih per Bulan</span>
                            <span>Rp {{ number_format($riwayat->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

            @else
                <!-- Hasil Mode Mitra Pembiayaan -->
                @if(isset($riwayat->snapshot_hasil['skema_tersedia']) && ! $riwayat->snapshot_hasil['skema_tersedia'])
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs">
                        <strong class="font-bold text-sm block mb-1">Informasi Pembiayaan Belum Tersedia</strong>
                        <p>{{ $riwayat->snapshot_hasil['pesan_skema'] ?? 'Skema perhitungan resmi mitra ini masih dalam proses penyesuaian regulasi sekolah dan mitra perbankan.' }}</p>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="p-5 rounded-2xl bg-purple-50/70 border border-purple-200">
                            <span class="text-[11px] font-bold text-purple-900 block mb-1 uppercase tracking-wide">
                                Estimasi Cicilan Angsuran Bulanan
                            </span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl sm:text-3xl font-extrabold text-purple-950">
                                    Rp {{ number_format($riwayat->snapshot_hasil['cicilan_bulanan'] ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="text-xs font-bold text-purple-800">/ bulan</span>
                            </div>
                            <span class="text-xs text-purple-700 block mt-1">
                                Tenor: {{ $riwayat->snapshot_hasil['tenor_bulan'] ?? $riwayat->snapshot_input['tenor_bulan'] ?? '-' }} Bulan &bull; Metode: {{ strtoupper($riwayat->snapshot_hasil['metode_perhitungan'] ?? '-') }}
                            </span>
                        </div>

                        <div class="space-y-2.5 text-xs text-slate-700">
                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span>Plafon Pinjaman yang Diajukan</span>
                                <strong class="font-bold">Rp {{ number_format($riwayat->snapshot_hasil['plafon_diajukan'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span>Uang Muka / DP</span>
                                <strong class="font-bold">Rp {{ number_format($riwayat->snapshot_hasil['uang_muka_dp'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span>Biaya Administrasi</span>
                                <strong class="font-bold">Rp {{ number_format($riwayat->snapshot_hasil['biaya_admin'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span>Total Pokok Pinjaman Finansial</span>
                                <strong class="font-bold">Rp {{ number_format($riwayat->snapshot_hasil['pokok_pinjaman'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-100">
                                <span>Total Estimasi Pengembalian (Pokok + Margin)</span>
                                <strong class="font-bold">Rp {{ number_format($riwayat->snapshot_hasil['total_pengembalian'] ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex justify-between py-2 border-t-2 border-slate-200 font-bold text-sm text-[#166534]">
                                <span>Sisa Bersih Setelah Cicilan</span>
                                <span>Rp {{ number_format($riwayat->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0, 0, ',', '.') }} / bln</span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

        </div>

    </div>

    <!-- Disclaimer Resmi -->
    <div class="p-6 rounded-3xl bg-slate-100/90 border border-slate-200 text-slate-600 text-xs leading-relaxed space-y-2">
        <strong class="font-bold text-slate-800 text-sm block">Disclaimer Resmi & Ketentuan Simulasi:</strong>
        <p>
            1. Angka-angka pada simulasi ini merupakan alat bantu estimasi dan perencanaan awal bagi siswa dan alumni SMKN 7 Jember, bukan merupakan jaminan pendapatan pasti di Jepang, bukan persetujuan kredit pinjaman, ataupun penawaran pembiayaan resmi.
        </p>
        <p>
            2. Gaji riil, lembur, pajak penghasilan Jepang (shoutoku-zei, jumin-zei), asuransi sosial (shakai hoken), dan potongan asrama akan disesuaikan dengan kontrak kerja resmi yang disepakati bersama pihak penerima kerja (kumiai / koushuu) di Jepang.
        </p>
        <p>
            3. Skema pembiayaan mitra tunduk pada verifikasi berkas, persetujuan orang tua/wali siswa, dan syarat ketentuan lembaga mitra terkait.
        </p>
    </div>

    <!-- Delete Action Button (Bottom) -->
    <div class="flex justify-end pt-4 print:hidden">
        <form action="{{ route('dashboard.simulasi.riwayat.destroy', $riwayat->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin hendak menghapus riwayat simulasi ini secara permanen?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="px-4 py-2 rounded-xl text-rose-600 hover:bg-rose-50 text-xs font-bold transition-all inline-flex items-center gap-2 cursor-pointer border border-rose-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Riwayat Ini</span>
            </button>
        </form>
    </div>

</div>
@endsection
