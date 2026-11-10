@extends(Auth::check() ? 'dashboard.layouts.dashboard' : 'layouts.app')

@php
    $dashboardRoute = Auth::user()->isSiswa()
        ? route('dashboard.siswa')
        : route('dashboard.alumni');
@endphp

@section('title', 'Perbaikan Dokumen ' . $pendaftaran->nomor_pendaftaran . ' | Karier Jepang')
@section('breadcrumb', 'Karier Jepang • Perbaikan Dokumen')
@section('header_title', 'Perbaikan Dokumen Karier Jepang')

@section('content')
<div class="bg-[#F8FAFC] py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Header -->
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                <a href="{{ $dashboardRoute }}" class="hover:text-[#155EAA]">Dashboard</a>
                <span>&rsaquo;</span>
                <a href="{{ route('karier-jepang.pendaftaran.show', $pendaftaran->id) }}" class="hover:text-[#155EAA]">Pendaftaran {{ $pendaftaran->nomor_pendaftaran }}</a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-bold">Unggah Ulang Perbaikan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                Perbaikan Berkas Administrasi
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Perbarui dokumen sesuai catatan verifikasi petugas BKK SMKN 7 Jember agar pengajuan Anda dapat diproses ke tahap selanjutnya.
            </p>
        </div>

        <!-- Catatan Admin Box -->
        <div class="mb-8 p-5 rounded-2xl bg-orange-50 border border-orange-200">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-xl bg-orange-100 text-orange-700 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-orange-950">Catatan Perbaikan dari Admin Sekolah:</h2>
                    <p class="text-xs sm:text-sm text-orange-900 mt-1.5 leading-relaxed bg-white/80 p-3 rounded-xl border border-orange-200">
                        "{{ $pendaftaran->catatan_admin }}"
                    </p>
                    <span class="text-[11px] text-orange-700 mt-2 block">
                        Diverifikasi oleh: <strong>{{ $pendaftaran->adminChecker?->name ?? 'Admin BKK' }}</strong> pada {{ $pendaftaran->admin_checked_at?->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Perbaikan -->
        <form action="{{ route('karier-jepang.pendaftaran.perbaikan.store', $pendaftaran->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
            @csrf

            @if($errors->any())
                <div role="alert" aria-labelledby="validation-errors-title" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800">
                    <strong id="validation-errors-title" class="font-bold block mb-1">Terdapat kesalahan pada formulir yang perlu diperbaiki:</strong>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-xs text-slate-500 pb-4 border-b border-slate-100">
                Pilih dan unggah kembali berkas yang perlu diperbaiki. Berkas yang tidak dipilih akan tetap mempertahankan file yang telah Anda unggah sebelumnya.
            </p>

            <div class="space-y-5 text-xs sm:text-sm">
                <!-- 1. KTP / Kartu Pelajar -->
                <div>
                    <label for="file_identitas" class="block font-bold text-slate-700 mb-1">
                        1. Unggah Ulang KTP / Kartu Pelajar (Opsional)
                    </label>
                    <input type="file" 
                           name="file_identitas" 
                           id="file_identitas" 
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#155EAA] hover:file:bg-blue-100 @error('file_identitas') border border-rose-500 rounded-lg ring-1 ring-rose-500 @enderror">
                    @error('file_identitas')
                        <p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Kartu Keluarga -->
                <div>
                    <label for="file_kk" class="block font-bold text-slate-700 mb-1">
                        2. Unggah Ulang Kartu Keluarga (Opsional)
                    </label>
                    <input type="file" 
                           name="file_kk" 
                           id="file_kk" 
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#155EAA] hover:file:bg-blue-100 @error('file_kk') border border-rose-500 rounded-lg ring-1 ring-rose-500 @enderror">
                    @error('file_kk')
                        <p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3. Rapor / Ijazah -->
                <div>
                    <label for="file_rapor_ijazah" class="block font-bold text-slate-700 mb-1">
                        3. Unggah Ulang Rapor / Ijazah (Opsional)
                    </label>
                    <input type="file" 
                           name="file_rapor_ijazah" 
                           id="file_rapor_ijazah" 
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#155EAA] hover:file:bg-blue-100 @error('file_rapor_ijazah') border border-rose-500 rounded-lg ring-1 ring-rose-500 @enderror">
                    @error('file_rapor_ijazah')
                        <p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 4. Surat Pernyataan Orang Tua -->
                <div>
                    <label for="file_surat_ortu" class="block font-bold text-slate-700 mb-1">
                        4. Unggah Ulang Surat Pernyataan Orang Tua / Wali (Opsional)
                    </label>
                    <input type="file" 
                           name="file_surat_ortu" 
                           id="file_surat_ortu" 
                           accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-[#155EAA] hover:file:bg-blue-100 @error('file_surat_ortu') border border-rose-500 rounded-lg ring-1 ring-rose-500 @enderror">
                    @error('file_surat_ortu')
                        <p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Peserta -->
                <div class="pt-4 border-t border-slate-100">
                    <label for="catatan_peserta" class="block font-bold text-slate-700 mb-1">
                        Penjelasan / Catatan Tambahan Peserta (Opsional)
                    </label>
                    <textarea name="catatan_peserta" 
                              id="catatan_peserta" 
                              rows="2" 
                              aria-invalid="@error('catatan_peserta') true @else false @enderror"
                              placeholder="Jelaskan perubahan yang telah Anda lakukan pada dokumen..."
                              class="w-full px-3.5 py-2.5 rounded-xl @error('catatan_peserta') border border-rose-500 ring-1 ring-rose-500 @else border border-slate-200 @enderror bg-white text-xs sm:text-sm text-slate-800 focus:outline-hidden focus:border-[#155EAA] focus:ring-1 focus:ring-[#155EAA]">{{ old('catatan_peserta') }}</textarea>
                    @error('catatan_peserta')
                        <p class="mt-1 text-xs text-rose-700" role="alert">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                <a href="{{ route('karier-jepang.pendaftaran.show', $pendaftaran->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                    &larr; Kembali
                </a>

                <button type="submit" 
                        class="px-6 py-3 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md transition-all cursor-pointer">
                    Kirimkan Perbaikan Berkas
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
