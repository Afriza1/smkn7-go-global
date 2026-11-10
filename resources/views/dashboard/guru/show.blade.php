@extends('dashboard.layouts.dashboard')

@section('title', 'Pertimbangan BK ' . $pendaftaran->nomor_pendaftaran . ' | Guru BK')
@section('breadcrumb', 'Pertimbangan BK &bull; ' . $pendaftaran->nomor_pendaftaran)
@section('header_title', 'Formulir Pertimbangan Kesiapan Siswa')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    
    <!-- Top Bar Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.guru') }}" class="text-xs font-bold text-slate-500 hover:text-[#155EAA] flex items-center gap-1">
            &larr; Kembali ke Antrean Bimbingan Konseling
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $pendaftaran->getStatusBadgeClass() }}">
            {{ $pendaftaran->getStatusLabel() }}
        </span>
    </div>

    <!-- Applicant Summary Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Calon Peserta Program</span>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#0F2F5F]">
                    {{ $pendaftaran->nama_lengkap }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $pendaftaran->jenis_peserta === 'siswa' ? 'Siswa Aktif SMKN 7 Jember' : 'Alumni SMKN 7 Jember' }} &bull; Jurusan {{ $pendaftaran->jurusan }}
                </p>
            </div>
            <div class="text-right">
                <span class="text-xs font-mono text-slate-400 block">No. Pendaftaran:</span>
                <strong class="text-sm sm:text-base font-mono font-extrabold text-slate-900">{{ $pendaftaran->nomor_pendaftaran }}</strong>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6 text-xs">
            <div>
                <span class="text-slate-400 block mb-1">Pilihan LPK Mitra</span>
                <strong class="text-slate-900 text-sm block">{{ $pendaftaran->lpk->nama_lpk }}</strong>
                <span class="text-[11px] text-slate-500">{{ $pendaftaran->lpk->bidang_program }} &bull; Wilayah {{ $pendaftaran->lpk->wilayah_penempatan }}</span>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Aspirasi / Motivasi Peserta</span>
                <p class="text-slate-700 italic leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-200/60">
                    "{{ $pendaftaran->alasan_minat ?: 'Tidak ada catatan motivasi tambahan dari peserta.' }}"
                </p>
            </div>

            <div class="sm:col-span-2 pt-4 border-t border-slate-100">
                <span class="text-slate-400 block mb-1">Hasil Verifikasi Administrasi BKK</span>
                <div class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-200 text-slate-800 leading-relaxed">
                    <strong>Catatan Admin:</strong> "{{ $pendaftaran->catatan_admin }}"
                    <span class="text-[11px] text-slate-500 mt-1 block">
                        Diverifikasi oleh: <strong>{{ $pendaftaran->adminChecker?->name ?? 'Admin BKK' }}</strong> pada {{ $pendaftaran->admin_checked_at?->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Pertimbangan Guru BK -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-emerald-500/30 shadow-md">
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-slate-100">
            <div class="p-2 rounded-xl bg-emerald-100 text-[#166534]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h2 class="text-base sm:text-lg font-bold text-[#0F2F5F]">
                    Catatan Pertimbangan & Rekomendasi Guru BK
                </h2>
                <p class="text-xs text-slate-500">
                    Tuliskan evaluasi kesiapan mental, ketahanan adaptasi budaya, dan catatan pendukung untuk pertimbangan Kepala Sekolah.
                </p>
            </div>
        </div>

        <form action="{{ route('dashboard.guru.pertimbangan', $pendaftaran->id) }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="catatan_bk" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Pertimbangan Kesiapan & Rekomendasi Karakter <span class="text-rose-500">*</span>
                </label>
                <textarea name="catatan_bk" 
                          id="catatan_bk" 
                          rows="4" 
                          required
                          placeholder="Contoh: Siswa memiliki ketahanan mental dan motivasi belajar bahasa yang sangat baik. Rekam jejak kedisiplinan dan absensi kelas memuaskan. Direkomendasikan penuh untuk mengikuti seleksi program Karier Jepang..."
                          class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-white focus:outline-hidden focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">{{ old('catatan_bk', $pendaftaran->catatan_bk) }}</textarea>
                <span class="text-[11px] text-slate-400 mt-1 block">
                    Catatan ini akan langsung ditampilkan di meja peninjauan persetujuan Kepala Sekolah.
                </span>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('dashboard.guru') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                    &larr; Batalkan
                </a>

                <button type="submit" 
                        class="px-6 py-3 rounded-xl bg-[#166534] hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer flex items-center gap-2">
                    <span>Teruskan Rekomendasi ke Kepala Sekolah</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Progress Tracker -->
    <x-career-progress-tracker :steps="$trackerSteps" />

</div>
@endsection
