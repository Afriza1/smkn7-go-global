@extends('dashboard.layouts.dashboard')

@section('title', 'Kelola Panduan Dokumen Keberangkatan | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Kelola Panduan Dokumen')
@section('header_title', 'Pengelolaan Panduan Dokumen Keberangkatan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <div>
        <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Admin BKK</p>
        <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Panduan Dokumen Keberangkatan</h1>
        <p class="mt-1 max-w-4xl text-sm leading-relaxed text-slate-600">Tentukan penerapan tiap dokumen untuk setiap jalur. Status awal “Belum Ditentukan” sengaja tidak ditampilkan sebagai persyaratan kepada pengguna.</p>
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
        <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Jalur Keberangkatan</h2>
                <p class="mt-1 text-sm text-slate-600">Jalur yang dipublikasikan dapat dipilih siswa dan alumni.</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">{{ $jalurs->count() }} jalur</span>
        </div>
        <form method="POST" action="{{ route('dashboard.admin.panduan-dokumen.jalur.store') }}" class="grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 lg:grid-cols-3">
            @csrf
            <div>
                <label for="jalur_nama" class="mb-1 block text-xs font-bold text-slate-700">Nama jalur</label>
                <input id="jalur_nama" name="nama" required maxlength="180" value="{{ old('nama') }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>
            <div>
                <label for="jalur_deskripsi" class="mb-1 block text-xs font-bold text-slate-700">Deskripsi (opsional)</label>
                <input id="jalur_deskripsi" name="deskripsi" maxlength="2000" value="{{ old('deskripsi') }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm">
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700"><input type="checkbox" name="is_published" value="1" @checked(old('is_published')) class="h-4 w-4 rounded border-slate-300 text-[#155EAA]"> Publikasikan</label>
                <button class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#0F2F5F]">Tambah jalur</button>
            </div>
        </form>

        <div class="mt-4 grid grid-cols-1 gap-3 xl:grid-cols-2">
            @foreach ($jalurs as $jalur)
                <article class="rounded-xl border border-slate-200 p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-bold text-slate-900">{{ $jalur->nama }}</h3>
                        <span class="rounded-full border px-3 py-1 text-[11px] font-bold {{ $jalur->is_published ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-600' }}">{{ $jalur->is_published ? 'Dipublikasikan' : 'Draft' }}</span>
                    </div>
                    @if ($jalur->deskripsi)<p class="mt-1 text-sm text-slate-600">{{ $jalur->deskripsi }}</p>@endif
                    <p class="mt-2 text-xs text-slate-500">{{ $jalur->checklists_count }} status checklist tersimpan · Checklist tetap dipertahankan saat metadata diubah.</p>
                    <details class="relative mt-3">
                        <summary class="cursor-pointer list-none text-xs font-bold text-[#155EAA] hover:underline">Ubah jalur</summary>
                        <form method="POST" action="{{ route('dashboard.admin.panduan-dokumen.jalur.update', $jalur) }}" class="mt-3 grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="jalur-nama-{{ $jalur->id }}" class="mb-1 block text-xs font-bold text-slate-700">Nama jalur</label>
                                <input id="jalur-nama-{{ $jalur->id }}" name="nama" required maxlength="180" value="{{ $jalur->nama }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label for="jalur-desc-{{ $jalur->id }}" class="mb-1 block text-xs font-bold text-slate-700">Deskripsi</label>
                                <input id="jalur-desc-{{ $jalur->id }}" name="deskripsi" maxlength="2000" value="{{ $jalur->deskripsi }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                            </div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 sm:col-span-2"><input type="checkbox" name="is_published" value="1" @checked($jalur->is_published) class="h-4 w-4 rounded border-slate-300 text-[#155EAA]"> Dipublikasikan</label>
                            <button class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white sm:col-span-2">Simpan jalur</button>
                        </form>
                    </details>
                </article>
            @endforeach
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-lg font-extrabold text-slate-900">Kategori Dokumen</h2>
            <p class="mt-1 text-sm text-slate-600">Kategori membantu pengelompokan panduan dan tidak menentukan kewajiban dokumen.</p>
        </div>
        <form method="POST" action="{{ route('dashboard.admin.panduan-dokumen.kategori.store') }}" class="grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-3">
            @csrf
            <input name="nama" required maxlength="120" value="{{ old('nama') }}" placeholder="Nama kategori" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <input name="deskripsi" maxlength="1000" value="{{ old('deskripsi') }}" placeholder="Deskripsi opsional" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm">
            <button class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#0F2F5F]">Tambah kategori</button>
        </form>
        <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-2">
            @foreach ($kategoris as $kategori)
                <form method="POST" action="{{ route('dashboard.admin.panduan-dokumen.kategori.update', $kategori) }}" class="grid grid-cols-1 gap-2 rounded-xl border border-slate-200 p-3 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="kategori-{{ $kategori->id }}" class="mb-1 block text-[11px] font-bold text-slate-600">Kategori · {{ $kategori->dokumens_count }} dokumen</label>
                        <input id="kategori-{{ $kategori->id }}" name="nama" required maxlength="120" value="{{ $kategori->nama }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <input name="deskripsi" maxlength="1000" value="{{ $kategori->deskripsi }}" aria-label="Deskripsi kategori {{ $kategori->nama }}" placeholder="Deskripsi opsional" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <button class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Simpan</button>
                </form>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Data Dokumen</h2>
            <p class="mt-1 text-sm text-slate-600">Pengaturan wajib/opsional/tidak berlaku dilakukan terpisah untuk setiap jalur.</p>
        </div>

        <form method="POST" action="{{ route('dashboard.admin.panduan-dokumen.dokumen.store') }}" class="grid grid-cols-1 gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:grid-cols-2">
            @csrf
            <h3 class="font-extrabold text-slate-900 lg:col-span-2">Tambah dokumen</h3>
            <div>
                <label for="dokumen_nama" class="mb-1 block text-xs font-bold text-slate-700">Nama dokumen</label>
                <input id="dokumen_nama" name="nama" required maxlength="180" value="{{ old('nama') }}" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">
            </div>
            <div>
                <label for="dokumen_kategori" class="mb-1 block text-xs font-bold text-slate-700">Kategori</label>
                <select id="dokumen_kategori" name="kategori_id" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm">
                    <option value="">Pilih kategori</option>
                    @foreach ($kategoris as $kategori)<option value="{{ $kategori->id }}" @selected(old('kategori_id') == $kategori->id)>{{ $kategori->nama }}</option>@endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label for="dokumen_penjelasan" class="mb-1 block text-xs font-bold text-slate-700">Penjelasan atau fungsi</label>
                <textarea id="dokumen_penjelasan" name="penjelasan" rows="2" maxlength="3000" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">{{ old('penjelasan') }}</textarea>
            </div>
            <div>
                <label for="dokumen_petunjuk" class="mb-1 block text-xs font-bold text-slate-700">Petunjuk pengurusan</label>
                <textarea id="dokumen_petunjuk" name="petunjuk_pengurusan" rows="3" maxlength="3000" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">{{ old('petunjuk_pengurusan') }}</textarea>
            </div>
            <div>
                <label for="dokumen_keterangan" class="mb-1 block text-xs font-bold text-slate-700">Keterangan tambahan</label>
                <textarea id="dokumen_keterangan" name="keterangan" rows="3" maxlength="2000" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm">{{ old('keterangan') }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 lg:col-span-2"><input type="checkbox" name="is_published" value="1" @checked(old('is_published')) class="h-4 w-4 rounded border-slate-300 text-[#155EAA]"> Publikasikan dokumen</label>
            <button class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#0F2F5F] lg:col-span-2">Tambah dokumen</button>
        </form>

        @forelse ($dokumens as $dokumen)
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-[11px] font-bold text-[#155EAA]">{{ $dokumen->kategori->nama }}</span>
                    <span class="rounded-full border px-3 py-1 text-[11px] font-bold {{ $dokumen->is_published ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-600' }}">{{ $dokumen->is_published ? 'Dipublikasikan' : 'Draft' }}</span>
                </div>
                <h3 class="mt-3 text-base font-extrabold text-slate-900">{{ $dokumen->nama }}</h3>
                @if ($dokumen->penjelasan)<p class="mt-1 text-sm text-slate-600">{{ $dokumen->penjelasan }}</p>@endif

                <details class="relative mt-3">
                    <summary class="cursor-pointer list-none text-xs font-bold text-[#155EAA] hover:underline">Ubah informasi dokumen</summary>
                    <form method="POST" action="{{ route('dashboard.admin.panduan-dokumen.dokumen.update', $dokumen) }}" class="mt-3 grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <input name="nama" required maxlength="180" value="{{ $dokumen->nama }}" aria-label="Nama dokumen" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                        <select name="kategori_id" required aria-label="Kategori dokumen" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                            @foreach ($kategoris as $kategori)<option value="{{ $kategori->id }}" @selected($dokumen->kategori_id === $kategori->id)>{{ $kategori->nama }}</option>@endforeach
                        </select>
                        <textarea name="penjelasan" rows="2" maxlength="3000" aria-label="Penjelasan" placeholder="Penjelasan atau fungsi" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm sm:col-span-2">{{ $dokumen->penjelasan }}</textarea>
                        <textarea name="petunjuk_pengurusan" rows="3" maxlength="3000" aria-label="Petunjuk pengurusan" placeholder="Petunjuk pengurusan" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">{{ $dokumen->petunjuk_pengurusan }}</textarea>
                        <textarea name="keterangan" rows="3" maxlength="2000" aria-label="Keterangan tambahan" placeholder="Keterangan tambahan" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">{{ $dokumen->keterangan }}</textarea>
                        <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 sm:col-span-2"><input type="checkbox" name="is_published" value="1" @checked($dokumen->is_published) class="h-4 w-4 rounded border-slate-300 text-[#155EAA]"> Publikasikan dokumen</label>
                        <button class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white sm:col-span-2">Simpan metadata</button>
                    </form>
                </details>

                <div class="mt-5 border-t border-slate-100 pt-4">
                    <h4 class="text-xs font-extrabold uppercase tracking-wide text-slate-600">Penerapan per jalur</h4>
                    <div class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-2">
                        @foreach ($jalurs as $jalur)
                            @php $penerapanSaatIni = $dokumen->jalurs->firstWhere('id', $jalur->id)?->pivot?->penerapan ?? 'unconfigured'; @endphp
                            <form method="POST" action="{{ route('dashboard.admin.panduan-dokumen.penerapan.update', [$jalur, $dokumen]) }}" class="flex flex-col gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:flex-row sm:items-end">
                                @csrf
                                @method('PUT')
                                <div class="min-w-0 flex-1">
                                    <label for="penerapan-{{ $jalur->id }}-{{ $dokumen->id }}" class="mb-1 block text-xs font-bold text-slate-700">{{ $jalur->nama }}</label>
                                    <select id="penerapan-{{ $jalur->id }}-{{ $dokumen->id }}" name="penerapan" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                                        <option value="unconfigured" @selected($penerapanSaatIni === 'unconfigured')>Belum Ditentukan</option>
                                        <option value="required" @selected($penerapanSaatIni === 'required')>Wajib</option>
                                        <option value="optional" @selected($penerapanSaatIni === 'optional')>Opsional</option>
                                        <option value="not_applicable" @selected($penerapanSaatIni === 'not_applicable')>Tidak Berlaku</option>
                                    </select>
                                </div>
                                <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100">Simpan</button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada dokumen panduan.</div>
        @endforelse
    </section>

    <p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs leading-relaxed text-amber-900">
        Penghapusan jalur, kategori, dan dokumen tidak disediakan pada MVP ini untuk menjaga relasi dan histori checklist. Nonaktifkan publikasi atau ubah penerapan menjadi “Tidak Berlaku” bila panduan tidak digunakan.
    </p>
</div>
@endsection
