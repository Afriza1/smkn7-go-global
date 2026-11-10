@extends('dashboard.layouts.dashboard')

@section('title', 'Panduan Dokumen Keberangkatan | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Panduan Dokumen Keberangkatan')
@section('header_title', 'Panduan Dokumen Keberangkatan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0F2F5F] p-6 text-white shadow-lg sm:p-8">
        <div class="absolute -right-10 -bottom-10 h-48 w-48 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-[#FBBF24]">
                <span class="h-2 w-2 rounded-full bg-[#FBBF24]"></span>
                Persiapan Dokumen Pribadi
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Panduan Dokumen Keberangkatan</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-200">Pilih jalur rencana Anda untuk melihat panduan dan memantau status persiapan. Checklist ini hanya mencatat progres pribadi, bukan pemeriksaan atau pengesahan dokumen.</p>
        </div>
    </section>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-rose-200 bg-rose-50 text-rose-800', 'info' => 'border-blue-200 bg-blue-50 text-blue-800'] as $flash => $flashClass)
        @if (session($flash))
            <div class="rounded-xl border p-4 text-sm {{ $flashClass }}" role="status">{{ session($flash) }}</div>
        @endif
    @endforeach
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <section class="space-y-5 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="panduan-pinjaman-title">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Informasi untuk siswa</p>
            <h2 id="panduan-pinjaman-title" class="mt-1 text-xl font-extrabold text-slate-900">Panduan Pinjaman Biaya Keberangkatan LPK Kanzo</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                Pengajuan dilakukan melalui bank mitra yang bekerja sama dengan LPK Kanzo di
                <a href="https://edu.dayainspirasi.id" target="_blank" rel="noopener noreferrer" class="font-bold text-[#155EAA] underline underline-offset-2 hover:text-[#0F2F5F]">edu.dayainspirasi.id</a>.
                Panduan ini membantu Anda menyiapkan informasi dan dokumen sebelum mengajukan.
            </p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-950">
                <p class="font-extrabold">Uang saku maksimal Rp5.000.000</p>
                <p class="mt-1 leading-relaxed">Dana ini dapat diajukan sebagai uang pegangan selama di Jepang. Pencairan uang saku dilakukan H-1 keberangkatan.</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900" role="note">
                <p class="font-extrabold">Perhatikan ketentuan pembatalan</p>
                <p class="mt-1 leading-relaxed">Jika pengajuan dibatalkan setelah Anda melakukan input, dikenakan penalti administrasi Rp500.000 dengan alasan apa pun.</p>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <h3 class="font-extrabold text-slate-900">Dokumen yang harus disiapkan</h3>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">Checklist ini hanya untuk membantu persiapan. Dokumen tidak diunggah atau disimpan pada halaman ini.</p>
                <ol class="mt-3 space-y-2.5">
                    @foreach ([
                        'Foto KTP siswa.',
                        'Foto selfie siswa sambil memegang KTP.',
                        'Foto KTP ayah.',
                        'Foto KTP ibu.',
                        'Foto KTP keluarga yang tidak serumah.',
                        'Foto Kartu Keluarga.',
                        'Bukti kepemilikan rumah yang ditinggali orang tua: sertifikat atau akta jual beli dalam format PDF seluruh halaman, atau foto PBB.',
                        'Copy kontrak kerja seluruh halaman dalam satu file PDF.',
                        'Copy invoice atau kwitansi tagihan dari SO dalam format JPG atau PDF.',
                        'Slip gaji atau foto usaha orang tua/keluarga yang tidak serumah (pilih salah satu, format JPG atau PDF).',
                    ] as $item)
                        <li class="flex gap-3 text-sm leading-relaxed text-slate-700">
                            <span aria-hidden="true" class="mt-0.5 h-5 w-5 shrink-0 rounded border border-slate-300 bg-white"></span>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="space-y-5">
                <div>
                    <h3 class="font-extrabold text-slate-900">Data tambahan yang harus disiapkan</h3>
                    <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm leading-relaxed text-slate-700">
                        <li>NPWP siswa atau orang tua. Jika tidak memiliki NPWP, isi dengan angka 0 sebanyak 16 digit.</li>
                        <li>KTP pasangan jika sudah menikah.</li>
                        <li>Link Google Maps alamat orang tua.</li>
                        <li>Nama kumiai tempat bekerja di Jepang.</li>
                    </ol>
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <h3 class="font-extrabold text-amber-950">Penjamin dan verifikasi</h3>
                    <ul class="mt-2 list-disc space-y-2 pl-5 text-sm leading-relaxed text-amber-950">
                        <li>Kedua orang tua, keluarga yang tidak serumah, serta pasangan jika sudah menikah harus mengetahui pengajuan dan bersedia menjadi penjamin pembayaran.</li>
                        <li>Pastikan nomor HP semua pihak dapat dihubungi melalui panggilan telepon biasa, baik prabayar maupun pascabayar.</li>
                        <li>Verifikasi tidak menggunakan WhatsApp Call.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <a href="https://edu.dayainspirasi.id" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#155EAA] px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#0F2F5F] focus:outline-none focus:ring-2 focus:ring-[#155EAA] focus:ring-offset-2">
                Ajukan Pinjaman
                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17 17 7M7 7h10v10"/></svg>
            </a>
        </div>
    </section>

    @if ($jalurs->isEmpty())
        <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm">
            <h2 class="font-extrabold text-slate-900">Panduan belum tersedia</h2>
            <p class="mt-2 text-sm text-slate-600">Belum ada jalur keberangkatan yang dipublikasikan oleh Admin BKK.</p>
        </section>
    @else
        <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="font-extrabold text-slate-900">Pilih jalur keberangkatan</h2>
                    <p class="mt-1 text-sm text-slate-600">Dokumen dan progres disimpan secara terpisah untuk setiap jalur.</p>
                </div>
                <form method="GET" action="{{ route('dashboard.panduan-dokumen.index') }}" class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                    <label for="jalur_id" class="sr-only">Jalur keberangkatan</label>
                    <select id="jalur_id" name="jalur_id" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm sm:min-w-72">
                        @foreach ($jalurs as $jalur)
                            <option value="{{ $jalur->id }}" @selected($selectedJalur?->id === $jalur->id)>{{ $jalur->nama }}</option>
                        @endforeach
                    </select>
                    <button class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#0F2F5F]">Tampilkan</button>
                </form>
            </div>
        </section>

        @if ($selectedJalur)
            @if ($konfigurasiBelumLengkap)
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900">
                    Admin BKK masih meninjau penerapan sebagian dokumen pada jalur ini. Hanya dokumen yang sudah ditandai wajib atau opsional yang tampil. Daftar referensi tidak otomatis menjadi persyaratan.
                </div>
            @endif

            <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Progres Dokumen Wajib</p>
                        <h2 class="mt-1 text-xl font-extrabold text-slate-900">{{ $selectedJalur->nama }}</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $siapWajib }} dari {{ $totalWajib }} dokumen wajib berstatus siap.</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-3xl font-extrabold text-[#155EAA]">{{ $progres }}%</p>
                        <p class="text-xs font-semibold text-slate-500">{{ $totalWajib > 0 ? 'kelengkapan checklist wajib' : 'belum ada dokumen wajib ditetapkan' }}</p>
                    </div>
                </div>
                <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-100" role="img" aria-label="Progres dokumen wajib {{ $progres }} persen">
                    <div class="h-full rounded-full bg-[#166534] transition-all" style="width: {{ $progres }}%"></div>
                </div>
            </section>

            @if ($dokumens->isEmpty())
                <section class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm">
                    <h2 class="font-extrabold text-slate-900">Dokumen untuk jalur ini belum dikonfigurasi</h2>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">Admin BKK akan menentukan dokumen yang wajib, opsional, atau tidak berlaku pada jalur ini. Tidak ada persyaratan yang diasumsikan otomatis.</p>
                </section>
            @else
                @foreach ($dokumens->groupBy('kategori.nama') as $kategori => $daftarDokumen)
                    <section class="space-y-4" aria-labelledby="kategori-dokumen-{{ $loop->index }}">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Kategori Dokumen</p>
                            <h2 id="kategori-dokumen-{{ $loop->index }}" class="mt-1 text-lg font-extrabold text-slate-900">{{ $kategori ?: 'Kategori lainnya' }}</h2>
                        </div>
                        <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                            @foreach ($daftarDokumen as $dokumen)
                                @php $statusSaatIni = $statuses[$dokumen->id] ?? \App\Models\ChecklistDokumenKeberangkatan::STATUS_BELUM_DISIAPKAN; @endphp
                                <article class="flex h-full flex-col rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="inline-flex rounded-full border px-3 py-1 text-[11px] font-bold {{ $dokumen->pivot->penerapan === 'required' ? 'border-blue-100 bg-blue-50 text-[#155EAA]' : 'border-amber-200 bg-amber-50 text-amber-800' }}">
                                            {{ $dokumen->pivot->penerapan === 'required' ? 'Wajib pada jalur ini' : 'Opsional pada jalur ini' }}
                                        </span>
                                        <span class="inline-flex rounded-full border px-3 py-1 text-[11px] font-bold {{ $statusSaatIni === 'siap' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : ($statusSaatIni === 'sedang_diproses' ? 'border-amber-200 bg-amber-50 text-amber-800' : 'border-slate-200 bg-slate-50 text-slate-600') }}">
                                            {{ match ($statusSaatIni) { 'siap' => 'Siap', 'sedang_diproses' => 'Sedang Diproses', default => 'Belum Disiapkan' } }}
                                        </span>
                                    </div>
                                    <h3 class="mt-4 text-base font-extrabold text-slate-900">{{ $dokumen->nama }}</h3>
                                    @if ($dokumen->penjelasan)
                                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $dokumen->penjelasan }}</p>
                                    @endif
                                    <div class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                                        <div>
                                            <p class="text-xs font-bold text-slate-700">Petunjuk pengurusan</p>
                                            <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $dokumen->petunjuk_pengurusan ?: 'Informasi pengurusan belum tersedia. Silakan konfirmasi kepada Admin BKK.' }}</p>
                                        </div>
                                        @if ($dokumen->keterangan)
                                            <div>
                                                <p class="text-xs font-bold text-slate-700">Keterangan</p>
                                                <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $dokumen->keterangan }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('dashboard.panduan-dokumen.status', [$selectedJalur, $dokumen]) }}" class="mt-5 flex flex-col gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:items-end">
                                        @csrf
                                        <div class="flex-1">
                                            <label for="status-{{ $dokumen->id }}" class="mb-1 block text-xs font-bold text-slate-700">Status persiapan saya</label>
                                            <select id="status-{{ $dokumen->id }}" name="status" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm">
                                                <option value="belum_disiapkan" @selected($statusSaatIni === 'belum_disiapkan')>Belum Disiapkan</option>
                                                <option value="sedang_diproses" @selected($statusSaatIni === 'sedang_diproses')>Sedang Diproses</option>
                                                <option value="siap" @selected($statusSaatIni === 'siap')>Siap</option>
                                            </select>
                                        </div>
                                        <button class="rounded-lg bg-[#155EAA] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#0F2F5F]">Simpan status</button>
                                    </form>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            @endif
        @endif
    @endif
</div>
@endsection
