<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramBantuan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'dokumen_dibutuhkan' => 'array',
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
            'is_aktif' => 'boolean',
            'is_demo' => 'boolean',
        ];
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(PengajuanBantuan::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function isBuka(): bool
    {
        if (!$this->is_aktif) {
            return false;
        }

        $now = now()->toDateString();
        if ($this->periode_mulai && $this->periode_mulai->toDateString() > $now) {
            return false;
        }
        if ($this->periode_selesai && $this->periode_selesai->toDateString() < $now) {
            return false;
        }

        return true;
    }

    public function badgeJenis(): array
    {
        return match ($this->jenis_program) {
            'dana_talangan' => ['label' => 'Dana Talangan Keberangkatan', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
            'beasiswa_pelatihan' => ['label' => 'Beasiswa Pelatihan Bahasa', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
            'subsidi_keberangkatan' => ['label' => 'Subsidi Biaya MCU & Paspor', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
            'pinjaman_lunak' => ['label' => 'Pembiayaan Lunak Tanpa Riba', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
            default => ['label' => ucwords(str_replace('_', ' ', $this->jenis_program)), 'class' => 'bg-slate-50 text-slate-700 border-slate-200'],
        };
    }
}
