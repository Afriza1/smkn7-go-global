@extends('dashboard.layouts.dashboard')

@section('title', 'Status Verifikasi Akun | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Status Verifikasi Akun')
@section('header_title', 'Status Verifikasi Akun')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <header class="rounded-3xl bg-gradient-to-r from-[#0F2F5F] via-[#155EAA] to-[#0A1E3F] p-6 text-white shadow-lg sm:p-8">
        <p class="text-xs font-bold uppercase tracking-wide text-[#FBBF24]">Portal Siswa</p>
        <h1 class="mt-2 text-2xl font-extrabold">Status Verifikasi Akun</h1>
        <p class="mt-2 text-sm leading-relaxed text-blue-100">Halo, {{ $user->name }}. Informasi di bawah ini menunjukkan perkembangan pemeriksaan akun oleh Guru BK.</p>
    </header>

    @if (session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert"><p class="font-bold">Periksa kembali data berikut:</p><ul class="mt-2 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    @if ($user->status === 'menunggu_verifikasi')
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">Menunggu Verifikasi</span>
            <h2 class="mt-3 text-lg font-extrabold text-slate-900">Akun sedang diperiksa Guru BK</h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-700">Akses ke fitur siswa akan tersedia setelah akun disetujui. Anda tetap dapat membuka halaman ini untuk melihat status pemeriksaan.</p>
            @if ($verificationRequest)
                <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2"><div><dt class="text-slate-500">Pengajuan ke-</dt><dd class="font-bold text-slate-800">{{ $verificationRequest->submission_number }}</dd></div><div><dt class="text-slate-500">Diajukan</dt><dd class="font-bold text-slate-800">{{ $verificationRequest->created_at?->format('d/m/Y H:i') ?? '—' }}</dd></div></dl>
            @endif
        </section>
    @else
        <section class="rounded-2xl border border-rose-200 bg-rose-50 p-5 sm:p-6">
            <span class="inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-bold text-rose-900">Perlu Perbaikan</span>
            <h2 class="mt-3 text-lg font-extrabold text-slate-900">Pengajuan akun memerlukan perbaikan</h2>
            <p class="mt-1 text-sm text-slate-700">{{ $verificationRequest?->rejection_reason ?: 'Guru BK belum mencatat alasan penolakan. Perbarui data akun yang diperlukan lalu ajukan kembali.' }}</p>
            @if ($verificationRequest?->reviewed_at)<p class="mt-2 text-xs text-slate-500">Diputuskan pada {{ $verificationRequest->reviewed_at->format('d/m/Y H:i') }}</p>@endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-extrabold text-slate-900">Perbarui data dan ajukan kembali</h2>
            <p class="mt-1 text-sm text-slate-600">Pengajuan ulang memakai akun yang sama dan akan diperiksa kembali oleh Guru BK.</p>
            <form method="POST" action="{{ route('dashboard.siswa.verification.resubmit') }}" class="mt-5 space-y-4">
                @csrf
                <div><label for="name" class="mb-1 block text-sm font-semibold text-slate-700">Nama lengkap</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-[#155EAA] focus:ring-[#155EAA]"></div>
                <div><label for="email" class="mb-1 block text-sm font-semibold text-slate-700">Email</label><input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="255" class="w-full rounded-lg border-slate-300 text-sm focus:border-[#155EAA] focus:ring-[#155EAA]"></div>
                <div><label for="phone" class="mb-1 block text-sm font-semibold text-slate-700">Nomor telepon</label><input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required maxlength="25" class="w-full rounded-lg border-slate-300 text-sm focus:border-[#155EAA] focus:ring-[#155EAA]"></div>
                <button type="submit" class="inline-flex rounded-lg bg-[#155EAA] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#0F2F5F]">Ajukan Kembali untuk Verifikasi</button>
            </form>
        </section>
    @endif
</div>
@endsection
