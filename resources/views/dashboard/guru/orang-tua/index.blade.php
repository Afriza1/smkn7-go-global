@extends('dashboard.layouts.dashboard')

@section('title', 'Verifikasi Orang Tua | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Guru BK · Verifikasi Orang Tua')
@section('header_title', 'Verifikasi Orang Tua')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">{{ session('success') }}</div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
            <h1 class="text-lg font-extrabold text-[#0F2F5F]">Pengajuan Hubungan Orang Tua dan Siswa</h1>
            <p class="mt-1 text-sm text-slate-600">Periksa kecocokan identitas dan status setiap pengajuan.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] table-fixed text-left text-sm">
                <colgroup>
                    <col class="w-[22%]"><col class="w-[22%]"><col class="w-[18%]"><col class="w-[23%]"><col class="w-[15%]">
                </colgroup>
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-600">
                    <tr class="border-b border-slate-200">
                        <th scope="col" class="px-5 py-3.5">Orang Tua</th>
                        <th scope="col" class="px-4 py-3.5">Siswa</th>
                        <th scope="col" class="px-4 py-3.5">Kecocokan Identitas</th>
                        <th scope="col" class="px-4 py-3.5">Status Pengajuan</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($links as $link)
                        @php
                            $statusClass = match ($link->status) {
                                'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
                                'approved' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                                default => 'border-rose-200 bg-rose-50 text-rose-800',
                            };
                            $statusDotClass = match ($link->status) {
                                'pending' => 'bg-amber-500',
                                'approved' => 'bg-emerald-500',
                                default => 'bg-rose-500',
                            };
                            $statusLabel = match ($link->status) {
                                'pending' => 'Menunggu Verifikasi BK',
                                'approved' => 'Terverifikasi',
                                default => 'Ditolak',
                            };
                        @endphp
                        <tr class="transition-colors hover:bg-blue-50/40">
                            <td class="px-5 py-4 align-middle">
                                <p class="font-bold leading-5 text-slate-900">{{ $link->parent_name_snapshot }}</p>
                                <p class="mt-1 text-xs text-slate-500">Akun orang tua</p>
                            </td>
                            <td class="px-4 py-4 align-middle">
                                <p class="font-semibold leading-5 text-slate-800">{{ $link->student_name_snapshot }}</p>
                                <p class="mt-1 text-xs text-slate-500">Siswa yang dihubungkan</p>
                            </td>
                            <td class="px-4 py-4 align-middle">
                                <span class="inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.2 7.2a1 1 0 01-1.414 0l-3.2-3.2a1 1 0 011.414-1.414l2.493 2.493 6.493-6.493a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ strtoupper($link->identity_type) }} cocok
                                </span>
                            </td>
                            <td class="px-4 py-4 align-middle">
                                <div class="space-y-1.5">
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-bold {{ $statusClass }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $statusDotClass }}" aria-hidden="true"></span>{{ $statusLabel }}
                                    </span>
                                    <p class="text-xs text-slate-500">{{ $link->submitted_at->format('d M Y, H:i') }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right align-middle">
                                <a href="{{ route('dashboard.guru.orang-tua.show', $link) }}" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg border border-blue-200 bg-white px-3 py-2 text-xs font-bold text-[#155EAA] shadow-sm transition hover:border-[#155EAA] hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300">
                                    Lihat Detail
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 001.06.02l4.25-4a.75.75 0 000-1.09l-4.25-4a.75.75 0 10-1.02 1.1L10.91 10l-3.7 3.48a.75.75 0 00.02 1.29z" clip-rule="evenodd"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-500">Belum ada pengajuan verifikasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($links->hasPages())
            <div class="border-t border-slate-100 px-5 py-4 sm:px-6">{{ $links->links() }}</div>
        @endif
    </section>
</div>
@endsection
