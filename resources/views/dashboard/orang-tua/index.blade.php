@extends('dashboard.layouts.dashboard')
@section('title', 'Dashboard Orang Tua | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Portal Orang Tua')
@section('header_title', 'Perkembangan Anak')
@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <section class="rounded-3xl bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#166534] p-6 sm:p-8 text-white shadow-lg">
        <p class="text-xs font-bold text-emerald-100">Portal Orang Tua / Wali</p><h1 class="mt-2 text-2xl font-extrabold">Halo, {{ auth()->user()->name }}</h1>
        <p class="mt-3 text-sm text-slate-100">Siswa terhubung: <strong>{{ $student->name }}</strong></p>
        <p class="mt-1 text-xs text-slate-200">{{ $applications->first()?->jurusan ?: 'Program keahlian belum tersedia' }} · SMKN 7 Jember</p>
    </section>
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-extrabold text-[#0F2F5F]">Perkembangan program</h2>
        @if($applications->isEmpty())
            <p class="mt-3 rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Belum ada data pendaftaran program yang tersedia untuk siswa ini.</p>
        @else
            <div class="mt-4 space-y-3">@foreach($applications as $application)
                <article class="rounded-2xl border border-slate-200 p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3"><div><h3 class="font-bold text-slate-800">{{ $application->lpk?->nama_lpk ?: 'Pendaftaran Program Jepang' }}</h3><p class="mt-1 text-xs text-slate-500">{{ $application->nomor_pendaftaran }} · {{ $application->created_at?->format('d M Y') }}</p></div><span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800">{{ $application->getStatusLabel() }}</span></div>
                </article>
            @endforeach</div>
        @endif
    </section>

    @if ($pendaftaranAktif)
        <x-career-progress-tracker :steps="$trackerSteps" :is-orang-tua="true" />
    @endif
</div>
@endsection
