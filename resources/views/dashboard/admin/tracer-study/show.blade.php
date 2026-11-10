@extends('dashboard.layouts.dashboard')

@section('title', 'Detail Respons Tracer Study | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Detail Tracer Study')
@section('header_title', 'Detail Respons Alumni')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('dashboard.admin.tracer-study.index', ['periode_id' => $tracerStudy->periode_id]) }}" class="text-sm font-bold text-[#155EAA] hover:underline">← Kembali ke daftar respons</a>
            <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Detail Respons Alumni</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $tracerStudy->periode?->nama_tampilan }} · {{ $tracerStudy->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <span class="inline-flex w-fit items-center rounded-full border px-3 py-1.5 text-xs font-bold {{ $tracerStudy->status_badge_class }}">{{ $tracerStudy->status_label }}</span>
    </div>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-rose-200 bg-rose-50 text-rose-800', 'info' => 'border-blue-200 bg-blue-50 text-blue-800'] as $flash => $flashClass)
        @if (session($flash))
            <div class="rounded-xl border p-4 text-sm {{ $flashClass }}" role="status">{{ session($flash) }}</div>
        @endif
    @endforeach
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">
            <p class="font-bold">Ada data yang perlu diperiksa:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Data alumni</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $tracerStudy->user?->name }} · {{ $tracerStudy->user?->email }}</p>
            </div>
            @if ($tracerStudy->diverifikasi_pada)
                <p class="text-xs text-slate-500">Diperiksa {{ $tracerStudy->diverifikasi_pada->format('d/m/Y H:i') }} oleh {{ $tracerStudy->verifier?->name ?? 'Petugas dihapus' }}</p>
            @endif
        </div>

        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ([
                'Nama lengkap' => $tracerStudy->nama_lengkap,
                'Tahun lulus' => $tracerStudy->tahun_lulus,
                'Jurusan' => $tracerStudy->jurusan,
                'Status kegiatan' => $tracerStudy->status_kegiatan === 'magang' ? 'Magang' : 'Bekerja',
                'Pekerjaan atau posisi' => $tracerStudy->pekerjaan,
                'Nama perusahaan' => $tracerStudy->nama_perusahaan,
                'Tahun masuk perusahaan' => $tracerStudy->tahun_masuk_perusahaan,
                'Alamat tempat tinggal' => $tracerStudy->alamat,
                'Alamat tempat kerja' => $tracerStudy->alamat_tempat_kerja,
            ] as $label => $value)
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 {{ str_contains($label, 'Alamat') ? 'sm:col-span-2' : '' }}">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</dt>
                    <dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-800">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
        <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">
            Persetujuan pemrosesan data: <strong>{{ $tracerStudy->persetujuan_penggunaan_data ? 'Diberikan' : 'Tidak tercatat' }}</strong>
            @if ($tracerStudy->disetujui_pada) <span class="text-blue-800">· {{ $tracerStudy->disetujui_pada->format('d/m/Y H:i') }}</span> @endif
        </div>
    </section>

    <section class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
        Pemeriksaan status dan permintaan perbaikan respons dilakukan oleh Guru BK melalui menu Kelola Tracer Study.
    </section>

    <section class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-extrabold text-slate-900">Histori perubahan dan status</h2>
            <div class="mt-4 space-y-3">
                @forelse ($tracerStudy->histories as $history)
                    <article class="border-l-2 border-[#155EAA] pl-4">
                        <p class="text-sm font-semibold text-slate-800">{{ $history->ringkasan_perubahan }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $history->actor?->name ?? 'Pengguna dihapus' }} · {{ $history->created_at->format('d/m/Y H:i') }}</p>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Belum ada histori.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-extrabold text-slate-900">Riwayat tindak lanjut</h2>
            <div class="mt-4 space-y-3">
                @forelse ($tracerStudy->followUps as $followUp)
                    <article class="rounded-xl border {{ $followUp->jenis === 'request_update' ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-slate-50' }} p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="text-xs font-extrabold uppercase tracking-wide text-slate-600">{{ $followUp->jenis === 'request_update' ? 'Permintaan pembaruan' : 'Catatan internal' }}</span>
                            <span class="text-xs text-slate-500">{{ $followUp->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $followUp->admin?->name ?? 'Admin dihapus' }}</p>
                        @if ($followUp->pesan_alumni)
                            <div class="mt-3 rounded-lg border border-amber-200 bg-white p-3">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-amber-800">Pesan yang diterima alumni</p>
                                <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $followUp->pesan_alumni }}</p>
                            </div>
                        @endif
                        @if ($followUp->catatan_internal)
                            <div class="mt-3 rounded-lg border border-slate-200 bg-white p-3">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Catatan privat Admin BKK</p>
                                <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $followUp->catatan_internal }}</p>
                            </div>
                        @endif
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Belum ada tindak lanjut.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
