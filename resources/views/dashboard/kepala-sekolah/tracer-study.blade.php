@extends('dashboard.layouts.dashboard')

@section('title', 'Statistik Tracer Study | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Statistik Tracer Study')
@section('header_title', 'Statistik Tracer Study')

@section('content')
@php
    $progressMax = max(1, (int) $progressGroups->max('total_records'));
    $majorMax = max(1, (int) $majorGroups->max('total_records'));
    $companyMax = max(1, (int) $companyGroups->max('total_records'));
    $positionMax = max(1, (int) $positionGroups->max('total_records'));
    $placementMax = max(1, (int) $placementGroups->max('total_records'));
    $statusColors = ['#155EAA', '#F59E0B', '#14B8A6', '#8B5CF6', '#EF4444', '#64748B'];
    $statusTotal = (int) $statusGroups->sum('total_records');
    $donutOffset = 0;
@endphp
<div class="mx-auto max-w-7xl space-y-5" @if(request()->routeIs('dashboard.guru.tracer-study.statistics')) data-tracer-statistics-start-top="true" @endif>
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0F2F5F] p-6 text-white shadow-lg sm:p-8">
        <div class="pointer-events-none absolute -bottom-10 -right-10 h-48 w-48 rounded-full bg-white/5"></div>
        <div class="relative z-10 max-w-3xl">
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Statistik Tracer Study</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-200">Statistik dan data alumni berdasarkan tahun pencatatan Tracer Study.</p>
        </div>
    </section>

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">Periksa kembali filter yang dipilih.</div>
    @endif

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="summary-heading">
        <div class="mb-5 flex items-center justify-between gap-3">
            <h2 id="summary-heading" class="text-lg font-extrabold text-slate-900">Ringkasan Data Alumni</h2>
            <p class="text-sm font-bold text-slate-600">Total {{ number_format($totalRecords) }}</p>
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @forelse ($yearTotals as $yearTotal)
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold text-slate-500">Data Tracer Study {{ $yearTotal->tahun }}</p>
                    <p class="mt-2 text-2xl font-extrabold text-[#0F2F5F]">{{ number_format($yearTotal->total_records) }}</p>
                </article>
            @empty
                <article class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:col-span-2"><p class="text-sm text-slate-500">Data belum tersedia.</p></article>
            @endforelse
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="progress-heading">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="progress-heading" class="text-lg font-extrabold text-slate-900">Perkembangan Data Tracer Study</h2>
                <p class="mt-1 text-xs text-slate-500">Jumlah pencatatan data alumni, bukan tingkat keberhasilan kerja alumni.</p>
            </div>
            <span class="text-xs font-semibold text-slate-400">Jumlah alumni</span>
        </div>
        @if ($progressGroups->isNotEmpty())
            @php
                $plotLeft = 58; $plotTop = 18; $plotHeight = 198; $plotWidth = 620;
                $slotWidth = $plotWidth / max(1, $progressGroups->count());
                $barWidth = min(112, $slotWidth * 0.48);
            @endphp
            <div class="mt-4 overflow-x-auto">
                <svg class="mx-auto h-[270px] min-w-[560px] w-full max-w-5xl sm:h-[280px]" viewBox="0 0 720 280" role="img" aria-label="Grafik batang perkembangan data alumni per tahun">
                    <title>Jumlah pencatatan data alumni berdasarkan tahun</title>
                    @for ($tick = 0; $tick <= 4; $tick++)
                        @php $tickValue = (int) ceil($progressMax * $tick / 4); $tickY = $plotTop + $plotHeight - ($plotHeight * $tick / 4); @endphp
                        <line x1="{{ $plotLeft }}" y1="{{ $tickY }}" x2="{{ $plotLeft + $plotWidth }}" y2="{{ $tickY }}" stroke="#E2E8F0" stroke-width="1" />
                        <text x="{{ $plotLeft - 12 }}" y="{{ $tickY + 4 }}" text-anchor="end" fill="#64748B" font-size="12">{{ $tickValue }}</text>
                    @endfor
                    <line x1="{{ $plotLeft }}" y1="{{ $plotTop }}" x2="{{ $plotLeft }}" y2="{{ $plotTop + $plotHeight }}" stroke="#94A3B8" />
                    <line x1="{{ $plotLeft }}" y1="{{ $plotTop + $plotHeight }}" x2="{{ $plotLeft + $plotWidth }}" y2="{{ $plotTop + $plotHeight }}" stroke="#94A3B8" />
                    @foreach ($progressGroups as $group)
                        @php
                            $barHeight = $plotHeight * $group->total_records / $progressMax;
                            $barX = $plotLeft + ($loop->index * $slotWidth) + (($slotWidth - $barWidth) / 2);
                            $barY = $plotTop + $plotHeight - $barHeight;
                        @endphp
                        <g tabindex="0" aria-label="{{ $group->label }}: {{ $group->total_records }} alumni">
                            <title>{{ $group->label }}: {{ number_format($group->total_records) }} alumni</title>
                            <rect x="{{ $barX }}" y="{{ $barY }}" width="{{ $barWidth }}" height="{{ $barHeight }}" rx="8" fill="#155EAA" />
                            <text x="{{ $barX + $barWidth / 2 }}" y="{{ $barY - 9 }}" text-anchor="middle" fill="#0F2F5F" font-size="15" font-weight="700">{{ $group->total_records }}</text>
                            <text x="{{ $barX + $barWidth / 2 }}" y="{{ $plotTop + $plotHeight + 27 }}" text-anchor="middle" fill="#334155" font-size="14" font-weight="700">{{ $group->label }}</text>
                        </g>
                    @endforeach
                </svg>
            </div>
        @else
            <p class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-500">Data belum tersedia.</p>
        @endif
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-2" aria-label="Status dan program keahlian alumni">
        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="status-chart-heading">
            <h2 id="status-chart-heading" class="font-extrabold text-slate-900">Status Alumni</h2>
            @if ($statusGroups->isNotEmpty() && $statusTotal > 0)
                <div class="mt-4 grid grid-cols-1 items-center gap-4 sm:grid-cols-[minmax(150px,0.9fr)_minmax(0,1.1fr)]">
                    <svg class="mx-auto h-[184px] w-[184px] max-w-full" viewBox="0 0 200 200" role="img" aria-label="Diagram donat status alumni, {{ $statusTotal }} record memiliki status">
                        <title>Distribusi {{ number_format($statusTotal) }} record alumni yang memiliki status</title>
                        <circle cx="100" cy="100" r="70" fill="none" stroke="#E2E8F0" stroke-width="28" />
                        @foreach ($statusGroups as $group)
                            @php $segment = 100 * $group->total_records / $statusTotal; $color = $statusColors[$loop->index % count($statusColors)]; @endphp
                            <circle cx="100" cy="100" r="70" fill="none" stroke="{{ $color }}" stroke-width="28" pathLength="100" stroke-dasharray="{{ $segment }} {{ 100 - $segment }}" stroke-dashoffset="{{ -$donutOffset }}" transform="rotate(-90 100 100)" tabindex="0" aria-label="{{ $group->status_label }}: {{ $group->total_records }} alumni">
                                <title>{{ $group->status_label }}: {{ number_format($group->total_records) }} alumni</title>
                            </circle>
                            @php $donutOffset += $segment; @endphp
                        @endforeach
                        <text x="100" y="96" text-anchor="middle" fill="#0F2F5F" font-size="23" font-weight="800">{{ number_format($statusTotal) }}</text>
                        <text x="100" y="116" text-anchor="middle" fill="#64748B" font-size="11">berstatus</text>
                    </svg>
                    <ul class="space-y-3" aria-label="Legenda status alumni">
                        @foreach ($statusGroups as $group)
                            <li class="flex items-start justify-between gap-3 text-sm" title="{{ $group->status_label }}: {{ $group->total_records }} alumni">
                                <span class="flex min-w-0 items-start gap-2"><span class="mt-1.5 h-3 w-3 shrink-0 rounded-sm" style="background-color: {{ $statusColors[$loop->index % count($statusColors)] }}"></span><span class="break-words text-slate-700">{{ $group->status_label }}</span></span>
                                <strong class="shrink-0 text-slate-900">{{ number_format($group->total_records) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-500">Data status belum tersedia.</p>
            @endif
        </article>

        <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="major-chart-heading">
            <h2 id="major-chart-heading" class="font-extrabold text-slate-900">Distribusi Program Keahlian</h2>
            @if ($majorGroups->isNotEmpty())
                <div class="mt-4 space-y-2">
                    @foreach ($majorGroups as $group)
                        @php $barWidth = 440 * $group->total_records / $majorMax; @endphp
                        <div class="grid grid-cols-1 items-center gap-1 sm:grid-cols-[minmax(9rem,0.95fr)_minmax(0,1.3fr)] sm:gap-3">
                            <span class="break-words text-xs font-semibold leading-5 text-slate-700">{{ $group->jurusan }}</span>
                            <svg class="h-8 w-full" viewBox="0 0 520 34" preserveAspectRatio="none" role="img" aria-label="{{ $group->jurusan }}: {{ $group->total_records }} alumni">
                                <title>{{ $group->jurusan }}: {{ number_format($group->total_records) }} alumni</title>
                                @foreach ([0, 110, 220, 330, 440] as $tickX)<line x1="{{ $tickX }}" y1="2" x2="{{ $tickX }}" y2="32" stroke="#E2E8F0" stroke-width="1" />@endforeach
                                <rect x="0" y="8" width="{{ $barWidth }}" height="18" rx="5" fill="#155EAA" />
                                <text x="456" y="22" fill="#334155" font-size="13" font-weight="700">{{ $group->total_records }}</text>
                            </svg>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-500">Data program keahlian belum tersedia.</p>
            @endif
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="company-chart-heading">
        <h2 id="company-chart-heading" class="font-extrabold text-slate-900">Distribusi Perusahaan / Instansi</h2>
        @if ($companyGroups->isNotEmpty())
            <div class="mt-4 max-h-[28rem] space-y-2 overflow-y-auto pr-2">
                @foreach ($companyGroups as $group)
                    @php $barWidth = 440 * $group->total_records / $companyMax; @endphp
                    <div class="grid grid-cols-1 items-center gap-1 sm:grid-cols-[minmax(12rem,1fr)_minmax(0,2fr)] sm:gap-4">
                        <span class="break-words text-sm font-medium leading-5 text-slate-700">{{ $group->label }}</span>
                        <svg class="h-8 w-full" viewBox="0 0 520 36" preserveAspectRatio="none" role="img" aria-label="{{ $group->label }}: {{ $group->total_records }} data">
                            <title>{{ $group->label }}: {{ number_format($group->total_records) }} data</title>
                            @foreach ([0, 110, 220, 330, 440] as $tickX)<line x1="{{ $tickX }}" y1="2" x2="{{ $tickX }}" y2="34" stroke="#E2E8F0" stroke-width="1" />@endforeach
                            <rect x="0" y="8" width="{{ $barWidth }}" height="20" rx="5" fill="#155EAA" />
                            <text x="456" y="23" fill="#334155" font-size="13" font-weight="700">{{ $group->total_records }}</text>
                        </svg>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-500">Data perusahaan/instansi belum tersedia.</p>
        @endif
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-2" aria-label="Posisi pekerjaan dan lokasi penempatan">
        @foreach ([['title' => 'Bidang Pekerjaan atau Posisi', 'groups' => $positionGroups, 'label' => 'label', 'maximum' => $positionMax], ['title' => 'Lokasi Penempatan', 'groups' => $placementGroups, 'label' => 'label', 'maximum' => $placementMax]] as $chart)
            <article class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="font-extrabold text-slate-900">{{ $chart['title'] }}</h2>
                @if ($chart['groups']->isNotEmpty())
                    <div class="mt-4 max-h-[28rem] space-y-2 overflow-y-auto pr-2">
                        @foreach ($chart['groups'] as $group)
                            @php $barWidth = 440 * $group->total_records / $chart['maximum']; $label = data_get($group, $chart['label']); @endphp
                            <div class="grid grid-cols-1 items-center gap-1 sm:grid-cols-[minmax(9rem,0.9fr)_minmax(0,1.2fr)] sm:gap-3">
                                <span class="break-words text-sm font-medium leading-5 text-slate-700">{{ $label }}</span>
                                <svg class="h-8 w-full" viewBox="0 0 520 36" preserveAspectRatio="none" role="img" aria-label="{{ $label }}: {{ $group->total_records }} data">
                                    <title>{{ $label }}: {{ number_format($group->total_records) }} data</title>
                                    @foreach ([0, 110, 220, 330, 440] as $tickX)<line x1="{{ $tickX }}" y1="2" x2="{{ $tickX }}" y2="34" stroke="#E2E8F0" stroke-width="1" />@endforeach
                                    <rect x="0" y="8" width="{{ $barWidth }}" height="20" rx="5" fill="#155EAA" />
                                    <text x="456" y="23" fill="#334155" font-size="13" font-weight="700">{{ $group->total_records }}</text>
                                </svg>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-500">Data belum tersedia.</p>
                @endif
            </article>
        @endforeach
    </section>

    <section id="data-alumni-tracer-study" class="scroll-mt-6 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="alumni-data-heading" tabindex="-1">
        <div class="mb-5"><h2 id="alumni-data-heading" class="text-lg font-extrabold text-slate-900">Data Alumni Tracer Study</h2></div>
        <div id="tracer-study-ajax-loading" class="mb-4 hidden rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-sm text-blue-800" role="status" aria-live="polite">
            <span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-blue-700 border-r-transparent align-middle"></span>
            <span class="ml-2">Memuat data alumni…</span>
        </div>
        <div id="tracer-study-ajax-error" class="mb-4 hidden items-center justify-between gap-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800" role="alert">
            <span>Tabel belum dapat diperbarui. Data sebelumnya tetap ditampilkan.</span>
            <button type="button" data-tracer-native-fallback class="shrink-0 font-bold underline">Muat ulang dengan GET</button>
        </div>
        @include('dashboard.kepala-sekolah.partials.tracer-study-table')
    </section>
</div>
@endsection
