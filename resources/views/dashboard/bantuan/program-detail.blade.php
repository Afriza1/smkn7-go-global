@extends('dashboard.layouts.dashboard')

@section('title', 'Detail Program: ' . $program->nama_program)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.bantuan.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#155EAA] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Daftar Program</span>
        </a>

        @if($program->is_demo)
            <span class="px-2.5 py-0.5 text-xs font-extrabold uppercase bg-amber-100 text-amber-800 rounded-md border border-amber-200">
                PROGRAM DEMO
            </span>
        @endif
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        @php
            $badge = $program->badgeJenis();
        @endphp

        <!-- Header Info -->
        <div class="space-y-3 pb-6 border-b border-slate-100">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                    Mitra Pelaksana: <strong class="text-slate-900">{{ $program->nama_mitra }}</strong>
                </span>
            </div>

            <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-snug">
                {{ $program->nama_program }}
            </h1>

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                {{ $program->deskripsi }}
            </p>
        </div>

        <!-- Periode & Status Ketersediaan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block font-medium">Lembaga Kemitraan</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $program->nama_mitra }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Periode Pendaftaran</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block">
                    @if($program->periode_mulai && $program->periode_selesai)
                        {{ $program->periode_mulai->translatedFormat('d M Y') }} s/d {{ $program->periode_selesai->translatedFormat('d M Y') }}
                    @else
                        Terbuka Sepanjang Tahun
                    @endif
                </span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Status Pendaftaran</span>
                <div class="mt-0.5">
                    @if($program->isBuka())
                        <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700 text-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Terbuka untuk Pengajuan
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 font-bold text-rose-600 text-sm">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            Pendaftaran Ditutup
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Persyaratan Program -->
        <div class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                <span>Kriteria & Persyaratan Peserta</span>
            </h2>
            <div class="p-5 bg-white rounded-xl border border-slate-200 text-slate-700 text-xs sm:text-sm leading-relaxed whitespace-pre-line">
                {{ $program->persyaratan }}
            </div>
        </div>

        <!-- Dokumen Persyaratan yang Wajib Diunggah -->
        <div class="space-y-3">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                <span>Dokumen Persyaratan yang Wajib Disiapkan (Format PDF/JPG/PNG Maks. 3MB)</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($program->dokumen_dibutuhkan ?? [] as $index => $dok)
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-[#166534]/10 text-[#166534] font-bold text-xs flex items-center justify-center flex-shrink-0">
                            {{ $index + 1 }}
                        </div>
                        <span class="text-xs font-semibold text-slate-800">{{ $dok }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tombol Aksi Pengajuan -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                Pastikan seluruh berkas telah disiapkan sebelum mengisi formulir.
            </div>

            @if($program->isBuka())
                <a href="{{ route('dashboard.bantuan.create', $program->slug) }}" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
                    <span>Mulai Ajukan Permohonan Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @else
                <button type="button" disabled class="px-6 py-3 bg-slate-200 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed">
                    Pendaftaran Program Tidak Tersedia
                </button>
            @endif
        </div>
    </div>
</div>
@endsection
