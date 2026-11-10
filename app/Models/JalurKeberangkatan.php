<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JalurKeberangkatan extends Model
{
    protected $table = 'jalur_keberangkatan';

    protected $fillable = ['slug', 'nama', 'deskripsi', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function dokumens(): BelongsToMany
    {
        return $this->belongsToMany(
            PanduanDokumenKeberangkatan::class,
            'panduan_dokumen_jalur',
            'jalur_id',
            'panduan_dokumen_id'
        )->withPivot('penerapan')->withTimestamps();
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ChecklistDokumenKeberangkatan::class, 'jalur_id');
    }
}
