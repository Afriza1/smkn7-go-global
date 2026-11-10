@extends('dashboard.layouts.dashboard')

@section('title', 'Verifikasi Akun Siswa | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Verifikasi Akun Siswa')
@section('header_title', 'Verifikasi Akun Siswa')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <header>
        <p class="text-xs font-bold uppercase tracking-wide text-[#155EAA]">Guru BK</p>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Verifikasi Akun Siswa</h1>
        <p class="mt-1 text-sm text-slate-600">Periksa data pendaftaran akun siswa dan catat keputusan verifikasi.</p>
    </header>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <form method="GET" class="mb-5 grid gap-3 sm:grid-cols-[1fr_220px_auto]">
            <label class="sr-only" for="search">Cari siswa</label>
            <input id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau email siswa" class="rounded-lg border-slate-300 text-sm focus:border-[#155EAA] focus:ring-[#155EAA]">
            <label class="sr-only" for="status">Status pengajuan</label>
            <select id="status" name="status" class="rounded-lg border-slate-300 text-sm focus:border-[#155EAA] focus:ring-[#155EAA]">
                <option value="menunggu_verifikasi" @selected($status === 'menunggu_verifikasi')>Menunggu Verifikasi</option>
                <option value="disetujui" @selected($status === 'disetujui')>Disetujui</option>
                <option value="ditolak" @selected($status === 'ditolak')>Perlu Perbaikan</option>
            </select>
            <button class="rounded-lg bg-[#155EAA] px-4 py-2 text-sm font-bold text-white transition hover:bg-[#0F2F5F] focus:outline-none focus:ring-2 focus:ring-blue-300">Terapkan</button>
        </form>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-600">
                    <tr class="border-b border-slate-200">
                        <th scope="col" class="px-5 py-3.5">Siswa</th>
                        <th scope="col" class="px-4 py-3.5">Kontak</th>
                        <th scope="col" class="px-4 py-3.5">Pengajuan</th>
                        <th scope="col" class="px-4 py-3.5">Diajukan</th>
                        <th scope="col" class="px-4 py-3.5">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($requests as $verificationRequest)
                        @php
                            $requestStatus = $verificationRequest->status;
                            $statusLabel = match ($requestStatus) {
                                'menunggu_verifikasi' => 'Menunggu',
                                'disetujui' => 'Disetujui',
                                default => 'Perlu Perbaikan',
                            };
                            $statusClass = match ($requestStatus) {
                                'menunggu_verifikasi' => 'border-amber-200 bg-amber-50 text-amber-800',
                                'disetujui' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                default => 'border-rose-200 bg-rose-50 text-rose-800',
                            };
                            $statusDotClass = match ($requestStatus) {
                                'menunggu_verifikasi' => 'bg-amber-500',
                                'disetujui' => 'bg-emerald-500',
                                default => 'bg-rose-500',
                            };
                        @endphp
                        <tr class="transition-colors hover:bg-blue-50/40">
                            <td class="px-5 py-4">
                                <p class="font-bold leading-5 text-slate-900">{{ $verificationRequest->user?->name ?? 'Akun tidak tersedia' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $verificationRequest->user?->email }}</p>
                            </td>
                            <td class="px-4 py-4 text-slate-700">{{ $verificationRequest->user?->phone ?: '—' }}</td>
                            <td class="px-4 py-4 font-medium text-slate-700">Ke-{{ $verificationRequest->submission_number }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-slate-600">{{ $verificationRequest->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusDotClass }}" aria-hidden="true"></span>{{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-bold text-[#155EAA] shadow-sm transition hover:border-[#155EAA] hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300" href="{{ route('dashboard.guru.verifikasi-akun-siswa.show', $verificationRequest) }}">
                                    Lihat Detail
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 001.06.02l4.25-4a.75.75 0 000-1.09l-4.25-4a.75.75 0 10-1.02 1.1L10.91 10l-3.7 3.48a.75.75 0 00.02 1.29z" clip-rule="evenodd"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">Tidak ada pengajuan pada status ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">{{ $requests->links() }}</div>
    </section>
</div>
@endsection
