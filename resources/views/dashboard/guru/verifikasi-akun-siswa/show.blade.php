@extends('dashboard.layouts.dashboard')

@section('title', 'Detail Verifikasi Akun Siswa | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Verifikasi Akun Siswa / Detail')
@section('header_title', 'Detail Verifikasi Akun Siswa')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">
    <a href="{{ route('dashboard.guru.verifikasi-akun-siswa.index') }}" class="inline-flex text-sm font-bold text-[#155EAA] hover:underline">&larr; Kembali ke daftar</a>
    @if (session('success'))<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Pengajuan ke-{{ $verificationRequest->submission_number }}</p><h1 class="mt-1 text-2xl font-extrabold text-slate-900">{{ $verificationRequest->user?->name }}</h1><p class="mt-1 text-sm text-slate-600">{{ $verificationRequest->user?->email }} &bull; {{ $verificationRequest->user?->phone ?: 'Nomor telepon belum diisi' }}</p></div><span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">{{ $verificationRequest->status === 'menunggu_verifikasi' ? 'Menunggu Verifikasi' : ($verificationRequest->status === 'disetujui' ? 'Disetujui' : 'Perlu Perbaikan') }}</span></div>
        @if ($verificationRequest->user)
            <dl class="mt-4 grid gap-3 border-t border-slate-100 pt-4 text-sm sm:grid-cols-2"><div><dt class="text-slate-500">Role</dt><dd class="font-semibold text-slate-800">Siswa</dd></div><div><dt class="text-slate-500">Status akun</dt><dd class="font-semibold text-slate-800">{{ $verificationRequest->user->getStatusDisplayName() }}</dd></div><div><dt class="text-slate-500">Terdaftar</dt><dd class="font-semibold text-slate-800">{{ $verificationRequest->user->created_at?->format('d/m/Y H:i') ?? '—' }}</dd></div><div><dt class="text-slate-500">Pengajuan ini</dt><dd class="font-semibold text-slate-800">{{ $verificationRequest->created_at?->format('d/m/Y H:i') ?? '—' }}</dd></div></dl>
        @endif
        @if ($verificationRequest->status === 'ditolak' && $verificationRequest->rejection_reason)<div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900"><strong>Alasan perbaikan sebelumnya:</strong><p class="mt-1">{{ $verificationRequest->rejection_reason }}</p></div>@endif
        @php($latestNumber = $history->max('submission_number'))
        @if ($verificationRequest->status === 'menunggu_verifikasi' && (int)$verificationRequest->submission_number === (int)$latestNumber && $verificationRequest->user?->status === 'menunggu_verifikasi')
            <form method="POST" action="{{ route('dashboard.guru.verifikasi-akun-siswa.decide', $verificationRequest) }}" class="mt-6 space-y-4 border-t border-slate-200 pt-5">
                @csrf
                <fieldset><legend class="mb-2 text-sm font-bold text-slate-800">Keputusan</legend><div class="flex flex-wrap gap-4 text-sm"><label class="inline-flex items-center gap-2"><input type="radio" name="decision" value="disetujui" @checked(old('decision') === 'disetujui') required> Setujui akun</label><label class="inline-flex items-center gap-2"><input type="radio" name="decision" value="ditolak" @checked(old('decision') === 'ditolak') required> Minta perbaikan</label></div></fieldset>
                <div><label for="rejection_reason" class="mb-1 block text-sm font-semibold text-slate-700">Alasan penolakan/perbaikan <span class="font-normal text-slate-500">(wajib jika memilih minta perbaikan)</span></label><textarea id="rejection_reason" name="rejection_reason" rows="4" maxlength="3000" class="w-full rounded-lg border-slate-300 text-sm focus:border-[#155EAA] focus:ring-[#155EAA]">{{ old('rejection_reason') }}</textarea></div>
                <button class="rounded-lg bg-[#155EAA] px-5 py-3 text-sm font-bold text-white hover:bg-[#0F2F5F]">Simpan Keputusan</button>
            </form>
        @endif
    </section>
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"><h2 class="text-lg font-extrabold text-slate-900">Riwayat Pengajuan</h2><ol class="mt-4 space-y-3">@foreach ($history as $item)<li class="rounded-xl border border-slate-200 p-4"><div class="flex flex-wrap justify-between gap-2"><strong class="text-sm text-slate-800">Pengajuan ke-{{ $item->submission_number }}</strong><span class="text-xs text-slate-500">{{ $item->created_at?->format('d/m/Y H:i') ?? '—' }}</span></div><p class="mt-1 text-sm text-slate-600">{{ $item->status === 'menunggu_verifikasi' ? 'Menunggu Verifikasi' : ($item->status === 'disetujui' ? 'Disetujui' : 'Perlu Perbaikan') }}@if($item->reviewer_name_snapshot) &bull; diproses oleh {{ $item->reviewer_name_snapshot }}@endif</p>@if($item->reviewed_at)<p class="mt-1 text-xs text-slate-500">Waktu keputusan: {{ $item->reviewed_at->format('d/m/Y H:i') }}</p>@endif @if($item->rejection_reason)<p class="mt-2 text-sm text-rose-800">{{ $item->rejection_reason }}</p>@endif</li>@endforeach</ol></section>
</div>
@endsection
