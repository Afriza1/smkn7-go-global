@extends('dashboard.layouts.dashboard')

@section('title', 'Profil Alumni: ' . $alumni->nama_lengkap)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard.alumni-global.index') }}" 
           class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-[#155EAA] transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Direktori Alumni</span>
        </a>

    </div>

    <!-- Main Profile Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Profile Banner -->
        <div class="h-32 bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#166534] relative">
            <div class="absolute inset-0 bg-black/10"></div>
        </div>

        <div class="px-6 pb-6 pt-0 relative">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 -mt-16 sm:-mt-14 mb-6">
                <!-- Avatar & Identitas -->
                <div class="flex items-end gap-4">
                    <img src="{{ $alumni->foto_url }}" 
                         alt="{{ $alumni->nama_lengkap }}" 
                         class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-4 border-white shadow-md bg-white flex-shrink-0">
                    <div class="space-y-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900">{{ $alumni->nama_lengkap }}</h1>
                            @if($isActiveMentor)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <svg class="w-3.5 h-3.5 text-[#FBBF24]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Mentor Aktif
                                </span>
                            @endif
                        </div>
                        <p class="text-xs sm:text-sm font-semibold text-slate-600">
                            Alumni Kelulusan {{ $alumni->tahun_kelulusan }} • {{ $alumni->jurusan }}
                        </p>
                    </div>
                </div>

                <!-- Action Hubungi jika diizinkan -->
                @if($alumni->hasPublicContact() && !empty($alumni->nomor_kontak))
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $alumni->nomor_kontak);
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode('Halo Kak ' . $alumni->nama_lengkap . ', saya siswa/alumni SMKN 7 Jember ingin berkonsultasi mengenai karier ke Jepang.') }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#166534] hover:bg-[#14532d] text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Hubungi via WhatsApp</span>
                    </a>
                @endif
            </div>

            <!-- Ringkasan Info Cepat -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 block font-medium">Negara Penempatan</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $alumni->negara_bekerja }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Kota / Wilayah</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $alumni->kota_wilayah ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Bidang Pekerjaan</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $alumni->bidang_pekerjaan }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Masa Pengalaman</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $alumni->lama_bekerja ?? '-' }}</span>
                </div>
            </div>

            <!-- Konten Detail Profil -->
            <div class="mt-6 space-y-6">
                <!-- 1. Perusahaan & Pengalaman Singkat -->
                @if($alumni->nama_perusahaan)
                    <div class="p-4 bg-blue-50/60 rounded-xl border border-blue-100 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-[#155EAA] text-white flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="text-[11px] font-semibold text-blue-600 uppercase tracking-wider">Perusahaan Penempatan</div>
                            <div class="text-sm font-bold text-slate-900">{{ $alumni->nama_perusahaan }}</div>
                        </div>
                    </div>
                @endif

                <!-- 2. Deskripsi Pengalaman Kerja -->
                <div class="space-y-2">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#155EAA]"></span>
                        <span>Pengalaman Kerja & Kehidupan di Jepang</span>
                    </h2>
                    <div class="p-4 bg-white rounded-xl border border-slate-200 text-slate-700 text-xs sm:text-sm leading-relaxed whitespace-pre-line">
                        {{ $alumni->deskripsi_pengalaman ?: $alumni->pengalaman_kerja_singkat }}
                    </div>
                </div>

                <!-- 3. Bahasa yang Dikuasai -->
                @if($alumni->bahasa_dikuasai)
                    <div class="space-y-2">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#166534]"></span>
                            <span>Kemampuan Bahasa & Sertifikasi</span>
                        </h2>
                        <div class="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100 text-emerald-900 text-xs sm:text-sm font-semibold flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                            <span>{{ $alumni->bahasa_dikuasai }}</span>
                        </div>
                    </div>
                @endif

                <!-- 4. Tips & Saran Karier untuk Calon Peserta -->
                @if($alumni->tips_karier)
                    <div class="space-y-2">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#FBBF24]"></span>
                            <span>Tips & Kiat Khusus untuk Adik Kelas</span>
                        </h2>
                        <div class="p-4 bg-amber-50/60 rounded-xl border border-amber-200 text-slate-800 text-xs sm:text-sm leading-relaxed">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                <div>{{ $alumni->tips_karier }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 5. Status Akses Kontak & Perlindungan Privasi -->
                <div class="pt-4 border-t border-slate-100">
                    @if($alumni->hasPublicContact())
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="space-y-0.5">
                                <div class="font-bold text-slate-800">Kontak yang Disetujui untuk Publikasi:</div>
                                <div class="text-slate-600">@if($alumni->nomor_kontak){{ $alumni->nomor_kontak }}@endif @if($alumni->email_kontak){{ $alumni->nomor_kontak ? ' • ' : '' }}{{ $alumni->email_kontak }}@endif</div>
                                <div class="text-[11px] text-slate-500">Alumni telah menyetujui penayangan kontak ini khusus untuk konsultasi persiapan karier.</div>
                            </div>
                            @if($alumni->nomor_kontak)
                                <a href="https://wa.me/{{ $cleanPhone }}" 
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="px-4 py-2 bg-[#166534] hover:bg-[#14532d] text-white font-bold rounded-xl text-xs text-center transition-all flex-shrink-0">
                                    Buka Chat WhatsApp
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 flex items-start gap-3">
                            <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <div class="space-y-1">
                                <div class="font-bold text-slate-800">Kontak Pribadi Bersifat Privat</div>
                                <p>
                                    Demi kenyamanan dan mematuhi etika perlindungan privasi data, kontak pribadi alumni ini tidak dipublikasikan secara umum. Apabila Anda memiliki pertanyaan persiapan wawancara atau administrasi terkait bidang kerja ini, silakan berkonsultasi melalui menu <a href="{{ route('dashboard.konseling.create') }}" class="text-[#155EAA] font-bold hover:underline">Konseling Digital Guru BK</a>.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
