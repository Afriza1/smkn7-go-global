@extends('dashboard.layouts.dashboard')

@section('title', 'Detail Periode Tracer Study | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Kelola Tracer Study')
@section('header_title', 'Detail Periode Tracer Study')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <a href="{{ route('dashboard.guru.tracer-study.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#155EAA] hover:underline">&larr; Kembali ke Kelola Tracer Study</a>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div><p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Tahun {{ $periode->tahun }}</p><h1 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $periode->nama_tampilan }}</h1><p class="mt-1 text-sm text-slate-600">{{ $periode->tanggal_mulai->format('d/m/Y') }} – {{ $periode->tanggal_berakhir->format('d/m/Y') }}</p></div>
            <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $periode->status_badge_class }}">{{ $periode->status_label }}</span>
        </div>
        @if ($periode->keterangan)<p class="mt-4 whitespace-pre-line text-sm text-slate-600">{{ $periode->keterangan }}</p>@endif
    </section>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-rose-200 bg-rose-50 text-rose-800'] as $flash => $flashClass)
        @if (session($flash))<div class="rounded-xl border p-4 text-sm {{ $flashClass }}" role="status">{{ session($flash) }}</div>@endif
    @endforeach
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert"><p class="font-bold">Periksa data berikut:</p><ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section aria-label="Ringkasan respons" class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach ([['Total respons', $stats['total'], 'text-[#155EAA]'], ['Belum diverifikasi', $stats['belum_diverifikasi'], 'text-amber-700'], ['Terverifikasi', $stats['terverifikasi'], 'text-emerald-700'], ['Perlu perbaikan', $stats['perlu_perbaikan'], 'text-rose-700']] as [$label, $count, $tone])
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold text-slate-500">{{ $label }}</p><p class="mt-2 text-2xl font-extrabold {{ $tone }}">{{ number_format($count) }}</p></article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4"><h2 class="text-lg font-extrabold text-slate-900">Daftar Respons Alumni</h2><p class="mt-1 text-sm text-slate-600">Daftar ini hanya mencakup respons periode {{ $periode->nama_tampilan }}.</p></div>
        <form method="GET" action="{{ route('dashboard.guru.tracer-study.show', $periode) }}" class="mb-5 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-3">
            <input name="search" value="{{ $filters['search'] ?? '' }}" maxlength="100" placeholder="Cari nama Alumni" aria-label="Cari nama Alumni" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-[#155EAA]">
            <select name="status_verifikasi" aria-label="Filter status verifikasi" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm">
                <option value="">Semua status</option>
                <option value="unverified" @selected(($filters['status_verifikasi'] ?? '') === 'unverified')>Belum diverifikasi</option>
                <option value="verified" @selected(($filters['status_verifikasi'] ?? '') === 'verified')>Terverifikasi</option>
                <option value="needs_update" @selected(($filters['status_verifikasi'] ?? '') === 'needs_update')>Perlu perbaikan</option>
                <option value="follow_up" @selected(($filters['status_verifikasi'] ?? '') === 'follow_up')>Dalam tindak lanjut</option>
            </select>
            <button class="rounded-lg bg-[#155EAA] px-4 py-2 text-sm font-bold text-white hover:bg-[#0F2F5F]">Terapkan filter</button>
        </form>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full min-w-[950px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-3">No.</th><th class="px-3 py-3">Nama Alumni</th><th class="px-3 py-3">Tahun lulus</th><th class="px-3 py-3">Jurusan</th><th class="px-3 py-3">Status kegiatan</th><th class="px-3 py-3">Dikirim / diperbarui</th><th class="px-3 py-3">Status verifikasi</th><th class="px-3 py-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($responses as $response)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-3 py-3 text-slate-600">{{ $responses->firstItem() + $loop->index }}</td>
                            <td class="px-3 py-3"><p class="font-bold text-slate-900">{{ $response->nama_lengkap }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $response->user?->email }}</p></td>
                            <td class="px-3 py-3 text-slate-700">{{ $response->tahun_lulus }}</td><td class="px-3 py-3 text-slate-700">{{ $response->jurusan }}</td>
                            <td class="px-3 py-3 text-slate-700">{{ ucfirst($response->status_kegiatan) }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600">{{ $response->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3 py-3"><span class="inline-flex whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-bold {{ $response->status_badge_class }}">{{ $response->status_label }}</span></td>
                            <td class="px-3 py-3"><a href="{{ route('dashboard.guru.tracer-study.response', [$periode, $response]) }}" class="font-bold text-[#155EAA] hover:underline">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada respons yang sesuai dengan periode dan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $responses->links() }}</div>
    </section>
</div>
@endsection
