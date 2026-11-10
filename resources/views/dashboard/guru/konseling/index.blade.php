@extends('dashboard.layouts.dashboard')

@section('title', 'Kelola Konseling Digital | Guru BK SMKN 7 Jember')
@section('breadcrumb', 'Guru BK &bull; Pengelolaan Konseling Digital')
@section('header_title', 'Pusat Pengelolaan Konseling Digital')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Top Bar Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-[#166534] border border-emerald-200 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                <span>Bimbingan Konseling &bull; SMKN 7 Jember</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                Pengelolaan Bimbingan &amp; Konseling Digital
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Verifikasi pengajuan konsultasi siswa dan alumni, tetapkan jadwal resmi, aktifkan ruang konsultasi online, dan kelola catatan tindak lanjut profesional.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Macro KPI Guru BK -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Pengajuan</span>
            <div class="text-2xl font-extrabold text-[#0F2F5F]">{{ $stats['total'] }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Semua Konseling</span>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 shadow-xs">
            <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block mb-1">Menunggu Konfirmasi</span>
            <div class="text-2xl font-extrabold text-amber-800">{{ $stats['menunggu'] }}</div>
            <span class="text-[10px] text-amber-600 mt-1 block font-semibold">Perlu Dijadwalkan</span>
        </div>

        <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 shadow-xs">
            <span class="text-[11px] font-bold text-[#155EAA] uppercase tracking-wider block mb-1">Dijadwalkan</span>
            <div class="text-2xl font-extrabold text-[#155EAA]">{{ $stats['dijadwalkan'] }}</div>
            <span class="text-[10px] text-blue-700 mt-1 block font-semibold">Siap Dilaksanakan</span>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Sedang Berlangsung</span>
            <div class="text-2xl font-extrabold text-emerald-800">{{ $stats['berlangsung'] }}</div>
            <span class="text-[10px] text-emerald-600 mt-1 block font-semibold">Ruang Chat Aktif</span>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Telah Selesai</span>
            <div class="text-2xl font-extrabold text-slate-700">{{ $stats['selesai'] }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Arsip Bimbingan</span>
        </div>

    </div>

    <!-- Antrean konsultasi yang belum ditugaskan -->
    <div class="bg-white rounded-3xl border border-amber-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-amber-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#0F2F5F]">Antrean Konsultasi Belum Ditugaskan ({{ $antreanKonselings->total() }} Data)</h2>
            <span class="text-xs text-amber-700">Ambil konsultasi untuk mulai menanganinya</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. Konseling</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Metode</th>
                        <th class="px-5 py-3.5">Tanggal Pengajuan</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($antreanKonselings as $antrean)
                        <tr>
                            <td class="px-5 py-4 font-mono font-bold text-slate-900">{{ $antrean->nomor_konseling }}</td>
                            <td class="px-5 py-4">{{ $antrean->getKategoriLabel() }}</td>
                            <td class="px-5 py-4">{{ $antrean->getMetodeLabel() }}</td>
                            <td class="px-5 py-4">{{ $antrean->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="px-5 py-4">{{ $antrean->getStatusLabel() }}</td>
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('dashboard.guru.konseling.ambil', $antrean->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all cursor-pointer">
                                        Ambil Konsultasi
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Tidak ada konsultasi dalam antrean.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($antreanKonselings->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $antreanKonselings->links() }}</div>
        @endif
    </div>

    <!-- Filter & Pencarian Toolbar -->
    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5 shadow-xs">
        <form method="GET" action="{{ route('dashboard.guru.konseling.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            
            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Status Sesi</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu_konfirmasi" {{ $status === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                    <option value="dijadwalkan" {{ $status === 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                    <option value="berlangsung" {{ $status === 'berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                    <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan" {{ $status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Filter Kategori -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Kategori</label>
                <select name="kategori" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <option value="">-- Semua Kategori --</option>
                    @foreach(\App\Models\KonselingDigital::getKategoriList() as $k => $l)
                        <option value="{{ $k }}" {{ $kategori === $k ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Metode -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Metode</label>
                <select name="metode" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                    <option value="">-- Semua Metode --</option>
                    <option value="online" {{ $metode === 'online' ? 'selected' : '' }}>Online (Chat Website)</option>
                    <option value="tatap_muka" {{ $metode === 'tatap_muka' ? 'selected' : '' }}>Tatap Muka di Sekolah</option>
                </select>
            </div>

            <!-- Search Input & Submit -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari Peserta / Topik</label>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Nama siswa, no konseling..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs">
                </div>
                <button type="submit" class="px-4 py-2 bg-[#0F2F5F] hover:bg-[#155EAA] text-white rounded-xl text-xs font-bold transition-all cursor-pointer">
                    Filter
                </button>
            </div>

        </form>
    </div>

    <!-- Tabel Pengajuan Konseling Guru BK -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#0F2F5F]">
                Daftar Permintaan &amp; Sesi Bimbingan ({{ $konselings->total() }} Data)
            </h2>
            <span class="text-xs text-slate-400">Pengelolaan terpusat Guru BK</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. &amp; Peserta</th>
                        <th class="px-5 py-3.5">Kategori &amp; Alasan</th>
                        <th class="px-5 py-3.5">Metode</th>
                        <th class="px-5 py-3.5">Jadwal / Usulan</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($konselings as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-mono font-bold text-slate-900 block text-xs">{{ $item->nomor_konseling }}</span>
                                <strong class="text-slate-800 text-sm block mt-0.5">{{ $item->user->name }}</strong>
                                <span class="text-[11px] text-slate-400 block">{{ $item->user->getRoleDisplayName() }} &bull; {{ $item->user->email }}</span>
                            </td>

                            <td class="px-5 py-4 max-w-xs">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-[#155EAA]">
                                    {{ $item->getKategoriLabel() }}
                                </span>
                                <p class="text-[11px] text-slate-600 line-clamp-2 mt-1 italic" title="{{ $item->alasan_konseling }}">
                                    "{{ $item->alasan_konseling }}"
                                </p>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($item->isOnline())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 font-semibold text-[11px]">
                                        <span>Online (Chat)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 font-semibold text-[11px]">
                                        <span>Tatap Muka</span>
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                @if($item->tanggal_jadwal)
                                    <strong class="text-slate-900 block">{{ $item->tanggal_jadwal->translatedFormat('d M Y') }}</strong>
                                    <span class="text-[11px] text-[#155EAA] font-mono font-bold">
                                        {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }} WIB
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 block">Usulan Peserta:</span>
                                    <span class="text-slate-700 font-semibold">{{ $item->tanggal_usulan->translatedFormat('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $item->waktu_usulan }}</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $item->getStatusBadgeClass() }}">
                                    {{ $item->getStatusLabel() }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    @if($item->isOnline() && $item->status === 'berlangsung')
                                        <a href="{{ route('dashboard.guru.konseling.chat', $item->id) }}" 
                                           class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-2xs animate-pulse">
                                            Buka Chat
                                        </a>
                                    @elseif($item->isOnline() && $item->status === 'dijadwalkan')
                                        <form action="{{ route('dashboard.guru.konseling.mulai', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-2xs cursor-pointer">
                                                Mulai Sesi
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('dashboard.guru.konseling.show', $item->id) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-2xs">
                                        Kelola &amp; Catatan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                Tidak ada data pengajuan konseling yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($konselings->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $konselings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
