<div id="tracer-study-table-content">
    <form id="tracer-filters" method="GET" action="{{ $tableRoute }}{{ request()->routeIs('dashboard.guru.tracer-study.statistics') ? '' : '#data-alumni-tracer-study' }}" data-tracer-filter-form class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <input type="hidden" name="per_page" value="{{ $perPage }}">
        <label class="text-xs font-bold text-slate-600">Tahun pencatatan
            <select name="tahun_pencatatan" data-tracer-filter class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal text-slate-700">
                <option value="all" @selected($selectedYear === 'all')>Semua Tahun</option>
                @foreach ($years as $year)<option value="{{ $year }}" @selected($selectedYear === (string) $year)>{{ $year }}</option>@endforeach
            </select>
        </label>
        <label class="text-xs font-bold text-slate-600">Program keahlian
            <select name="jurusan" data-tracer-filter class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal text-slate-700">
                <option value="">Semua Program Keahlian</option>
                @foreach ($programOptions as $option)<option value="{{ $option }}" @selected($selectedMajor === $option)>{{ $option }}</option>@endforeach
            </select>
        </label>
        <label class="text-xs font-bold text-slate-600">Status setelah lulus
            <select name="status" data-tracer-filter class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm font-normal text-slate-700">
                <option value="">Semua Status</option>
                @foreach ($statusOptions as $option)<option value="{{ $option }}" @selected($selectedStatus === $option)>{{ $option }}</option>@endforeach
            </select>
        </label>
        <label class="text-xs font-bold text-slate-600">Cari nama, kabupaten/kota, atau instansi
            <div class="mt-1 flex gap-2"><input name="q" value="{{ $search }}" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-normal" placeholder="Ketik kata pencarian"><button class="rounded-lg bg-[#155EAA] px-4 text-sm font-bold text-white hover:bg-[#0F2F5F]">Cari</button></div>
        </label>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200">
        <table class="min-w-[1050px] w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr>
                <th class="px-3 py-3">No.</th><th class="px-3 py-3">Kabupaten/Kota</th><th class="px-3 py-3">Jenjang</th><th class="px-3 py-3">Nama Sekolah</th><th class="px-3 py-3">Nama Alumni</th><th class="px-3 py-3">Program Keahlian</th><th class="px-3 py-3">Status Setelah Lulus</th><th class="px-3 py-3">Nama Instansi</th><th class="px-3 py-3 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($records as $record)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-3 py-3 text-slate-500">{{ $records->firstItem() + $loop->index }}</td><td class="px-3 py-3">{{ $record->kabupaten }}</td><td class="px-3 py-3">{{ $record->jenjang }}</td><td class="px-3 py-3">{{ $record->sekolah }}</td><td class="px-3 py-3 font-semibold text-slate-800">{{ $record->nama }}</td><td class="px-3 py-3">{{ $record->jurusan }}</td><td class="px-3 py-3">{{ $record->status }}</td><td class="px-3 py-3">{{ $record->instansi }}</td>
                        <td class="px-3 py-3 text-center"><button type="button" onclick="document.getElementById('alumni-detail-{{ $record->id }}').showModal()" class="inline-flex rounded-lg p-2 text-[#155EAA] hover:bg-blue-50" aria-label="Lihat detail {{ $record->nama }}"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6-9.75-6-9.75-6Z"/><circle cx="12" cy="12" r="3" stroke-width="2"/></svg></button></td>
                    </tr>
                    <dialog id="alumni-detail-{{ $record->id }}" class="m-auto w-[calc(100%-2rem)] max-w-2xl rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/50">
                        <div class="flex items-center justify-between bg-[#0F2F5F] px-5 py-4 text-white"><div><h3 class="font-extrabold">Detail Alumni</h3><p class="text-xs text-blue-100">Tahun pencatatan {{ $record->tahun ?? '—' }}</p></div><button type="button" onclick="this.closest('dialog').close()" class="rounded-lg px-3 py-1 text-xl hover:bg-white/10" aria-label="Tutup">&times;</button></div>
                        <div class="grid gap-5 p-5 sm:grid-cols-2">
                            <div><h4 class="mb-3 text-sm font-extrabold text-[#0F2F5F]">Identitas Alumni</h4><dl class="space-y-2 text-sm">
                                @foreach (['Nomor data' => $record->nomor, 'Kabupaten/Kota' => $record->kabupaten, 'Jenjang' => $record->jenjang, 'Nama Sekolah' => $record->sekolah, 'Nama Alumni' => $record->nama, 'Program Keahlian' => $record->jurusan] as $label => $value)<div class="grid grid-cols-[8rem_1fr] gap-2"><dt class="text-slate-500">{{ $label }}</dt><dd class="break-words font-medium text-slate-800">{{ $value ?: '—' }}</dd></div>@endforeach
                            </dl></div>
                            <div><h4 class="mb-3 text-sm font-extrabold text-[#0F2F5F]">Informasi Tracer Study</h4><dl class="space-y-2 text-sm">
                                @foreach (['Tahun pencatatan' => $record->tahun, 'Status Setelah Lulus' => $record->status, 'Nama Instansi/Perusahaan' => $record->instansi, 'Posisi atau bidang pekerjaan' => $record->posisi, 'Lokasi penempatan' => $record->penempatan] as $label => $value)<div class="grid grid-cols-[8rem_1fr] gap-2"><dt class="text-slate-500">{{ $label }}</dt><dd class="break-words font-medium text-slate-800">{{ $value ?: '—' }}</dd></div>@endforeach
                            </dl></div>
                        </div>
                    </dialog>
                @empty
                    <tr><td colspan="9" class="px-4 py-10 text-center text-slate-500">Tidak ada data alumni yang sesuai dengan filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-slate-600">Menampilkan {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} dari {{ number_format($records->total()) }} data</p>
        <form method="GET" action="{{ $tableRoute }}{{ request()->routeIs('dashboard.guru.tracer-study.statistics') ? '' : '#data-alumni-tracer-study' }}" data-tracer-per-page-form class="flex items-center gap-2 text-sm text-slate-600">
            <input type="hidden" name="tahun_pencatatan" value="{{ $selectedYear }}"><input type="hidden" name="jurusan" value="{{ $selectedMajor }}"><input type="hidden" name="status" value="{{ $selectedStatus }}"><input type="hidden" name="q" value="{{ $search }}">
            <label for="per_page">Data per halaman</label><select id="per_page" name="per_page" data-tracer-filter class="rounded-lg border border-slate-200 bg-white px-2 py-2">
                @foreach ([10, 25, 50, 100] as $option)<option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>@endforeach
            </select>
            <button type="submit" class="rounded-lg border border-slate-200 px-2 py-2">Terapkan</button>
        </form>
    </div>
    <div class="mt-3" data-tracer-pagination>{{ $records->onEachSide(1)->links() }}</div>
</div>
