<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistDokumenKeberangkatan extends Model
{
    public const STATUS_BELUM_DISIAPKAN = 'belum_disiapkan';
    public const STATUS_SEDANG_DIPROSES = 'sedang_diproses';
    public const STATUS_SIAP = 'siap';

    protected $table = 'checklist_dokumen_keberangkatan';

    protected $fillable = ['user_id', 'jalur_id', 'panduan_dokumen_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jalur(): BelongsTo
    {
        return $this->belongsTo(JalurKeberangkatan::class, 'jalur_id');
    }

    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(PanduanDokumenKeberangkatan::class, 'panduan_dokumen_id');
    }
}
