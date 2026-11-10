@extends('dashboard.layouts.dashboard')

@section('title', 'Manajemen Direktori Alumni Global & Komunitas')

@section('content')
<div class="space-y-8" x-data="{ openAddModal: false, editAlumni: null, openKomunitasModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-[#155EAA] rounded-full text-xs font-semibold mb-2">
                <span>Panel Pengelolaan Admin BKK</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900">Manajemen Alumni Global & Komunitas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola profil alumni SMKN 7 Jember di luar negeri, status mentor bimbingan, izin kontak, dan pengaturan tautan grup komunitas WhatsApp.
            </p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <button @click="openKomunitasModal = true" 
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                <span>Kelola Grup WhatsApp</span>
            </button>

            <button @click="openAddModal = true" 
                    type="button"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold rounded-xl shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Data Alumni</span>
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
    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm space-y-1">
            <div class="font-bold">Terjadi kesalahan pada data yang diisi:</div>
            <ul class="list-disc pl-5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Data Alumni</span>
            <div class="text-2xl font-black text-slate-900 mt-2">{{ $stats['total'] }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Tercatat di sistem Go Global</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Mentor Aktif</span>
            <div class="text-2xl font-black text-amber-600 mt-2">{{ $stats['mentor'] }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Siap berbagi pengalaman</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Kontak Dipublikasikan</span>
            <div class="text-2xl font-black text-emerald-600 mt-2">{{ $stats['kontak_publik'] }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Disetujui oleh alumni</span>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Status Publikasi</span>
            <div class="text-2xl font-black text-[#155EAA] mt-2">{{ $stats['aktif'] }}</div>
            <span class="text-[11px] text-slate-400 mt-1 block">Tampil di portal siswa/alumni</span>
        </div>
    </div>

    <!-- Status Komunitas WhatsApp Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl @if($komunitas->is_aktif && !empty($komunitas->link_whatsapp)) bg-emerald-100 text-emerald-700 @else bg-slate-100 text-slate-400 @endif flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            </div>
            <div>
                <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                    <span>Status Komunitas WhatsApp:</span>
                    @if($komunitas->is_aktif && !empty($komunitas->link_whatsapp))
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif Terhubung</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Tautan Non-Aktif</span>
                    @endif
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5">
                    {{ $komunitas->link_whatsapp ?: 'Belum ada URL tautan grup yang dikonfigurasi.' }}
                </div>
            </div>
        </div>
        <button @click="openKomunitasModal = true" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
            Edit Tautan & Tata Tertib
        </button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form method="GET" action="{{ route('dashboard.admin.alumni.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari nama alumni, jurusan, kota, atau perusahaan..."
                   class="py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#155EAA]">

            <select name="mentor" class="py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#155EAA]">
                <option value="">Semua Peran Mentor</option>
                <option value="1" @selected(request('mentor') === '1')>Hanya Mentor Aktif</option>
                <option value="0" @selected(request('mentor') === '0')>Bukan Mentor</option>
            </select>

            <div class="flex items-center gap-2">
                <select name="status" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#155EAA]">
                    <option value="">Semua Status Publikasi</option>
                    <option value="1" @selected(request('status') === '1')>Aktif / Dipublikasikan</option>
                    <option value="0" @selected(request('status') === '0')>Non-Aktif</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold rounded-xl shadow-sm">
                    Cari
                </button>
                @if(request()->hasAny(['search', 'mentor', 'status']))
                    <a href="{{ route('dashboard.admin.alumni.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Alumni -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-4">Alumni</th>
                        <th class="py-3.5 px-4">Kelulusan & Jurusan</th>
                        <th class="py-3.5 px-4">Penempatan & Bidang</th>
                        <th class="py-3.5 px-4 text-center">Status Mentor</th>
                        <th class="py-3.5 px-4 text-center">Izin Kontak</th>
                        <th class="py-3.5 px-4 text-center">Publikasi</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($alumnis as $alumni)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $alumni->foto_url }}" alt="{{ $alumni->nama_lengkap }}" class="w-9 h-9 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate flex items-center gap-1.5">
                                            <span>{{ $alumni->nama_lengkap }}</span>
                                            @if($alumni->is_demo)
                                                <span class="px-1.5 py-0.2 bg-amber-100 text-amber-800 rounded text-[9px] font-extrabold uppercase">Demo</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $alumni->nama_perusahaan ?: 'Perusahaan Industri' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $alumni->tahun_kelulusan }}</div>
                                <div class="text-[11px] text-slate-500">{{ $alumni->jurusan }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $alumni->negara_bekerja }} @if($alumni->kota_wilayah) ({{ $alumni->kota_wilayah }}) @endif</div>
                                <div class="text-[11px] text-slate-500">{{ $alumni->bidang_pekerjaan }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('dashboard.admin.alumni.toggle-mentor', $alumni->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold transition-all @if($alumni->is_mentor) bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 @else bg-slate-100 text-slate-500 hover:bg-slate-200 @endif">
                                        @if($alumni->is_mentor) ★ Mentor @else Bukan Mentor @endif
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($alumni->is_demo)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">DEMO — Kontak Privat</span>
                                @elseif($alumni->hasPublicContact())
                                    <form method="POST" action="{{ route('dashboard.admin.alumni.toggle-kontak', $alumni->id) }}" onsubmit="return confirm('Cabut persetujuan publikasi dan hapus nomor serta email kontak ini?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-rose-50 hover:text-rose-700">
                                            Cabut Persetujuan
                                        </button>
                                    </form>
                                @elseif($alumni->kontak_disetujui)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700">Perlu Konfirmasi Ulang</span>
                                    <span class="block mt-1 text-[10px] text-slate-400">Masukkan kembali kontak dan konfirmasi persetujuan baru.</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">Privat</span>
                                    <span class="block mt-1 text-[10px] text-slate-400">Aktifkan melalui Edit setelah persetujuan baru.</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <form method="POST" action="{{ route('dashboard.admin.alumni.toggle-aktif', $alumni->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold transition-all @if($alumni->is_aktif) bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 @else bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 @endif">
                                        @if($alumni->is_aktif) Aktif @else Arsip @endif
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            @click="editAlumni = {{ json_encode(array_merge($alumni->toArray(), ['nomor_kontak' => $alumni->hasPublicContact() ? $alumni->nomor_kontak : null, 'email_kontak' => $alumni->hasPublicContact() ? $alumni->email_kontak : null])) }}" 
                                            class="p-1.5 text-slate-500 hover:text-[#155EAA] hover:bg-slate-100 rounded-lg transition-colors"
                                            title="Edit Profil">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <form method="POST" action="{{ route('dashboard.admin.alumni.destroy', $alumni->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data alumni ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Alumni">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada data alumni yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100">
            {{ $alumnis->links() }}
        </div>
    </div>

    <!-- MODAL TAMBAH ALUMNI -->
    <div x-show="openAddModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 my-8" @click.away="openAddModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Tambah Data Profil Alumni Baru</h3>
                <button @click="openAddModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('dashboard.admin.alumni.store') }}" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Lengkap Alumni *</label>
                        <input type="text" name="nama_lengkap" required placeholder="Contoh: Budi Santoso" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tahun Kelulusan *</label>
                        <input type="number" name="tahun_kelulusan" required value="{{ date('Y')-1 }}" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Jurusan Asal *</label>
                        <input type="text" name="jurusan" required placeholder="Teknik Komputer dan Jaringan" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Negara Tempat Bekerja *</label>
                        <input type="text" name="negara_bekerja" required value="Jepang" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Kota / Wilayah Penempatan</label>
                        <input type="text" name="kota_wilayah" placeholder="Contoh: Toyota-shi, Aichi" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Bidang Industri / Pekerjaan *</label>
                        <input type="text" name="bidang_pekerjaan" required placeholder="Contoh: Manufaktur & Otomasi" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Perusahaan di Jepang</label>
                        <input type="text" name="nama_perusahaan" placeholder="Nama Perusahaan / Pabrik" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Masa Pengalaman Bekerja</label>
                        <input type="text" name="lama_bekerja" placeholder="Contoh: 2 Tahun 4 Bulan" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Bahasa Dikuasai / Sertifikasi</label>
                        <input type="text" name="bahasa_dikuasai" placeholder="Contoh: Bahasa Jepang (JLPT N3)" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nomor Kontak WhatsApp (Opsional)</label>
                        <input type="text" name="nomor_kontak" placeholder="Contoh: +81 80 1234 5678" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Email Kontak (Opsional)</label>
                        <input type="email" name="email_kontak" placeholder="alamat@email.com" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Pengalaman Singkat (1-2 Kalimat) *</label>
                    <textarea name="pengalaman_kerja_singkat" rows="2" required placeholder="Ringkasan tugas dan divisi pekerjaan di Jepang..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Deskripsi Pengalaman Lengkap (Opsional)</label>
                    <textarea name="deskripsi_pengalaman" rows="3" placeholder="Cerita adaptasi kerja, lingkungan pabrik, dll..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tips & Kiat untuk Adik Kelas (Opsional)</label>
                    <textarea name="tips_karier" rows="2" placeholder="Saran persiapan fisik, bahasa, kedisiplinan 5S..." class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                </div>

                <!-- Opsi Checkbox -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3 bg-slate-50 rounded-xl">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_mentor" value="1" class="rounded text-[#155EAA]">
                        <span class="font-semibold text-slate-700">Tetapkan sebagai Mentor</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="kontak_disetujui" value="1" class="rounded text-emerald-600">
                        <span class="font-semibold text-slate-700">Publikasikan kontak setelah persetujuan alumni</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_demo" value="1" class="rounded text-amber-600">
                        <span class="font-semibold text-slate-700">Tandai Data DEMO</span>
                    </label>
                </div>
                <label class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-slate-700">
                    <input type="checkbox" name="konfirmasi_persetujuan_alumni" value="1" class="mt-0.5 rounded text-emerald-600">
                    <span>Saya telah memperoleh persetujuan eksplisit alumni untuk mempublikasikan kontak yang dimasukkan. Persetujuan ini wajib untuk aktivasi; data DEMO tidak dapat dipublikasikan.</span>
                </label>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#0F2F5F] hover:bg-[#155EAA] text-white font-bold rounded-xl shadow-md">
                        Simpan Alumni
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT ALUMNI -->
    <template x-if="editAlumni">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 my-8" @click.away="editAlumni = null">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Edit Profil Alumni: <span x-text="editAlumni.nama_lengkap"></span></h3>
                    <button @click="editAlumni = null" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>

                <form method="POST" :action="`/dashboard/admin/alumni-global/${editAlumni.id}`" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Nama Lengkap Alumni *</label>
                            <input type="text" name="nama_lengkap" x-model="editAlumni.nama_lengkap" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Tahun Kelulusan *</label>
                            <input type="number" name="tahun_kelulusan" x-model="editAlumni.tahun_kelulusan" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Jurusan Asal *</label>
                            <input type="text" name="jurusan" x-model="editAlumni.jurusan" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Negara Tempat Bekerja *</label>
                            <input type="text" name="negara_bekerja" x-model="editAlumni.negara_bekerja" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Kota / Wilayah</label>
                            <input type="text" name="kota_wilayah" x-model="editAlumni.kota_wilayah" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Bidang Industri *</label>
                            <input type="text" name="bidang_pekerjaan" x-model="editAlumni.bidang_pekerjaan" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Perusahaan di Jepang</label>
                            <input type="text" name="nama_perusahaan" x-model="editAlumni.nama_perusahaan" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Masa Pengalaman Bekerja</label>
                            <input type="text" name="lama_bekerja" x-model="editAlumni.lama_bekerja" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Bahasa Dikuasai</label>
                            <input type="text" name="bahasa_dikuasai" x-model="editAlumni.bahasa_dikuasai" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Nomor WhatsApp</label>
                            <input type="text" name="nomor_kontak" x-model="editAlumni.nomor_kontak" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                        <div>
                            <label class="font-bold text-slate-700 block mb-1">Email Kontak</label>
                            <input type="email" name="email_kontak" x-model="editAlumni.email_kontak" class="w-full py-2 px-3 rounded-xl border border-slate-200">
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Pengalaman Singkat *</label>
                        <textarea name="pengalaman_kerja_singkat" x-model="editAlumni.pengalaman_kerja_singkat" rows="2" required class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Deskripsi Pengalaman Lengkap</label>
                        <textarea name="deskripsi_pengalaman" x-model="editAlumni.deskripsi_pengalaman" rows="3" class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tips untuk Adik Kelas</label>
                        <textarea name="tips_karier" x-model="editAlumni.tips_karier" rows="2" class="w-full py-2 px-3 rounded-xl border border-slate-200"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3 bg-slate-50 rounded-xl">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_mentor" value="1" :checked="editAlumni.is_mentor" class="rounded text-[#155EAA]">
                            <span class="font-semibold text-slate-700">Status Mentor</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="kontak_disetujui" value="1" :checked="!editAlumni.is_demo && editAlumni.kontak_disetujui && !!editAlumni.kontak_disetujui_pada && !!editAlumni.kontak_disetujui_oleh" class="rounded text-emerald-600">
                            <span class="font-semibold text-slate-700">Publikasikan kontak setelah persetujuan alumni</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_demo" value="1" :checked="editAlumni.is_demo" class="rounded text-amber-600">
                            <span class="font-semibold text-slate-700">Data DEMO</span>
                        </label>
                    </div>
                    <label class="flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-slate-700">
                        <input type="checkbox" name="konfirmasi_persetujuan_alumni" value="1" class="mt-0.5 rounded text-emerald-600">
                        <span>Saya telah memperoleh persetujuan baru yang eksplisit dari alumni untuk mempublikasikan kontak yang dimasukkan kembali. Wajib saat aktivasi ulang; data DEMO tetap privat.</span>
                    </label>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="editAlumni = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#0F2F5F] hover:bg-[#155EAA] text-white font-bold rounded-xl shadow-md">
                            Perbarui Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- MODAL PENGATURAN KOMUNITAS WHATSAPP -->
    <div x-show="openKomunitasModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 my-8" @click.away="openKomunitasModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Pengaturan Akses Komunitas WhatsApp Alumni</h3>
                <button @click="openKomunitasModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('dashboard.admin.alumni.komunitas') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nama Grup / Komunitas *</label>
                    <input type="text" name="nama_komunitas" value="{{ $komunitas->nama_komunitas }}" required class="w-full py-2 px-3 rounded-xl border border-slate-200">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tautan / Link Invite WhatsApp Resmi</label>
                    <input type="url" name="link_whatsapp" value="{{ $komunitas->link_whatsapp }}" placeholder="https://chat.whatsapp.com/..." class="w-full py-2 px-3 rounded-xl border border-slate-200">
                    <p class="text-[11px] text-slate-400 mt-1">Hanya siswa dan alumni login yang dapat melihat dan mengklik tombol gabung.</p>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Deskripsi Singkat Komunitas *</label>
                    <textarea name="deskripsi" rows="2" required class="w-full py-2 px-3 rounded-xl border border-slate-200">{{ $komunitas->deskripsi }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Tujuan Komunitas *</label>
                    <textarea name="tujuan_komunitas" rows="3" required class="w-full py-2 px-3 rounded-xl border border-slate-200">{{ $komunitas->tujuan_komunitas }}</textarea>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Aturan & Tata Tertib (1 baris per aturan) *</label>
                    <textarea name="aturan_komunitas" rows="3" required class="w-full py-2 px-3 rounded-xl border border-slate-200">{{ $komunitas->aturan_komunitas }}</textarea>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_aktif" value="1" @checked($komunitas->is_aktif) class="rounded text-[#166534]">
                        <span class="font-bold text-slate-800">Aktifkan Tautan (Buka akses join untuk siswa/alumni)</span>
                    </label>
                    <p class="text-[11px] text-slate-500 mt-1 pl-6">Jika tidak dicentang, siswa akan melihat keterangan bahwa tautan komunitas belum tersedia.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="openKomunitasModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-[#166534] hover:bg-[#14532d] text-white font-bold rounded-xl shadow-md">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
