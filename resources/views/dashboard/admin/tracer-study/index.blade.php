@extends('dashboard.layouts.dashboard')

@section('title', 'Kelola Tracer Study | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Kelola Tracer Study')
@section('header_title', 'Pengelolaan Tracer Study')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Admin BKK</p>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Kelola Tracer Study</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola periode, pantau respons, dan tindak lanjuti data alumni secara terpusat.</p>
        </div>
        @if ($selectedPeriod)
            <a href="{{ route('dashboard.admin.tracer-study.export', array_merge(request()->query(), ['periode_id' => $selectedPeriod->id])) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#166534] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-green-800">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z"/></svg>
                Export XLSX
            </a>
        @endif
    </div>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-rose-200 bg-rose-50 text-rose-800', 'info' => 'border-blue-200 bg-blue-50 text-blue-800'] as $flash => $flashClass)
        @if (session($flash))
            <div class="rounded-xl border p-4 text-sm {{ $flashClass }}" role="status">{{ session($flash) }}</div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">
            <p class="font-bold">Ada data yang perlu diperiksa:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4">
            <h2 class="text-lg font-extrabold text-slate-900">Periode Tahunan</h2>
            <p class="mt-1 text-sm text-slate-600">Periode baru berstatus draf. Membuka periode tidak melewati tanggal mulai dan akhir yang ditentukan.</p>
        </div>
        <form method="POST" action="{{ route('dashboard.admin.tracer-study.periode.store') }}" class="grid grid-cols-1 gap-4 rounded-xl bg-slate-50 p-4 md:grid-cols-4">
            @csrf
            <div>
                <label for="periode_nama" class="mb-1 block text-xs font-bold text-slate-700">Nama periode (opsional)</label>
                <input id="periode_nama" name="nama" type="text" maxlength="120" value="{{ old('nama') }}" placeholder="Tracer Study Tahunan"
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label for="periode_tahun" class="mb-1 block text-xs font-bold text-slate-700">Tahun</label>
                <input id="periode_tahun" name="tahun" type="number" min="1950" max="{{ now()->year + 1 }}" required value="{{ old('tahun', now()->year) }}"
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label for="periode_mulai" class="mb-1 block text-xs font-bold text-slate-700">Tanggal mulai</label>
                <input id="periode_mulai" name="tanggal_mulai" type="date" required value="{{ old('tanggal_mulai') }}"
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label for="periode_akhir" class="mb-1 block text-xs font-bold text-slate-700">Tanggal akhir</label>
                <input id="periode_akhir" name="tanggal_berakhir" type="date" required value="{{ old('tanggal_berakhir') }}"
                       class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
            </div>
            <div class="md:col-span-4 md:flex md:justify-end">
                <button type="submit" class="w-full rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#0F2F5F] md:w-auto">Buat Periode Draf</button>
            </div>
        </form>

        @if ($periods->isEmpty())
            <p class="mt-4 rounded-xl border border-dashed border-slate-300 p-5 text-center text-sm text-slate-500">Belum ada periode Tracer Study.</p>
        @else
            <div class="mt-4 space-y-3">
                @foreach ($periods as $periode)
                    <article class="rounded-xl border border-slate-200 p-4">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                            <div class="flex flex-wrap items-center gap-3">
                                <div>
                                    <h3 class="font-bold text-slate-900">{{ $periode->nama_tampilan }}</h3>
                                    <p class="mt-1 text-xs text-slate-500">{{ $periode->tanggal_mulai->format('d/m/Y') }} – {{ $periode->tanggal_berakhir->format('d/m/Y') }}</p>
                                </div>
                                <span class="inline-flex rounded-full border px-3 py-1 text-xs font-bold {{ $periode->status_badge_class }}">{{ $periode->status_label }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if ($periode->status !== \App\Models\TracerStudyPeriode::STATUS_CLOSED)
                                    <details class="group">
                                        <summary class="cursor-pointer list-none rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Ubah informasi</summary>
                                        <form method="POST" action="{{ route('dashboard.admin.tracer-study.periode.update', $periode) }}" class="mt-2 grid w-full gap-2 rounded-lg border border-slate-200 bg-slate-50 p-3 sm:grid-cols-3 xl:absolute xl:z-10 xl:w-[560px]">
                                            @csrf
                                            @method('PUT')
                                            <input name="nama" type="text" maxlength="120" value="{{ $periode->nama }}" placeholder="Nama periode"
                                                   class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs outline-none focus:border-[#155EAA]">
                                            <input name="tanggal_mulai" type="date" required value="{{ $periode->tanggal_mulai->format('Y-m-d') }}"
                                                   class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs outline-none focus:border-[#155EAA]">
                                            <input name="tanggal_berakhir" type="date" required value="{{ $periode->tanggal_berakhir->format('Y-m-d') }}"
                                                   class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs outline-none focus:border-[#155EAA]">
                                            <button class="rounded-lg bg-[#155EAA] px-3 py-2 text-xs font-bold text-white sm:col-span-3">Simpan informasi</button>
                                        </form>
                                    </details>
                                    @if ($periode->status === \App\Models\TracerStudyPeriode::STATUS_DRAFT)
                                        <form method="POST" action="{{ route('dashboard.admin.tracer-study.periode.open', $periode) }}" onsubmit="return confirm('Buka periode ini? Periode lain harus ditutup terlebih dahulu.')">
                                            @csrf
                                            <button class="rounded-lg bg-[#166534] px-3 py-2 text-xs font-bold text-white hover:bg-green-800">Buka periode</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('dashboard.admin.tracer-study.periode.close', $periode) }}" onsubmit="return confirm('Tutup periode ini? Respons alumni akan menjadi read-only.')">
                                            @csrf
                                            <button class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100">Tutup periode</button>
                                        </form>
                                    @endif
                                @else
                                    <span class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-500">Riwayat dipertahankan</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Ringkasan Periode</h2>
                <p class="mt-1 text-sm text-slate-600">Statistik agregat mengikuti periode yang dipilih.</p>
            </div>
            <form method="GET" action="{{ route('dashboard.admin.tracer-study.index') }}" class="flex flex-col gap-2 sm:flex-row">
                <select name="periode_id" aria-label="Pilih periode statistik" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm" onchange="this.form.submit()">
                    @foreach ($periods as $periode)
                        <option value="{{ $periode->id }}" @selected($selectedPeriod?->id === $periode->id)>{{ $periode->nama_tampilan }}</option>
                    @endforeach
                </select>
                @if ($periods->isEmpty()) <span class="text-sm text-slate-500">Buat periode untuk melihat statistik.</span> @endif
            </form>
        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 xl:grid-cols-6">
            @foreach ([['Total respons', $stats['total_respons'], 'text-[#155EAA]'], ['Alumni unik', $stats['alumni_unik'], 'text-[#0F2F5F]'], ['Bekerja', $stats['bekerja'], 'text-[#166534]'], ['Magang', $stats['magang'], 'text-amber-600'], ['Belum diverifikasi', $stats['belum_diverifikasi'], 'text-slate-700'], ['Perlu pembaruan', $stats['perlu_pembaruan'], 'text-rose-700']] as [$label, $value, $tone])
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-2xl font-extrabold {{ $tone }}">{{ number_format($value) }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 p-4">
                <h3 class="mb-3 font-bold text-slate-900">Rekap tahun lulusan</h3>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[320px] text-left text-sm">
                        <thead class="border-b border-slate-200 text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Tahun</th><th class="py-2 pr-3">Respons</th><th class="py-2">Alumni unik</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($rekapTahunLulus as $row)
                                <tr><td class="py-2.5 pr-3 font-semibold text-slate-800">{{ $row->tahun_lulus }}</td><td class="py-2.5 pr-3 text-slate-600">{{ $row->total_respons }}</td><td class="py-2.5 text-slate-600">{{ $row->alumni_unik }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="py-4 text-center text-slate-500">Belum ada respons pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <h3 class="mb-3 font-bold text-slate-900">Rekap jurusan</h3>
                <div class="max-h-72 overflow-auto">
                    <table class="w-full min-w-[320px] text-left text-sm">
                        <thead class="sticky top-0 border-b border-slate-200 bg-white text-xs uppercase text-slate-500"><tr><th class="py-2 pr-3">Jurusan</th><th class="py-2 pr-3">Respons</th><th class="py-2">Alumni unik</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($rekapJurusan as $row)
                                <tr><td class="py-2.5 pr-3 font-semibold text-slate-800">{{ $row->jurusan }}</td><td class="py-2.5 pr-3 text-slate-600">{{ $row->total_respons }}</td><td class="py-2.5 text-slate-600">{{ $row->alumni_unik }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="py-4 text-center text-slate-500">Belum ada respons pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5">
            <h2 class="text-lg font-extrabold text-slate-900">Respons Alumni</h2>
            <p class="mt-1 text-sm text-slate-600">Data respons dapat diperiksa sesuai kewenangan masing-masing role.</p>
        </div>
        <form method="GET" action="{{ route('dashboard.admin.tracer-study.index') }}" class="mb-5 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <input type="hidden" name="periode_id" value="{{ $selectedPeriod?->id }}">
            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama/perusahaan" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-[#155EAA]">
            <select name="tahun_lulus" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"><option value="">Semua tahun lulus</option>@foreach ($rekapTahunLulus as $row)<option value="{{ $row->tahun_lulus }}" @selected(($filters['tahun_lulus'] ?? '') == $row->tahun_lulus)>{{ $row->tahun_lulus }}</option>@endforeach</select>
            <select name="jurusan" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"><option value="">Semua jurusan</option>@foreach ($rekapJurusan as $row)<option value="{{ $row->jurusan }}" @selected(($filters['jurusan'] ?? '') === $row->jurusan)>{{ $row->jurusan }}</option>@endforeach</select>
            <select name="status_kegiatan" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"><option value="">Semua kegiatan</option><option value="bekerja" @selected(($filters['status_kegiatan'] ?? '') === 'bekerja')>Bekerja</option><option value="magang" @selected(($filters['status_kegiatan'] ?? '') === 'magang')>Magang</option></select>
            <select name="status_verifikasi" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"><option value="">Semua status</option><option value="unverified" @selected(($filters['status_verifikasi'] ?? '') === 'unverified')>Belum diverifikasi</option><option value="verified" @selected(($filters['status_verifikasi'] ?? '') === 'verified')>Terverifikasi</option><option value="needs_update" @selected(($filters['status_verifikasi'] ?? '') === 'needs_update')>Perlu perbaikan</option><option value="follow_up" @selected(($filters['status_verifikasi'] ?? '') === 'follow_up')>Dalam tindak lanjut</option></select>
            <button class="rounded-lg bg-[#155EAA] px-4 py-2 text-sm font-bold text-white hover:bg-[#0F2F5F]">Terapkan filter</button>
        </form>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Alumni</th><th class="px-4 py-3">Lulus / Jurusan</th><th class="px-4 py-3">Kegiatan</th><th class="px-4 py-3">Perusahaan</th><th class="px-4 py-3">Periode</th><th class="px-4 py-3">Pembaruan</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($responses as $response)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3"><p class="font-bold text-slate-900">{{ $response->nama_lengkap }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $response->user?->email }}</p></td>
                            <td class="px-4 py-3"><p class="font-semibold text-slate-800">{{ $response->tahun_lulus }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $response->jurusan }}</p></td>
                            <td class="px-4 py-3 text-slate-700">{{ $response->status_kegiatan === 'magang' ? 'Magang' : 'Bekerja' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $response->nama_perusahaan }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $response->periode?->nama_tampilan ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $response->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3"><span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold {{ $response->status_badge_class }}">{{ $response->status_label }}</span></td>
                            <td class="px-4 py-3"><a href="{{ route('dashboard.admin.tracer-study.show', $response) }}" class="font-bold text-[#155EAA] hover:underline">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">Tidak ada respons yang cocok dengan periode dan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $responses->links() }}</div>
    </section>
</div>
@endsection
