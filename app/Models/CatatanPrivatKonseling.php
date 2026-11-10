<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanPrivatKonseling extends Model
{
    use HasFactory;

    protected $table = 'catatan_privat_konselings';

    protected $fillable = [
        'konseling_id',
        'guru_id',
        'ringkasan_masalah',
        'tindak_lanjut',
        'evaluasi_psikologis_perilaku',
        'status_urgensi',
        'perlu_eskalasi_kepala_sekolah',
        'catatan_eskalasi',
        'tanggal_eskalasi',
    ];

    protected function casts(): array
    {
        return [
            'perlu_eskalasi_kepala_sekolah' => 'boolean',
            'tanggal_eskalasi' => 'datetime',
        ];
    }

    public function konseling(): BelongsTo
    {
        return $this->belongsTo(KonselingDigital::class, 'konseling_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function getUrgensiBadgeClass(): string
    {
        return match ($this->status_urgensi) {
            'tinggi' => 'bg-rose-100 text-rose-800 border-rose-300',
            'sedang' => 'bg-amber-100 text-amber-800 border-amber-300',
            'rendah' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            default => 'bg-slate-100 text-slate-700 border-slate-300',
        };
    }
}
