@extends('dashboard.layouts.dashboard')

@section('title', 'Ruang Chat Konsultasi Digital | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Ruang Chat Konseling')
@section('header_title', 'Ruang Percakapan Konsultasi Digital')

@section('content')
<div class="max-w-5xl mx-auto space-y-4">
    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('dashboard.konseling.show', $konseling->id) }}" class="hover:text-[#155EAA] flex items-center gap-1 font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Detail Sesi</span>
            </a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-bold">Ruang Chat #{{ $konseling->nomor_konseling }}</span>
        </nav>

        <span class="text-xs text-slate-400">
            Enkripsi Sesi: <strong>Privat Siswa &amp; Guru BK</strong>
        </span>
    </div>

    <!-- Livewire Real-Time Chat Component -->
    @livewire('konseling-chat-room', ['konselingId' => $konseling->id])

</div>
@endsection
