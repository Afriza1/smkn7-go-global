<?php

namespace App\Http\Controllers;

use App\Models\JalurKeberangkatan;
use App\Models\KategoriPanduanDokumen;
use App\Models\PanduanDokumenKeberangkatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminPanduanDokumenController extends Controller
{
    public function index()
    {
        $jalurs = JalurKeberangkatan::query()->withCount('checklists')->orderBy('nama')->get();
        $kategoris = KategoriPanduanDokumen::query()->withCount('dokumens')->orderBy('nama')->get();
        $dokumens = PanduanDokumenKeberangkatan::query()
            ->with(['kategori', 'jalurs'])
            ->orderBy('nama')
            ->get();

        return view('dashboard.admin.panduan-dokumen.index', compact('jalurs', 'kategoris', 'dokumens'));
    }

    public function storeJalur(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:180|unique:jalur_keberangkatan,nama',
            'deskripsi' => 'nullable|string|max:2000',
            'is_published' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $baseSlug = Str::slug($validated['nama']) ?: 'jalur';
            $slug = $baseSlug;
            $suffix = 2;
            while (JalurKeberangkatan::query()->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $suffix++;
            }

            $jalur = JalurKeberangkatan::create([
                ...$validated,
                'slug' => $slug,
                'is_published' => $request->boolean('is_published'),
            ]);

            $jalur->dokumens()->syncWithoutDetaching(
                PanduanDokumenKeberangkatan::query()->pluck('id')->mapWithKeys(fn ($id) => [
                    $id => ['penerapan' => 'unconfigured'],
                ])->all()
            );
        });

        return back()->with('success', 'Jalur keberangkatan berhasil ditambahkan. Penerapan dokumen perlu dikonfigurasi satu per satu.');
    }

    public function updateJalur(Request $request, JalurKeberangkatan $jalur)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:180', Rule::unique('jalur_keberangkatan', 'nama')->ignore($jalur->id)],
            'deskripsi' => 'nullable|string|max:2000',
            'is_published' => 'nullable|boolean',
        ]);

        $jalur->update([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Informasi jalur keberangkatan berhasil diperbarui.');
    }

    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:120|unique:kategori_panduan_dokumen,nama',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        KategoriPanduanDokumen::create($validated);

        return back()->with('success', 'Kategori dokumen berhasil ditambahkan.');
    }

    public function updateKategori(Request $request, KategoriPanduanDokumen $kategori)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120', Rule::unique('kategori_panduan_dokumen', 'nama')->ignore($kategori->id)],
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $kategori->update($validated);

        return back()->with('success', 'Kategori dokumen berhasil diperbarui.');
    }

    public function storeDokumen(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_panduan_dokumen,id',
            'nama' => 'required|string|max:180',
            'penjelasan' => 'nullable|string|max:3000',
            'petunjuk_pengurusan' => 'nullable|string|max:3000',
            'keterangan' => 'nullable|string|max:2000',
            'is_published' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $dokumen = PanduanDokumenKeberangkatan::create([
                ...$validated,
                'is_published' => $request->boolean('is_published'),
            ]);

            $dokumen->jalurs()->syncWithoutDetaching(
                JalurKeberangkatan::query()->pluck('id')->mapWithKeys(fn ($id) => [
                    $id => ['penerapan' => 'unconfigured'],
                ])->all()
            );
        });

        return back()->with('success', 'Dokumen berhasil ditambahkan. Tentukan penerapannya pada setiap jalur.');
    }

    public function updateDokumen(Request $request, PanduanDokumenKeberangkatan $dokumen)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_panduan_dokumen,id',
            'nama' => 'required|string|max:180',
            'penjelasan' => 'nullable|string|max:3000',
            'petunjuk_pengurusan' => 'nullable|string|max:3000',
            'keterangan' => 'nullable|string|max:2000',
            'is_published' => 'nullable|boolean',
        ]);

        $dokumen->update([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Informasi dokumen berhasil diperbarui. Checklist pengguna tetap tersimpan.');
    }

    public function updatePenerapan(Request $request, JalurKeberangkatan $jalur, PanduanDokumenKeberangkatan $dokumen)
    {
        $validated = $request->validate([
            'penerapan' => ['required', Rule::in(['required', 'optional', 'not_applicable', 'unconfigured'])],
        ]);

        DB::table('panduan_dokumen_jalur')->upsert([
            [
                'jalur_id' => $jalur->id,
                'panduan_dokumen_id' => $dokumen->id,
                'penerapan' => $validated['penerapan'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['jalur_id', 'panduan_dokumen_id'], ['penerapan', 'updated_at']);

        return back()->with('success', 'Penerapan dokumen pada jalur berhasil diperbarui. Checklist pengguna tidak dihapus.');
    }
}
