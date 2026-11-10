@extends('dashboard.layouts.dashboard')

@section('title', 'Dashboard Guru BK | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Guru BK &bull; Pertimbangan Kesiapan Siswa')
@section('header_title', 'Pertimbangan Bimbingan Konseling & Kesiapan')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-[#166534] border border-emerald-200 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                <span>Bimbingan Konseling &bull; SMKN 7 Jember</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                Pertimbangan Kesiapan Siswa Karier Jepang
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Evaluasi kesiapan mental, kedisiplinan, motivasi, dan rekomendasi karakter calon peserta sebelum usulan pendaftaran diajukan kepada Kepala Sekolah.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif


    <!-- Banner Akses Cepat Konseling Digital -->
    <div class="bg-gradient-to-r from-[#0F2F5F] via-[#166534] to-[#0A1E3F] rounded-3xl p-6 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-5">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#FBBF24] text-xs font-bold">
                <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Layanan Konseling Digital</span>
            </div>
            <h3 class="text-lg font-bold">Pengelolaan Permintaan Konsultasi Siswa &amp; Alumni</h3>
            <p class="text-xs text-slate-200">
                Kelola jadwal konsultasi, aktifkan ruang chat real-time, dan catat hasil observasi serta tindak lanjut bimbingan secara rahasia.
            </p>
        </div>
        <a href="{{ route('dashboard.guru.konseling.index') }}" 
           class="px-5 py-3 rounded-2xl bg-[#FBBF24] hover:bg-amber-400 text-[#0F2F5F] text-xs font-bold shadow-sm transition-all flex items-center gap-2 cursor-pointer flex-shrink-0">
            <span>Buka Panel Konseling Digital</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>

    <!-- Quick Counter KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 shadow-xs">
            <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block mb-1">Menunggu Pertimbangan BK</span>
            <div class="text-3xl font-extrabold text-amber-900">{{ $menungguPertimbangan->count() }}</div>
            <p class="text-xs text-amber-700 mt-1">Peserta telah lolos verifikasi administrasi BKK</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Telah Diteruskan ke KS</span>
            <div class="text-3xl font-extrabold text-[#155EAA]">{{ $riwayatPertimbangan->count() }}</div>
            <p class="text-xs text-slate-500 mt-1">Rekomendasi berhasil dicatat</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Peran Penguji</span>
            <strong class="text-sm font-bold text-slate-800 block mt-1">{{ Auth::user()->name }}</strong>
            <p class="text-xs text-slate-500 mt-0.5">Guru BK / Pembimbing Karier Vokasi</p>
        </div>
    </div>

    <!-- ==============================================================
         TABEL SISWA MENUNGGU PERTIMBANGAN BK
         ============================================================== -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#0F2F5F]">
                Antrean Usulan Menunggu Pertimbangan BK ({{ $menungguPertimbangan->count() }} Peserta)
            </h2>
            <span class="text-xs text-amber-700 font-semibold bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                Prioritas Evaluasi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. Pendaftaran</th>
                        <th class="px-5 py-3.5">Nama Siswa / Alumni</th>
                        <th class="px-5 py-3.5">Jurusan</th>
                        <th class="px-5 py-3.5">LPK Mitra</th>
                        <th class="px-5 py-3.5">Hasil Verifikasi Admin</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($menungguPertimbangan as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $item->nomor_pendaftaran }}
                            </td>
                            <td class="px-5 py-4">
                                <strong class="text-slate-900 text-sm block">{{ $item->nama_lengkap }}</strong>
                                <span class="text-[11px] text-slate-500">{{ $item->jenis_peserta === 'siswa' ? 'Siswa Aktif' : 'Alumni' }}</span>
                            </td>
                            <td class="px-5 py-4 font-medium">{{ $item->jurusan }}</td>
                            <td class="px-5 py-4">
                                <span class="block font-semibold text-slate-800">{{ $item->lpk->nama_lpk }}</span>
                                <span class="text-[11px] text-slate-400">{{ $item->lpk->bidang_program }}</span>
                            </td>
                            <td class="px-5 py-4 max-w-xs">
                                <p class="text-slate-600 truncate bg-slate-50 p-1.5 rounded border border-slate-200/60" title="{{ $item->catatan_admin }}">
                                    "{{ $item->catatan_admin }}"
                                </p>
                                <span class="text-[10px] text-slate-400 mt-0.5 block">Oleh: {{ $item->adminChecker?->name ?? 'Admin BKK' }}</span>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('dashboard.guru.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#166534] hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs">
                                    <span>Beri Pertimbangan</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Saat ini tidak ada usulan peserta yang menunggu pertimbangan Guru BK.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Usulan yang Telah Diteruskan -->
    @if($riwayatPertimbangan->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6">
            <h3 class="text-sm font-bold text-[#0F2F5F] mb-4 pb-3 border-b border-slate-100">
                10 Usulan Terakhir yang Telah Diteruskan ke Kepala Sekolah
            </h3>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($riwayatPertimbangan as $riw)
                    <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <strong class="text-slate-900 block font-bold">{{ $riw->nama_lengkap }} ({{ $riw->nomor_pendaftaran }})</strong>
                            <p class="text-slate-600 mt-0.5 italic">"{{ $riw->catatan_bk }}"</p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $riw->getStatusBadgeClass() }}">
                                {{ $riw->getStatusLabel() }}
                            </span>
                            <span class="text-slate-400 text-[11px] font-mono">{{ $riw->bk_checked_at?->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
