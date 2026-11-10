@extends('dashboard.layouts.dashboard')

@section('title', 'Riwayat Simulasi Finansial | SMKN 7 Jember Go Global')
@section('breadcrumb', 'Riwayat Simulasi Finansial')
@section('header_title', 'Riwayat Simulasi Finansial Anda')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Top Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                <a href="{{ route('dashboard.simulasi.index') }}" class="hover:text-[#155EAA]">&larr; Kembali ke Kalkulator Simulasi</a>
                <span>&rsaquo;</span>
                <span class="text-slate-800 font-bold">Riwayat Tersimpan</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0F2F5F]">
                Daftar Riwayat Simulasi Finansial
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Catatan perencanaan finansial yang telah Anda simpan. Nilai hasil perhitungan tersimpan secara snapshot independen.
            </p>
        </div>

        <a href="{{ route('dashboard.simulasi.index') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold shadow-xs transition-all self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4 text-[#FBBF24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Buat Simulasi Baru</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs sm:text-sm text-emerald-800 flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-[#0F2F5F]">
                Daftar Hasil Perhitungan Tersimpan ({{ $riwayats->total() }} Rencana)
            </h2>
            <span class="text-xs text-slate-400">Terkunci snapshot pada saat penyimpanan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Judul Rencana & Jenis</th>
                        <th class="px-5 py-3.5">Tanggal Simpan</th>
                        <th class="px-5 py-3.5">Biaya Keberangkatan</th>
                        <th class="px-5 py-3.5">Gaji Kotor Dikonversi</th>
                        <th class="px-5 py-3.5">Sisa Bersih / Bulan</th>
                        <th class="px-5 py-3.5">Pengembalian / Cicilan</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayats as $rw)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-4">
                                <strong class="text-slate-900 text-sm block font-bold">{{ $rw->judul_simulasi }}</strong>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $rw->jenis_simulasi === 'mandiri' ? 'bg-blue-50 text-[#155EAA]' : 'bg-purple-50 text-purple-700' }}">
                                        {{ $rw->jenis_simulasi === 'mandiri' ? 'Simulasi Mandiri' : 'Mitra: ' . ($rw->nama_mitra_snapshot ?? 'Pembiayaan') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-slate-500">
                                <div>{{ $rw->created_at->format('d M Y') }}</div>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $rw->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-800">
                                Rp {{ number_format($rw->snapshot_hasil['total_biaya_keberangkatan'] ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-[#155EAA] font-bold">
                                Rp {{ number_format($rw->snapshot_hasil['pendapatan_kotor_idr'] ?? 0, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-400 block font-normal">&yen; {{ number_format($rw->snapshot_input['gaji_jpy'] ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-bold {{ ($rw->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0) > 0 ? 'text-[#166534]' : 'text-rose-600' }}">
                                Rp {{ number_format($rw->snapshot_hasil['sisa_pendapatan_bulanan'] ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($rw->jenis_simulasi === 'mandiri')
                                    @if(isset($rw->snapshot_hasil['waktu_pengembalian_bulan']) && $rw->snapshot_hasil['waktu_pengembalian_bulan'])
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                            &plusmn; {{ $rw->snapshot_hasil['waktu_pengembalian_bulan'] }} Bulan Balik Modal
                                        </span>
                                    @else
                                        <span class="text-rose-600 font-semibold text-[11px]">Belum Dapat Dihitung</span>
                                    @endif
                                @else
                                    @if(isset($rw->snapshot_hasil['cicilan_bulanan']) && $rw->snapshot_hasil['cicilan_bulanan'] > 0)
                                        <div class="font-bold text-orange-700">
                                            Rp {{ number_format($rw->snapshot_hasil['cicilan_bulanan'], 0, ',', '.') }} / bln
                                        </div>
                                        <span class="text-[10px] text-slate-400">Tenor: {{ $rw->snapshot_hasil['tenor_bulan'] ?? ($rw->snapshot_input['tenor_bulan'] ?? '-') }} bln</span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Skema belum tersedia</span>
                                    @endif
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('dashboard.simulasi.riwayat.show', $rw->id) }}" 
                                       class="px-3 py-1.5 rounded-lg bg-[#0F2F5F] hover:bg-[#155EAA] text-white text-xs font-bold transition-all shadow-2xs">
                                        Detail
                                    </a>

                                    <form action="{{ route('dashboard.simulasi.riwayat.destroy', $rw->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin hendak menghapus riwayat simulasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors" title="Hapus riwayat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <p class="text-sm font-semibold text-slate-600">Belum ada riwayat simulasi finansial yang disimpan.</p>
                                <p class="text-xs text-slate-400 mt-1">Gunakan kalkulator simulasi untuk menghitung dan menyimpan estimasi rencana Anda.</p>
                                <a href="{{ route('dashboard.simulasi.index') }}" class="inline-block mt-4 px-4 py-2 bg-[#0F2F5F] text-white rounded-xl text-xs font-bold">
                                    Mulai Hitung Simulasi Sekarang
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayats->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $riwayats->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
