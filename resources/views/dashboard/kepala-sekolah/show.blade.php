@extends('dashboard.layouts.dashboard')

@section('title', 'Putusan Usulan ' . $pendaftaran->nomor_pendaftaran . ' | Kepala Sekolah')
@section('breadcrumb', 'Kepala Sekolah &bull; Putusan Usulan ' . $pendaftaran->nomor_pendaftaran)
@section('header_title', 'Meja Putusan Eksekutif Kepala Sekolah')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    
    <!-- Top Bar Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                <a href="{{ route('dashboard.kepala-sekolah') }}" class="hover:text-[#155EAA]">&larr; Kembali ke Dashboard Kepala Sekolah</a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-bold">Usulan {{ $pendaftaran->nomor_pendaftaran }}</span>
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

        <a href="{{ route('karier-jepang.pendaftaran.show', $pendaftaran->id) }}" 
           target="_blank"
           class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-[#155EAA] text-xs font-bold shadow-2xs transition-all flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            <span>Buka Detail Peserta</span>
        </a>
    </div>

    <!-- ==============================================================
         1. DUA PILAR PERTIMBANGAN: ADMIN BKK & GURU BK
         ============================================================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Hasil Verifikasi Admin BKK -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-[#155EAA] mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#155EAA]"></span>
                    <span>1. Hasil Pemeriksaan Administrasi (BKK)</span>
                </div>
                <div class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-100 text-xs text-slate-800 leading-relaxed mb-4">
                    "{{ $pendaftaran->catatan_admin ?: 'Seluruh berkas administrasi dan surat pernyataan wali terverifikasi sah.' }}"
                </div>
            </div>
            <div class="text-[11px] text-slate-400 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span>Pemeriksa: <strong>{{ $pendaftaran->adminChecker?->name ?? 'Admin BKK' }}</strong></span>
                <span class="font-mono">{{ $pendaftaran->admin_checked_at?->format('d M Y') }}</span>
            </div>
        </div>

        <!-- Rekomendasi Guru BK -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-[#166534] mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#166534]"></span>
                    <span>2. Pertimbangan Kesiapan Siswa (Guru BK)</span>
                </div>
                <div class="p-3.5 bg-emerald-50/60 rounded-xl border border-emerald-100 text-xs text-slate-800 leading-relaxed mb-4">
                    "{{ $pendaftaran->catatan_bk ?: 'Siswa memiliki rekam jejak kedisiplinan baik dan direkomendasikan untuk program luar negeri.' }}"
                </div>
            </div>
            <div class="text-[11px] text-slate-400 pt-3 border-t border-slate-100 flex items-center justify-between">
                <span>Konselor BK: <strong>{{ $pendaftaran->bkChecker?->name ?? 'Guru BK' }}</strong></span>
                <span class="font-mono">{{ $pendaftaran->bk_checked_at?->format('d M Y') }}</span>
            </div>
        </div>

    </div>

    <!-- Ringkasan Identitas & Pilihan LPK -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs text-xs">
        <h3 class="text-sm font-bold text-[#0F2F5F] mb-4 pb-3 border-b border-slate-100">
            Ringkasan Profil Usulan Peserta
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <span class="text-slate-400 block mb-0.5">Status Calon</span>
                <strong class="text-slate-900 text-sm block">
                    {{ $pendaftaran->jenis_peserta === 'siswa' ? 'Siswa Aktif (Kelas XII)' : 'Alumni SMKN 7 Jember' }}
                </strong>
            </div>

            <div>
                <span class="text-slate-400 block mb-0.5">Jurusan / Kejuruan</span>
                <strong class="text-[#155EAA] text-sm block">{{ $pendaftaran->jurusan }}</strong>
            </div>

            <div>
                <span class="text-slate-400 block mb-0.5">Mitra LPK Yang Dituju</span>
                <strong class="text-slate-900 text-sm block">{{ $pendaftaran->lpk->nama_lpk }}</strong>
            </div>

            <div>
                <span class="text-slate-400 block mb-0.5">Wilayah & Program</span>
                <strong class="text-slate-900 text-sm block">{{ $pendaftaran->lpk->bidang_program }}</strong>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         2. PANEL PUTUSAN RESMI KEPALA SEKOLAH
         ============================================================== -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-[#0F2F5F] shadow-lg">
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
            <div class="p-2.5 rounded-xl bg-[#0F2F5F] text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold text-[#0F2F5F]">
                    Formulir Putusan Resmi Kepala Sekolah
                </h2>
                <p class="text-xs text-slate-500">
                    Berdasarkan laporan hasil pemeriksaan administrasi dan pertimbangan Guru BK di atas, tentukan putusan pimpinan.
                </p>
            </div>
        </div>

        <form action="{{ route('dashboard.kepala-sekolah.putusan', $pendaftaran->id) }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="catatan" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Catatan Keputusan / Wejangan Kepala Sekolah (Wajib diisi jika menolak usulan)
                </label>
                <textarea name="catatan" 
                          id="catatan" 
                          rows="3" 
                          placeholder="Tuliskan arahan, motivasi, atau alasan penolakan bagi peserta..."
                          class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white focus:outline-hidden focus:border-[#0F2F5F]">{{ old('catatan', $pendaftaran->catatan_kepala_sekolah) }}</textarea>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                <a href="{{ route('dashboard.kepala-sekolah') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                    &larr; Batalkan & Kembali
                </a>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Tombol Tolak -->
                    <button type="submit" 
                            name="putusan" 
                            value="tolak"
                            onclick="return confirm('Apakah Anda yakin hendak MENOLAK usulan pendaftaran peserta ini?')"
                            class="flex-1 sm:flex-none px-6 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                        Tolak Usulan Pendaftaran
                    </button>

                    <!-- Tombol Setujui -->
                    <button type="submit" 
                            name="putusan" 
                            value="setujui"
                            onclick="return confirm('Apakah Anda yakin hendak MENYETUJUI usulan pendaftaran peserta ini menuju program Karier Jepang?')"
                            class="flex-1 sm:flex-none px-8 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Setujui Pendaftaran Program</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Progress Tracker -->
    <x-career-progress-tracker :steps="$trackerSteps" />

</div>
@endsection
