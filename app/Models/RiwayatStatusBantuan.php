<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatusBantuan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function pengajuanBantuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanBantuan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
