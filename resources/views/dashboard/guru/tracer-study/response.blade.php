@extends('dashboard.layouts.dashboard')

@section('title', 'Pemeriksaan Respons Tracer Study | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Kelola Tracer Study')
@section('header_title', 'Pemeriksaan Respons Alumni')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <a href="{{ route('dashboard.guru.tracer-study.show', $periode) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[#155EAA] hover:underline">&larr; Kembali ke daftar respons periode {{ $periode->tahun }}</a>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-rose-200 bg-rose-50 text-rose-800'] as $flash => $flashClass)
        @if (session($flash))<div class="rounded-xl border p-4 text-sm {{ $flashClass }}" role="status">{{ session($flash) }}</div>@endif
    @endforeach
    @if ($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-start sm:justify-between">
            <div><p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">{{ $periode->nama_tampilan }} · {{ $periode->tahun }}</p><h1 class="mt-1 text-xl font-extrabold text-slate-900">{{ $response->nama_lengkap }}</h1><p class="mt-1 text-sm text-slate-600">{{ $response->user?->email }} · Diperbarui {{ $response->updated_at->format('d/m/Y H:i') }}</p></div>
            <span class="inline-flex w-fit items-center rounded-full border px-3 py-1.5 text-xs font-bold {{ $response->status_badge_class }}">{{ $response->status_label }}</span>
        </div>
        @if ($response->diverifikasi_pada)
            <p class="mb-4 text-xs text-slate-500">Pemeriksaan terakhir oleh {{ $response->verifier?->name ?? 'Pengguna dihapus' }} pada {{ $response->diverifikasi_pada->format('d/m/Y H:i') }}.</p>
        @endif
        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @foreach ([
                'Nama lengkap' => $response->nama_lengkap,
                'Tahun lulus' => $response->tahun_lulus,
                'Jurusan' => $response->jurusan,
                'Status kegiatan' => ucfirst($response->status_kegiatan),
                'Pekerjaan atau posisi' => $response->pekerjaan,
                'Nama perusahaan' => $response->nama_perusahaan,
                'Tahun mulai bekerja atau magang' => $response->tahun_masuk_perusahaan,
                'Alamat tempat tinggal' => $response->alamat,
                'Alamat tempat kerja atau magang' => $response->alamat_tempat_kerja,
            ] as $label => $value)
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 {{ str_contains($label, 'Alamat') ? 'sm:col-span-2' : '' }}"><dt class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</dt><dd class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-800">{{ $value }}</dd></div>
            @endforeach
        </dl>
        <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-900">Persetujuan pemrosesan data: <strong>{{ $response->persetujuan_penggunaan_data ? 'Diberikan' : 'Tidak tercatat' }}</strong>@if ($response->disetujui_pada) · {{ $response->disetujui_pada->format('d/m/Y H:i') }}@endif</div>
    </section>

    <section class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <article class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-extrabold text-slate-900">Tandai Terverifikasi</h2>
            <p class="mt-1 text-sm text-slate-600">Simpan keputusan setelah data diperiksa.</p>
            @if ($response->status_verifikasi !== \App\Models\TracerStudy::STATUS_VERIFIED)
                <form method="POST" action="{{ route('dashboard.guru.tracer-study.verify', [$periode, $response]) }}" class="mt-4 space-y-3" onsubmit="return confirm('Tandai respons ini sebagai terverifikasi?')">
                    @csrf
                    <label for="catatan-verifikasi" class="block text-xs font-bold text-slate-700">Catatan pemeriksaan (opsional)</label>
                    <textarea id="catatan-verifikasi" name="catatan" rows="3" maxlength="3000" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-[#155EAA]">{{ old('catatan') }}</textarea>
                    <button class="w-full rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-800">Tandai Terverifikasi</button>
                </form>
            @else
                <p class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm font-semibold text-emerald-800">Respons sudah terverifikasi.</p>
            @endif
        </article>

        <article class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-extrabold text-slate-900">Minta Perbaikan</h2>
            @if ($periode->isPengisianTerbuka())
                <p class="mt-1 text-sm text-slate-600">Catatan wajib diisi dan akan ditampilkan kepada Alumni.</p>
                <form method="POST" action="{{ route('dashboard.guru.tracer-study.revision', [$periode, $response]) }}" class="mt-4 space-y-3" onsubmit="return confirm('Kirim catatan perbaikan ini kepada Alumni?')">
                    @csrf
                    <label for="catatan-perbaikan" class="block text-xs font-bold text-slate-700">Bagian yang perlu diperbaiki</label>
                    <textarea id="catatan-perbaikan" name="catatan" rows="4" required maxlength="3000" class="w-full rounded-lg border border-amber-200 px-3 py-2.5 text-sm outline-none focus:border-[#155EAA]">{{ old('catatan') }}</textarea>
                    <button class="w-full rounded-xl bg-amber-500 px-4 py-3 text-sm font-bold text-[#0F2F5F] hover:bg-amber-400">Kirim Permintaan Perbaikan</button>
                </form>
            @else
                <p class="mt-3 rounded-lg bg-slate-50 p-3 text-sm text-slate-600">Periode pengisian sudah ditutup; Alumni tidak dapat memperbarui respons.</p>
            @endif
        </article>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-lg font-extrabold text-slate-900">Riwayat Pemeriksaan dan Perubahan</h2>
        <div class="mt-4 space-y-3">
            @forelse ($response->histories as $history)
                <article class="border-l-2 border-[#155EAA] pl-4"><p class="text-sm font-semibold text-slate-800">{{ $history->ringkasan_perubahan }}</p><p class="mt-1 text-xs text-slate-500">{{ $history->actor?->name ?? 'Pengguna dihapus' }} · {{ $history->created_at->format('d/m/Y H:i') }}</p></article>
            @empty
                <p class="text-sm text-slate-500">Belum ada histori pemeriksaan.</p>
            @endforelse
        </div>
        @if ($response->followUps->isNotEmpty())
            <h3 class="mt-6 font-bold text-slate-800">Catatan perbaikan sebelumnya</h3>
            <div class="mt-3 space-y-3">
                @foreach ($response->followUps->where('jenis', 'request_update') as $followUp)
                    <article class="rounded-xl border border-amber-200 bg-amber-50 p-4"><p class="whitespace-pre-line text-sm text-slate-700">{{ $followUp->pesan_alumni }}</p><p class="mt-2 text-xs text-slate-500">{{ $followUp->admin?->name ?? 'Pengguna dihapus' }} · {{ ($followUp->diberitahukan_pada ?? $followUp->created_at)->format('d/m/Y H:i') }}</p></article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
