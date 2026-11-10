@extends('dashboard.layouts.dashboard')

@section('title', 'Ajukan Konsultasi Digital | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Ajukan Konsultasi')
@section('header_title', 'Formulir Pengajuan Bimbingan Konseling')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    
    <!-- Top Bar Navigation -->
    <div>
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
            <a href="{{ route('dashboard.konseling.index') }}" class="hover:text-[#155EAA] flex items-center gap-1 font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Konseling Digital</span>
            </a>
            <span>&rsaquo;</span>
            <span class="text-slate-800 font-bold">Form Pengajuan Baru</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
            Pengajuan Bimbingan Konseling Baru
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Silakan pilih kategori dan sampaikan maksud konsultasi Anda secara singkat. Data Anda dijaga kerahasiaannya oleh Guru BK SMKN 7 Jember.
        </p>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800 space-y-1 shadow-2xs">
            <strong class="font-bold block">Terdapat kesalahan pada isian formulir:</strong>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Kartu Identitas Peserta (Auto-Filled dari Akun Login) -->
    <div class="bg-blue-50/60 rounded-3xl border border-blue-200 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-[#0F2F5F] text-[#FBBF24] flex items-center justify-center font-extrabold text-base flex-shrink-0">
                {{ substr($user->name, 0, 2) }}
            </div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Identitas Pemohon</span>
                <strong class="text-sm sm:text-base font-bold text-slate-900 block">{{ $user->name }}</strong>
                <span class="text-xs text-slate-500">{{ $user->email }} &bull; Peran: <strong class="text-[#155EAA]">{{ $user->getRoleDisplayName() }}</strong></span>
            </div>
        </div>

        <div class="text-xs text-slate-500 bg-white/80 p-3 rounded-2xl border border-blue-100 sm:text-right">
            <span class="text-[11px] text-slate-400 block">Status Data Akun</span>
            <strong class="text-emerald-700 font-bold">Terverifikasi Otomatis</strong>
        </div>
    </div>

    <!-- Formulir Pengajuan -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form action="{{ route('dashboard.konseling.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- 1. Kategori Konsultasi -->
            <div>
                <label for="kategori" class="block text-xs font-bold text-slate-800 mb-1.5">
                    Kategori Konsultasi <span class="text-rose-500">*</span>
                </label>
                <select name="kategori" id="kategori" required 
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-[#155EAA] focus:border-[#155EAA]">
                    <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>-- Pilih Kategori Permasalahan / Diskusi --</option>
                    @foreach($kategoriList as $key => $label)
                        <option value="{{ $key }}" {{ old('kategori') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <span class="text-[10px] text-slate-400 mt-1 block">
                    Pilih kategori yang paling sesuai dengan kebutuhan bimbingan Anda.
                </span>
            </div>

            <!-- 2. Pilihan Metode Konsultasi -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-2">
                    Metode Pelaksanaan Konsultasi <span class="text-rose-500">*</span>
                </label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Option Online -->
                    <label class="relative flex items-start gap-3 p-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition-all has-checked:border-[#155EAA] has-checked:bg-blue-50/40">
                        <input type="radio" name="metode" value="online" {{ old('metode', 'online') === 'online' ? 'checked' : '' }} class="mt-0.5 text-[#155EAA] focus:ring-[#155EAA]">
                        <div>
                            <strong class="text-xs sm:text-sm font-bold text-slate-900 block flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Online via Website</span>
                            </strong>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                Percakapan teks interaktif dua arah melalui ruang chat real-time website SMKN 7 Jember.
                            </p>
                        </div>
                    </label>

                    <!-- Option Tatap Muka -->
                    <label class="relative flex items-start gap-3 p-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition-all has-checked:border-[#155EAA] has-checked:bg-blue-50/40">
                        <input type="radio" name="metode" value="tatap_muka" {{ old('metode') === 'tatap_muka' ? 'checked' : '' }} class="mt-0.5 text-[#155EAA] focus:ring-[#155EAA]">
                        <div>
                            <strong class="text-xs sm:text-sm font-bold text-slate-900 block flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Tatap Muka di Sekolah</span>
                            </strong>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                Bertemu langsung dengan Guru BK di Ruang Bimbingan Konseling SMKN 7 Jember sesuai jadwal yang disetujui.
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 3. Usulan Tanggal & Waktu -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="tanggal_usulan" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Usulan Tanggal Konsultasi <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_usulan" 
                           id="tanggal_usulan" 
                           required 
                           min="{{ date('Y-m-d') }}"
                           value="{{ old('tanggal_usulan', date('Y-m-d', strtotime('+1 day'))) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 focus:ring-2 focus:ring-[#155EAA]">
                    <span class="text-[10px] text-slate-400 mt-1 block">Guru BK akan menyesuaikan atau mengonfirmasi jadwal ini.</span>
                </div>

                <div>
                    <label for="waktu_usulan" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Usulan Waktu / Perkiraan Jam <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="waktu_usulan" 
                           id="waktu_usulan" 
                           required 
                           placeholder="Contoh: 09:30 WIB atau Jam Istirahat Sekolah"
                           value="{{ old('waktu_usulan') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 focus:ring-2 focus:ring-[#155EAA]">
                    <span class="text-[10px] text-slate-400 mt-1 block">Jam layanan resmi sekolah: 07.30 - 15.30 WIB.</span>
                </div>
            </div>

            <!-- 4. Alasan / Deskripsi Singkat Konsultasi -->
            <div>
                <label for="alasan_konseling" class="block text-xs font-bold text-slate-800 mb-1.5">
                    Alasan / Hal yang Ingin Dikonsultasikan <span class="text-rose-500">*</span>
                </label>
                <textarea name="alasan_konseling" 
                          id="alasan_konseling" 
                          rows="4" 
                          required 
                          placeholder="Jelaskan secara singkat topik atau pertanyaan yang ingin Anda diskusikan bersama Guru BK..."
                          class="w-full px-4 py-3 rounded-2xl border border-slate-300 text-xs sm:text-sm text-slate-800 focus:ring-2 focus:ring-[#155EAA]">{{ old('alasan_konseling') }}</textarea>
                
                <div class="mt-2 p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-600 flex items-start gap-2">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span>
                        <strong>Catatan Privasi:</strong> Anda tidak diwajibkan menuliskan detail yang bersifat terlalu sensitif di formulir ini. Cukup sampaikan gambaran umum; pembahasan mendalam dapat dilanjutkan langsung saat sesi berlangsung.
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('dashboard.konseling.index') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition-all">
                    Batal
                </a>

                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs sm:text-sm font-bold shadow-xs transition-all cursor-pointer">
                    Kirim Pengajuan Konsultasi
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
