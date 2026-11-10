@extends('dashboard.layouts.dashboard')

@section('title', 'Tracer Study Alumni | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Tracer Study')
@section('header_title', 'Tracer Study Alumni')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0F2F5F] p-6 sm:p-8 text-white shadow-lg">
        <div class="absolute -right-10 -bottom-12 h-48 w-48 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl">
            <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold text-[#FBBF24]">
                <span class="h-2 w-2 rounded-full bg-[#FBBF24]"></span>
                Pendataan Alumni SMKN 7 Jember
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Tracer Study</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-200">
                Bagikan perkembangan karier Anda setelah lulus. Data digunakan untuk evaluasi dan pengembangan pendidikan serta layanan karier sekolah, bukan untuk dipublikasikan.
            </p>
        </div>
    </section>

    @foreach (['success' => 'border-emerald-200 bg-emerald-50 text-emerald-800', 'error' => 'border-rose-200 bg-rose-50 text-rose-800', 'info' => 'border-blue-200 bg-blue-50 text-blue-800'] as $flash => $flashClass)
        @if (session($flash))
            <div class="rounded-xl border p-4 text-sm {{ $flashClass }}" role="status">
                {{ session($flash) }}
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">
            <p class="font-bold">Periksa kembali data berikut:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($periodeAktif)
        <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Periode Pengisian</p>
                    <h2 class="mt-1 text-xl font-extrabold text-slate-900">Tracer Study {{ $periodeAktif->tahun }}</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Pengisian dibuka {{ $periodeAktif->tanggal_mulai->format('d/m/Y') }}
                        sampai {{ $periodeAktif->tanggal_berakhir->format('d/m/Y') }}.
                    </p>
                </div>
                @if ($responsSaatIni)
                    <span class="inline-flex w-fit items-center rounded-full border px-3 py-1.5 text-xs font-bold {{ $responsSaatIni->status_badge_class }}">
                        {{ $responsSaatIni->status_label }}
                    </span>
                @else
                    <span class="inline-flex w-fit items-center rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-800">
                        Belum Mengisi
                    </span>
                @endif
            </div>
        </section>

        @if ($responsSaatIni)
            <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">Respons periode {{ $periodeAktif->tahun }}</h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Terakhir diperbarui {{ $responsSaatIni->updated_at->format('d/m/Y H:i') }}.
                            Anda dapat memperbarui data selama periode masih dibuka.
                        </p>
                    </div>
                    <span class="inline-flex w-fit items-center rounded-full border px-3 py-1.5 text-xs font-bold {{ $responsSaatIni->status_badge_class }}">
                        {{ $responsSaatIni->status_label }}
                    </span>
                </div>
                @if ($responsSaatIni->status_verifikasi === \App\Models\TracerStudy::STATUS_NEEDS_UPDATE)
                    <p class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-relaxed text-amber-900">Respons Anda perlu diperbaiki. Tinjau catatan perbaikan di bawah, lalu perbarui data melalui formulir selama periode pengisian masih terbuka.</p>
                @endif
            </section>
        @endif

        @if ($pesanPembaruan->isNotEmpty())
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6" aria-labelledby="pesan-pembaruan-title">
                <h2 id="pesan-pembaruan-title" class="font-extrabold text-amber-900">Catatan Perbaikan</h2>
                <div class="mt-3 space-y-3">
                    @foreach ($pesanPembaruan as $pesan)
                        <div class="rounded-xl border border-amber-200/80 bg-white p-4">
                            <p class="text-xs font-bold text-amber-900">{{ $pesan->admin?->role === 'guru' ? 'Catatan Guru BK' : 'Catatan Admin BKK' }}</p>
                            <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $pesan->pesan_alumni }}</p>
                            <p class="mt-2 text-xs text-slate-500">{{ ($pesan->diberitahukan_pada ?? $pesan->created_at)->format('d/m/Y H:i') }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
            <div class="mb-5">
                <h2 class="text-lg font-extrabold text-slate-900">
                    {{ $responsSaatIni ? 'Perbarui Data Tracer Study' : 'Isi Data Tracer Study' }}
                </h2>
                <p class="mt-1 text-sm text-slate-600">Lengkapi seluruh data sesuai kondisi Anda saat ini.</p>
            </div>

            <form method="POST"
                  action="{{ $responsSaatIni ? route('dashboard.tracer-study.update', $responsSaatIni) : route('dashboard.tracer-study.store') }}"
                  class="space-y-5">
                @csrf
                @if ($responsSaatIni)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="nama_lengkap" class="mb-1.5 block text-sm font-bold text-slate-700">Nama lengkap</label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" required maxlength="255"
                               value="{{ old('nama_lengkap', $responsSaatIni?->nama_lengkap ?? $profil?->nama_lengkap ?? $user->name) }}"
                               class="w-full rounded-xl border {{ $errors->has('nama_lengkap') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
                        @error('nama_lengkap') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tahun_lulus" class="mb-1.5 block text-sm font-bold text-slate-700">Tahun lulus</label>
                        <input id="tahun_lulus" name="tahun_lulus" type="number" required min="1950" max="{{ now()->year }}"
                               value="{{ old('tahun_lulus', $responsSaatIni?->tahun_lulus ?? $profil?->tahun_kelulusan) }}"
                               class="w-full rounded-xl border {{ $errors->has('tahun_lulus') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
                        @error('tahun_lulus') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="jurusan" class="mb-1.5 block text-sm font-bold text-slate-700">Jurusan</label>
                        <input id="jurusan" name="jurusan" type="text" required maxlength="255"
                               value="{{ old('jurusan', $responsSaatIni?->jurusan ?? $profil?->jurusan) }}"
                               class="w-full rounded-xl border {{ $errors->has('jurusan') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
                        @error('jurusan') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="status_kegiatan" class="mb-1.5 block text-sm font-bold text-slate-700">Status kegiatan</label>
                        <select id="status_kegiatan" name="status_kegiatan" required
                                class="w-full rounded-xl border {{ $errors->has('status_kegiatan') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
                            <option value="">Pilih status kegiatan</option>
                            <option value="bekerja" @selected(old('status_kegiatan', $responsSaatIni?->status_kegiatan) === 'bekerja')>Bekerja</option>
                            <option value="magang" @selected(old('status_kegiatan', $responsSaatIni?->status_kegiatan) === 'magang')>Magang</option>
                        </select>
                        @error('status_kegiatan') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="pekerjaan" class="mb-1.5 block text-sm font-bold text-slate-700">Pekerjaan atau posisi</label>
                        <input id="pekerjaan" name="pekerjaan" type="text" required maxlength="255"
                               value="{{ old('pekerjaan', $responsSaatIni?->pekerjaan) }}"
                               class="w-full rounded-xl border {{ $errors->has('pekerjaan') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
                        @error('pekerjaan') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="nama_perusahaan" class="mb-1.5 block text-sm font-bold text-slate-700">Nama perusahaan</label>
                        <input id="nama_perusahaan" name="nama_perusahaan" type="text" required maxlength="255"
                               value="{{ old('nama_perusahaan', $responsSaatIni?->nama_perusahaan) }}"
                               class="w-full rounded-xl border {{ $errors->has('nama_perusahaan') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
                        @error('nama_perusahaan') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tahun_masuk_perusahaan" class="mb-1.5 block text-sm font-bold text-slate-700">Tahun mulai bekerja atau magang</label>
                        <input id="tahun_masuk_perusahaan" name="tahun_masuk_perusahaan" type="number" required min="1950" max="{{ now()->year + 1 }}"
                               value="{{ old('tahun_masuk_perusahaan', $responsSaatIni?->tahun_masuk_perusahaan) }}"
                               class="w-full rounded-xl border {{ $errors->has('tahun_masuk_perusahaan') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">
                        @error('tahun_masuk_perusahaan') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="alamat" class="mb-1.5 block text-sm font-bold text-slate-700">Alamat tempat tinggal</label>
                        <textarea id="alamat" name="alamat" rows="3" required maxlength="2000"
                                  class="w-full rounded-xl border {{ $errors->has('alamat') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">{{ old('alamat', $responsSaatIni?->alamat) }}</textarea>
                        @error('alamat') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="alamat_tempat_kerja" class="mb-1.5 block text-sm font-bold text-slate-700">Alamat tempat kerja atau magang</label>
                        <textarea id="alamat_tempat_kerja" name="alamat_tempat_kerja" rows="3" required maxlength="2000"
                                  class="w-full rounded-xl border {{ $errors->has('alamat_tempat_kerja') ? 'border-rose-400 ring-2 ring-rose-100' : 'border-slate-200' }} px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-[#155EAA] focus:ring-2 focus:ring-blue-100">{{ old('alamat_tempat_kerja', $responsSaatIni?->alamat_tempat_kerja) }}</textarea>
                        @error('alamat_tempat_kerja') <p class="mt-1.5 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="rounded-xl border {{ $errors->has('persetujuan_penggunaan_data') ? 'border-rose-300 bg-rose-50' : 'border-slate-200 bg-slate-50' }} p-4">
                    <label for="persetujuan_penggunaan_data" class="flex cursor-pointer items-start gap-3">
                        <input id="persetujuan_penggunaan_data" name="persetujuan_penggunaan_data" type="checkbox" value="1" required
                               @checked(old('persetujuan_penggunaan_data', $responsSaatIni?->persetujuan_penggunaan_data))
                               class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#155EAA] focus:ring-[#155EAA]">
                        <span class="text-sm leading-relaxed text-slate-700">
                            Saya menyetujui pemrosesan data ini untuk keperluan Tracer Study, evaluasi pendidikan, dan pengembangan layanan karier SMKN 7 Jember. Persetujuan ini bukan izin untuk mempublikasikan data pribadi saya.
                        </span>
                    </label>
                    @error('persetujuan_penggunaan_data') <p class="mt-2 text-xs font-medium text-rose-700">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs leading-relaxed text-slate-500">Data pribadi Anda hanya ditampilkan kepada Anda dan petugas yang berwenang.</p>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[#155EAA] px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#0F2F5F] focus:outline-none focus:ring-2 focus:ring-blue-200">
                        {{ $responsSaatIni ? 'Perbarui Data' : 'Kirim Respons' }}
                    </button>
                </div>
            </form>
        </section>
    @else
        <section class="rounded-2xl border border-blue-200 bg-blue-50 p-5 sm:p-6">
            <h2 class="font-extrabold text-[#0F2F5F]">Periode pengisian belum dibuka</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-700">Belum ada periode Tracer Study yang sedang menerima respons. Riwayat pengisian Anda tetap dapat dilihat di bawah.</p>
        </section>
    @endif

    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Riwayat Tracer Study</h2>
                <p class="mt-1 text-sm text-slate-600">Respons setiap periode tersimpan sebagai riwayat tersendiri.</p>
            </div>
            <span class="text-xs font-semibold text-slate-500">{{ $riwayat->count() }} respons</span>
        </div>

        @if ($riwayat->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                Belum ada respons Tracer Study pada akun Anda.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($riwayat as $respons)
                    @php
                        $masihDapatDiedit = $periodeAktif && $respons->periode_id === $periodeAktif->id;
                    @endphp
                    <article class="flex flex-col gap-3 rounded-xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900">Tracer Study {{ $respons->periode?->tahun ?? '—' }}</h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Dikirim {{ $respons->created_at->format('d/m/Y H:i') }}
                                @if ($respons->updated_at->gt($respons->created_at))
                                    <span class="mx-1">·</span>Diperbarui {{ $respons->updated_at->format('d/m/Y H:i') }}
                                @endif
                            </p>
                            @if ($respons->status_verifikasi === \App\Models\TracerStudy::STATUS_NEEDS_UPDATE)
                                <p class="mt-2 text-xs font-semibold text-rose-700">Respons ini memerlukan perbaikan.</p>
                            @endif
                            @if ((! $responsSaatIni || $respons->id !== $responsSaatIni->id) && $respons->followUps->isNotEmpty())
                                <div class="mt-3 space-y-2">
                                    @foreach ($respons->followUps as $pesanRiwayat)
                                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                                            <p class="text-xs font-bold text-amber-900">{{ $pesanRiwayat->admin?->role === 'guru' ? 'Catatan Guru BK' : 'Catatan Admin BKK' }}</p>
                                            <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $pesanRiwayat->pesan_alumni }}</p>
                                            <p class="mt-1 text-[11px] text-slate-500">{{ ($pesanRiwayat->diberitahukan_pada ?? $pesanRiwayat->created_at)->format('d/m/Y H:i') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full border px-3 py-1.5 text-xs font-bold {{ $respons->status_badge_class }}">{{ $respons->status_label }}</span>
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600">
                                {{ $masihDapatDiedit ? 'Dapat diperbarui' : 'Read-only' }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
