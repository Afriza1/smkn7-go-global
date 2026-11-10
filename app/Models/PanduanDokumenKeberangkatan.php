<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PanduanDokumenKeberangkatan extends Model
{
    protected $table = 'panduan_dokumen_keberangkatan';

    protected $fillable = [
        'kategori_id', 'nama', 'penjelasan', 'petunjuk_pengurusan', 'keterangan', 'is_published',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriPanduanDokumen::class, 'kategori_id');
    }

    public function jalurs(): BelongsToMany
    {
        return $this->belongsToMany(
            JalurKeberangkatan::class,
            'panduan_dokumen_jalur',
            'panduan_dokumen_id',
            'jalur_id'
        )->withPivot('penerapan')->withTimestamps();
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(ChecklistDokumenKeberangkatan::class, 'panduan_dokumen_id');
    }
}
