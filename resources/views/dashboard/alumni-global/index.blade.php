@extends('dashboard.layouts.dashboard')

@section('title', 'Direktori Alumni Global & Komunitas Jepang')

@section('content')
<div class="space-y-8">
    <!-- Header & Hero Banner -->
    <div class="relative bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0F2F5F] rounded-2xl p-6 sm:p-8 text-white shadow-xl overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-[#FBBF24] border border-white/10 mb-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Jejaring Internasional SMKN 7 Jember</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Direktori Alumni Global & Mentor</h1>
            <p class="mt-2 text-sm sm:text-base text-slate-200 leading-relaxed">
                Temukan informasi dari profil alumni SMKN 7 Jember dan hubungi mentor untuk bertukar informasi karier di Jepang.
            </p>
        </div>

        <!-- Macro Stats Strip -->
        <div class="mt-6 pt-6 border-t border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                <div class="text-2xl font-black text-white">{{ $stats['total_alumni'] }}</div>
                <div class="text-xs text-slate-300 font-medium">Alumni di Luar Negeri</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 border border-white/10">
                <div class="text-2xl font-black text-[#FBBF24]">{{ $stats['total_mentor'] }}</div>
                <div class="text-xs text-slate-300 font-medium">Mentor Aktif</div>
            </div>
        </div>
    </div>

    <!-- Mentor contacts are a separate curated list, not alumni profile records. -->
    <section class="space-y-4" aria-labelledby="mentor-contacts-title">
        <div>
            <h2 id="mentor-contacts-title" class="text-xl font-extrabold text-slate-900">Mentor Aktif</h2>
            <p class="mt-1 text-sm text-slate-600">Hubungi mentor untuk berbagi informasi karier di Jepang.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($mentorContacts as $mentor)
                <article class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 flex items-center gap-4">
                    <div aria-hidden="true" class="w-14 h-14 rounded-2xl bg-[#0F2F5F] text-white flex items-center justify-center text-lg font-extrabold flex-shrink-0">
                        {{ $mentor['initials'] }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-bold text-slate-900 truncate">{{ $mentor['name'] }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $mentor['job'] }}</p>
                    </div>
                    <a href="{{ $mentor['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl bg-[#166534] px-4 py-2.5 text-xs font-bold text-white transition-colors hover:bg-[#14532d] focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                        <span>Hubungi Mentor</span>
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Section Komunitas WhatsApp Resmi -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    </span>
                    <h2 class="text-lg font-bold text-slate-900">{{ $komunitas->nama_komunitas }}</h2>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed">{{ $komunitas->deskripsi }}</p>
            </div>

            <div class="flex-shrink-0">
                @if($komunitas->is_aktif && !empty($komunitas->link_whatsapp))
                    <a href="{{ $komunitas->link_whatsapp }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2.5 px-6 py-3 bg-[#166534] hover:bg-[#14532d] text-white font-bold rounded-xl text-sm shadow-md transition-all transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Gabung Grup WhatsApp</span>
                    </a>
                @else
                    <div class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-xs font-semibold text-amber-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Tautan komunitas resmi sedang dalam finalisasi oleh BKK.</span>
                    </div>
                @endif
            </div>
        </div>

        @if(!empty($komunitas->aturan_array))
            <div class="mt-4 pt-4 border-t border-slate-100">
                <details class="text-xs text-slate-500 cursor-pointer">
                    <summary class="font-semibold text-slate-700 hover:text-[#155EAA] select-none">Tata Tertib & Etika Komunitas WhatsApp (Klik untuk melihat)</summary>
                    <ul class="mt-2.5 space-y-1.5 pl-4 list-disc text-slate-600">
                        @foreach($komunitas->aturan_array as $aturan)
                            <li>{{ $aturan }}</li>
                        @endforeach
                    </ul>
                </details>
            </div>
        @endif
    </div>


</div>
@endsection
