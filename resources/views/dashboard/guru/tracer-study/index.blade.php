@extends('dashboard.layouts.dashboard')

@section('title', 'Kelola Tracer Study | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Kelola Tracer Study')
@section('header_title', 'Kelola Tracer Study')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <header>
        <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Guru BK</p>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Kelola Periode Tracer Study</h1>
        <p class="mt-1 text-sm text-slate-600">Pengelolaan periode pendataan alumni SMKN 7 Jember untuk memantau perkembangan lulusan.</p>
    </header>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-rose-200 bg-rose-50 text-rose-800', 'info' => 'border-blue-200 bg-blue-50 text-blue-800'] as $flash => $flashClass)
        @if (session($flash))<div class="rounded-xl border p-4 text-sm {{ $flashClass }}" role="status">{{ session($flash) }}</div>@endif
    @endforeach
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">
            <p class="font-bold">Ada data yang perlu diperiksa:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section aria-label="Ringkasan statistik" class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([['Total Periode', $stats['total_periode'], 'text-[#155EAA]'], ['Periode Aktif', $stats['periode_aktif'], 'text-emerald-700'], ['Total Respons Alumni', $stats['total_respons'], 'text-[#0F2F5F]'], ['Periode Ditutup', $stats['periode_ditutup'], 'text-slate-700']] as [$label, $value, $tone])
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-2xl font-extrabold {{ $tone }}">{{ number_format($value) }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Informasi Periode Aktif</h2>
                <p class="mt-1 text-sm text-slate-600">Periode yang dibuka akan digunakan untuk pengisian Tracer Study Alumni.</p>
            </div>
        </div>
        @if ($activePeriod)
            <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 sm:p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg font-bold text-slate-900">{{ $activePeriod->nama_tampilan }}</h3>
                            <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $activePeriod->status_badge_class }}">{{ $activePeriod->status_label }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600">Tahun pendataan {{ $activePeriod->tahun }}</p>
                    </div>
                    <a href="{{ route('dashboard.guru.tracer-study.show', $activePeriod) }}" class="inline-flex justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-[#155EAA] hover:bg-slate-50">Lihat Detail</a>
                </div>
                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
                    <div><dt class="text-xs text-slate-500">Tanggal mulai</dt><dd class="mt-1 font-semibold text-slate-800">{{ $activePeriod->tanggal_mulai->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Batas akhir pengisian</dt><dd class="mt-1 font-semibold text-slate-800">{{ $activePeriod->tanggal_berakhir->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Alumni sudah mengisi</dt><dd class="mt-1 font-semibold text-slate-800">{{ number_format($activePeriod->tracer_studies_count) }} dari {{ number_format($alumniCount) }}</dd></div>
                    <div><dt class="text-xs text-slate-500">Persentase respons</dt><dd class="mt-1 font-semibold text-slate-800">{{ $responseRate !== null ? number_format($responseRate, 1) . '%' : 'Belum tersedia' }}</dd></div>
                </dl>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                <h3 class="font-bold text-slate-800">Belum ada periode aktif</h3>
                <p class="mt-1 text-sm text-slate-600">Buat periode Tracer Study, lalu aktifkan saat pengisian alumni siap dimulai.</p>
                <a href="#buat-periode" class="mt-4 inline-flex rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#0F2F5F]">Buat Periode Tracer Study</a>
            </div>
        @endif
    </section>

    <section id="buat-periode" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-lg font-extrabold text-slate-900">Buat Periode Tracer Study</h2>
            <p class="mt-1 text-sm text-slate-600">Periode baru dibuat sebagai draf dan dapat diaktifkan setelah diperiksa.</p>
        </div>
        <form method="POST" action="{{ route('dashboard.guru.tracer-study.periode.store') }}" class="grid grid-cols-1 gap-4 rounded-xl bg-slate-50 p-4 md:grid-cols-4">
            @csrf
            <div><label for="guru_periode_nama" class="mb-1 block text-xs font-bold text-slate-700">Nama Periode</label><input id="guru_periode_nama" name="nama" type="text" maxlength="120" value="{{ old('nama') }}" placeholder="Tracer Study Tahunan" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100"></div>
            <div><label for="guru_periode_tahun" class="mb-1 block text-xs font-bold text-slate-700">Tahun Pendataan</label><input id="guru_periode_tahun" name="tahun" type="number" min="1950" max="{{ now()->year + 1 }}" required value="{{ old('tahun', now()->year) }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100"></div>
            <div><label for="guru_periode_mulai" class="mb-1 block text-xs font-bold text-slate-700">Tanggal Mulai</label><input id="guru_periode_mulai" name="tanggal_mulai" type="date" required value="{{ old('tanggal_mulai') }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100"></div>
            <div><label for="guru_periode_akhir" class="mb-1 block text-xs font-bold text-slate-700">Tanggal Akhir</label><input id="guru_periode_akhir" name="tanggal_berakhir" type="date" required value="{{ old('tanggal_berakhir') }}" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100"></div>
            <div class="md:col-span-4"><label for="guru_periode_keterangan" class="mb-1 block text-xs font-bold text-slate-700">Deskripsi atau Keterangan (opsional)</label><textarea id="guru_periode_keterangan" name="keterangan" rows="2" maxlength="5000" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">{{ old('keterangan') }}</textarea></div>
            <div class="md:col-span-4 md:flex md:justify-end"><button type="submit" class="w-full rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#0F2F5F] md:w-auto">Buat Periode Draf</button></div>
        </form>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4"><h2 class="text-lg font-extrabold text-slate-900">Daftar Periode Tracer Study</h2><p class="mt-1 text-sm text-slate-600">Riwayat respons tetap tersimpan ketika periode ditutup.</p></div>
        @if ($periods->isEmpty())
            <p class="rounded-xl border border-dashed border-slate-300 p-5 text-center text-sm text-slate-500">Belum ada periode Tracer Study.</p>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full min-w-[850px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-3 py-3">No.</th><th class="px-3 py-3">Nama Periode</th><th class="px-3 py-3">Tahun</th><th class="px-3 py-3">Tanggal Mulai</th><th class="px-3 py-3">Batas Pengisian</th><th class="px-3 py-3">Jumlah Respons</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($periods as $periode)
                            <tr class="align-top">
                                <td class="px-3 py-3 text-slate-600">{{ $loop->iteration }}</td><td class="px-3 py-3 font-semibold text-slate-800">{{ $periode->nama_tampilan }}</td><td class="px-3 py-3 text-slate-600">{{ $periode->tahun }}</td><td class="px-3 py-3 whitespace-nowrap text-slate-600">{{ $periode->tanggal_mulai->format('d/m/Y') }}</td><td class="px-3 py-3 whitespace-nowrap text-slate-600">{{ $periode->tanggal_berakhir->format('d/m/Y') }}</td><td class="px-3 py-3 text-slate-600">{{ number_format($periode->tracer_studies_count) }}</td><td class="px-3 py-3"><span class="inline-flex whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-bold {{ $periode->status_badge_class }}">{{ $periode->status_label }}</span></td>
                                <td class="px-3 py-3"><div class="flex min-w-[150px] flex-wrap gap-2">
                                    <a href="{{ route('dashboard.guru.tracer-study.show', $periode) }}" class="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-[#155EAA] hover:bg-slate-50">Lihat Detail</a>
                                    @if ($periode->status !== \App\Models\TracerStudyPeriode::STATUS_CLOSED)
                                        <details class="relative"><summary class="cursor-pointer list-none rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Edit Periode</summary>
                                            <form method="POST" action="{{ route('dashboard.guru.tracer-study.periode.update', $periode) }}" class="absolute right-0 z-10 mt-2 grid w-72 gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-lg">@csrf @method('PUT')
                                                <label class="text-xs font-semibold text-slate-600">Nama Periode<input name="nama" type="text" maxlength="120" value="{{ $periode->nama }}" class="mt-1 w-full rounded-lg border border-slate-200 px-2.5 py-2 text-xs"></label>
                                                <label class="text-xs font-semibold text-slate-600">Keterangan (opsional)<textarea name="keterangan" rows="3" maxlength="5000" class="mt-1 w-full rounded-lg border border-slate-200 px-2.5 py-2 text-xs">{{ $periode->keterangan }}</textarea></label>
                                                <label class="text-xs font-semibold text-slate-600">Tanggal Mulai<input name="tanggal_mulai" type="date" required value="{{ $periode->tanggal_mulai->format('Y-m-d') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-2.5 py-2 text-xs"></label>
                                                <label class="text-xs font-semibold text-slate-600">Tanggal Akhir<input name="tanggal_berakhir" type="date" required value="{{ $periode->tanggal_berakhir->format('Y-m-d') }}" class="mt-1 w-full rounded-lg border border-slate-200 px-2.5 py-2 text-xs"></label>
                                                <button class="rounded-lg bg-[#155EAA] px-3 py-2 text-xs font-bold text-white">Simpan Perubahan</button>
                                            </form>
                                        </details>
                                        @if ($periode->status === \App\Models\TracerStudyPeriode::STATUS_DRAFT)
                                            <form method="POST" action="{{ route('dashboard.guru.tracer-study.periode.open', $periode) }}" onsubmit="return confirm('Aktifkan periode ini? Tutup periode lain terlebih dahulu jika masih terbuka.')">@csrf<button class="rounded-lg bg-emerald-700 px-2.5 py-1.5 text-xs font-bold text-white hover:bg-emerald-800">Aktifkan</button></form>
                                        @else
                                            <form method="POST" action="{{ route('dashboard.guru.tracer-study.periode.close', $periode) }}" onsubmit="return confirm('Tutup periode ini? Alumni tidak dapat mengirim atau mengubah respons periode ini.')">@csrf<button class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 hover:bg-rose-100">Tutup Periode</button></form>
                                        @endif
                                    @endif
                                </div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
