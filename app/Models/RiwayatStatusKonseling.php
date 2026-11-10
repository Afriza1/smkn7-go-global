<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatusKonseling extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status_konselings';

    protected $fillable = [
        'konseling_id',
        'user_id',
        'status_sebelumnya',
        'status_baru',
        'keterangan',
    ];

    public function konseling(): BelongsTo
    {
        return $this->belongsTo(KonselingDigital::class, 'konseling_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
