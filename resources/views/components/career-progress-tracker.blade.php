@props(['steps' => [], 'isOrangTua' => false])

<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-6 md:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-8 border-b border-slate-100 gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 text-[#155EAA] border border-blue-100 text-xs font-bold mb-2">
                <span class="w-2 h-2 rounded-full bg-[#155EAA] animate-pulse"></span>
                <span>Alur Resmi Go Global SMKN 7 Jember</span>
            </div>
            <h3 class="text-lg md:text-xl font-extrabold text-[#0F2F5F]">
                Progress Tracker Perjalanan Karier Jepang (10 Tahapan)
            </h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau status berkas, verifikasi sekolah, kesiapan, hingga keberangkatan secara transparan dan akuntabel.
            </p>
        </div>

        <!-- Legend / Keterangan Warna -->
        <div class="flex flex-wrap items-center gap-3 text-[11px] font-semibold text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200/60">
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Selesai
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#155EAA]"></span> Sedang Berjalan
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span> Revisi
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Ditolak
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span> Menunggu
            </span>
        </div>
    </div>

    <!-- Timeline List -->
    <div class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-3 sm:before:left-4 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200">
        @foreach($steps as $index => $step)
            @php
                $status = $step['status'] ?? 'pending';
                $isCompleted = $status === 'completed';
                $isCurrent = $status === 'current';
                $isRevision = $status === 'revision';
                $isRejected = $status === 'rejected';
                $isPending = $status === 'pending';
            @endphp

            <div class="relative group">
                <!-- Timeline Marker Circle -->
                <div class="absolute -left-6 sm:-left-8 top-1.5 flex items-center justify-center">
                    @if($isCompleted)
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-xs ring-4 ring-emerald-50">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    @elseif($isCurrent)
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#155EAA] text-white flex items-center justify-center shadow-md ring-4 ring-blue-100 animate-pulse">
                            <span class="text-xs font-bold">{{ $index }}</span>
                        </div>
                    @elseif($isRevision)
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-orange-500 text-white flex items-center justify-center shadow-xs ring-4 ring-orange-100">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    @elseif($isRejected)
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shadow-xs ring-4 ring-rose-100">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                    @else
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-100 text-slate-400 border border-slate-300 flex items-center justify-center">
                            <span class="text-xs font-semibold">{{ $index }}</span>
                        </div>
                    @endif
                </div>

                <!-- Step Card Content -->
                <div class="p-4 sm:p-5 rounded-xl border transition-all @if($isCurrent) bg-blue-50/40 border-[#155EAA]/40 shadow-xs @elseif($isRevision) bg-orange-50/40 border-orange-300 @elseif($isRejected) bg-rose-50/40 border-rose-300 @else bg-slate-50/70 border-slate-200/80 @endif">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-400">Tahap {{ $index }}</span>
                            <h4 class="text-sm sm:text-base font-bold text-[#0F2F5F]">
                                {{ $step['name'] }}
                            </h4>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($isCompleted)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Selesai Divalidasi
                                </span>
                            @elseif($isCurrent)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#155EAA] text-white shadow-xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                    Sedang Berlangsung
                                </span>
                            @elseif($isRevision)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800 border border-orange-300">
                                    Perlu Perbaikan
                                </span>
                            @elseif($isRejected)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                    Belum Memenuhi Syarat
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-slate-200/70 text-slate-600">
                                    Menunggu Tahapan Sebelumnya
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed mb-3">
                        {{ $step['description'] }}
                    </p>

                    <!-- Metadata: Actor & Timestamp -->
                    <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-500 pt-2 border-t border-slate-200/60">
                        @if(!empty($step['actor']))
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Petugas/Pihak: <strong class="text-slate-700">{{ $step['actor'] }}</strong></span>
                            </div>
                        @endif

                        @if(!empty($step['date']))
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Tanggal Pembaruan: <strong class="text-slate-700">{{ $step['date'] }}</strong></span>
                            </div>
                        @else
                            <span class="text-slate-400 italic">Tanggal pembaruan belum tercatat</span>
                        @endif
                    </div>

                    <!-- Note / Feedback Alert Box (If present and not restricted) -->
                    @if(!empty($step['note']) && !($isOrangTua && $index === 3))
                        <div class="mt-3 p-3 rounded-lg text-xs leading-relaxed @if($isRevision) bg-orange-100/70 text-orange-900 border border-orange-200 @elseif($isRejected) bg-rose-100/70 text-rose-900 border border-rose-200 @else bg-white border border-slate-200 text-slate-700 @endif">
                            <span class="font-bold block mb-0.5 text-slate-800">Catatan Resmi:</span>
                            {{ $step['note'] }}
                        </div>
                    @endif

                </div>
            </div>
        @endforeach
    </div>
</div>
