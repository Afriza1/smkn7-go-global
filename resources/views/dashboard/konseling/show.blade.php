@extends('dashboard.layouts.dashboard')

@section('title', 'Detail Konsultasi | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Detail Konsultasi')
@section('header_title', 'Informasi & Status Konseling Digital')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard.konseling.index') }}" class="hover:text-[#155EAA] flex items-center gap-1 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Daftar Konseling</span>
                </a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-bold">Detail #{{ $konseling->nomor_konseling }}</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                {{ $konseling->getKategoriLabel() }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Diajukan pada {{ $konseling->created_at->translatedFormat('d F Y') }} pukul {{ $konseling->created_at->format('H:i') }} WIB
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($konseling->isOnline() && in_array($konseling->status, ['berlangsung', 'dijadwalkan', 'selesai']))
                <a href="{{ route('dashboard.konseling.chat', $konseling->id) }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-white text-xs font-bold shadow-xs transition-all cursor-pointer {{ $konseling->status === 'berlangsung' ? 'bg-emerald-600 hover:bg-emerald-700 animate-pulse' : 'bg-[#0F2F5F] hover:bg-[#155EAA]' }}">
                    <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>{{ $konseling->status === 'berlangsung' ? 'Masuk ke Ruang Chat' : 'Buka Percakapan Chat' }}</span>
                </a>
            @endif

            @if($konseling->bisaBatal())
                <form action="{{ route('dashboard.konseling.batal', $konseling->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin hendak membatalkan pengajuan konsultasi ini?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold transition-all cursor-pointer">
                        Batalkan Pengajuan
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Banner Status Utama -->
    @if($konseling->status === \App\Models\KonselingDigital::STATUS_BERLANGSUNG)
        <div class="p-6 rounded-3xl bg-emerald-500 text-white shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <span class="w-3.5 h-3.5 rounded-full bg-white animate-ping flex-shrink-0"></span>
                <div>
                    <strong class="text-base font-extrabold block">Ruang Chat Konsultasi Sedang Aktif!</strong>
                    <span class="text-xs text-emerald-100">Guru BK telah membuka sesi chat. Silakan bergabung ke ruang percakapan.</span>
                </div>
            </div>
            <a href="{{ route('dashboard.konseling.chat', $konseling->id) }}" 
               class="px-5 py-2.5 rounded-xl bg-[#FBBF24] hover:bg-amber-400 text-[#0F2F5F] text-xs font-bold shadow-xs transition-all">
                Masuk Ruang Chat Sekarang &rarr;
            </a>
        </div>
    @elseif($konseling->status === \App\Models\KonselingDigital::STATUS_DIJADWALKAN)
        <div class="p-6 rounded-3xl bg-blue-50 border border-blue-200 text-blue-950 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <span class="text-[10px] uppercase font-bold tracking-wider text-[#155EAA] block">Status Penjadwalan</span>
                <strong class="text-base font-extrabold block text-[#0F2F5F]">Jadwal Konsultasi Telah Ditetapkan</strong>
                <p class="text-xs text-slate-600">
                    Pelaksanaan: <strong>{{ $konseling->tanggal_jadwal ? $konseling->tanggal_jadwal->translatedFormat('l, d F Y') : '-' }}</strong> 
                    pukul <strong>{{ $konseling->jam_mulai ? substr($konseling->jam_mulai, 0, 5) . ' - ' . substr($konseling->jam_selesai, 0, 5) : '-' }} WIB</strong>.
                </p>
                @if(! $konseling->isOnline())
                    <p class="text-xs font-semibold text-amber-900 mt-1">
                        Tempat: {{ $konseling->tempat_tatap_muka ?? 'Ruang BK SMKN 7 Jember' }}
                    </p>
                @endif
            </div>

            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-[#155EAA]">
                Telah Terjadwal
            </span>
        </div>
    @elseif($konseling->status === \App\Models\KonselingDigital::STATUS_MENUNGGU_KONFIRMASI)
        <div class="p-5 rounded-3xl bg-amber-50 border border-amber-200 text-amber-900 shadow-2xs">
            <strong class="text-sm font-bold block mb-1">Pengajuan Sedang Menunggu Konfirmasi Guru BK</strong>
            <p class="text-xs leading-relaxed text-amber-800">
                Pengajuan Anda telah berhasil dicatat ke sistem. Guru BK akan memeriksa ketersediaan waktu dan menetapkan jadwal resmi. Anda dapat memantau perkembangan jadwal di halaman ini.
            </p>
        </div>
    @elseif($konseling->status === \App\Models\KonselingDigital::STATUS_DITOLAK)
        <div class="p-5 rounded-3xl bg-rose-50 border border-rose-200 text-rose-950 shadow-2xs space-y-1.5">
            <strong class="text-sm font-bold block text-rose-900">Pengajuan Konseling Ditolak</strong>
            <p class="text-xs text-rose-800">
                Alasan Penolakan dari Guru BK: <span class="font-semibold italic">"{{ $konseling->alasan_penolakan }}"</span>
            </p>
        </div>
    @endif

    <!-- Card 2 Kolom: Detail Informasi & Jadwal Resmi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Kolom Kiri: Rincian Pengajuan Awal -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="text-sm font-bold text-[#0F2F5F] flex items-center gap-2 border-b border-slate-100 pb-3">
                <svg class="w-4 h-4 text-[#155EAA]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Rincian Pengajuan Konsultasi</span>
            </h2>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Nomor Registrasi</span>
                    <strong class="font-mono font-bold text-slate-800">{{ $konseling->nomor_konseling }}</strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Kategori Topik</span>
                    <strong class="text-slate-800 font-bold">{{ $konseling->getKategoriLabel() }}</strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Metode Pilihan</span>
                    <strong class="text-slate-800 font-bold">{{ $konseling->getMetodeLabel() }}</strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Usulan Tanggal Peserta</span>
                    <strong class="text-slate-800">{{ $konseling->tanggal_usulan->translatedFormat('d F Y') }}</strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Usulan Waktu Peserta</span>
                    <strong class="text-slate-800">{{ $konseling->waktu_usulan }}</strong>
                </div>

                <div class="pt-2">
                    <span class="text-slate-500 block mb-1 font-semibold">Alasan / Hal yang Disampaikan:</span>
                    <p class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700 leading-relaxed italic">
                        "{{ $konseling->alasan_konseling }}"
                    </p>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Keputusan Jadwal & Guru Pembimbing -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="text-sm font-bold text-[#0F2F5F] flex items-center gap-2 border-b border-slate-100 pb-3">
                <svg class="w-4 h-4 text-[#166534]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Jadwal &amp; Guru Pembimbing</span>
            </h2>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Status Sesi</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $konseling->getStatusBadgeClass() }}">
                        {{ $konseling->getStatusLabel() }}
                    </span>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Guru Pembimbing BK</span>
                    <strong class="text-slate-800 font-bold">{{ $konseling->guru->name ?? 'Akan Ditugaskan Guru BK' }}</strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Tanggal Pelaksanaan</span>
                    <strong class="text-[#155EAA] font-bold">
                        {{ $konseling->tanggal_jadwal ? $konseling->tanggal_jadwal->translatedFormat('d F Y') : 'Menunggu Konfirmasi' }}
                    </strong>
                </div>

                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Jam Sesi</span>
                    <strong class="text-slate-800 font-mono">
                        {{ $konseling->jam_mulai ? substr($konseling->jam_mulai, 0, 5) . ' - ' . substr($konseling->jam_selesai, 0, 5) . ' WIB' : 'Belum Ditentukan' }}
                    </strong>
                </div>

                @if(! $konseling->isOnline())
                    <div class="py-2 border-b border-slate-100">
                        <span class="text-slate-500 block mb-1">Lokasi Tatap Muka:</span>
                        <strong class="text-slate-800 block">{{ $konseling->tempat_tatap_muka ?? 'Ruang BK SMKN 7 Jember' }}</strong>
                    </div>
                @endif

                @if($konseling->catatan_jadwal)
                    <div class="pt-2">
                        <span class="text-slate-500 block mb-1 font-semibold">Pesan / Catatan dari Guru BK:</span>
                        <p class="p-3 rounded-xl bg-blue-50/70 border border-blue-200 text-blue-900 leading-relaxed">
                            {{ $konseling->catatan_jadwal }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Riwayat Perubahan Status (Audit Trail) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <h3 class="text-sm font-bold text-[#0F2F5F] mb-4 pb-3 border-b border-slate-100">
            Log Riwayat Perjalanan Pengajuan
        </h3>

        <ol class="relative border-l border-slate-200 ml-3 space-y-6 text-xs">
            @forelse($konseling->riwayatStatuses as $log)
                <li class="ml-6">
                    <span class="absolute -left-2.5 flex items-center justify-center w-5 h-5 bg-[#155EAA] rounded-full ring-4 ring-white text-white font-bold text-[9px]">
                        &bull;
                    </span>
                    <div class="flex items-center gap-2">
                        <strong class="text-slate-800 capitalize">{{ str_replace('_', ' ', $log->status_baru) }}</strong>
                        <span class="text-[10px] text-slate-400">&bull; {{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                    </div>
                    @if($log->keterangan)
                        <p class="text-slate-600 mt-1 text-[11px] bg-slate-50 p-2 rounded-xl border border-slate-200/60">
                            {{ $log->keterangan }}
                        </p>
                    @endif
                </li>
            @empty
                <li class="text-slate-400">Belum ada riwayat perubahan status.</li>
            @endforelse
        </ol>
    </div>

</div>
@endsection
