@extends('dashboard.layouts.dashboard')

@section('title', 'Manajemen Dokumentasi | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Dokumentasi')
@section('header_title', 'Manajemen Dokumentasi')

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
    <div class="flex flex-wrap items-center justify-between gap-3"><div><h1 class="text-2xl font-extrabold text-slate-900">Dokumentasi Kegiatan</h1><p class="mt-1 text-sm text-slate-600">Kelola album dan foto kegiatan sekolah.</p></div><a href="{{ route('dashboard.guru.gallery.create') }}" class="rounded-xl bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white">Tambah Album</a></div>
    <form method="GET" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_200px_auto]"><input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari judul atau kategori" class="rounded-lg border-slate-300 text-sm"><select name="status" class="rounded-lg border-slate-300 text-sm"><option value="">Semua status</option><option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option><option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option></select><button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-bold text-white">Cari</button></form>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($albums as $album)
            @php($cover = $album->photos->first())
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                @if($cover)<img src="{{ Storage::disk('public')->url($cover->path) }}" alt="{{ $cover->alt_text }}" class="h-44 w-full object-cover">@else<div class="flex h-44 items-center justify-center bg-slate-100 text-sm text-slate-400">Belum ada foto</div>@endif
                <div class="space-y-3 p-4">
                    <div class="flex justify-between gap-2"><span class="text-xs font-bold text-[#155EAA]">{{ $album->category ?: 'Kegiatan' }}</span><span class="text-xs font-bold {{ $album->status === 'published' ? 'text-emerald-700' : 'text-amber-700' }}">{{ ucfirst($album->status) }}</span></div>
                    <h2 class="font-extrabold text-slate-900">{{ $album->title }}</h2>
                    <p class="text-xs text-slate-500">{{ $album->photos_count }} foto @if($album->event_date) &middot; {{ $album->event_date->format('d/m/Y') }} @endif</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dashboard.guru.gallery.show', $album) }}" class="rounded-lg bg-[#155EAA] px-3 py-2 text-xs font-bold text-white">Detail/Kelola</a>
                        <a href="{{ route('dashboard.guru.gallery.edit', $album) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-[#155EAA]">Edit Informasi</a>
                        <form method="POST" action="{{ route('dashboard.guru.gallery.destroy', $album) }}" onsubmit="return confirm('Hapus album dan seluruh foto di dalamnya?')">@csrf @method('DELETE')<button class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700">Hapus Album</button></form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500 sm:col-span-2 xl:col-span-4">Belum ada album dokumentasi.</div>
        @endforelse
    </div>
    {{ $albums->links() }}
</div>
@endsection
