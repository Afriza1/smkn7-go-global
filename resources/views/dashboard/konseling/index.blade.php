@extends('dashboard.layouts.dashboard')

@section('title', 'Konseling Digital | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Konseling Digital')
@section('header_title', 'Layanan Bimbingan & Konseling Digital')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Header Utama & Tombol Buat Pengajuan -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#155EAA] border border-blue-200 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                <span>Fasilitas Bimbingan Konseling &bull; SMKN 7 Jember</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                Konseling &amp; Pendampingan Karier Digital
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Konsultasikan persiapan karier Jepang, motivasi belajar, tantangan pribadi, maupun kesiapan keberangkatan langsung dengan Guru BK secara aman dan privat.
            </p>
        </div>

        <a href="{{ route('dashboard.konseling.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs sm:text-sm font-bold shadow-xs transition-all self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Ajukan Konsultasi Baru</span>
        </a>
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

    <!-- Alert Sesi Konsultasi Sedang Aktif Sekarang (Prioritas Utama) -->
    @if($sesiAktif)
        <div class="bg-gradient-to-r from-emerald-600 to-[#166534] rounded-3xl p-6 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-5 animate-pulse">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center font-bold text-white text-lg flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div>
                    <span class="text-[11px] uppercase tracking-wider font-extrabold bg-white/20 px-2.5 py-0.5 rounded-full inline-block mb-1">
                        Sesi Konsultasi Aktif Sekarang
                    </span>
                    <h2 class="text-base sm:text-lg font-bold">
                        {{ $sesiAktif->getKategoriLabel() }} (No. {{ $sesiAktif->nomor_konseling }})
                    </h2>
                    <p class="text-xs text-emerald-100 mt-0.5">
                        Guru BK telah membuka ruang konsultasi online. Silakan bergabung untuk memulai percakapan langsung.
                    </p>
                </div>
            </div>

            <a href="{{ route('dashboard.konseling.chat', $sesiAktif->id) }}" 
               class="px-6 py-3 rounded-2xl bg-[#FBBF24] hover:bg-amber-400 text-[#0F2F5F] text-xs sm:text-sm font-extrabold shadow-lg transition-all flex items-center gap-2 cursor-pointer flex-shrink-0">
                <span>Masuk ke Ruang Chat Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    @endif

    <!-- Macro KPI Counters -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        
        <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Konsultasi</span>
            <div class="text-2xl font-extrabold text-[#0F2F5F]">{{ $stats['total'] }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Seluruh Pengajuan</span>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 shadow-xs">
            <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider block mb-1">Menunggu BK</span>
            <div class="text-2xl font-extrabold text-amber-800">{{ $stats['menunggu'] }}</div>
            <span class="text-[10px] text-amber-600 mt-1 block font-semibold">Proses Konfirmasi</span>
        </div>

        <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 shadow-xs">
            <span class="text-[11px] font-bold text-[#155EAA] uppercase tracking-wider block mb-1">Dijadwalkan</span>
            <div class="text-2xl font-extrabold text-[#155EAA]">{{ $stats['dijadwalkan'] }}</div>
            <span class="text-[10px] text-blue-700 mt-1 block font-semibold">Jadwal Telah Ditetapkan</span>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-1">Sedang Berlangsung</span>
            <div class="text-2xl font-extrabold text-emerald-800">{{ $stats['berlangsung'] }}</div>
            <span class="text-[10px] text-emerald-600 mt-1 block font-semibold">Sesi Aktif</span>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 shadow-xs col-span-2 sm:col-span-1">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Telah Selesai</span>
            <div class="text-2xl font-extrabold text-slate-700">{{ $stats['selesai'] }}</div>
            <span class="text-[10px] text-slate-500 mt-1 block">Arsip Riwayat</span>
        </div>

    </div>

    <!-- Filter Tabs Status -->
    <div class="border-b border-slate-200 flex items-center gap-2 overflow-x-auto">
        <a href="{{ route('dashboard.konseling.index') }}" 
           class="px-4 py-2.5 text-xs font-bold border-b-2 {{ empty($statusFilter) ? 'border-[#155EAA] text-[#155EAA]' : 'border-transparent text-slate-500 hover:text-slate-700' }} transition-colors whitespace-nowrap">
            Semua ({{ $stats['total'] }})
        </a>
        <a href="{{ route('dashboard.konseling.index', ['status' => 'menunggu_konfirmasi']) }}" 
           class="px-4 py-2.5 text-xs font-bold border-b-2 {{ $statusFilter === 'menunggu_konfirmasi' ? 'border-amber-600 text-amber-700' : 'border-transparent text-slate-500 hover:text-slate-700' }} transition-colors whitespace-nowrap">
            Menunggu ({{ $stats['menunggu'] }})
        </a>
        <a href="{{ route('dashboard.konseling.index', ['status' => 'dijadwalkan']) }}" 
           class="px-4 py-2.5 text-xs font-bold border-b-2 {{ $statusFilter === 'dijadwalkan' ? 'border-blue-600 text-[#155EAA]' : 'border-transparent text-slate-500 hover:text-slate-700' }} transition-colors whitespace-nowrap">
            Dijadwalkan ({{ $stats['dijadwalkan'] }})
        </a>
        <a href="{{ route('dashboard.konseling.index', ['status' => 'berlangsung']) }}" 
           class="px-4 py-2.5 text-xs font-bold border-b-2 {{ $statusFilter === 'berlangsung' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-slate-500 hover:text-slate-700' }} transition-colors whitespace-nowrap">
            Berlangsung ({{ $stats['berlangsung'] }})
        </a>
        <a href="{{ route('dashboard.konseling.index', ['status' => 'selesai']) }}" 
           class="px-4 py-2.5 text-xs font-bold border-b-2 {{ $statusFilter === 'selesai' ? 'border-slate-700 text-slate-800' : 'border-transparent text-slate-500 hover:text-slate-700' }} transition-colors whitespace-nowrap">
            Riwayat Selesai ({{ $stats['selesai'] }})
        </a>
    </div>

    <!-- Tabel Daftar Pengajuan Konseling -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#0F2F5F]">
                Daftar Pengajuan &amp; Sesi Konsultasi Anda
            </h2>
            <span class="text-xs text-slate-400">Total: {{ $konselings->total() }} Sesi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">No. Konseling &amp; Kategori</th>
                        <th class="px-5 py-3.5">Metode</th>
                        <th class="px-5 py-3.5">Usulan / Jadwal Pelaksanaan</th>
                        <th class="px-5 py-3.5">Guru Pembimbing</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($konselings as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-mono font-bold text-slate-900 block text-xs">{{ $item->nomor_konseling }}</span>
                                <strong class="text-[#0F2F5F] text-sm block mt-0.5">{{ $item->getKategoriLabel() }}</strong>
                                <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5 max-w-xs" title="{{ $item->alasan_konseling }}">
                                    "{{ $item->alasan_konseling }}"
                                </p>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($item->isOnline())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 font-semibold text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        <span>Online (Chat)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 font-semibold text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <span>Tatap Muka</span>
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                @if($item->tanggal_jadwal)
                                    <div class="font-bold text-slate-800">{{ $item->tanggal_jadwal->translatedFormat('d M Y') }}</div>
                                    <span class="text-[11px] text-[#155EAA] font-mono font-bold">
                                        {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }} WIB
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[11px] block">Usulan Anda:</span>
                                    <div class="font-medium text-slate-700">{{ $item->tanggal_usulan->translatedFormat('d M Y') }}</div>
                                    <span class="text-[10px] text-slate-400">{{ $item->waktu_usulan }}</span>
                                @endif
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-semibold text-slate-800 block">{{ $item->guru->name ?? 'Tim Bimbingan Konseling' }}</span>
                                <span class="text-[10px] text-slate-400">Guru BK SMKN 7 Jember</span>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $item->getStatusBadgeClass() }}">
                                    {{ $item->getStatusLabel() }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    @if($item->isOnline() && ($item->status === 'berlangsung' || $item->status === 'selesai' || $item->status === 'dijadwalkan'))
                                        <a href="{{ route('dashboard.konseling.chat', $item->id) }}" 
                                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs {{ $item->status === 'berlangsung' ? 'bg-emerald-600 hover:bg-emerald-700 text-white animate-pulse' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                                            {{ $item->status === 'berlangsung' ? 'Masuk Chat' : 'Buka Chat' }}
                                        </a>
                                    @endif

                                    <a href="{{ route('dashboard.konseling.show', $item->id) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-2xs">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <p class="text-sm font-semibold text-slate-600">Belum ada pengajuan bimbingan konseling.</p>
                                <p class="text-xs text-slate-400 mt-1">Gunakan fasilitas konseling digital ini untuk mendiskusikan berbagai persiapan dan pertanyaan karier Anda.</p>
                                <a href="{{ route('dashboard.konseling.create') }}" class="inline-block mt-4 px-5 py-2.5 bg-[#0F2F5F] hover:bg-[#155EAA] text-white rounded-xl text-xs font-bold shadow-xs">
                                    Mulai Ajukan Konsultasi Sekarang
                                </a>
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

    <!-- Informasi Kontak Layanan Konseling Resmi -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pusat Bantuan &amp; Konseling Resmi</span>
                <h3 class="text-lg font-bold text-[#0F2F5F]">{{ $kontak->nama_layanan }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $kontak->keterangan_tambahan }}</p>
            </div>
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-[#166534] border border-emerald-200 text-xs font-bold self-start md:self-auto">
                <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                <span>Layanan Aktif Sekolah</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                <span class="text-slate-400 block mb-1 font-semibold uppercase tracking-wider text-[10px]">Koordinator / Guru BK</span>
                <strong class="text-slate-900 text-sm block font-bold">{{ $kontak->nama_guru_bk }}</strong>
                <span class="text-[11px] text-slate-500 mt-0.5 block">Pembimbing Karier Vokasi</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                <span class="text-slate-400 block mb-1 font-semibold uppercase tracking-wider text-[10px]">Jam Pelayanan</span>
                <strong class="text-slate-900 text-sm block font-bold">{{ $kontak->jam_layanan }}</strong>
                <span class="text-[11px] text-slate-500 mt-0.5 block">Sesuai Kalender Pendidikan</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                <span class="text-slate-400 block mb-1 font-semibold uppercase tracking-wider text-[10px]">Lokasi Ruang BK</span>
                <strong class="text-slate-900 text-sm block font-bold">{{ $kontak->lokasi_ruangan }}</strong>
                <span class="text-[11px] text-slate-500 mt-0.5 block">Kampus SMKN 7 Jember</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                <span class="text-slate-400 block mb-1 font-semibold uppercase tracking-wider text-[10px]">Kontak Resmi / BKK</span>
                <strong class="text-[#155EAA] text-sm block font-bold font-mono">{{ $kontak->kontak_resmi }}</strong>
                <span class="text-[10px] text-slate-400 mt-0.5 block">Eskalasi: {{ $kontak->kontak_bantuan_eskalasi }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
