@extends('dashboard.layouts.dashboard')

@section('title', 'Status Pengajuan: ' . $pengajuan->nomor_pengajuan)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ openBatalModal: false }">
    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.bantuan.index', ['tab' => 'riwayat']) }}" 
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#155EAA] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Riwayat Bantuan</span>
        </a>

        @if($pengajuan->programBantuan->is_demo)
            <span class="px-2.5 py-0.5 text-xs font-extrabold uppercase bg-amber-100 text-amber-800 rounded-md border border-amber-200">
                PENGAJUAN DEMO
            </span>
        @endif
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Status Tracking Card -->
    @php
        $badge = $pengajuan->badgeStatus();
    @endphp
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-mono font-bold text-slate-400">Nomor Registrasi Pengajuan</span>
                <h1 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ $pengajuan->nomor_pengajuan }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Diajukan pada {{ $pengajuan->created_at->translatedFormat('l, d F Y - H:i') }} WIB
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border {{ $badge['class'] }}">
                    <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                    <span>{{ $badge['label'] }}</span>
                </span>
            </div>
        </div>

        <!-- Banner Keterangan Pemohon -->
        <div class="p-4 rounded-xl border @if($pengajuan->status === \App\Models\PengajuanBantuan::STATUS_PERLU_PERBAIKAN) bg-rose-50 border-rose-200 text-rose-900 @elseif($pengajuan->status === \App\Models\PengajuanBantuan::STATUS_DISETUJUI) bg-emerald-50 border-emerald-200 text-emerald-900 @else bg-slate-50 border-slate-200 text-slate-800 @endif">
            <div class="text-xs font-bold uppercase tracking-wider mb-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Pemberitahuan Resmi Sekolah & Mitra:</span>
            </div>
            <p class="text-xs sm:text-sm leading-relaxed">
                {{ $pengajuan->keterangan_pemohon ?: 'Permohonan Anda telah diterima dan sedang diproses sesuai tahapan verifikasi BKK.' }}
            </p>
        </div>

        <!-- Alert Aksi Perbaikan jika Berkas Perlu Diperbaiki -->
        @if($pengajuan->canBeFixed())
            <div class="p-5 bg-rose-50 rounded-2xl border border-rose-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="text-xs font-bold text-rose-900">Perhatian: Berkas Persyaratan Perlu Diperbaiki</div>
                    <p class="text-xs text-rose-700 leading-relaxed">
                        Catatan Admin BKK: <strong>{{ $pengajuan->catatan_perbaikan }}</strong>
                    </p>
                </div>
                <a href="{{ route('dashboard.bantuan.perbaikan', $pengajuan->id) }}" 
                   class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md transition-all flex-shrink-0 text-center">
                    Perbaiki & Unggah Ulang Berkas
                </a>
            </div>
        @endif

        <!-- Informasi Program & Alasan Pengajuan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                <div class="font-bold text-slate-700">Program Bantuan Dipilih:</div>
                <div class="text-sm font-bold text-slate-900">{{ $pengajuan->programBantuan->nama_program }}</div>
                <div class="text-slate-500">Mitra Kemitraan: <strong class="text-slate-800">{{ $pengajuan->programBantuan->nama_mitra }}</strong></div>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                <div class="font-bold text-slate-700">Alasan Permohonan:</div>
                <p class="text-slate-700 leading-relaxed italic">
                    "{{ $pengajuan->alasan_pengajuan }}"
                </p>
                @if($pengajuan->informasi_tambahan)
                    <div class="pt-2 border-t border-slate-200/60 text-slate-500">
                        Info tambahan: {{ $pengajuan->informasi_tambahan }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Dokumen yang Dilampirkan (Secure Private Download) -->
        <div class="space-y-3">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                <span>Dokumen Persyaratan yang Telah Dilampirkan (Penyimpanan Privat)</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($pengajuan->dokumens as $dok)
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200 flex items-center justify-between gap-3 text-xs shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#155EAA] flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 truncate">{{ $dok->nama_dokumen }}</div>
                                <div class="text-[11px] text-slate-400 truncate">{{ $dok->file_name }} ({{ $dok->formatted_size }})</div>
                            </div>
                        </div>

                        <a href="{{ route('dashboard.bantuan.dokumen.unduh', $dok->id) }}" 
                           target="_blank"
                           class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs flex-shrink-0 transition-colors">
                            Unduh
                        </a>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-400 text-xs col-span-2">
                        Tidak ada berkas dokumen yang terunggah.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Audit Trail Timeline Riwayat Status -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#0F2F5F]"></span>
                <span>Kronologi Perkembangan Pengajuan (Audit Trail)</span>
            </h2>

            <div class="space-y-3 relative pl-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @foreach($pengajuan->riwayatStatuses as $riwayat)
                    <div class="relative text-xs space-y-0.5">
                        <span class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-[#155EAA] border-2 border-white shadow-xs"></span>
                        <div class="font-bold text-slate-800">
                            {{ $riwayat->keterangan }}
                        </div>
                        <div class="text-[11px] text-slate-400">
                            {{ $riwayat->created_at->translatedFormat('d M Y, H:i') }} WIB • Petugas / Sistem
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tombol Batalkan Pengajuan -->
        @if($pengajuan->canBeCancelled())
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="button" 
                        @click="openBatalModal = true" 
                        class="px-4 py-2 bg-slate-100 hover:bg-rose-50 text-rose-600 font-bold rounded-xl text-xs transition-colors">
                    Batalkan Permohonan Ini
                </button>
            </div>
        @endif
    </div>

    <!-- MODAL BATALKAN PENGAJUAN -->
    @if($pengajuan->canBeCancelled())
        <div x-show="openBatalModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="openBatalModal = false">
                <h3 class="text-base font-bold text-slate-900">Konfirmasi Pembatalan Pengajuan</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Apakah Anda yakin ingin membatalkan permohonan <strong>{{ $pengajuan->nomor_pengajuan }}</strong>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form method="POST" action="{{ route('dashboard.bantuan.batal', $pengajuan->id) }}" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Alasan Pembatalan *</label>
                        <textarea name="alasan_batal" rows="2" required placeholder="Tuliskan alasan pembatalan singkat..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="openBatalModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-semibold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md">
                            Ya, Batalkan Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
