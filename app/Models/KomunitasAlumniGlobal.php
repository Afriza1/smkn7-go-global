<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KomunitasAlumniGlobal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function getAturanArrayAttribute(): array
    {
        if (empty($this->aturan_komunitas)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode("\n", str_replace("\r", "", $this->aturan_komunitas)))));
    }
}
