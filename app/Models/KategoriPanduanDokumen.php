<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPanduanDokumen extends Model
{
    protected $table = 'kategori_panduan_dokumen';

    protected $fillable = ['nama', 'deskripsi'];

    public function dokumens(): HasMany
    {
        return $this->hasMany(PanduanDokumenKeberangkatan::class, 'kategori_id');
    }
}
