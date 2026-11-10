@extends('dashboard.layouts.dashboard')

@section('title', 'Modul Edukasi | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Modul Edukasi')
@section('header_title', 'Modul Edukasi')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0F2F5F] p-6 text-white shadow-lg sm:p-8">
        <div class="pointer-events-none absolute -bottom-10 -right-10 h-48 w-48 rounded-full bg-white/5"></div>
        <div class="relative z-10 max-w-3xl">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-[#FBBF24]"><span class="h-2 w-2 rounded-full bg-[#FBBF24]"></span>Pusat Pembelajaran Digital</div>
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Modul Edukasi</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-200">Pilih kategori materi untuk melihat panduan yang tersedia.</p>
        </div>
    </section>

    <section class="grid grid-cols-1 items-stretch gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-label="Kategori Modul Edukasi">
        @foreach ($kategoriMateri as $category)
            <article class="flex h-full min-h-64 flex-col rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-[#155EAA]">
                    @if ($category['icon'] === 'shield')
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3 4.5 6v5.2c0 4.7 3.2 8.1 7.5 9.8 4.3-1.7 7.5-5.1 7.5-9.8V6L12 3Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 12 2 2 4-4"/></svg>
                    @elseif ($category['icon'] === 'heart')
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.8 8.8c0 5.1-8.8 10.2-8.8 10.2S3.2 13.9 3.2 8.8A4.3 4.3 0 0 1 12 7.1a4.3 4.3 0 0 1 8.8 1.7Z"/></svg>
                    @else
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.5 12h17M12 3a15 15 0 0 1 0 18m0-18a15 15 0 0 0 0 18"/></svg>
                    @endif
                </div>
                <p class="mt-5 text-[11px] font-bold uppercase tracking-wide text-[#155EAA]">Kategori Materi</p>
                <h2 class="mt-1 text-lg font-extrabold leading-snug text-[#0F2F5F]">{{ $category['name'] }}</h2>
                <p class="mt-2 flex-1 text-sm leading-relaxed text-slate-600">{{ $category['summary'] ?: 'Materi pada kategori ini belum tersedia.' }}</p>
                <a href="{{ route('dashboard.modul-edukasi.category', $category['slug']) }}" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#155EAA] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#0F2F5F]">
                    Lihat Detail
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
            </article>
        @endforeach
    </section>
</div>
@endsection
