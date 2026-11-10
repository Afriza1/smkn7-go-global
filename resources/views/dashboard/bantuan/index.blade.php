@extends('dashboard.layouts.dashboard')

@section('title', 'Kolaborasi & Bantuan Sosial - SMKN 7 Jember Go Global')

@section('content')
    <div class="space-y-8" x-data="{ activeTab: '{{ request('tab', 'program') }}' }">
        <!-- Header & Hero Banner -->
        <div
            class="relative bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#166534] rounded-2xl p-6 sm:p-8 text-white shadow-xl overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10 max-w-3xl">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-[#FBBF24] border border-white/10 mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Dukungan Finansial & Beasiswa SMKN 7 Jember</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Kolaborasi & Bantuan Sosial</h1>
                <p class="mt-2 text-sm sm:text-base text-slate-200 leading-relaxed">
                    Pusat layanan informasi dan pengajuan program bantuan dana talangan keberangkatan, subsidi pelatihan
                    bahasa, dan beasiswa persiapan kerja luar negeri yang diselenggarakan melalui kerja sama BKK SMKN 7
                    Jember bersama mitra resmi.
                </p>
            </div>

            <!-- Quick Summary Ribbon -->
            <div
                class="mt-6 pt-6 border-t border-white/10 flex flex-wrap items-center gap-4 text-xs font-medium text-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#FBBF24]"></span>
                    <span>Proses Seleksi Resmi Sekolah & Mitra</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Penyimpanan Dokumen Pribadi Terenkripsi Privat</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-300"></span>
                    <span>Monitoring Perkembangan Real-Time</span>
                </div>
            </div>
        </div>

        <!-- Alert Notifikasi -->
        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('info'))
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tab Selector -->
        <div class="flex items-center gap-3 border-b border-slate-200 pb-2">
            <button @click="activeTab = 'program'" type="button"
                :class="activeTab === 'program' ? 'bg-[#0F2F5F] text-white shadow-sm' :
                    'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Daftar Program Bantuan Tersedia</span>
                <span
                    class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 font-extrabold">{{ $programs->count() }}</span>
            </button>

            <button @click="activeTab = 'riwayat'" type="button"
                :class="activeTab === 'riwayat' ? 'bg-[#0F2F5F] text-white shadow-sm' :
                    'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span>Riwayat Pengajuan Saya</span>
                @if ($pengajuans->count() > 0)
                    <span
                        class="px-2 py-0.5 rounded-full text-[10px] bg-amber-400 text-slate-900 font-extrabold">{{ $pengajuans->count() }}</span>
                @endif
            </button>
        </div>

        <!-- TAB 1: PROGRAM BANTUAN TERSEDIA -->
        <div x-show="activeTab === 'program'" class="space-y-6">
            @if ($programs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($programs as $program)
                        @php
                            $badge = $program->badgeJenis();
                        @endphp
                        <div
                            class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all p-6 flex flex-col justify-between relative overflow-hidden group">
                            @if ($program->is_demo)
                                <div class="absolute top-4 right-4">
                                    {{-- <span class="px-2.5 py-0.5 text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 rounded border border-amber-200">
                                    PROGRAM DEMO
                                </span> --}}
                                </div>
                            @endif

                            <div>
                                <!-- Header Program -->
                                <div class="flex items-center gap-2">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badge['class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500">
                                        Mitra: <strong class="text-slate-800">{{ $program->nama_mitra }}</strong>
                                    </span>
                                </div>

                                <h3
                                    class="text-lg font-bold text-slate-900 mt-3 group-hover:text-[#155EAA] transition-colors">
                                    {{ $program->nama_program }}
                                </h3>

                                <p class="text-xs text-slate-600 mt-2 leading-relaxed line-clamp-3">
                                    {{ $program->deskripsi }}
                                </p>

                                <!-- Persyaratan Ringkas -->
                                <div class="mt-4 p-3 bg-slate-50 rounded-xl space-y-2 text-xs">
                                    <div class="font-bold text-slate-700 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Dokumen Persyaratan Wajib:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach (array_slice($program->dokumen_dibutuhkan ?? [], 0, 4) as $dok)
                                            <span
                                                class="px-2 py-0.5 bg-white text-slate-600 rounded-md border border-slate-200 text-[11px]">
                                                {{ $dok }}
                                            </span>
                                        @endforeach
                                        @if (count($program->dokumen_dibutuhkan ?? []) > 4)
                                            <span
                                                class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded-md text-[11px] font-bold">
                                                +{{ count($program->dokumen_dibutuhkan) - 4 }} Berkas Lainnya
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Program & Action -->
                            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <a href="{{ route('dashboard.bantuan.program', $program->slug) }}"
                                    class="text-xs font-bold text-[#155EAA] hover:underline inline-flex items-center gap-1">
                                    <span>Lihat Detail Syarat</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>

                                @if ($program->isBuka())
                                    <a href="{{ route('dashboard.bantuan.create', $program->slug) }}"
                                        class="px-4 py-2 bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold rounded-xl shadow-sm transition-all transform hover:-translate-y-0.5">
                                        Ajukan Permohonan
                                    </a>
                                @else
                                    <span class="px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-semibold rounded-xl">
                                        Pendaftaran Ditutup
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-sm">
                    <div
                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Program Bantuan Aktif</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Program bantuan dan pembiayaan kemitraan sedang dalam proses sinkronisasi dengan BKK SMKN 7 Jember.
                    </p>
                </div>
            @endif
        </div>

        <!-- TAB 2: RIWAYAT PENGAJUAN SAYA -->
        <div x-show="activeTab === 'riwayat'" class="space-y-4">
            @if ($pengajuans->count() > 0)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
                                    <th class="py-3.5 px-4">Nomor & Tanggal</th>
                                    <th class="py-3.5 px-4">Program & Mitra</th>
                                    <th class="py-3.5 px-4">Status Pengajuan</th>
                                    <th class="py-3.5 px-4">Keterangan Sekolah / Mitra</th>
                                    <th class="py-3.5 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach ($pengajuans as $p)
                                    @php
                                        $badge = $p->badgeStatus();
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900 font-mono">{{ $p->nomor_pengajuan }}
                                            </div>
                                            <div class="text-[11px] text-slate-400">
                                                {{ $p->created_at->translatedFormat('d M Y, H:i') }} WIB</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-semibold text-slate-800">
                                                {{ $p->programBantuan->nama_program }}</div>
                                            <div class="text-[11px] text-slate-500">Mitra:
                                                {{ $p->programBantuan->nama_mitra }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['class'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                                {{ $badge['label'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 max-w-xs">
                                            <p class="text-[11px] text-slate-600 truncate">
                                                {{ $p->keterangan_pemohon ?: 'Menunggu tanggapan dari tim verifikator BKK.' }}
                                            </p>
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <a href="{{ route('dashboard.bantuan.show', $p->id) }}"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#0F2F5F] hover:bg-[#155EAA] text-white font-bold rounded-xl text-xs transition-all shadow-sm">
                                                <span>Pantau Detail</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-sm">
                    <div
                        class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Riwayat Permohonan</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                        Anda belum pernah mengajukan permohonan bantuan atau pembiayaan kemitraan di website Go Global.
                    </p>
                    <button @click="activeTab = 'program'" type="button"
                        class="mt-4 inline-block px-4 py-2 bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold rounded-xl transition-all">
                        Lihat Program Bantuan
                    </button>
                </div>
            @endif
        </div>
    </div>
@endsection
