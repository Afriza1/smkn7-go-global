<?php

namespace App\Http\Controllers;

use App\Models\ChecklistDokumenKeberangkatan;
use App\Models\JalurKeberangkatan;
use App\Models\PanduanDokumenKeberangkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PanduanDokumenKeberangkatanController extends Controller
{
    public function index(Request $request)
    {
        $jalurs = JalurKeberangkatan::query()->where('is_published', true)->orderBy('nama')->get();
        $filters = $request->validate([
            'jalur_id' => ['nullable', 'integer', Rule::in($jalurs->pluck('id')->all())],
        ]);
        $selectedJalur = isset($filters['jalur_id'])
            ? $jalurs->firstWhere('id', (int) $filters['jalur_id'])
            : $jalurs->first();

        $dokumens = $selectedJalur
            ? $selectedJalur->dokumens()
                ->where('panduan_dokumen_keberangkatan.is_published', true)
                ->wherePivotIn('penerapan', ['required', 'optional'])
                ->with('kategori')
                ->orderBy('kategori_id')
                ->orderBy('nama')
                ->get()
            : collect();

        $statuses = $selectedJalur && $dokumens->isNotEmpty()
            ? ChecklistDokumenKeberangkatan::query()
                ->where('user_id', Auth::id())
                ->where('jalur_id', $selectedJalur->id)
                ->whereIn('panduan_dokumen_id', $dokumens->pluck('id'))
                ->pluck('status', 'panduan_dokumen_id')
            : collect();

        $dokumenWajib = $dokumens->where('pivot.penerapan', 'required');
        $totalWajib = $dokumenWajib->count();
        $siapWajib = $dokumenWajib->filter(fn ($dokumen) =>
            ($statuses[$dokumen->id] ?? ChecklistDokumenKeberangkatan::STATUS_BELUM_DISIAPKAN)
                === ChecklistDokumenKeberangkatan::STATUS_SIAP
        )->count();
        $progres = $totalWajib > 0 ? (int) round(($siapWajib / $totalWajib) * 100) : 0;
        $konfigurasiBelumLengkap = $selectedJalur
            ? $selectedJalur->dokumens()->wherePivot('penerapan', 'unconfigured')->exists()
            : false;

        return view('dashboard.panduan-dokumen.index', compact(
            'jalurs', 'selectedJalur', 'dokumens', 'statuses', 'totalWajib', 'siapWajib', 'progres', 'konfigurasiBelumLengkap'
        ));
    }

    public function updateStatus(Request $request, JalurKeberangkatan $jalur, PanduanDokumenKeberangkatan $dokumen)
    {
        abort_unless($jalur->is_published && $dokumen->is_published, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                ChecklistDokumenKeberangkatan::STATUS_BELUM_DISIAPKAN,
                ChecklistDokumenKeberangkatan::STATUS_SEDANG_DIPROSES,
                ChecklistDokumenKeberangkatan::STATUS_SIAP,
            ])],
        ]);

        $penerapan = DB::table('panduan_dokumen_jalur')
            ->where('jalur_id', $jalur->id)
            ->where('panduan_dokumen_id', $dokumen->id)
            ->value('penerapan');

        abort_unless(in_array($penerapan, ['required', 'optional'], true), 404);

        $now = now();
        DB::table('checklist_dokumen_keberangkatan')->upsert([
            [
                'user_id' => Auth::id(),
                'jalur_id' => $jalur->id,
                'panduan_dokumen_id' => $dokumen->id,
                'status' => $validated['status'],
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['user_id', 'jalur_id', 'panduan_dokumen_id'], ['status', 'updated_at']);

        return redirect()->route('dashboard.panduan-dokumen.index', ['jalur_id' => $jalur->id])
            ->with('success', 'Status persiapan dokumen berhasil diperbarui.');
    }
}
