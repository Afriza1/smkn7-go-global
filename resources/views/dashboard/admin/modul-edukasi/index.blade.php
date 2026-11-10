@extends('dashboard.layouts.dashboard')

@section('title', 'Kelola Modul Edukasi | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Kelola Modul Edukasi')
@section('header_title', 'Pengelolaan Modul Edukasi')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <div>
        <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Admin BKK</p>
        <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Kelola Modul Edukasi</h1>
        <p class="mt-1 text-sm text-slate-600">Kelola metadata dan publikasi dokumen pembelajaran. File PDF sumber tidak diubah atau dihapus saat metadata dikelola.</p>
    </div>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-rose-200 bg-rose-50 text-rose-800', 'info' => 'border-blue-200 bg-blue-50 text-blue-800'] as $flash => $flashClass)
        @if (session($flash))
            <div class="rounded-xl border p-4 text-sm {{ $flashClass }}" role="status">{{ session($flash) }}</div>
        @endif
    @endforeach
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">
            <p class="font-bold">Periksa kembali data berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-lg font-extrabold text-slate-900">Tambah metadata materi</h2>
            <p class="mt-1 text-sm text-slate-600">Materi baru hanya dapat dipasangkan dengan PDF statis yang tersedia dan belum terdaftar.</p>
        </div>
        @if (count($availableFiles))
            <form method="POST" action="{{ route('dashboard.admin.modul-edukasi.store') }}" class="grid grid-cols-1 gap-4 rounded-xl bg-slate-50 p-4 lg:grid-cols-2">
                @csrf
                <div>
                    <label for="judul" class="mb-1.5 block text-xs font-bold text-slate-700">Judul materi</label>
                    <input id="judul" name="judul" type="text" required maxlength="180" value="{{ old('judul') }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA]">
                </div>
                <div>
                    <label for="kategori" class="mb-1.5 block text-xs font-bold text-slate-700">Kategori</label>
                    <input id="kategori" name="kategori" type="text" required maxlength="120" value="{{ old('kategori') }}" placeholder="Contoh: Persiapan Karier Jepang" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA]">
                </div>
                <div class="lg:col-span-2">
                    <label for="deskripsi" class="mb-1.5 block text-xs font-bold text-slate-700">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" required maxlength="3000" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA]">{{ old('deskripsi') }}</textarea>
                </div>
                <div>
                    <label for="file_path" class="mb-1.5 block text-xs font-bold text-slate-700">PDF statis</label>
                    <select id="file_path" name="file_path" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA]">
                        <option value="">Pilih file yang tersedia</option>
                        @foreach ($availableFiles as $path => $label)
                            <option value="{{ $path }}" @selected(old('file_path') === $path)>{{ $label }} — {{ basename($path) }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 self-center text-sm font-semibold text-slate-700">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published')) class="h-4 w-4 rounded border-slate-300 text-[#155EAA] focus:ring-[#155EAA]">
                    Publikasikan langsung
                </label>
                <div class="lg:col-span-2 lg:flex lg:justify-end">
                    <button class="w-full rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#0F2F5F] lg:w-auto">Tambah materi</button>
                </div>
            </form>
        @else
            <p class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-slate-700">Semua PDF statis yang tersedia sudah terdaftar. Fitur upload file baru belum disediakan.</p>
        @endif
    </section>

    <section class="space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-2">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Daftar Materi</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $materis->count() }} metadata materi terdaftar.</p>
            </div>
        </div>

        @forelse ($materis as $materi)
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-[11px] font-bold text-[#155EAA]">{{ $materi->kategori }}</span>
                            @if ($materi->is_published)
                                <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-700">Dipublikasikan</span>
                            @else
                                <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[11px] font-bold text-amber-800">Draft</span>
                            @endif
                        </div>
                        <h3 class="mt-3 text-base font-extrabold text-slate-900">{{ $materi->judul }}</h3>
                        <p class="mt-1 max-w-3xl text-sm leading-relaxed text-slate-600">{{ $materi->deskripsi }}</p>
                        <p class="mt-2 break-all text-xs text-slate-500">{{ $materi->file_path }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2 xl:justify-end">
                        <a href="{{ route('dashboard.admin.modul-edukasi.read', $materi) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Preview PDF</a>
                        <a href="{{ route('dashboard.admin.modul-edukasi.download', $materi) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Unduh</a>
                        <details>
                            <summary class="cursor-pointer list-none rounded-lg bg-[#155EAA] px-3 py-2 text-xs font-bold text-white hover:bg-[#0F2F5F]">Edit metadata</summary>
                            <form method="POST" action="{{ route('dashboard.admin.modul-edukasi.update', $materi) }}" class="mt-3 grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2 xl:absolute xl:z-10 xl:w-[600px]">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label for="judul-{{ $materi->id }}" class="mb-1 block text-xs font-bold text-slate-700">Judul</label>
                                    <input id="judul-{{ $materi->id }}" name="judul" type="text" required maxlength="180" value="{{ $materi->judul }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label for="kategori-{{ $materi->id }}" class="mb-1 block text-xs font-bold text-slate-700">Kategori</label>
                                    <input id="kategori-{{ $materi->id }}" name="kategori" type="text" required maxlength="120" value="{{ $materi->kategori }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="deskripsi-{{ $materi->id }}" class="mb-1 block text-xs font-bold text-slate-700">Deskripsi</label>
                                    <textarea id="deskripsi-{{ $materi->id }}" name="deskripsi" rows="3" required maxlength="3000" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">{{ $materi->deskripsi }}</textarea>
                                </div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 sm:col-span-2">
                                    <input type="checkbox" name="is_published" value="1" @checked($materi->is_published) class="h-4 w-4 rounded border-slate-300 text-[#155EAA] focus:ring-[#155EAA]">
                                    Dipublikasikan untuk siswa dan alumni
                                </label>
                                <button class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white sm:col-span-2">Simpan metadata</button>
                            </form>
                        </details>
                        <form method="POST" action="{{ route('dashboard.admin.modul-edukasi.destroy', $materi) }}" onsubmit="return confirm('Hapus metadata materi ini? File PDF statis tidak akan dihapus.')">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100">Hapus metadata</button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada metadata materi.</div>
        @endforelse
    </section>
</div>
@endsection
