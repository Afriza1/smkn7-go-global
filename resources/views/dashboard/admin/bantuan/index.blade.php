@extends('dashboard.layouts.dashboard')

@section('title', 'Manajemen Kolaborasi & Bantuan Sosial (BKK)')

@section('content')
<div class="space-y-8" x-data="{ activeTab: '{{ request('tab', 'pengajuan') }}', openAddProgramModal: false, editProgram: null }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-[#166534] rounded-full text-xs font-semibold mb-2">
                <span>Panel Pengelolaan Admin BKK</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900">Manajemen Kolaborasi & Bantuan Sosial</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pusat verifikasi berkas permohonan bantuan/pembiayaan, koordinasi tindak lanjut dengan mitra di luar website, dan pengelolaan program kemitraan.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button @click="openAddProgramModal = true" 
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold rounded-xl shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Program Bantuan Baru</span>
            </button>
        </div>
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
            <div class="font-bold">Terjadi kesalahan input:</div>
            <ul class="list-disc pl-5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Pengajuan</span>
            <div class="text-xl font-black text-slate-900 mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block">Menunggu Cek</span>
            <div class="text-xl font-black text-amber-600 mt-1">{{ $stats['menunggu'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider block">Perlu Perbaikan</span>
            <div class="text-xl font-black text-rose-600 mt-1">{{ $stats['perbaikan'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider block">Terverifikasi</span>
            <div class="text-xl font-black text-blue-600 mt-1">{{ $stats['terverifikasi'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block">Di Mitra</span>
            <div class="text-xl font-black text-indigo-600 mt-1">{{ $stats['diteruskan'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block">Disetujui</span>
            <div class="text-xl font-black text-emerald-600 mt-1">{{ $stats['disetujui'] }}</div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-3 border-b border-slate-200 pb-2">
        <button @click="activeTab = 'pengajuan'" 
                type="button"
                :class="activeTab === 'pengajuan' ? 'bg-[#0F2F5F] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <span>Antrean Permohonan Bantuan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 font-extrabold">{{ $stats['total'] }}</span>
        </button>

        <button @click="activeTab = 'program'" 
                type="button"
                :class="activeTab === 'program' ? 'bg-[#0F2F5F] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
            <span>Kelola Program & Mitra Kemitraan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200 text-slate-800 font-extrabold">{{ $programs->count() }}</span>
        </button>
    </div>

    <!-- TAB 1: ANTREAN PENGAJUAN -->
    <div x-show="activeTab === 'pengajuan'" class="space-y-4">
        <!-- Filter Bar -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
            <form method="GET" action="{{ route('dashboard.admin.bantuan.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="hidden" name="tab" value="pengajuan">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nomor pengajuan atau nama siswa/alumni..."
                       class="py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#155EAA]">

                <select name="status" class="py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#155EAA]">
                    <option value="">Semua Status Pengajuan</option>
                    <option value="menunggu_pemeriksaan" @selected(request('status') === 'menunggu_pemeriksaan')>Menunggu Pemeriksaan</option>
                    <option value="perlu_perbaikan" @selected(request('status') === 'perlu_perbaikan')>Perlu Perbaikan</option>
                    <option value="terverifikasi_sekolah" @selected(request('status') === 'terverifikasi_sekolah')>Terverifikasi Sekolah</option>
                    <option value="diteruskan_mitra" @selected(request('status') === 'diteruskan_mitra')>Diteruskan ke Mitra</option>
                    <option value="sedang_diproses_mitra" @selected(request('status') === 'sedang_diproses_mitra')>Sedang Diproses Mitra</option>
                    <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui Mitra</option>
                    <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                    <option value="selesai" @selected(request('status') === 'selesai')>Selesai / Tersalurkan</option>
                    <option value="dibatalkan" @selected(request('status') === 'dibatalkan')>Dibatalkan Pemohon</option>
                </select>

                <div class="flex items-center gap-2">
                    <select name="program_id" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#155EAA]">
                        <option value="">Semua Program Bantuan</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}" @selected(request('program_id') == $prog->id)>{{ $prog->nama_program }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold rounded-xl shadow-sm">
                        Cari
                    </button>
                    @if(request()->hasAny(['search', 'status', 'program_id']))
                        <a href="{{ route('dashboard.admin.bantuan.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Antrean -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4">Nomor & Tanggal</th>
                            <th class="py-3.5 px-4">Nama Pemohon</th>
                            <th class="py-3.5 px-4">Program & Mitra</th>
                            <th class="py-3.5 px-4">Berkas</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($pengajuans as $item)
                            @php
                                $badge = $item->badgeStatus();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 font-mono">{{ $item->nomor_pengajuan }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $item->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $item->user->name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $item->user->email }} ({{ $item->user->getRoleDisplayName() }})</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $item->programBantuan->nama_program }}</div>
                                    <div class="text-[11px] text-slate-500">Mitra: {{ $item->programBantuan->nama_mitra }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                        {{ $item->dokumens->count() }} Dokumen
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['class'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('dashboard.admin.bantuan.show', $item->id) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#0F2F5F] hover:bg-[#155EAA] text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                                        <span>Periksa Berkas</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Tidak ada data permohonan yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $pengajuans->links() }}
            </div>
        </div>
    </div>

    <!-- TAB 2: KELOLA PROGRAM BANTUAN -->
    <div x-show="activeTab === 'program'" class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4">Nama Program & Mitra</th>
                            <th class="py-3.5 px-4">Jenis Skema</th>
                            <th class="py-3.5 px-4">Periode Berlaku</th>
                            <th class="py-3.5 px-4 text-center">Total Pemohon</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($programs as $prog)
                            @php
                                $badge = $prog->badgeJenis();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <span>{{ $prog->nama_program }}</span>
                                        @if($prog->is_demo)
                                            <span class="px-1.5 py-0.2 bg-amber-100 text-amber-800 rounded text-[9px] font-extrabold uppercase">Demo</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500">Mitra: <strong>{{ $prog->nama_mitra }}</strong></div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($prog->periode_mulai && $prog->periode_selesai)
                                        <div class="font-medium text-slate-800">{{ $prog->periode_mulai->format('d/m/Y') }} - {{ $prog->periode_selesai->format('d/m/Y') }}</div>
                                    @else
                                        <span class="text-slate-400">Tidak dibatasi</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-bold text-slate-800">{{ $prog->pengajuans_count }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <form method="POST" action="{{ route('dashboard.admin.bantuan.program.toggle', $prog->id) }}">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold @if($prog->is_aktif) bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 @else bg-slate-100 text-slate-500 hover:bg-slate-200 @endif">
                                            @if($prog->is_aktif) Buka @else Non-Aktif @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button type="button" 
                                            @click="editProgram = {{ json_encode($prog) }}" 
                                            class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH PROGRAM -->
    <div x-show="openAddProgramModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 my-8" @click.away="openAddProgramModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Tambah Program Bantuan Baru</h3>
                <button @click="openAddProgramModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('dashboard.admin.bantuan.program.store') }}" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="font-bold text-slate-700 block mb-1">Nama Program Bantuan *</label>
                        <input type="text" name="nama_program" required placeholder="Contoh: Program Dana Talangan Keberangkatan BMT NU Tanggul" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Mitra Kerjasama *</label>
                        <input type="text" name="nama_mitra" required placeholder="Contoh: BMT NU Tanggul" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Jenis Program / Skema *</label>
                        <select name="jenis_program" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                            <option value="dana_talangan">Dana Talangan Keberangkatan</option>
                            <option value="beasiswa_pelatihan">Beasiswa Pelatihan Bahasa & FMD</option>
                            <option value="subsidi_keberangkatan">Subsidi MCU & Pembuatan Paspor</option>
                            <option value="pinjaman_lunak">Pembiayaan Lunak</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Periode Buka Pendaftaran</label>
                        <input type="date" name="periode_mulai" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Periode Tutup Pendaftaran</label>
                        <input type="date" name="periode_selesai" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Deskripsi Program *</label>
                    <textarea name="deskripsi" rows="2" required placeholder="Uraian manfaat dan cakupan program bantuan..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Persyaratan & Kriteria Calon Penerima *</label>
                    <textarea name="persyaratan" rows="3" required placeholder="Tuliskan poin-poin persyaratan..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Dokumen Persyaratan yang Wajib Diunggah (1 dokumen per baris) *</label>
                    <textarea name="dokumen_dibutuhkan_raw" rows="3" required placeholder="KTP Siswa / Alumni&#10;Kartu Keluarga (KK)&#10;Surat Izin Orang Tua&#10;Bukti Kelulusan Interview LPK" class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1" checked class="rounded text-[#166534]">
                        <span class="font-semibold text-slate-700">Aktifkan Program</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_demo" value="1" class="rounded text-amber-600">
                        <span class="font-semibold text-slate-700">Tandai Data DEMO</span>
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="openAddProgramModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#166534] hover:bg-[#14532d] text-white font-bold rounded-xl shadow-md">
                        Simpan Program Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PROGRAM -->
    <template x-if="editProgram">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 my-8" @click.away="editProgram = null">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Edit Program: <span x-text="editProgram.nama_program"></span></h3>
                    <button @click="editProgram = null" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" :action="`/dashboard/admin/bantuan/program/${editProgram.id}`" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="sm:col-span-2">
                            <label class="font-bold text-slate-700 block mb-1">Nama Program Bantuan *</label>
                            <input type="text" name="nama_program" x-model="editProgram.nama_program" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Nama Mitra *</label>
                            <input type="text" name="nama_mitra" x-model="editProgram.nama_mitra" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Jenis Program *</label>
                            <select name="jenis_program" x-model="editProgram.jenis_program" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                                <option value="dana_talangan">Dana Talangan Keberangkatan</option>
                                <option value="beasiswa_pelatihan">Beasiswa Pelatihan Bahasa & FMD</option>
                                <option value="subsidi_keberangkatan">Subsidi MCU & Pembuatan Paspor</option>
                                <option value="pinjaman_lunak">Pembiayaan Lunak</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Deskripsi *</label>
                        <textarea name="deskripsi" x-model="editProgram.deskripsi" rows="2" required class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Persyaratan *</label>
                        <textarea name="persyaratan" x-model="editProgram.persyaratan" rows="3" required class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Dokumen Wajib (1 per baris) *</label>
                        <textarea name="dokumen_dibutuhkan_raw" :value="Array.isArray(editProgram.dokumen_dibutuhkan) ? editProgram.dokumen_dibutuhkan.join('\n') : ''" rows="3" required class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_aktif" value="1" :checked="editProgram.is_aktif" class="rounded text-[#166534]">
                            <span class="font-semibold text-slate-700">Aktifkan Program</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_demo" value="1" :checked="editProgram.is_demo" class="rounded text-amber-600">
                            <span class="font-semibold text-slate-700">Data DEMO</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="editProgram = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#166534] hover:bg-[#14532d] text-white font-bold rounded-xl shadow-md">
                            Perbarui Program
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
