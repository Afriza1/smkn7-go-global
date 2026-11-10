@extends('dashboard.layouts.dashboard')

@section('title', ($album->exists ? 'Edit Informasi Album' : 'Tambah Album').' | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Dokumentasi')
@section('header_title', $album->exists ? 'Edit Informasi Album' : 'Tambah Album Dokumentasi')

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-5">
    <a href="{{ $album->exists ? route('dashboard.guru.gallery.show', $album) : route('dashboard.guru.gallery.index') }}" class="text-sm font-bold text-[#155EAA]">← Kembali</a>
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
    <form method="POST" enctype="multipart/form-data" action="{{ $album->exists ? route('dashboard.guru.gallery.update', $album) : route('dashboard.guru.gallery.store') }}" class="w-full space-y-4 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
        @csrf @if($album->exists) @method('PUT') @endif
        <div><label for="title" class="mb-1 block text-sm font-bold">Nama kegiatan</label><input id="title" name="title" required maxlength="180" value="{{ old('title', $album->title) }}" placeholder="Masukkan nama kegiatan" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20"></div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="category" class="mb-1 block text-sm font-bold">Kategori</label>
                @php($currentCategory = old('category', $album->category))
                <select id="category" name="category" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">
                    <option value="" @selected(!$currentCategory)>Pilih kategori kegiatan</option>
                    @foreach (['Seleksi', 'Kesehatan', 'Pembekalan', 'Kemitraan', 'Kegiatan Sekolah'] as $category)
                        <option value="{{ $category }}" @selected($currentCategory === $category)>{{ $category }}</option>
                    @endforeach
                    @if ($currentCategory && !in_array($currentCategory, ['Seleksi', 'Kesehatan', 'Pembekalan', 'Kemitraan', 'Kegiatan Sekolah'], true))
                        <option value="{{ $currentCategory }}" selected>{{ $currentCategory }}</option>
                    @endif
                </select>
            </div>
            <div><label for="event_date" class="mb-1 block text-sm font-bold">Tanggal kegiatan</label><input id="event_date" type="date" name="event_date" value="{{ old('event_date', $album->event_date?->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20"></div>
        </div>
        <div><label for="description" class="mb-1 block text-sm font-bold">Deskripsi</label><textarea id="description" name="description" rows="3" placeholder="Tuliskan deskripsi kegiatan..." class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">{{ old('description', $album->description) }}</textarea></div>
        @unless($album->exists)<div><label for="photos" class="mb-1 block text-sm font-bold">Foto (opsional, maksimal 20 foto; 5 MB per foto)</label><input id="photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple class="w-full rounded-lg border border-slate-300 bg-white p-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20"></div>@endunless
        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-4"><button name="status" value="draft" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700">Simpan Draft</button><button name="status" value="published" class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white">Publish</button></div>
    </form>
</div>
@endsection
