@extends('dashboard.layouts.dashboard')
@section('title', 'Detail Verifikasi Orang Tua | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Guru BK â€¢ Verifikasi Orang Tua')
@section('header_title', 'Detail Pengajuan Orang Tua')
@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <a href="{{ route('dashboard.guru.orang-tua.index') }}" class="text-sm font-semibold text-[#155EAA]">â† Kembali ke antrean</a>
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-xl font-extrabold text-[#0F2F5F]">{{ $link->parent_name_snapshot }} mengajukan hubungan</h1>
        <dl class="mt-5 grid gap-4 sm:grid-cols-2 text-sm"><div><dt class="text-slate-500">Akun orang tua</dt><dd class="font-semibold">{{ $link->parent_name_snapshot }} Â· {{ $link->parent?->email ?? 'Email tidak tersedia' }}</dd></div><div><dt class="text-slate-500">Siswa yang cocok</dt><dd class="font-semibold">{{ $link->student_name_snapshot }}</dd></div><div><dt class="text-slate-500">Jenis identitas yang cocok</dt><dd class="font-semibold">{{ strtoupper($link->identity_type) }} (nomor tidak ditampilkan)</dd></div><div><dt class="text-slate-500">Diajukan</dt><dd class="font-semibold">{{ $link->submitted_at->format('d M Y, H:i') }}</dd></div><div><dt class="text-slate-500">Status</dt><dd class="font-semibold">{{ $link->status }}</dd></div></dl>
        @if($link->status === 'pending')<form method="POST" action="{{ route('dashboard.guru.orang-tua.decide', $link) }}" class="mt-6 space-y-4">@csrf
            <label class="block text-sm font-semibold">Catatan / alasan penolakan<textarea name="note" rows="3" class="mt-1 block w-full rounded-xl border-slate-300">{{ old('note') }}</textarea></label>@error('note')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            <div class="flex flex-wrap gap-3"><button name="decision" value="approved" class="rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white">Setujui</button><button name="decision" value="rejected" class="rounded-xl bg-rose-700 px-5 py-3 text-sm font-bold text-white">Tolak</button></div>
        </form>@elseif($link->review_note)<p class="mt-5 rounded-xl bg-slate-50 p-4 text-sm">Catatan: {{ $link->review_note }}</p>@endif
        <div class="mt-6 border-t border-slate-100 pt-5"><h2 class="font-bold text-slate-800">Riwayat</h2>@forelse($link->decisions as $decision)<p class="mt-2 text-xs text-slate-600">{{ $decision->decided_at->format('d M Y H:i') }} Â· {{ $decision->actor_name_snapshot }} Â· {{ $decision->decision }}{{ $decision->note ? ' â€” '.$decision->note : '' }}</p>@empty<p class="mt-2 text-xs text-slate-500">Belum ada keputusan.</p>@endforelse</div>
    </section>
</div>
@endsection
