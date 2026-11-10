<div class="flex flex-col h-[calc(100vh-210px)] min-h-[550px] bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden" 
     x-data="{ 
        scrollToBottom() {
            const el = this.$refs.chatContainer;
            if (el) {
                el.scrollTop = el.scrollHeight;
            }
        }
     }"
     x-init="scrollToBottom(); Livewire.hook('morph.updated', () => { scrollToBottom() })"
     @pesan-terkirim.window="$nextTick(() => scrollToBottom())">

    <!-- Header Ruang Chat -->
    <div class="px-6 py-4 bg-gradient-to-r from-[#0F2F5F] to-[#155EAA] text-white flex items-center justify-between flex-shrink-0 shadow-xs">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center font-extrabold text-sm text-[#FBBF24]">
                {{ $currentUser->isGuru() ? substr($konseling->user->name, 0, 2) : 'BK' }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-sm sm:text-base font-bold text-white">
                        {{ $currentUser->isGuru() ? $konseling->user->name : ($konseling->guru->name ?? 'Tim Bimbingan Konseling SMKN 7') }}
                    </h2>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $konseling->getStatusBadgeClass() }}">
                        {{ $konseling->getStatusLabel() }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-200 truncate max-w-xs sm:max-w-md">
                    Topik: <span class="font-semibold text-[#FBBF24]">{{ $konseling->getKategoriLabel() }}</span> &bull; No: {{ $konseling->nomor_konseling }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Terkoneksi Real-Time</span>
            </span>

            @if($currentUser->isGuru())
                <a href="{{ route('dashboard.guru.konseling.show', $konseling->id) }}" 
                   class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/20">
                    Detail &amp; Catatan
                </a>
            @else
                <a href="{{ route('dashboard.konseling.show', $konseling->id) }}" 
                   class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/20">
                    Informasi Sesi
                </a>
            @endif
        </div>
    </div>

    <!-- Container Pesan (dengan Polling 2s untuk Pesan Baru) -->
    <div wire:poll.2s 
         x-ref="chatContainer"
         class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-4 bg-slate-50/70">
        
        <!-- Informasi Awal Sesi -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-xs text-slate-600 max-w-xl mx-auto text-center space-y-1.5 shadow-2xs">
            <span class="font-bold text-[#0F2F5F] block text-sm">Ruang Konsultasi Digital SMKN 7 Jember</span>
            <p>
                Sesi ini bersifat privat dan terjaga kerahasiaannya. Seluruh percakapan diarsipkan untuk rekam jejak bimbingan karier peserta.
            </p>
            <span class="text-[10px] text-slate-400 block font-mono">
                Jadwal: {{ $konseling->tanggal_jadwal ? $konseling->tanggal_jadwal->translatedFormat('l, d F Y') : 'Sesuai kesepakatan' }} 
                ({{ $konseling->jam_mulai ? substr($konseling->jam_mulai, 0, 5) . ' - ' . substr($konseling->jam_selesai, 0, 5) . ' WIB' : 'Waktu Fleksibel' }})
            </span>
        </div>

        @forelse($pesans as $msg)
            @php
                $isMine = ($msg->sender_id === $currentUser->id);
                $isSenderGuru = $msg->sender->isGuru();
            @endphp

            <div class="flex items-end gap-2.5 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                
                @if(! $isMine)
                    <!-- Avatar Penerima -->
                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-xs shadow-2xs {{ $isSenderGuru ? 'bg-[#166534] text-white' : 'bg-[#155EAA] text-white' }}"
                         title="{{ $msg->sender->name }} ({{ $msg->sender->getRoleDisplayName() }})">
                        {{ substr($msg->sender->name, 0, 2) }}
                    </div>
                @endif

                <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }} max-w-[85%] sm:max-w-lg">
                    
                    @if(! $isMine)
                        <div class="flex items-center gap-1.5 mb-1 px-1">
                            <span class="text-[11px] font-bold text-slate-700">{{ $msg->sender->name }}</span>
                            @if($isSenderGuru)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">Guru BK</span>
                            @else
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-blue-100 text-[#155EAA]">Peserta</span>
                            @endif
                        </div>
                    @endif

                    <!-- Bubble Pesan -->
                    <div class="p-3.5 sm:p-4 rounded-2xl text-xs sm:text-sm leading-relaxed shadow-2xs {{ $isMine ? 'bg-[#0F2F5F] text-white rounded-br-none' : 'bg-white text-slate-800 border border-slate-200/90 rounded-bl-none' }}">
                        {!! nl2br(e($msg->pesan)) !!}
                    </div>

                    <!-- Timestamp & Read Status -->
                    <div class="flex items-center gap-1 mt-1 px-1 text-[10px] text-slate-400">
                        <span>{{ $msg->created_at->format('H:i') }} WIB</span>
                        @if($isMine)
                            <span class="text-blue-500 font-bold">&bull; Terkirim</span>
                        @endif
                    </div>
                </div>

                @if($isMine)
                    <!-- Avatar Pengirim (Diri Sendiri) -->
                    <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-xs shadow-2xs bg-[#0F2F5F] text-white">
                        {{ substr($currentUser->name, 0, 2) }}
                    </div>
                @endif

            </div>
        @empty
            <div class="text-center py-12 text-slate-400">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p class="text-sm font-semibold text-slate-600">Belum ada percakapan dalam sesi ini.</p>
                <p class="text-xs text-slate-400 mt-0.5">Kirim pesan pembuka untuk memulai konsultasi.</p>
            </div>
        @endforelse

    </div>

    <!-- Bagian Bawah: Form Input atau Banner Status -->
    <div class="p-4 bg-white border-t border-slate-200 flex-shrink-0">
        
        @if($konseling->status === \App\Models\KonselingDigital::STATUS_BERLANGSUNG)
            <!-- Sesi Sedang Berlangsung: Input Aktif -->
            <form wire:submit.prevent="kirimPesan" class="space-y-2">
                @error('pesanBaru')
                    <div class="text-xs text-rose-600 font-semibold px-2">
                        {{ $message }}
                    </div>
                @enderror

                <div class="flex items-end gap-3">
                    <div class="flex-1 relative">
                        <textarea wire:model.defer="pesanBaru" 
                                  rows="2" 
                                  placeholder="Ketik pesan konsultasi Anda di sini... (Tekan Kirim atau Enter)"
                                  class="w-full px-4 py-2.5 rounded-2xl border border-slate-300 text-xs sm:text-sm text-slate-800 focus:ring-2 focus:ring-[#155EAA] focus:border-[#155EAA] resize-none"
                                  @keydown.enter.exact.prevent="$wire.kirimPesan()"></textarea>
                    </div>

                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="px-5 py-3 rounded-2xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs sm:text-sm font-bold shadow-xs transition-all flex items-center gap-2 cursor-pointer flex-shrink-0 disabled:opacity-50">
                        <span wire:loading.remove>Kirim</span>
                        <span wire:loading class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>Mengirim...</span>
                        </span>
                        <svg wire:loading.remove class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>

                <div class="flex items-center justify-between px-2 text-[10px] text-slate-400">
                    <span>Tekan <kbd class="px-1 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono">Enter</kbd> untuk kirim, <kbd class="px-1 py-0.5 bg-slate-100 border border-slate-300 rounded font-mono">Shift+Enter</kbd> untuk baris baru.</span>
                    <span>Maks. 2000 karakter</span>
                </div>
            </form>

        @elseif($konseling->status === \App\Models\KonselingDigital::STATUS_SELESAI)
            <!-- Sesi Selesai: Input Nonaktif -->
            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-center text-xs text-slate-600">
                <strong class="font-bold text-slate-800 block text-sm mb-0.5">Sesi Konsultasi Ini Telah Ditutup</strong>
                <p>
                    Guru BK telah menyelesaikan sesi konsultasi ini pada {{ $konseling->sesi_berakhir_pada ? $konseling->sesi_berakhir_pada->translatedFormat('d F Y, H:i') . ' WIB' : 'waktu sebelumnya' }}. Riwayat percakapan tetap tersimpan dan dapat ditinjau sewaktu-waktu.
                </p>
            </div>

        @elseif($konseling->status === \App\Models\KonselingDigital::STATUS_DIJADWALKAN)
            <!-- Sesi Dijadwalkan: Menunggu Guru BK Memulai -->
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-center text-xs text-blue-900 space-y-2">
                <strong class="font-bold block text-sm">Konsultasi Telah Dijadwalkan</strong>
                <p>
                    Jadwal pelaksanaan: <strong>{{ $konseling->tanggal_jadwal ? $konseling->tanggal_jadwal->translatedFormat('l, d F Y') : '-' }}</strong> pukul <strong>{{ $konseling->jam_mulai ?? '-' }} - {{ $konseling->jam_selesai ?? '-' }} WIB</strong>.
                </p>
                @if($currentUser->isGuru())
                    <form action="{{ route('dashboard.guru.konseling.mulai', $konseling->id) }}" method="POST" class="pt-1">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer">
                            Aktifkan Ruang Chat &amp; Mulai Sesi Sekarang
                        </button>
                    </form>
                @else
                    <p class="text-[11px] text-blue-700">
                        Ruang pesan akan otomatis aktif saat Guru BK membuka sesi.
                    </p>
                @endif
            </div>

        @else
            <!-- Status Lainnya (Menunggu, Ditolak, Dibatalkan) -->
            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-center text-xs text-slate-500">
                Status saat ini: <strong>{{ $konseling->getStatusLabel() }}</strong>. Ruang chat belum aktif untuk status ini.
            </div>
        @endif

    </div>

</div>
