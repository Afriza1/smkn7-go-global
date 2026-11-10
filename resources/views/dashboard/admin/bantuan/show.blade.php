@extends('dashboard.layouts.dashboard')

@section('title', 'Pemeriksaan Pengajuan Bantuan: ' . $pengajuan->nomor_pengajuan)

@section('content')
<div class="space-y-6" x-data="{ 
    openPerbaikanModal: false, 
    openTeruskanModal: false, 
    openProsesMitraModal: false, 
    openPutusanModal: false 
}">
    <!-- Top Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.admin.bantuan.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#155EAA] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Antrean Pengajuan</span>
        </a>

        @if($pengajuan->programBantuan->is_demo)
            <span class="px-2.5 py-0.5 text-xs font-extrabold uppercase bg-amber-100 text-amber-800 rounded-md border border-amber-200">
                DATA DEMO
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
    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm space-y-1">
            <div class="font-bold">Terjadi kesalahan:</div>
            <ul class="list-disc pl-5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $badge = $pengajuan->badgeStatus();
    @endphp

    <!-- Detail Box -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
        <!-- Header Registrasi -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="text-xs font-mono font-bold text-slate-400">Nomor Registrasi Pengajuan</span>
                <h1 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ $pengajuan->nomor_pengajuan }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Tercatat sistem pada {{ $pengajuan->created_at->translatedFormat('l, d F Y - H:i') }} WIB
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold border {{ $badge['class'] }}">
                    <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                    <span>{{ $badge['label'] }}</span>
                </span>
            </div>
        </div>

        <!-- Macro Info Strip -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block font-medium">Pemohon Bantuan</span>
                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $pengajuan->user->name }}</span>
                <span class="text-slate-500 text-[11px]">{{ $pengajuan->user->email }} ({{ $pengajuan->user->getRoleDisplayName() }})</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Program Kemitraan</span>
                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $pengajuan->programBantuan->nama_program }}</span>
                <span class="text-slate-500 text-[11px]">Mitra: <strong class="text-[#166534]">{{ $pengajuan->programBantuan->nama_mitra }}</strong></span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Verifikasi Berkas Sekolah</span>
                <div class="mt-0.5">
                    @if($pengajuan->diverifikasi_pada)
                        <span class="font-bold text-blue-700 text-xs block">
                            ✓ Lolos Verifikasi Sekolah
                        </span>
                        <span class="text-slate-400 text-[10px]">{{ $pengajuan->diverifikasi_pada->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="text-amber-600 font-bold text-xs">Belum diverifikasi</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Uraian Alasan Pemohon -->
        <div class="p-4 bg-white rounded-xl border border-slate-200 space-y-2 text-xs">
            <span class="font-bold text-slate-800 uppercase tracking-wider text-[11px] block">Alasan Pengajuan oleh Pemohon:</span>
            <p class="text-slate-700 leading-relaxed italic bg-slate-50 p-3 rounded-lg">
                "{{ $pengajuan->alasan_pengajuan }}"
            </p>
            @if($pengajuan->informasi_tambahan)
                <div class="text-slate-600 pt-1 text-[11px]">
                    <strong>Informasi Tambahan:</strong> {{ $pengajuan->informasi_tambahan }}
                </div>
            @endif
        </div>

        <!-- Berkas Persyaratan Terlampir (Private Download) -->
        <div class="space-y-3">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                    <span>Dokumen Persyaratan yang Diunggah Pemohon</span>
                </span>
                <span class="text-[11px] font-normal text-slate-400">Tersimpan di Penyimpanan Privat Terenkripsi</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @forelse($pengajuan->dokumens as $dok)
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-[#155EAA] flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 truncate">{{ $dok->nama_dokumen }}</div>
                                <div class="text-[11px] text-slate-500 truncate">{{ $dok->file_name }} ({{ $dok->formatted_size }})</div>
                            </div>
                        </div>

                        <a href="{{ route('dashboard.bantuan.dokumen.unduh', $dok->id) }}" 
                           target="_blank"
                           class="px-3 py-1.5 bg-[#0F2F5F] hover:bg-[#155EAA] text-white font-bold rounded-lg text-xs flex-shrink-0 transition-colors">
                            Buka Berkas
                        </a>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-400 text-xs col-span-2">
                        Tidak ada berkas yang ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- PANEL AKSI STATUS RESMI (ADMIN BKK) -->
        <div class="pt-6 border-t border-slate-200 space-y-4">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                <span>Alur Tindak Lanjut & Keputusan Admin BKK</span>
            </h2>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- 1. Aksi Verifikasi Sekolah -->
                @if(in_array($pengajuan->status, [\App\Models\PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN, \App\Models\PengajuanBantuan::STATUS_PERLU_PERBAIKAN]))
                    <form method="POST" action="{{ route('dashboard.admin.bantuan.verifikasi', $pengajuan->id) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                            ✓ Loloskan Verifikasi Sekolah
                        </button>
                    </form>
                @endif

                <!-- 2. Aksi Minta Perbaikan -->
                @if(in_array($pengajuan->status, [\App\Models\PengajuanBantuan::STATUS_MENUNGGU_PEMERIKSAAN, \App\Models\PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH]))
                    <button type="button" 
                            @click="openPerbaikanModal = true" 
                            class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs border border-rose-200 transition-all">
                        Minta Perbaikan Berkas
                    </button>
                @endif

                <!-- 3. Aksi Teruskan ke Mitra -->
                @if($pengajuan->status === \App\Models\PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH)
                    <button type="button" 
                            @click="openTeruskanModal = true" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                        Teruskan Berkas ke Mitra Kemitraan
                    </button>
                @endif

                <!-- 4. Aksi Update Proses Mitra -->
                @if(in_array($pengajuan->status, [\App\Models\PengajuanBantuan::STATUS_DITERUSKAN_MITRA, \App\Models\PengajuanBantuan::STATUS_SEDANG_DIPROSES_MITRA]))
                    <button type="button" 
                            @click="openProsesMitraModal = true" 
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                        Update Perkembangan Mitra
                    </button>
                @endif

                <!-- 5. Aksi Keputusan Akhir Mitra -->
                @if(in_array($pengajuan->status, [\App\Models\PengajuanBantuan::STATUS_DITERUSKAN_MITRA, \App\Models\PengajuanBantuan::STATUS_SEDANG_DIPROSES_MITRA, \App\Models\PengajuanBantuan::STATUS_TERVERIFIKASI_SEKOLAH]))
                    <button type="button" 
                            @click="openPutusanModal = true" 
                            class="px-4 py-2 bg-[#166534] hover:bg-[#14532d] text-white font-bold rounded-xl text-xs shadow-sm transition-all">
                        Tetapkan Keputusan Akhir Mitra
                    </button>
                @endif
            </div>
        </div>

        <!-- PANEL CATATAN INTERNAL KOORDINASI ADMIN BKK DENGAN MITRA (PRIVAT RAHASIA) -->
        <div class="pt-6 border-t border-slate-200 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Catatan Internal Koordinasi Admin BKK & Mitra (Luar Website)</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Privat & Rahasia: Catatan ini hanya dapat dibaca oleh staf Admin BKK SMKN 7 Jember dan tidak pernah tampil pada antarmuka siswa/alumni.
                    </p>
                </div>
            </div>

            <!-- Form Tambah Catatan Koordinasi Internal -->
            <form method="POST" action="{{ route('dashboard.admin.bantuan.catatan-internal', $pengajuan->id) }}" class="p-4 bg-amber-50/50 rounded-2xl border border-amber-200/80 space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-800 block mb-1">Tanggal Koordinasi *</label>
                        <input type="date" name="tanggal_koordinasi" required value="{{ date('Y-m-d') }}" class="w-full py-2 px-3 rounded-xl border border-slate-200 bg-white">
                    </div>
                    <div>
                        <label class="font-bold text-slate-800 block mb-1">Pihak Mitra yang Dihubungi / Ditemui *</label>
                        <input type="text" name="pihak_mitra" required placeholder="Contoh: Bpk. Ahmad (BMT NU Tanggul)" class="w-full py-2 px-3 rounded-xl border border-slate-200 bg-white">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-800 block mb-1">Catatan Hasil Koordinasi Internal *</label>
                    <textarea name="catatan_koordinasi" rows="2" required placeholder="Tuliskan perkembangan negosiasi, syarat tambahan dari analis, atau catatan internal..." class="w-full py-2 px-3 rounded-xl border border-slate-200 bg-white"></textarea>
                </div>

                <div class="flex items-center justify-between gap-3 pt-1">
                    <input type="text" name="keputusan_mitra" placeholder="Keputusan sementara mitra jika ada (Opsional)" class="w-full py-2 px-3 rounded-xl border border-slate-200 bg-white">
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl text-xs flex-shrink-0 shadow-sm">
                        Simpan Catatan Internal
                    </button>
                </div>
            </form>

            <!-- Daftar Riwayat Catatan Internal -->
            <div class="space-y-2.5">
                @forelse($pengajuan->catatanInternals as $catatan)
                    <div class="p-3.5 bg-white rounded-xl border border-slate-200 text-xs space-y-1 shadow-xs">
                        <div class="flex items-center justify-between font-bold text-slate-800">
                            <span>Koordinasi dengan: {{ $catatan->pihak_mitra }}</span>
                            <span class="text-slate-400 font-normal text-[11px]">{{ $catatan->tanggal_koordinasi->format('d M Y') }} • {{ $catatan->admin->name }}</span>
                        </div>
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $catatan->catatan_koordinasi }}</p>
                        @if($catatan->keputusan_mitra)
                            <div class="text-[11px] text-amber-700 font-semibold pt-1">
                                Hasil / Keputusan: {{ $catatan->keputusan_mitra }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-xs text-slate-400 p-2 text-center">
                        Belum ada catatan internal koordinasi mitra untuk pengajuan ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Audit Trail Timeline Riwayat Status -->
        <div class="space-y-4 pt-6 border-t border-slate-100">
            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#0F2F5F]"></span>
                <span>Audit Trail Perubahan Status Pengajuan</span>
            </h2>

            <div class="space-y-3 relative pl-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @foreach($pengajuan->riwayatStatuses as $riwayat)
                    <div class="relative text-xs space-y-0.5">
                        <span class="absolute -left-6 top-1 w-2.5 h-2.5 rounded-full bg-[#155EAA] border-2 border-white shadow-xs"></span>
                        <div class="font-bold text-slate-800">
                            {{ $riwayat->keterangan }}
                        </div>
                        <div class="text-[11px] text-slate-400">
                            {{ $riwayat->created_at->translatedFormat('d M Y, H:i') }} WIB • Oleh: {{ $riwayat->user->name }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- MODAL MINTA PERBAIKAN -->
    <div x-show="openPerbaikanModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="openPerbaikanModal = false">
            <h3 class="text-base font-bold text-slate-900">Minta Perbaikan Dokumen kepada Pemohon</h3>
            <form method="POST" action="{{ route('dashboard.admin.bantuan.perbaikan', $pengajuan->id) }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Rincian Berkas yang Harus Diperbaiki *</label>
                    <textarea name="catatan_perbaikan" rows="3" required placeholder="Contoh: Scan Kartu Keluarga buram dan tidak terbaca. Mohon unggah ulang dengan foto/scan yang jelas." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="openPerbaikanModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-md">Kirim Permintaan Perbaikan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TERUSKAN KE MITRA -->
    <div x-show="openTeruskanModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="openTeruskanModal = false">
            <h3 class="text-base font-bold text-slate-900">Teruskan Berkas ke Pihak Mitra</h3>
            <form method="POST" action="{{ route('dashboard.admin.bantuan.teruskan', $pengajuan->id) }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Kontak / Staf Mitra</label>
                    <input type="text" name="nama_kontak_mitra" placeholder="Contoh: Bapak Ahmad Fauzi (BMT NU Tanggul)" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Pemberitahuan untuk Pemohon (Opsional)</label>
                    <textarea name="keterangan_terusan" rows="2" placeholder="Pesan resmi yang akan tampil di status siswa..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="openTeruskanModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md">Konfirmasi Teruskan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL UPDATE PERKEMBANGAN MITRA -->
    <div x-show="openProsesMitraModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="openProsesMitraModal = false">
            <h3 class="text-base font-bold text-slate-900">Update Perkembangan Telaah Mitra</h3>
            <form method="POST" action="{{ route('dashboard.admin.bantuan.proses-mitra', $pengajuan->id) }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Keterangan Perkembangan untuk Pemohon *</label>
                    <textarea name="keterangan" rows="3" required placeholder="Contoh: Berkas Anda sedang dalam proses verifikasi komite pembiayaan mitra..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="openProsesMitraModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-md">Update Perkembangan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KEPUTUSAN AKHIR MITRA -->
    <div x-show="openPutusanModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="openPutusanModal = false">
            <h3 class="text-base font-bold text-slate-900">Tetapkan Keputusan Akhir Mitra</h3>
            <form method="POST" action="{{ route('dashboard.admin.bantuan.putusan', $pengajuan->id) }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Keputusan Status *</label>
                    <select name="status_putusan" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        <option value="disetujui">DISETUJUI oleh Mitra</option>
                        <option value="ditolak">DITOLAK oleh Mitra / Sekolah</option>
                        <option value="selesai">SELESAI / Dana Telah Tersalurkan</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Keterangan Resmi untuk Pemohon *</label>
                    <textarea name="keterangan" rows="3" required placeholder="Tuliskan rincian persetujuan atau alasan jika ditolak..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="openPutusanModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-[#166534] hover:bg-[#14532d] text-white font-bold rounded-xl shadow-md">Simpan Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
