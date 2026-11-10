<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesanKonseling extends Model
{
    use HasFactory;

    protected $table = 'pesan_konselings';

    protected $fillable = [
        'konseling_id',
        'sender_id',
        'pesan',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function konseling(): BelongsTo
    {
        return $this->belongsTo(KonselingDigital::class, 'konseling_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
