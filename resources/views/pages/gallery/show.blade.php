@extends('layouts.app')

@section('title', $album->title.' | Dokumentasi SMKN 7 Jember Go Global')

@section('content')
<section class="bg-[#F8FAFC] py-10 sm:py-14"><div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
    <a href="{{ route('landing') }}#galeri" class="text-sm font-bold text-[#155EAA]">← Kembali ke galeri</a>
    <header class="rounded-2xl bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0F2F5F] p-6 text-white shadow-lg sm:p-8"><p class="text-xs font-bold uppercase tracking-wide text-[#FBBF24]">{{ $album->category ?: 'Dokumentasi Kegiatan' }} @if($album->event_date) · {{ $album->event_date->translatedFormat('d F Y') }} @endif</p><h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">{{ $album->title }}</h1>@if($album->description)<p class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-200">{{ $album->description }}</p>@endif</header>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">@forelse($album->photos as $photo)<button type="button" class="gallery-lightbox-item group relative overflow-hidden rounded-2xl border border-slate-200 bg-white text-left shadow-sm" data-image="{{ Storage::disk('public')->url($photo->path) }}" data-title="{{ $photo->caption ?: $album->title }}" data-desc="{{ $photo->caption }}" data-category="{{ $album->category ?: 'Dokumentasi Kegiatan' }}" onclick="window.openGalleryLightbox({{ $loop->index }})"><img src="{{ Storage::disk('public')->url($photo->path) }}" alt="{{ $photo->alt_text }}" class="h-56 w-full object-cover transition group-hover:scale-[1.02]" loading="lazy">@if($photo->caption)<span class="block p-3 text-sm font-semibold text-slate-700">{{ $photo->caption }}</span>@endif</button>@empty<div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500 sm:col-span-2 lg:col-span-3">Album ini belum memiliki foto.</div>@endforelse</div>
</div></section>
@endsection
