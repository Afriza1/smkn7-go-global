@extends('dashboard.layouts.dashboard')

@section('title', 'Kelola Pengajuan Konseling | Guru BK SMKN 7 Jember')
@section('breadcrumb', 'Kelola Pengajuan Konseling')
@section('header_title', 'Rincian & Pengelolaan Sesi Konseling')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard.guru.konseling.index') }}" class="hover:text-[#155EAA] flex items-center gap-1 font-semibold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Daftar Konseling</span>
                </a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-bold">Kelola #{{ $konseling->nomor_konseling }}</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                Konseling: {{ $konseling->user->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kategori: <strong class="text-[#0F2F5F]">{{ $konseling->getKategoriLabel() }}</strong> &bull; Terdaftar pada {{ $konseling->created_at->translatedFormat('d F Y, H:i') }} WIB
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($konseling->isOnline())
                @if($konseling->status === 'berlangsung')
                    <a href="{{ route('dashboard.guru.konseling.chat', $konseling->id) }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-all animate-pulse">
                        <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Masuk Ruang Chat</span>
                    </a>

                    <form action="{{ route('dashboard.guru.konseling.selesai', $konseling->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin hendak mengakhiri sesi konsultasi ini?')">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl border border-rose-300 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-all cursor-pointer">
                            Akhiri Sesi
                        </button>
                    </form>
                @elseif($konseling->status === 'dijadwalkan')
                    <form action="{{ route('dashboard.guru.konseling.mulai', $konseling->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Mulai Sesi Chat Online</span>
                        </button>
                    </form>
                @elseif($konseling->status === 'selesai')
                    <a href="{{ route('dashboard.guru.konseling.chat', $konseling->id) }}" 
                       class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                        Lihat Arsip Chat
                    </a>
                @endif
            @else
                @if($konseling->status === 'dijadwalkan')
                    <form action="{{ route('dashboard.guru.konseling.mulai', $konseling->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all cursor-pointer">
                            Tandai Sedang Berlangsung
                        </button>
                    </form>
                @elseif($konseling->status === 'berlangsung')
                    <form action="{{ route('dashboard.guru.konseling.selesai', $konseling->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all cursor-pointer">
                            Tandai Konsultasi Selesai
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3 shadow-2xs">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-800 space-y-1 shadow-2xs">
            <strong class="font-bold block">Terdapat kesalahan validasi formulir:</strong>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Peserta & Rincian Pengajuan -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-[#0F2F5F] text-[#FBBF24] flex items-center justify-center font-extrabold text-base flex-shrink-0">
                    {{ substr($konseling->user->name, 0, 2) }}
                </div>
                <div>
                    <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Profil Peserta Konseling</span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">{{ $konseling->user->name }}</h2>
                    <span class="text-xs text-slate-500">{{ $konseling->user->email }} &bull; Peran: <strong class="text-[#155EAA]">{{ $konseling->user->getRoleDisplayName() }}</strong></span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $konseling->getStatusBadgeClass() }}">
                    Status: {{ $konseling->getStatusLabel() }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 text-xs">
            <div class="space-y-3">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Kategori Bimbingan</span>
                    <strong class="text-slate-800">{{ $konseling->getKategoriLabel() }}</strong>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Metode Diinginkan</span>
                    <strong class="text-slate-800">{{ $konseling->getMetodeLabel() }}</strong>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Usulan Tanggal &amp; Waktu</span>
                    <strong class="text-[#155EAA]">{{ $konseling->tanggal_usulan->translatedFormat('d M Y') }} ({{ $konseling->waktu_usulan }})</strong>
                </div>
            </div>

            <div>
                <span class="text-slate-500 block mb-1 font-semibold">Alasan yang Disampaikan Peserta:</span>
                <p class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-700 leading-relaxed italic">
                    "{{ $konseling->alasan_konseling }}"
                </p>
            </div>
        </div>
    </div>

    <!-- Panel 1: Konfirmasi / Penjadwalan Resmi oleh Guru BK -->
    @if(in_array($konseling->status, ['menunggu_konfirmasi', 'dijadwalkan']))
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
            <div class="pb-4 border-b border-slate-100 mb-6">
                <h3 class="text-base font-bold text-[#0F2F5F] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#155EAA]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Penetapan Jadwal &amp; Persetujuan Guru BK</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Tentukan tanggal, jam, metode, dan lokasi pasti pelaksanaan konsultasi bagi peserta ini.
                </p>
            </div>

            <form action="{{ route('dashboard.guru.konseling.konfirmasi', $konseling->id) }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Jadwal Pasti <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_jadwal" required 
                               value="{{ old('tanggal_jadwal', $konseling->tanggal_jadwal ? $konseling->tanggal_jadwal->format('Y-m-d') : $konseling->tanggal_usulan->format('Y-m-d')) }}" 
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                        <input type="time" name="jam_mulai" required 
                               value="{{ old('jam_mulai', $konseling->jam_mulai ? substr($konseling->jam_mulai, 0, 5) : '09:00') }}" 
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                        <input type="time" name="jam_selesai" required 
                               value="{{ old('jam_selesai', $konseling->jam_selesai ? substr($konseling->jam_selesai, 0, 5) : '10:00') }}" 
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-300">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Metode Pelaksanaan <span class="text-rose-500">*</span></label>
                        <select name="metode" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-semibold">
                            <option value="online" {{ old('metode', $konseling->metode) === 'online' ? 'selected' : '' }}>Online (Chat Website)</option>
                            <option value="tatap_muka" {{ old('metode', $konseling->metode) === 'tatap_muka' ? 'selected' : '' }}>Tatap Muka di Sekolah</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4">
                        <label class="block font-bold text-slate-700 mb-1">Lokasi Tatap Muka (Diperlukan bila Tatap Muka)</label>
                        <input type="text" name="tempat_tatap_muka" 
                               value="{{ old('tempat_tatap_muka', $konseling->tempat_tatap_muka ?? 'Ruang Bimbingan Konseling (BK) SMKN 7 Jember, Gedung A Lantai 1') }}" 
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-300" placeholder="Contoh: Ruang Bimbingan Konseling Gedung A Lt. 1">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-4">
                        <label class="block font-bold text-slate-700 mb-1">Pesan Pengingat / Petunjuk Khusus untuk Peserta</label>
                        <textarea name="catatan_jadwal" rows="2" 
                                  placeholder="Contoh: Harap hadir tepat waktu dan membawa berkas yang diperlukan..."
                                  class="w-full px-3 py-2 rounded-xl border border-slate-300">{{ old('catatan_jadwal', $konseling->catatan_jadwal) }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
                    <button type="submit" name="aksi" value="setujui" 
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-xs cursor-pointer">
                        {{ $konseling->status === 'dijadwalkan' ? 'Perbarui Jadwal Resmi' : 'Konfirmasi & Jadwalkan Konsultasi' }}
                    </button>

                    <!-- Tombol & Input Tolak -->
                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <input type="text" name="alasan_penolakan" placeholder="Alasan penolakan jika ditolak..." class="px-3 py-2 text-xs border border-rose-300 rounded-xl w-64">
                        <button type="submit" name="aksi" value="tolak" 
                                onclick="return confirm('Apakah Anda yakin hendak menolak pengajuan ini?')"
                                class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition-all cursor-pointer">
                            Tolak
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    <!-- Panel 2: Catatan Tindak Lanjut Privat Guru BK (TERPISAH & RAHASIA) -->
    <div class="bg-white rounded-3xl border-2 border-emerald-500/30 shadow-xs p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-100 mb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-[#166534] border border-emerald-300 text-xs font-bold mb-2">
                    <svg class="w-3.5 h-3.5 text-[#166534]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Bagian Catatan Privat Khusus Guru BK (Confidential)</span>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-[#0F2F5F]">Catatan Tindak Lanjut &amp; Observasi Profesional</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Catatan ini bersifat rahasia profesional. <strong>Tidak pernah ditampilkan</strong> di ruang chat maupun dapat dilihat oleh siswa, alumni, atau pengguna lain.
                </p>
            </div>

            @if($konseling->catatanPrivat)
                <span class="text-xs text-emerald-700 font-bold bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200">
                    Tersimpan
                </span>
            @endif
        </div>

        <form action="{{ route('dashboard.guru.konseling.catatan', $konseling->id) }}" method="POST" class="space-y-5 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-800 mb-1">
                        Rencana Aksi &amp; Rekomendasi Tindak Lanjut <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="tindak_lanjut" rows="3" required 
                              placeholder="Tuliskan rekomendasi bimbingan, arahan latihan bahasa, atau langkah konkret berikutnya..."
                              class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 focus:ring-2 focus:ring-[#166534]">{{ old('tindak_lanjut', $konseling->catatanPrivat?->tindak_lanjut) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-800 mb-1">Ringkasan Analisis Masalah</label>
                    <textarea name="ringkasan_masalah" rows="3" 
                              placeholder="Observasi Guru BK terkait akar kendala siswa..."
                              class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">{{ old('ringkasan_masalah', $konseling->catatanPrivat?->ringkasan_masalah) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-800 mb-1">Evaluasi Sikap, Psikologis &amp; Karakter</label>
                    <textarea name="evaluasi_psikologis_perilaku" rows="3" 
                              placeholder="Kesiapan mental, motivasi vokasi, stabilitas emosi, kedisiplinan..."
                              class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs">{{ old('evaluasi_psikologis_perilaku', $konseling->catatanPrivat?->evaluasi_psikologis_perilaku) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-800 mb-1">Tingkat Urgensi Bimbingan <span class="text-rose-500">*</span></label>
                    <select name="status_urgensi" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-semibold text-xs">
                        <option value="rendah" {{ old('status_urgensi', $konseling->catatanPrivat?->status_urgensi) === 'rendah' ? 'selected' : '' }}>Rendah (Konsultasi Rutin)</option>
                        <option value="sedang" {{ old('status_urgensi', $konseling->catatanPrivat?->status_urgensi ?? 'sedang') === 'sedang' ? 'selected' : '' }}>Sedang (Memerlukan Pemantauan)</option>
                        <option value="tinggi" {{ old('status_urgensi', $konseling->catatanPrivat?->status_urgensi) === 'tinggi' ? 'selected' : '' }}>Tinggi (Perlu Penanganan Intensif)</option>
                    </select>
                </div>

                <!-- Eskalasi Terpilih ke Kepala Sekolah -->
                <div class="sm:col-span-2 p-4 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-3">
                    <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-amber-950">
                        <input type="checkbox" name="perlu_eskalasi_kepala_sekolah" value="1" 
                               {{ old('perlu_eskalasi_kepala_sekolah', $konseling->catatanPrivat?->perlu_eskalasi_kepala_sekolah) ? 'checked' : '' }}
                               class="rounded border-amber-400 text-amber-700 focus:ring-amber-500">
                        <span>Perlu Koordinasi Khusus / Eskalasi ke Kepala Sekolah</span>
                    </label>
                    <p class="text-[11px] text-amber-800 leading-relaxed">
                        Centang opsi ini apabila kasus bimbingan membutuhkan arahan atau putusan kebijakan dari pimpinan sekolah. Hanya ringkasan eskalasi di bawah ini yang dibagikan secara terbatas; seluruh rekaman percakapan chat tetap dirahasiakan.
                    </p>

                    <div>
                        <label class="block font-bold text-amber-950 mb-1">Ringkasan Eskalasi Pimpinan (Opsional/Bila Perlu):</label>
                        <textarea name="catatan_eskalasi" rows="2" 
                                  placeholder="Catatan ringkas isu kebijakan untuk Kepala Sekolah..."
                                  class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-white text-xs">{{ old('catatan_eskalasi', $konseling->catatanPrivat?->catatan_eskalasi) }}</textarea>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#166534] hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold shadow-xs transition-all cursor-pointer">
                    Simpan Catatan Privat Guru BK
                </button>
            </div>
        </form>
    </div>

    <!-- Riwayat Status & Audit Trail -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <h3 class="text-sm font-bold text-[#0F2F5F] mb-4 pb-3 border-b border-slate-100">
            Audit Trail Perjalanan Konseling
        </h3>

        <ol class="relative border-l border-slate-200 ml-3 space-y-5 text-xs">
            @forelse($konseling->riwayatStatuses as $log)
                <li class="ml-6">
                    <span class="absolute -left-2.5 flex items-center justify-center w-5 h-5 bg-[#155EAA] rounded-full ring-4 ring-white text-white font-bold text-[9px]">
                        &bull;
                    </span>
                    <div class="flex items-center gap-2">
                        <strong class="text-slate-800 capitalize">{{ str_replace('_', ' ', $log->status_baru) }}</strong>
                        <span class="text-[10px] text-slate-400">Oleh: {{ $log->user->name }} &bull; {{ $log->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                    </div>
                    @if($log->keterangan)
                        <p class="text-slate-600 mt-1 text-[11px] bg-slate-50 p-2 rounded-xl border border-slate-200/60">
                            {{ $log->keterangan }}
                        </p>
                    @endif
                </li>
            @empty
                <li class="text-slate-400">Belum ada riwayat tercatat.</li>
            @endforelse
        </ol>
    </div>

</div>
@endsection
