<?php

namespace App\Http\Controllers;

use App\Models\ModulEdukasiMateri;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminModulEdukasiController extends Controller
{
    public function index()
    {
        $materis = ModulEdukasiMateri::query()->orderBy('kategori')->orderBy('judul')->get();
        $availableFiles = array_diff_key(
            ModulEdukasiMateri::STATIC_FILE_OPTIONS,
            array_flip(ModulEdukasiMateri::query()->pluck('file_path')->all())
        );

        return view('dashboard.admin.modul-edukasi.index', compact('materis', 'availableFiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:180',
            'deskripsi' => 'required|string|max:3000',
            'kategori' => 'required|string|max:120',
            'file_path' => ['required', Rule::in(array_keys(ModulEdukasiMateri::STATIC_FILE_OPTIONS)), 'unique:modul_edukasi_materis,file_path'],
            'is_published' => 'nullable|boolean',
        ]);

        $materi = new ModulEdukasiMateri([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);
        $materi->resolvePdfPath();

        try {
            $materi->save();
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintViolation($exception)) {
                throw ValidationException::withMessages([
                    'file_path' => 'File PDF tersebut sudah terdaftar sebagai materi.',
                ]);
            }

            throw $exception;
        }

        return redirect()->route('dashboard.admin.modul-edukasi.index')
            ->with('success', 'Metadata materi berhasil ditambahkan.');
    }

    public function update(Request $request, ModulEdukasiMateri $materi)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:180',
            'deskripsi' => 'required|string|max:3000',
            'kategori' => 'required|string|max:120',
            'is_published' => 'nullable|boolean',
        ]);

        $materi->update([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Metadata materi berhasil diperbarui.');
    }

    public function destroy(ModulEdukasiMateri $materi)
    {
        $materi->delete();

        return back()->with('success', 'Metadata materi dihapus. File PDF statis tetap disimpan.');
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return in_array((string) $exception->getCode(), ['23000', '23505'], true);
    }
}
