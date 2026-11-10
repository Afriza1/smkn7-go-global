<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ModulEdukasiMateri extends Model
{
    public const STATIC_FILE_OPTIONS = [
        'materi/Menjadi-Pekerja-Migran-Indonesia.pdf' => 'Menjadi Pekerja Migran Indonesia',
        'materi/Panduan-Pintar-Pekerja-Migran-Indonesia.pdf' => 'Panduan Pintar Pekerja Migran Indonesia',
        'materi/Panduan-Sukses-Karier-di-Jepang.pdf' => 'Panduan Sukses Karier di Jepang',
    ];

    protected $table = 'modul_edukasi_materis';

    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori',
        'file_path',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function resolvePdfPath(): string
    {
        if (! in_array($this->file_path, array_keys(self::STATIC_FILE_OPTIONS), true)) {
            abort(404);
        }

        $privateRoot = realpath(Storage::disk('local')->path(''));
        $materiDirectory = realpath(Storage::disk('local')->path('materi'));
        $resolvedPath = realpath(Storage::disk('local')->path($this->file_path));

        if (
            $privateRoot === false
            ||
            $materiDirectory === false
            || $resolvedPath === false
            || ! str_starts_with($materiDirectory, $privateRoot . DIRECTORY_SEPARATOR)
            || ! str_starts_with($resolvedPath, $materiDirectory . DIRECTORY_SEPARATOR)
            || ! is_file($resolvedPath)
            || strtolower(pathinfo($resolvedPath, PATHINFO_EXTENSION)) !== 'pdf'
        ) {
            abort(404);
        }

        return $resolvedPath;
    }
}
