@extends('dashboard.layouts.dashboard')

@section('title', 'Formulir Pengajuan Bantuan: ' . $program->nama_program)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Button -->
    <a href="{{ route('dashboard.bantuan.program', $program->slug) }}" 
       class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#155EAA] transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Detail Program</span>
    </a>

    <!-- Header Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex items-center gap-2 text-xs font-semibold text-[#155EAA] mb-1">
            <span>Formulir Permohonan Resmi</span>
        </div>
        <h1 class="text-xl font-black text-slate-900">{{ $program->nama_program }}</h1>
        <p class="text-xs text-slate-500 mt-1">
            Kemitraan bersama <strong class="text-slate-800">{{ $program->nama_mitra }}</strong> • Silakan lengkapi data dan unggah berkas persyaratan di bawah ini.
        </p>
    </div>

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm space-y-1">
            <div class="font-bold">Mohon periksa kembali isian formulir:</div>
            <ul class="list-disc pl-5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Pengajuan -->
    <form method="POST" 
          action="{{ route('dashboard.bantuan.store', $program->slug) }}" 
          enctype="multipart/form-data" 
          class="space-y-6">
        @csrf

        <!-- 1. Data Pemohon (Auto-filled) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                <span>1. Data Identitas Pemohon (Terisi Otomatis)</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block font-medium">Nama Lengkap</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $user->name }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block font-medium">Status Akun / Peran</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $user->getRoleDisplayName() }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block font-medium">Email Terdaftar</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $user->email }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block font-medium">Lembaga Kemitraan Tujuan</span>
                    <span class="font-bold text-[#166534] text-sm">{{ $program->nama_mitra }}</span>
                </div>
            </div>
        </div>

        <!-- 2. Alasan & Informasi Pengajuan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                <span>2. Alasan Pengajuan & Informasi Pendukung</span>
            </h2>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Alasan Mengajukan Permohonan Bantuan *
                </label>
                <textarea name="alasan_pengajuan" 
                          rows="4" 
                          required 
                          placeholder="Jelaskan kondisi finansial keluarga, tahapan seleksi yang sudah Anda ikuti, dan mengapa Anda membutuhkan bantuan atau dana talangan ini..."
                          class="w-full py-2.5 px-3.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#166534] leading-relaxed">{{ old('alasan_pengajuan') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Minimal 15 karakter. Uraikan secara jujur dan transparan.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Informasi Tambahan / Prestasi / Penjamin (Opsional)
                </label>
                <textarea name="informasi_tambahan" 
                          rows="2" 
                          placeholder="Misal: Nomor kontak orang tua/wali penjamin, prestasi kejuaraan kejuruan, atau catatan LPK mitra..."
                          class="w-full py-2.5 px-3.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#166534] leading-relaxed">{{ old('informasi_tambahan') }}</textarea>
            </div>
        </div>

        <!-- 3. Unggah Berkas Persyaratan (Private Storage) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#FBBF24]"></span>
                <span>3. Unggah Dokumen Persyaratan Wajib (Format PDF/JPG/PNG, Maks. 3MB)</span>
            </h2>

            <div class="space-y-4">
                @foreach($program->dokumen_dibutuhkan ?? [] as $index => $namaDok)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                        <label class="block text-xs font-bold text-slate-800">
                            {{ $index + 1 }}. {{ $namaDok }} *
                        </label>
                        <input type="file" 
                               name="dokumen_{{ $index }}" 
                               required 
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="block w-full text-xs text-slate-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-xl file:border-0
                                      file:text-xs file:font-semibold
                                      file:bg-[#0F2F5F] file:text-white
                                      hover:file:bg-[#155EAA] file:cursor-pointer cursor-pointer">
                    </div>
                @endforeach
            </div>

            <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-100 flex items-start gap-2 text-[11px] text-blue-800">
                <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Dokumen yang diunggah disimpan di server privat terenkripsi. Berkas ini hanya dapat diakses oleh Anda dan Admin BKK SMKN 7 Jember yang bertugas.</span>
            </div>
        </div>

        <!-- 4. Persetujuan Pemrosesan Data -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" 
                       name="persetujuan_data" 
                       value="1" 
                       required 
                       @checked(old('persetujuan_data'))
                       class="mt-1 rounded text-[#166534] focus:ring-[#166534]">
                <span class="text-xs text-slate-700 leading-relaxed">
                    Saya menyatakan bahwa seluruh data dan dokumen yang saya unggah adalah benar dan sah. Saya memberikan persetujuan kepada BKK SMKN 7 Jember untuk memverifikasi berkas ini dan meneruskannya kepada pihak <strong>{{ $program->nama_mitra }}</strong> untuk proses seleksi dan administrasi permohonan bantuan.
                </span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between gap-4 pt-2">
            <a href="{{ route('dashboard.bantuan.program', $program->slug) }}" 
               class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition-all">
                Batal
            </a>

            <button type="submit" 
                    class="px-6 py-3 bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
                Kirim Permohonan Bantuan Sekarang
            </button>
        </div>
    </form>
</div>
@endsection
