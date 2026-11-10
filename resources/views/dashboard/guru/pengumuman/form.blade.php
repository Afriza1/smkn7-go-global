@extends('dashboard.layouts.dashboard')

@section('title', ($announcement->exists ? 'Edit' : 'Tambah').' Pengumuman | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Pengumuman')
@section('header_title', $announcement->exists ? 'Edit Pengumuman' : 'Tambah Pengumuman')

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-5">
    <a href="{{ route('dashboard.guru.announcements.index') }}" class="text-sm font-bold text-[#155EAA]">← Kembali ke daftar</a>
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
    <form method="POST" enctype="multipart/form-data" action="{{ $announcement->exists ? route('dashboard.guru.announcements.update', $announcement) : route('dashboard.guru.announcements.store') }}" class="w-full space-y-4 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7">
        @csrf @if($announcement->exists) @method('PUT') @endif
        <div><label class="mb-1 block text-sm font-bold">Judul</label><input name="title" required maxlength="180" value="{{ old('title', $announcement->title) }}" placeholder="Masukkan judul pengumuman" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20"></div>
        <div>
            <label for="category" class="mb-1 block text-sm font-bold">Kategori</label>
            <select id="category" name="category" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">
                <option value="" disabled @selected(old('category', $announcement->category) === null || old('category', $announcement->category) === '')>Pilih kategori pengumuman</option>
                @foreach (['Seleksi & Karier', 'Diklat Vokasi', 'Kesehatan & MCU', 'Kemitraan Global', 'Informasi Sekolah'] as $category)
                    <option value="{{ $category }}" @selected(old('category', $announcement->category) === $category)>{{ $category }}</option>
                @endforeach
                @if (old('category', $announcement->category) && !in_array(old('category', $announcement->category), ['Seleksi & Karier', 'Diklat Vokasi', 'Kesehatan & MCU', 'Kemitraan Global', 'Informasi Sekolah'], true))
                    <option value="{{ old('category', $announcement->category) }}" selected>{{ old('category', $announcement->category) }}</option>
                @endif
            </select>
        </div>
        <div><label class="mb-1 block text-sm font-bold">Ringkasan</label><textarea name="excerpt" required maxlength="500" rows="2" placeholder="Tuliskan ringkasan singkat pengumuman" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">{{ old('excerpt', $announcement->excerpt) }}</textarea></div>
        <div><label class="mb-1 block text-sm font-bold">Isi Pengumuman</label><textarea name="body" required rows="8" placeholder="Tuliskan isi pengumuman secara lengkap..." class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20">{{ old('body', $announcement->body) }}</textarea></div>
        <div><label class="mb-1 block text-sm font-bold">Gambar sampul (opsional, maks. 5 MB)</label><input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="w-full rounded-lg border border-slate-300 bg-white p-2 focus:border-[#155EAA] focus:outline-none focus:ring-2 focus:ring-[#155EAA]/20"><p class="mt-1 text-xs text-slate-500">JPG, PNG, atau WebP.</p>@if($announcement->cover_image)<img src="{{ Storage::disk('public')->url($announcement->cover_image) }}" alt="Sampul saat ini" class="mt-3 h-36 rounded-xl object-cover">@endif</div>
        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 pt-4"><button name="status" value="draft" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700">Simpan Draft</button><button name="status" value="published" class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white">Publish</button></div>
    </form>
</div>
@endsection
