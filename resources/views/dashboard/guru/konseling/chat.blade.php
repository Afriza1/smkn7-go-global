@extends('dashboard.layouts.dashboard')

@section('title', 'Ruang Chat Konseling Guru BK | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Guru BK &bull; Ruang Chat Konseling')
@section('header_title', 'Ruang Percakapan Konsultasi Digital')

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    
    <!-- Action Bar Guru BK -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <nav class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('dashboard.guru.konseling.show', $konseling->id) }}" class="hover:text-[#155EAA] flex items-center gap-1 font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Detail &amp; Catatan Privat</span>
            </a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-bold">Ruang Chat #{{ $konseling->nomor_konseling }}</span>
        </nav>

        <div class="flex items-center gap-2">
            @if($konseling->status === 'berlangsung')
                <form action="{{ route('dashboard.guru.konseling.selesai', $konseling->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin hendak mengakhiri sesi konsultasi ini?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                        Akhiri &amp; Tutup Sesi
                    </button>
                </form>
            @endif

            <a href="{{ route('dashboard.guru.konseling.show', $konseling->id) }}" 
               class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-2xs">
                Catatan Privat BK
            </a>
        </div>
    </div>

    <!-- Livewire Real-Time Chat Component -->
    @livewire('konseling-chat-room', ['konselingId' => $konseling->id])

</div>
@endsection
