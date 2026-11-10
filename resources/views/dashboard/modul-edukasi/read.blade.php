@extends('dashboard.layouts.dashboard')

@section('title', $materi->judul . ' | Modul Edukasi')
@section('breadcrumb', 'Baca Modul Edukasi')
@section('header_title', 'Pembaca Materi')

@section('content')
<div class="mx-auto max-w-7xl space-y-5">
    <div class="flex flex-col gap-4 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div>
            <a href="{{ Auth::user()->isAdminSekolah() ? route('dashboard.admin.modul-edukasi.index') : route('dashboard.modul-edukasi.index') }}" class="text-xs font-bold text-[#155EAA] hover:underline">← Kembali ke daftar Modul Edukasi</a>
            <p class="mt-3 text-xs font-bold uppercase tracking-wide text-[#155EAA]">{{ $materi->kategori }}</p>
            <h1 class="mt-1 text-xl font-extrabold leading-snug text-slate-900 sm:text-2xl">{{ $materi->judul }}</h1>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-600">{{ $materi->deskripsi }}</p>
        </div>
        <div class="flex shrink-0 flex-col gap-2 sm:w-44">
            <a href="{{ $downloadRoute }}" class="inline-flex items-center justify-center rounded-xl bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#0F2F5F]">Unduh PDF</a>
            <a href="{{ $fileRoute }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Buka di tab baru</a>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-200 shadow-sm" aria-label="Pembaca PDF">
        <iframe src="{{ $fileRoute }}#toolbar=1&view=FitH" title="{{ $materi->judul }}" class="h-[65vh] min-h-[480px] w-full bg-slate-100 sm:h-[75vh] lg:h-[calc(100vh-260px)]"></iframe>
    </section>

    <p class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-xs leading-relaxed text-slate-700">
        Jika PDF tidak tampil di browser, gunakan tombol <strong>Buka di tab baru</strong> atau <strong>Unduh PDF</strong> di atas.
    </p>
</div>
@endsection
