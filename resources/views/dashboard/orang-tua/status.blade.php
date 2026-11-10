@extends('dashboard.layouts.dashboard')
@section('title', 'Portal Orang Tua | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Portal Orang Tua')
@section('header_title', 'Portal Orang Tua / Wali')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    @if(session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    <section class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Status pengajuan</p>
        @if(!$link)
            <h1 class="mt-2 text-2xl font-extrabold text-[#0F2F5F]">Ajukan hubungan dengan siswa</h1>
            <p class="mt-2 text-sm text-slate-600">Masukkan NISN yang telah disimpan oleh siswa melalui akun mereka. Data siswa tidak ditampilkan sebelum verifikasi Guru BK.</p>
            <form method="POST" action="{{ route('dashboard.orang-tua.submit') }}" class="mt-6 grid gap-4 sm:grid-cols-3">@csrf
                <label class="text-xs font-semibold sm:col-span-3">Nomor Induk Siswa Nasional (NISN)<input name="identity_number" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" placeholder="Masukkan 10 digit NISN siswa" required autocomplete="off" class="mt-1 block w-full rounded-xl border-slate-300 text-sm"></label>
                @error('identity_number')<p class="sm:col-span-3 text-xs text-rose-600">{{ $message }}</p>@enderror
                <button class="sm:col-span-3 justify-self-start rounded-xl bg-[#166534] px-5 py-3 text-sm font-bold text-white">Kirim pengajuan</button>
            </form>
        @else
            <h1 class="mt-2 text-2xl font-extrabold text-[#0F2F5F]">{{ $link->status === 'pending' ? 'Menunggu Verifikasi BK' : ($link->status === 'approved' ? 'Terverifikasi' : 'Pengajuan Ditolak') }}</h1>
            <p class="mt-2 text-sm text-slate-600">Tanggal pengajuan: {{ $link->submitted_at?->format('d M Y, H:i') }}</p>
            @if($link->status === 'pending')<p class="mt-3 text-sm text-slate-600">Guru BK sedang memeriksa pengajuan. Dashboard dan informasi siswa akan tersedia setelah hubungan disetujui.</p>@endif
            @if($link->status === 'rejected')
                @if($link->review_note)<p class="mt-3 rounded-xl bg-rose-50 p-4 text-sm text-rose-800">Catatan Guru BK: {{ $link->review_note }}</p>@endif
                <form method="POST" action="{{ route('dashboard.orang-tua.submit') }}" class="mt-5 grid gap-3 sm:grid-cols-3">@csrf
                    <label class="text-xs font-semibold sm:col-span-3">Nomor Induk Siswa Nasional (NISN)<input name="identity_number" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" placeholder="Masukkan 10 digit NISN siswa" required autocomplete="off" class="mt-1 block w-full rounded-xl border-slate-300"></label>
                    <button class="sm:col-span-3 justify-self-start rounded-xl bg-[#166534] px-5 py-3 text-sm font-bold text-white">Ajukan ulang</button>
                </form>
            @endif
        @endif
    </section>
</div>
@endsection
