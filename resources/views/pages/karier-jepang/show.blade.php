@extends(Auth::check() ? 'dashboard.layouts.dashboard' : 'layouts.app')

@php
    $dashboardRoute = match (Auth::user()->role) {
        'siswa' => route('dashboard.siswa'),
        'alumni' => route('dashboard.alumni'),
        'admin_sekolah' => route('dashboard.admin'),
        'guru' => route('dashboard.guru'),
        'kepala_sekolah' => route('dashboard.kepala-sekolah'),
        default => route('dashboard'),
    };
@endphp

@section('title', 'Detail Pendaftaran ' . $pendaftaran->nomor_pendaftaran . ' | Karier Jepang SMKN 7 Jember')
@section('breadcrumb', Auth::user()->getRoleDisplayName() . ' • Detail Pendaftaran ' . $pendaftaran->nomor_pendaftaran)
@section('header_title', 'Detail Pendaftaran Karier Jepang')

@section('content')
<div class="bg-[#F8FAFC] py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2" aria-label="Breadcrumb">
                    <a href="{{ $dashboardRoute }}" class="hover:text-[#155EAA]">Dashboard</a>
                    <span>&rsaquo;</span>
                    <span class="text-slate-800 font-bold">Detail Pendaftaran</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                        {{ $pendaftaran->nomor_pendaftaran }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $pendaftaran->getStatusBadgeClass() }}">
                        {{ $pendaftaran->getStatusLabel() }}
                    </span>
                </div>
            </div>

            <!-- Action buttons depending on role and status -->
            <div class="flex flex-wrap items-center gap-3">
                @if($pendaftaran->canBeEditedByApplicant() && Auth::id() === $pendaftaran->user_id)
                    <a href="{{ route('karier-jepang.pendaftaran.perbaikan', $pendaftaran->id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-sm transition-all animate-bounce">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Perbaiki Berkas Dokumen</span>
                    </a>
                @endif

                @if(Auth::user()->isAdminSekolah())
                    <a href="{{ route('dashboard.admin.show', $pendaftaran->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all">
                        <span>Buka Panel Pemeriksaan Admin</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @elseif(Auth::user()->isGuru())
                    <a href="{{ route('dashboard.guru.show', $pendaftaran->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#166534] hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-all">
                        <span>Buka Panel Guru BK</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @elseif(Auth::user()->isKepalaSekolah())
                    <a href="{{ route('dashboard.kepala-sekolah.show', $pendaftaran->id) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all">
                        <span>Buka Meja Putusan Kepala Sekolah</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs sm:text-sm text-blue-800 flex items-start gap-3">
                <svg class="w-5 h-5 text-[#155EAA] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        <!-- High-Priority Revision Notification Banner -->
        @if($pendaftaran->status === 'perlu_perbaikan')
            <div class="p-6 rounded-2xl bg-orange-50 border-2 border-orange-300 shadow-xs">
                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-xl bg-orange-100 text-orange-700 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-bold text-orange-950">Berkas Memerlukan Perbaikan Administrasi</h3>
                        <p class="text-xs sm:text-sm text-orange-900 mt-1 leading-relaxed">
                            Petugas administrasi BKK SMKN 7 Jember mengembalikan berkas Anda dengan catatan resmi berikut:
                        </p>
                        <div class="mt-2.5 p-3.5 bg-white rounded-xl border border-orange-200 text-xs sm:text-sm text-slate-800 font-medium leading-relaxed">
                            "{{ $pendaftaran->catatan_admin }}"
                        </div>
                        <div class="mt-4">
                            @if(Auth::id() === $pendaftaran->user_id)
                                <a href="{{ route('karier-jepang.pendaftaran.perbaikan', $pendaftaran->id) }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold transition-all shadow-sm">
                                    <span>Unggah Dokumen Perbaikan Sekarang</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                <span class="text-xs text-orange-800 italic">Menunggu peserta mengunggah ulang dokumen revisi.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ==============================================================
             1. DATA PESERTA & PILIHAN LPK (2 KOLOM GRID)
             ============================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left: Profil Calon Peserta -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs">
                <h3 class="text-base font-bold text-[#0F2F5F] mb-4 pb-3 border-b border-slate-100 flex items-center justify-between">
                    <span>Identitas Calon Peserta</span>
                    <span class="text-xs font-semibold text-slate-400">Diajukan: {{ $pendaftaran->created_at->format('d M Y, H:i') }}</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Nama Lengkap</span>
                        <strong class="text-slate-900 text-sm font-bold">{{ $pendaftaran->nama_lengkap }}</strong>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Status Peserta</span>
                        <strong class="text-slate-900 text-sm font-bold">
                            {{ $pendaftaran->jenis_peserta === 'siswa' ? 'Siswa Aktif (Kelas XII)' : 'Alumni SMKN 7 Jember' }}
                        </strong>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">
                            {{ $pendaftaran->jenis_peserta === 'siswa' ? 'NIS / NISN Siswa' : 'Tahun Kelulusan' }}
                        </span>
                        <strong class="text-slate-900 font-bold">
                            {{ $pendaftaran->jenis_peserta === 'siswa' ? ($pendaftaran->nis_nisn ?? '-') : ($pendaftaran->tahun_lulus ?? '-') }}
                        </strong>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Jurusan / Konsentrasi</span>
                        <strong class="text-[#155EAA] font-bold">{{ $pendaftaran->jurusan }}</strong>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Nomor WhatsApp</span>
                        <strong class="text-slate-900 font-mono">{{ $pendaftaran->nomor_telepon }}</strong>
                    </div>

                    <div>
                        <span class="text-slate-400 block mb-0.5">Alamat Email</span>
                        <strong class="text-slate-900">{{ $pendaftaran->email }}</strong>
                    </div>

                    <div class="sm:col-span-2">
                        <span class="text-slate-400 block mb-0.5">Alamat Lengkap Domisili</span>
                        <p class="text-slate-700 leading-relaxed">{{ $pendaftaran->alamat }}</p>
                    </div>

                    @if($pendaftaran->alasan_minat)
                        <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                            <span class="text-slate-400 block mb-0.5">Aspirasi & Motivasi Mengikuti Program</span>
                            <p class="text-slate-700 italic leading-relaxed">"{{ $pendaftaran->alasan_minat }}"</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: LPK Pilihan & Dokumen -->
            <div class="space-y-6">
                <!-- Card LPK -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Pilihan Lembaga Mitra</span>
                    <h3 class="text-base font-bold text-[#0F2F5F]">
                        {{ $pendaftaran->lpk->nama_lpk }}
                    </h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        {{ $pendaftaran->lpk->ringkasan }}
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Bidang:</span>
                            <strong class="text-slate-800 text-right">{{ $pendaftaran->lpk->bidang_program }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Penempatan:</span>
                            <strong class="text-slate-800">{{ $pendaftaran->lpk->wilayah_penempatan }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Dokumen Verifikasi (Private Storage) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                        Dokumen Persyaratan (Tersimpan Aman)
                    </h3>
                    
                    <div class="space-y-2 text-xs">
                        <!-- KTP -->
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <span class="font-medium text-slate-700">1. KTP / Kartu Pelajar</span>
                            <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'identitas']) }}" 
                               target="_blank" 
                               class="text-[#155EAA] hover:underline font-bold flex items-center gap-1">
                                <span>Lihat File</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>

                        <!-- KK -->
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <span class="font-medium text-slate-700">2. Kartu Keluarga</span>
                            <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'kk']) }}" 
                               target="_blank" 
                               class="text-[#155EAA] hover:underline font-bold flex items-center gap-1">
                                <span>Lihat File</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>

                        <!-- Rapor / Ijazah -->
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <span class="font-medium text-slate-700">3. Rapor / Ijazah</span>
                            <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'rapor_ijazah']) }}" 
                               target="_blank" 
                               class="text-[#155EAA] hover:underline font-bold flex items-center gap-1">
                                <span>Lihat File</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>

                        <!-- Surat Ortu -->
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                            <span class="font-medium text-slate-700">4. Surat Pernyataan Wali</span>
                            <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'surat_ortu']) }}" 
                               target="_blank" 
                               class="text-[#155EAA] hover:underline font-bold flex items-center gap-1">
                                <span>Lihat File</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </div>

                        @if($pendaftaran->file_tambahan)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/60">
                                <span class="font-medium text-slate-700">5. Dokumen Tambahan</span>
                                <a href="{{ route('karier-jepang.dokumen.unduh', [$pendaftaran->id, 'tambahan']) }}" 
                                   target="_blank" 
                                   class="text-[#155EAA] hover:underline font-bold flex items-center gap-1">
                                    <span>Lihat File</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- ==============================================================
             2. PROGRESS TRACKER LENGKAP 10 TAHAPAN
             ============================================================== -->
        <x-career-progress-tracker :steps="$trackerSteps" />

        <!-- ==============================================================
             3. TIMELINE RIWAYAT TINDAKAN & AUDIT LOG
             ============================================================== -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
            <h3 class="text-base font-bold text-[#0F2F5F] mb-4 pb-3 border-b border-slate-100">
                Riwayat Jejak Audit Pendaftaran & Verifikasi
            </h3>

            <div class="space-y-4">
                @forelse($pendaftaran->riwayats as $riwayat)
                    <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/70 text-xs">
                        <div class="w-2 h-2 rounded-full bg-[#155EAA] mt-1.5 flex-shrink-0"></div>
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                <strong class="text-slate-800 text-xs sm:text-sm font-bold">
                                    {{ $riwayat->getActionLabel() }}
                                </strong>
                                <span class="text-slate-400 font-mono text-[11px]">
                                    {{ $riwayat->created_at->format('d M Y, H:i:s') }}
                                </span>
                            </div>
                            <p class="text-slate-600 leading-relaxed">
                                {{ $riwayat->catatan ?: 'Perubahan status dicatat oleh sistem.' }}
                            </p>
                            @if($riwayat->user)
                                <span class="text-[11px] text-slate-400 mt-1 block">
                                    Oleh: <strong>{{ $riwayat->user->name }}</strong> ({{ $riwayat->user->getRoleDisplayName() }})
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic">Belum ada riwayat tercatat.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
