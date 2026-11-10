<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlumniGlobal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tahun_kelulusan' => 'integer',
            'is_mentor' => 'boolean',
            'kontak_disetujui' => 'boolean',
            'kontak_disetujui_pada' => 'datetime',
            'is_aktif' => 'boolean',
            'is_demo' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }

    public function scopeMentor($query)
    {
        return $query->where('is_mentor', true);
    }

    public function hasPublicContact(): bool
    {
        return ! $this->is_demo
            && $this->kontak_disetujui
            && $this->kontak_disetujui_pada !== null
            && $this->kontak_disetujui_oleh !== null
            && (! blank($this->nomor_kontak) || ! blank($this->email_kontak));
    }

    public function hidePrivateContactFromSerialization(): self
    {
        if (! $this->hasPublicContact()) {
            $this->makeHidden(['nomor_kontak', 'email_kontak']);
        }

        return $this;
    }

    public function badgeMentorClass(): string
    {
        return $this->is_mentor 
            ? 'bg-amber-50 text-amber-700 border-amber-200' 
            : 'bg-slate-50 text-slate-600 border-slate-200';
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto_profil && file_exists(public_path('storage/' . $this->foto_profil))) {
            return asset('storage/' . $this->foto_profil);
        }

        // Inisial avatar jika tidak ada foto
        $name = urlencode($this->nama_lengkap);
        return "https://ui-avatars.com/api/?name={$name}&background=0F2F5F&color=FFFFFF&bold=true";
    }
}
