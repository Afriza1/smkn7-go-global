@extends('dashboard.layouts.dashboard')

@section('title', $reportData['title'] . ' | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Arsip Laporan Program')
@section('header_title', $reportData['title'])

@section('content')
<style>
    @media print {
        @page { margin: 14mm; }
        html, body { height: auto !important; min-height: 0 !important; overflow: visible !important; background: #fff !important; }
        body { display: block !important; }
        #dashboard-sidebar, #sidebar-backdrop, body > div.flex-1 > header, .report-actions { display: none !important; }
        body > div.flex-1 { display: block !important; height: auto !important; overflow: visible !important; }
        body > div.flex-1 > main { display: block !important; height: auto !important; overflow: visible !important; padding: 0 !important; background: #fff !important; }
        .report-card { break-inside: avoid; box-shadow: none !important; }
        a { color: inherit !important; text-decoration: none !important; }
    }
</style>

<div class="mx-auto max-w-7xl space-y-6">
    <div class="report-actions flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('dashboard.kepala-sekolah') }}#laporan-program" class="inline-flex items-center gap-2 text-sm font-bold text-[#155EAA] hover:underline">&larr; Kembali ke Dashboard Kepala Sekolah</a>
        <div class="flex flex-wrap gap-2">
            @if ($excelAvailable)
                <a href="{{ route('dashboard.kepala-sekolah.laporan.excel', $reportData['slug']) }}" class="inline-flex items-center justify-center rounded-lg bg-[#166534] px-4 py-2.5 text-sm font-bold text-white hover:bg-green-800">Unduh Excel</a>
            @else
                <span class="rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-500">Ekspor Excel tidak tersedia di server ini</span>
            @endif
            <button type="button" onclick="window.print()" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">Cetak / Simpan PDF</button>
        </div>
    </div>

    <header class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Arsip Laporan Program · Pratinjau</p>
        <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-[#0F2F5F]">{{ $reportData['title'] }}</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $reportData['description'] }}</p>
        <p class="mt-3 text-xs text-slate-500">Dibuat pada {{ now()->format('d/m/Y H:i') }}</p>
    </header>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan laporan">
        @foreach ($reportData['summary'] as [$label, $value])
            <article class="report-card rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-xl font-extrabold text-[#155EAA]">{{ is_numeric($value) ? number_format($value) : $value }}</p>
            </article>
        @endforeach
    </section>

    @if ($reportData['notice'])
        <p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900" role="note">{{ $reportData['notice'] }}</p>
    @endif

    @foreach ($reportData['sections'] as $section)
        <section class="report-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-4"><h2 class="text-lg font-extrabold text-slate-900">{{ $section['title'] }}</h2></div>
            @if (empty($section['rows']))
                <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada data aktual untuk ditampilkan.</p>
            @else
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full min-w-[520px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr>@foreach ($section['headers'] as $heading)<th class="px-4 py-3">{{ $heading }}</th>@endforeach</tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($section['rows'] as $row)
                                <tr class="hover:bg-slate-50/70">@foreach ($row as $cell)<td class="px-4 py-3 text-slate-700">{{ is_numeric($cell) ? number_format($cell) : $cell }}</td>@endforeach</tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endforeach

    <p class="text-xs text-slate-500">Laporan ini disusun dari data yang tersimpan pada aplikasi. Informasi yang tidak memiliki sumber data tidak ditaksir.</p>
</div>
@endsection
