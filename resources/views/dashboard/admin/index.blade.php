@extends('dashboard.layouts.dashboard')

@section('title', 'Kelola Karier Jepang | Admin BKK SMKN 7 Jember')
@section('breadcrumb', 'Admin &bull; Pengelolaan Karier Jepang')
@section('header_title', 'Pengelolaan Pendaftaran Karier Jepang')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Top Bar Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#155EAA] border border-blue-200 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                <span>Bursa Kerja Khusus (BKK) &bull; SMKN 7 Jember</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                Pusat Pengelolaan Pendaftaran Karier Jepang
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Verifikasi kelengkapan berkas administratif, kelola revisi dokumen, teruskan usulan ke Guru BK, dan perbarui progres LPK.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('karier-jepang.index') }}" 
               target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-[#155EAA] hover:border-[#155EAA] text-xs font-bold shadow-xs transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span>Halaman Informasi Publik</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- ==============================================================
         1. MACRO KPI CARDS FOR ADMIN
         ============================================================== -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3 sm:gap-4">
        
        <!-- Total -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Masuk</span>
            <div class="text-2xl font-extrabold text-slate-900">{{ $stats['total'] }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Semua Pengajuan</span>
        </div>

        <!-- Menunggu Admin -->
        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 shadow-xs">
            <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block mb-1">Menunggu Admin</span>
            <div class="text-2xl font-extrabold text-amber-800">{{ $stats['menunggu_admin'] }}</div>
            <span class="text-[10px] text-amber-600 mt-1 block font-semibold">Perlu Diperiksa</span>
        </div>

        <!-- Perlu Perbaikan -->
        <div class="p-4 rounded-2xl bg-orange-50/70 border border-orange-200 shadow-xs">
            <span class="text-[11px] font-bold text-orange-700 uppercase tracking-wider block mb-1">Revisi / Perbaikan</span>
            <div class="text-2xl font-extrabold text-orange-800">{{ $stats['perlu_perbaikan'] }}</div>
            <span class="text-[10px] text-orange-600 mt-1 block font-semibold">Menunggu Peserta</span>
        </div>

        <!-- Menunggu BK -->
        <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 shadow-xs">
            <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider block mb-1">Di Meja BK</span>
            <div class="text-2xl font-extrabold text-blue-800">{{ $stats['menunggu_bk'] }}</div>
            <span class="text-[10px] text-blue-600 mt-1 block">Pertimbangan BK</span>
        </div>

        <!-- Menunggu Kepala Sekolah -->
        <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 shadow-xs">
            <span class="text-[11px] font-bold text-purple-700 uppercase tracking-wider block mb-1">Di Meja KS</span>
            <div class="text-2xl font-extrabold text-purple-800">{{ $stats['menunggu_ks'] }}</div>
            <span class="text-[10px] text-purple-600 mt-1 block">Persetujuan Akhir</span>
        </div>

        <!-- Disetujui Sekolah -->
        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Disetujui</span>
            <div class="text-2xl font-extrabold text-emerald-800">{{ $stats['disetujui'] }}</div>
            <span class="text-[10px] text-emerald-600 mt-1 block font-semibold">Lolos ke LPK</span>
        </div>

        <!-- Ditolak -->
        <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200 shadow-xs">
            <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wider block mb-1">Ditolak</span>
            <div class="text-2xl font-extrabold text-rose-800">{{ $stats['ditolak'] }}</div>
            <span class="text-[10px] text-rose-600 mt-1 block">Administrasi / KS</span>
        </div>

    </div>

    <!-- ==============================================================
         2. FILTER & PENCARIAN
         ============================================================== -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
        <form action="{{ route('dashboard.admin') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            
            <!-- Search Query -->
            <div>
                <label for="q" class="block text-xs font-bold text-slate-700 mb-1.5">Pencarian Peserta</label>
                <input type="text" 
                       name="q" 
                       id="q" 
                       value="{{ request('q') }}" 
                       placeholder="Cari nama, NISN, No. Pendaftaran..."
                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 focus:outline-hidden focus:border-[#155EAA]">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-700 mb-1.5">Status Tahapan</label>
                <select name="status" id="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-hidden focus:border-[#155EAA]">
                    <option value="semua">Semua Status</option>
                    <option value="menunggu_admin" @selected(request('status') === 'menunggu_admin')>Menunggu Pemeriksaan Admin</option>
                    <option value="perlu_perbaikan" @selected(request('status') === 'perlu_perbaikan')>Perlu Perbaikan Administrasi</option>
                    <option value="menunggu_bk" @selected(request('status') === 'menunggu_bk')>Menunggu Pertimbangan BK</option>
                    <option value="menunggu_kepala_sekolah" @selected(request('status') === 'menunggu_kepala_sekolah')>Menunggu Persetujuan KS</option>
                    <option value="disetujui_sekolah" @selected(request('status') === 'disetujui_sekolah')>Disetujui Sekolah</option>
                    <option value="pelatihan" @selected(request('status') === 'pelatihan')>Pelatihan Bahasa & Budaya</option>
                    <option value="diberangkatkan" @selected(request('status') === 'diberangkatkan')>Diberangkatkan ke Jepang</option>
                    <option value="ditolak_admin" @selected(request('status') === 'ditolak_admin')>Ditolak Admin</option>
                </select>
            </div>

            <!-- LPK Filter -->
            <div>
                <label for="lpk_id" class="block text-xs font-bold text-slate-700 mb-1.5">Pilihan Mitra LPK</label>
                <select name="lpk_id" id="lpk_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-hidden focus:border-[#155EAA]">
                    <option value="semua">Semua LPK Mitra</option>
                    @foreach($lpks as $lpk)
                        <option value="{{ $lpk->id }}" @selected(request('lpk_id') == $lpk->id)>{{ $lpk->nama_lpk }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-xs">
                    Filter Data
                </button>
                <a href="{{ route('dashboard.admin') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-all">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- ==============================================================
         3. TABEL DATA PENDAFTARAN
         ============================================================== -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-[#0F2F5F]">
                Daftar Seluruh Pengajuan Peserta ({{ $pendaftarans->total() }} Peserta Ditemukan)
            </h3>
            <span class="text-xs text-slate-400">Menampilkan 10 per halaman</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. Pengajuan</th>
                        <th class="px-5 py-3.5">Nama & Jurusan</th>
                        <th class="px-5 py-3.5">Jenis</th>
                        <th class="px-5 py-3.5">Mitra LPK</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Status Saat Ini</th>
                        <th class="px-5 py-3.5 text-right">Aksi Pemeriksaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendaftarans as $pendaftaran)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $pendaftaran->nomor_pendaftaran }}
                            </td>
                            <td class="px-5 py-4">
                                <strong class="text-slate-900 text-sm block">{{ $pendaftaran->nama_lengkap }}</strong>
                                <span class="text-[11px] text-slate-500">{{ $pendaftaran->jurusan }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $pendaftaran->jenis_peserta === 'siswa' ? 'bg-blue-50 text-[#155EAA]' : 'bg-emerald-50 text-[#166534]' }}">
                                    {{ $pendaftaran->jenis_peserta === 'siswa' ? 'Siswa Aktif' : 'Alumni' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-medium text-slate-800 block line-clamp-1">{{ $pendaftaran->lpk->nama_lpk }}</span>
                                <span class="text-[11px] text-slate-400">{{ $pendaftaran->lpk->wilayah_penempatan }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500">
                                {{ $pendaftaran->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $pendaftaran->getStatusBadgeClass() }}">
                                    {{ $pendaftaran->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('dashboard.admin.show', $pendaftaran->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-2xs">
                                    <span>Periksa Berkas</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada pendaftaran yang sesuai dengan kriteria filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendaftarans->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $pendaftarans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
