@extends('dashboard.layouts.dashboard')

@section('title', 'Perbaikan Berkas Pengajuan: ' . $pengajuan->nomor_pengajuan)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Button -->
    <a href="{{ route('dashboard.bantuan.show', $pengajuan->id) }}" 
       class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#155EAA] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Detail Pengajuan</span>
    </a>

    <!-- Header Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-3">
        <div class="flex items-center gap-2 text-xs font-semibold text-rose-600 mb-1">
            <span>Perbaikan Berkas Administrasi</span>
        </div>
        <h1 class="text-xl font-black text-slate-900">Perbaiki Dokumen: {{ $pengajuan->nomor_pengajuan }}</h1>
        <p class="text-xs text-slate-500">
            Program: <strong class="text-slate-800">{{ $pengajuan->programBantuan->nama_program }}</strong>
        </p>

        <!-- Catatan Perbaikan Admin -->
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 text-xs sm:text-sm space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-rose-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Catatan Koreksi dari Admin BKK SMKN 7 Jember:</span>
            </div>
            <p class="leading-relaxed pl-5 font-semibold">
                "{{ $pengajuan->catatan_perbaikan }}"
            </p>
        </div>
    </div>

    <!-- Form Perbaikan -->
    <form method="POST" 
          action="{{ route('dashboard.bantuan.perbaikan.store', $pengajuan->id) }}" 
          enctype="multipart/form-data" 
          class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                <span>Pilih Berkas Pengganti yang Ingin Diperbarui</span>
            </h2>

            <div class="space-y-4">
                @foreach($pengajuan->dokumens as $dok)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">{{ $dok->nama_dokumen }}</span>
                            <span class="text-[11px] text-slate-400">Berkas saat ini: {{ $dok->file_name }}</span>
                        </div>
                        <input type="file" 
                               name="dokumen_perbaikan[{{ $dok->id }}]" 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full text-xs text-slate-500
                                      file:mr-4 file:py-1.5 file:px-3
                                      file:rounded-xl file:border-0
                                      file:text-xs file:font-semibold
                                      file:bg-slate-200 file:text-slate-700
                                      hover:file:bg-slate-300 file:cursor-pointer cursor-pointer">
                    </div>
                @endforeach
            </div>

            <div class="pt-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Catatan Tanggapan Pemohon (Opsional)
                </label>
                <textarea name="catatan_pemohon" 
                          rows="2" 
                          placeholder="Jelaskan perbaikan apa saja yang telah Anda lakukan..."
                          class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#155EAA]"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('dashboard.bantuan.show', $pengajuan->id) }}" 
               class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl">
                Batal
            </a>

            <button type="submit" 
                    class="px-6 py-3 bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold rounded-xl shadow-md transition-all">
                Kirimkan Berkas Perbaikan
            </button>
        </div>
    </form>
</div>
@endsection
