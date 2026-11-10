<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatSimulasiFinansial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_simulasi',
        'judul_simulasi',
        'mitra_pembiayaan_id',
        'nama_mitra_snapshot',
        'snapshot_input',
        'snapshot_parameter',
        'snapshot_hasil',
        'catatan_pengguna',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_input' => 'array',
            'snapshot_parameter' => 'array',
            'snapshot_hasil' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mitraPembiayaan(): BelongsTo
    {
        return $this->belongsTo(MitraPembiayaan::class, 'mitra_pembiayaan_id');
    }
}
