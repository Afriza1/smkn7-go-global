@extends('dashboard.layouts.dashboard')

@section('title', 'Pemeriksaan Berkas ' . $pendaftaran->nomor_pendaftaran . ' | Admin BKK')
@section('breadcrumb', 'Pemeriksaan Berkas &bull; ' . $pendaftaran->nomor_pendaftaran)
@section('header_title', 'Pemeriksaan Berkas Peserta')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Top Bar Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                <a href="{{ route('dashboard.admin') }}" class="hover:text-[#155EAA]">&larr; Kembali ke Daftar Pengajuan</a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-bold">Pemeriksaan {{ $pendaftaran->nomor_pendaftaran }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                    {{ $pendaftaran->nama_lengkap }}
                </h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $pendaftaran->getStatusBadgeClass() }}">
                    {{ $pendaftaran->getStatusLabel() }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('karier-jepang.pendaftaran.show', $pendaftaran->id) }}" 
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-[#155EAA] text-xs font-bold shadow-2xs transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Halaman Detail Peserta</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800">
            <strong class="font-bold block mb-1">Peringatan Tindakan:</strong>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ==============================================================
         1. PANEL TINDAKAN VERIFIKASI ADMIN (CALL TO ACTION)
         ============================================================== -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-[#155EAA]/30 shadow-md">
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
            <div class="p-2 rounded-xl bg-blue-100 text-[#155EAA]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#0F2F5F]">
                    Tindakan Keputusan Administrasi BKK
                </h2>
                <p class="text-xs text-slate-500">
                    Pilih keputusan verifikasi untuk menentukan kelanjutan berkas ke tahap bimbingan konseling Guru BK.
                </p>
            </div>
        </div>

        <!-- Form Tindakan Admin -->
        <form action="{{ route('dashboard.admin.verifikasi', $pendaftaran->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Opsi 1: Loloskan ke BK -->
                <label class="p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 hover:bg-emerald-50/40 hover:border-emerald-500 transition-all cursor-pointer relative has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                    <input type="radio" name="tindakan" value="lolos" class="mb-2 text-emerald-600" checked>
                    <strong class="block text-sm font-bold text-slate-900 mb-1">1. Lolos Administrasi</strong>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Dokumen lengkap, sah, dan valid. Berkas akan diteruskan ke meja Guru BK.
                    </p>
                </label>

                <!-- Opsi 2: Kembalikan untuk Perbaikan -->
                <label class="p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 hover:bg-orange-50/40 hover:border-orange-500 transition-all cursor-pointer relative has-[:checked]:border-orange-600 has-[:checked]:bg-orange-50/40">
                    <input type="radio" name="tindakan" value="perbaikan" class="mb-2 text-orange-600">
                    <strong class="block text-sm font-bold text-slate-900 mb-1">2. Kembalikan (Revisi)</strong>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Dokumen buram, salah, atau belum bermaterai. Peserta dapat mengunggah ulang.
                    </p>
                </label>

                <!-- Opsi 3: Tolak Administrasi -->
                <label class="p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 hover:bg-rose-50/40 hover:border-rose-500 transition-all cursor-pointer relative has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50/40">
                    <input type="radio" name="tindakan" value="tolak" class="mb-2 text-rose-600">
                    <strong class="block text-sm font-bold text-slate-900 mb-1">3. Tolak Pengajuan</strong>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Tidak memenuhi persyaratan batas usia/keabsahan sekolah. Berkas ditolak resmi.
                    </p>
                </label>

            </div>

            <!-- Catatan Wajib jika revisi/tolak -->
            <div>
                <label for="catatan" class="block text-xs font-bold text-slate-700 mb-1">
                    Catatan Verifikator BKK (Wajib diisi jika mengembalikan atau menolak berkas)
                </label>
                <textarea name="catatan" 
                          id="catatan" 
                          rows="2" 
                          placeholder="Tuliskan catatan pemeriksaan administrasi secara jelas dan objektif..."
                          class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white focus:outline-hidden focus:border-[#155EAA]">{{ old('catatan', $pendaftaran->catatan_admin) }}</textarea>
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-md transition-all cursor-pointer">
                    Simpan & Eksekusi Putusan Administrasi
                </button>
            </div>
        </form>

        <!-- Pembaruan Status Pasca-Persetujuan (Jika Disetujui Sekolah) -->
        @if(in_array($pendaftaran->status, [
            'disetujui_sekolah', 'menunggu_seleksi_lpk', 'seleksi_berlangsung', 'lolos_seleksi',
            'tidak_lolos_seleksi', 'pelatihan', 'mcu', 'pengurusan_dokumen', 'persiapan_keberangkatan',
            'diberangkatkan', 'mengundurkan_diri', 'proses_dihentikan'
        ]))
            <div class="mt-8 pt-6 border-t border-slate-200">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm font-bold text-[#0F2F5F]">
                        Pembaruan Progres Seleksi & Pembinaan di LPK (11 Status)
                    </h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    Peserta telah disetujui sekolah. Admin memperbarui perkembangan peserta berdasarkan laporan koordinasi dari pihak LPK mitra.
                </p>

                <form action="{{ route('dashboard.admin.status-lpk', $pendaftaran->id) }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    @csrf
                    <div>
                        <label for="status_lpk" class="block text-xs font-bold text-slate-700 mb-1">Status Progres LPK</label>
                        <select name="status_lpk" id="status_lpk" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white font-semibold">
                            <option value="menunggu_seleksi_lpk" @selected($pendaftaran->status === 'menunggu_seleksi_lpk')>1. Menunggu Seleksi LPK</option>
                            <option value="seleksi_berlangsung" @selected($pendaftaran->status === 'seleksi_berlangsung')>2. Seleksi LPK Berlangsung</option>
                            <option value="lolos_seleksi" @selected($pendaftaran->status === 'lolos_seleksi')>3. Lolos Seleksi LPK</option>
                            <option value="tidak_lolos_seleksi" @selected($pendaftaran->status === 'tidak_lolos_seleksi')>4. Tidak Lolos Seleksi LPK</option>
                            <option value="pelatihan" @selected($pendaftaran->status === 'pelatihan')>5. Pelatihan dan Pembekalan (N4/N5)</option>
                            <option value="mcu" @selected($pendaftaran->status === 'mcu')>6. Medical Check Up (MCU)</option>
                            <option value="pengurusan_dokumen" @selected($pendaftaran->status === 'pengurusan_dokumen')>7. Pengurusan Dokumen (COE & Visa)</option>
                            <option value="persiapan_keberangkatan" @selected($pendaftaran->status === 'persiapan_keberangkatan')>8. Persiapan Keberangkatan</option>
                            <option value="diberangkatkan" @selected($pendaftaran->status === 'diberangkatkan')>9. Diberangkatkan ke Jepang</option>
                            <option value="mengundurkan_diri" @selected($pendaftaran->status === 'mengundurkan_diri')>10. Mengundurkan Diri</option>
                            <option value="proses_dihentikan" @selected($pendaftaran->status === 'proses_dihentikan')>11. Proses Dihentikan</option>
                        </select>
                    </div>

                    <div>
                        <label for="catatan_lpk" class="block text-xs font-bold text-slate-700 mb-1">Keterangan Tambahan LPK</label>
                        <input type="text" 
                               name="catatan_lpk" 
                               id="catatan_lpk" 
                               value="{{ $pendaftaran->catatan_lpk_admin }}" 
                               placeholder="Contoh: Lulus wawancara user Nagoya..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white">
                    </div>

                    <button type="submit" class="py-2 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-all shadow-xs">
                        Perbarui Status LPK
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- ==============================================================
         2. DOKUMEN PESERTA (PEMERIKSAAN DOKUMEN SENSITIF PRIVATE)
         ============================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Dokumen 1 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dokumen 1</span>
                <strong class="text-sm font-bold text-slate-900 block mb-1">KTP / Kartu Pelajar</strong>
                <p class="text-[11px] text-slate-500">Bukti identitas resmi kependudukan atau kesiswaan.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">Tersedia</span>
                <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'identitas']) }}" 
                   target="_blank" 
                   class="text-xs font-bold text-[#155EAA] hover:underline flex items-center gap-1">
                    <span>Buka File</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>

        <!-- Dokumen 2 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dokumen 2</span>
                <strong class="text-sm font-bold text-slate-900 block mb-1">Kartu Keluarga (KK)</strong>
                <p class="text-[11px] text-slate-500">Legalitas hubungan keluarga & domisili.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">Tersedia</span>
                <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'kk']) }}" 
                   target="_blank" 
                   class="text-xs font-bold text-[#155EAA] hover:underline flex items-center gap-1">
                    <span>Buka File</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>

        <!-- Dokumen 3 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dokumen 3</span>
                <strong class="text-sm font-bold text-slate-900 block mb-1">Rapor / Ijazah</strong>
                <p class="text-[11px] text-slate-500">Rekam nilai kompetensi kejuruan.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">Tersedia</span>
                <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'rapor_ijazah']) }}" 
                   target="_blank" 
                   class="text-xs font-bold text-[#155EAA] hover:underline flex items-center gap-1">
                    <span>Buka File</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>

        <!-- Dokumen 4 -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Dokumen 4</span>
                <strong class="text-sm font-bold text-slate-900 block mb-1">Surat Pernyataan Wali</strong>
                <p class="text-[11px] text-slate-500">Persetujuan orang tua/wali bermaterai sah.</p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">Tersedia</span>
                <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'surat_ortu']) }}" 
                   target="_blank" 
                   class="text-xs font-bold text-[#155EAA] hover:underline flex items-center gap-1">
                    <span>Buka File</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>

    </div>

    <!-- Progress Tracker Full View -->
    <x-career-progress-tracker :steps="$trackerSteps" />

</div>
@endsection
