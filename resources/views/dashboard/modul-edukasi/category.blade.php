@extends('dashboard.layouts.dashboard')

@section('title', $category['name'] . ' | Modul Edukasi')
@section('breadcrumb', $category['name'])
@section('header_title', 'Modul Edukasi')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('dashboard') }}" class="font-semibold hover:text-[#155EAA]">Dashboard</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('dashboard.modul-edukasi.index') }}" class="font-semibold hover:text-[#155EAA]">Modul Edukasi</a>
        <span aria-hidden="true">/</span>
        <span class="font-bold text-[#0F2F5F]" aria-current="page">{{ $category['name'] }}</span>
    </nav>

    <section class="rounded-2xl bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0F2F5F] p-6 text-white shadow-lg sm:p-8">
        <p class="text-xs font-bold uppercase tracking-wide text-[#FBBF24]">Kategori Modul Edukasi</p>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $category['name'] }}</h1>
        @if ($description)
            <p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-200">{{ $description }}</p>
        @endif
    </section>

    @if ($materis->isEmpty())
        <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm">
            <h2 class="font-extrabold text-slate-900">Materi belum tersedia</h2>
            <p class="mt-2 text-sm text-slate-600">Belum ada dokumen yang dipublikasikan pada kategori ini.</p>
        </section>
    @else
        <section class="grid grid-cols-1 items-stretch gap-4 lg:grid-cols-2" aria-label="Daftar materi PDF">
            @foreach ($materis as $materi)
                <article class="flex h-full flex-col rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <span class="inline-flex rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-[11px] font-bold text-[#155EAA]">PDF Materi</span>
                        <svg class="h-6 w-6 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm7 0v5h5M8 14h8m-8 3h8"/></svg>
                    </div>
                    <h2 class="mt-4 text-base font-extrabold leading-snug text-slate-900">{{ $materi->judul }}</h2>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600">{{ $materi->deskripsi }}</p>
                    <div class="mt-5 flex flex-col gap-2 border-t border-slate-100 pt-4 sm:flex-row">
                        <a href="{{ route('dashboard.modul-edukasi.read', $materi) }}" class="inline-flex flex-1 items-center justify-center rounded-xl bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#0F2F5F]">Baca Materi</a>
                        <a href="{{ route('dashboard.modul-edukasi.download', $materi) }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50">Unduh PDF</a>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    <a href="{{ route('dashboard.modul-edukasi.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-[#155EAA] shadow-sm hover:bg-slate-50">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 18-6-6 6-6"/></svg>
        Kembali ke daftar Modul Edukasi
    </a>
</div>
@endsection
