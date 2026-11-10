<?php

namespace App\Http\Controllers;

use App\Models\ModulEdukasiMateri;
use Illuminate\Support\Facades\Auth;

class ModulEdukasiController extends Controller
{
    public function index()
    {
        $materis = ModulEdukasiMateri::query()
            ->where('is_published', true)
            ->orderBy('kategori')
            ->orderBy('judul')
            ->get();

        $kategoriMateri = $this->categories()->map(function (array $category, string $slug) use ($materis): array {
            $category['slug'] = $slug;
            $category['materis'] = $materis->where('kategori', $category['name'])->values();
            $category['summary'] = $category['materis']->first()?->deskripsi;

            return $category;
        });

        return view('dashboard.modul-edukasi.index', compact('kategoriMateri'));
    }

    public function category(string $kategori)
    {
        $category = $this->categories()->get($kategori);
        abort_unless($category, 404);

        $materis = ModulEdukasiMateri::query()
            ->where('is_published', true)
            ->where('kategori', $category['name'])
            ->orderBy('judul')
            ->get();
        $description = $materis->first()?->deskripsi;

        return view('dashboard.modul-edukasi.category', compact('category', 'kategori', 'materis', 'description'));
    }

    public function read(ModulEdukasiMateri $materi)
    {
        $this->authorizeVisibility($materi);
        $fileRoute = Auth::user()->isAdminSekolah()
            ? route('dashboard.admin.modul-edukasi.file', $materi)
            : route('dashboard.modul-edukasi.file', $materi);
        $downloadRoute = Auth::user()->isAdminSekolah()
            ? route('dashboard.admin.modul-edukasi.download', $materi)
            : route('dashboard.modul-edukasi.download', $materi);

        return view('dashboard.modul-edukasi.read', compact('materi', 'fileRoute', 'downloadRoute'));
    }

    public function file(ModulEdukasiMateri $materi)
    {
        $path = $this->authorizeVisibility($materi);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function download(ModulEdukasiMateri $materi)
    {
        $path = $this->authorizeVisibility($materi);

        return response()->download($path, basename($materi->file_path), [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function authorizeVisibility(ModulEdukasiMateri $materi): string
    {
        $user = Auth::user();
        abort_unless($user, 403);
        abort_unless(in_array($user->role, ['admin_sekolah', 'siswa', 'alumni'], true), 403);
        abort_unless($materi->is_published || $user->isAdminSekolah(), 404);

        return $materi->resolvePdfPath();
    }

    private function categories(): \Illuminate\Support\Collection
    {
        return collect([
            'dasar-pekerja-migran-indonesia' => ['name' => 'Dasar Pekerja Migran Indonesia', 'icon' => 'shield'],
            'perlindungan-pekerja-migran-indonesia' => ['name' => 'Perlindungan Pekerja Migran Indonesia', 'icon' => 'heart'],
            'persiapan-karier-jepang' => ['name' => 'Persiapan Karier Jepang', 'icon' => 'globe'],
        ]);
    }
}
