@extends('dashboard.layouts.dashboard')

@section('title', $album->title.' | Dokumentasi')
@section('breadcrumb', 'Dokumentasi')
@section('header_title', 'Detail Album Dokumentasi')

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-5">
    <a href="{{ route('dashboard.guru.gallery.index') }}" class="text-sm font-bold text-[#155EAA]">← Kembali ke daftar album</a>

    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-[#155EAA]">{{ $album->category ?: 'Kegiatan' }}</span>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $album->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($album->status) }}</span>
                    @if($album->event_date)<span class="text-sm text-slate-500">{{ $album->event_date->format('d/m/Y') }}</span>@endif
                </div>
                <h1 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $album->title }}</h1>
                @if($album->description)<p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $album->description }}</p>@endif
            </div>
            <a href="{{ route('dashboard.guru.gallery.edit', $album) }}" class="shrink-0 rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-bold text-[#155EAA]">Edit Informasi Album</a>
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div><h2 class="text-xl font-extrabold text-slate-900">Foto Album</h2><p class="mt-1 text-sm text-slate-600">{{ $photos->total() }} foto tersimpan</p></div>
        </div>

        <form method="POST" enctype="multipart/form-data" action="{{ route('dashboard.guru.gallery.photos.store', $album) }}" class="space-y-3 rounded-2xl border border-slate-200 bg-white p-5 sm:flex sm:items-end sm:justify-between sm:space-y-0 sm:gap-4">
            @csrf
            <div class="min-w-0 flex-1">
                <label for="photos" class="mb-1 block text-sm font-bold">Upload Foto (hingga 20 foto; maks. 5 MB per foto)</label>
                <input id="photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple required class="w-full rounded-lg border border-slate-300 bg-white p-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">
                <p class="mt-1 text-xs text-slate-500">JPG, PNG, atau WebP.</p>
            </div>
            <button class="shrink-0 rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white">Upload Foto</button>
        </form>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($photos as $photo)
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <img src="{{ Storage::disk('public')->url($photo->path) }}" alt="{{ $photo->alt_text }}" class="h-48 w-full object-cover" loading="lazy">
                    <div class="space-y-3 p-3">
                        <p class="line-clamp-2 text-sm font-semibold text-slate-700">{{ $photo->caption ?: $photo->alt_text }}</p>
                        <form method="POST" enctype="multipart/form-data" action="{{ route('dashboard.guru.gallery.photos.update', [$album, $photo]) }}" class="space-y-2 border-t border-slate-100 pt-3">
                            @csrf @method('PUT')
                            <label class="block text-xs font-bold text-slate-700">Ganti file foto</label>
                            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required class="w-full rounded-lg border border-slate-300 bg-white p-2 text-xs focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">
                            <label class="block text-xs font-bold text-slate-700">Keterangan foto</label>
                            <input name="caption" maxlength="255" value="{{ $photo->caption }}" placeholder="Keterangan foto" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">
                            <label class="block text-xs font-bold text-slate-700">Teks alternatif</label>
                            <input name="alt_text" required maxlength="255" value="{{ $photo->alt_text }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">
                            <button class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-[#155EAA]">Ganti Foto</button>
                        </form>
                        <form method="POST" action="{{ route('dashboard.guru.gallery.photos.destroy', [$album, $photo]) }}" onsubmit="return confirm('Hapus foto ini?')">
                            @csrf @method('DELETE')
                            <button class="w-full rounded-lg border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700">Hapus Foto</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500 sm:col-span-2 lg:col-span-3 xl:col-span-4">Album belum memiliki foto.</div>
            @endforelse
        </div>
        <div>{{ $photos->links() }}</div>
    </section>
</div>
@endsection
