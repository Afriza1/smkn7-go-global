@extends('dashboard.layouts.dashboard')

@section('title', 'Manajemen Pengumuman | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Pengumuman')
@section('header_title', 'Manajemen Pengumuman')

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
    <div class="flex flex-wrap items-center justify-between gap-3"><div><h1 class="text-2xl font-extrabold text-slate-900">Pengumuman</h1><p class="mt-1 text-sm text-slate-600">Kelola informasi yang ditampilkan pada landing page.</p></div><a href="{{ route('dashboard.guru.announcements.create') }}" class="rounded-xl bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white">Tambah Pengumuman</a></div>
    <form method="GET" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_200px_auto]">
        <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari judul, kategori, ringkasan" class="rounded-lg border-slate-300 text-sm">
        <select name="status" class="rounded-lg border-slate-300 text-sm"><option value="">Semua status</option><option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option><option value="published" @selected(($filters['status'] ?? '') === 'published')>Published</option></select>
        <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-bold text-white">Cari</button>
    </form>
    <div class="space-y-3">
        @forelse($announcements as $announcement)
            <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center">
                @if($announcement->cover_image)<img src="{{ Storage::disk('public')->url($announcement->cover_image) }}" alt="" class="h-24 w-full rounded-xl object-cover sm:w-36">@endif
                <div class="min-w-0 flex-1"><div class="flex flex-wrap gap-2"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-[#155EAA]">{{ $announcement->category }}</span><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $announcement->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($announcement->status) }}</span></div><h2 class="mt-2 font-extrabold text-slate-900">{{ $announcement->title }}</h2><p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $announcement->excerpt }}</p></div>
                <div class="flex shrink-0 gap-2"><a href="{{ route('dashboard.guru.announcements.edit', $announcement) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-[#155EAA]">Edit</a><form method="POST" action="{{ route('dashboard.guru.announcements.destroy', $announcement) }}" onsubmit="return confirm('Hapus pengumuman ini?')">@csrf @method('DELETE')<button class="rounded-lg border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700">Hapus</button></form></div>
            </article>
        @empty<div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada pengumuman.</div>@endforelse
    </div>
    {{ $announcements->links() }}
</div>
@endsection
