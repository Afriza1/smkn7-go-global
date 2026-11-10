<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class TracerStudyPeriode extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    protected $table = 'tracer_studies';

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::addGlobalScope('period_record', fn (Builder $query) => $query->where('record_type', 'period'));
        static::creating(function (self $period): void {
            $period->record_type = 'period';
            $period->sumber_data = 'internal';
            $period->tahun_pencatatan = $period->tahun;
        });
        static::saving(function (self $period): void {
            if ($period->isDirty('tahun')) {
                $period->tahun_pencatatan = $period->tahun;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_berakhir' => 'date',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function tracerStudies(): HasMany
    {
        return $this->hasMany(TracerStudy::class, 'periode_id');
    }

    public function getNamaTampilanAttribute(): string
    {
        return $this->nama ?: 'Tracer Study ' . $this->tahun;
    }

    public function isPengisianTerbuka(): bool
    {
        $today = Carbon::today();

        return $this->status === self::STATUS_OPEN
            && $today->betweenIncluded($this->tanggal_mulai, $this->tanggal_berakhir);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => $this->isPengisianTerbuka() ? 'Dibuka' : 'Di luar tanggal pengisian',
            self::STATUS_CLOSED => 'Ditutup',
            default => 'Draf',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_CLOSED => 'bg-slate-100 text-slate-600 border-slate-200',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }
}
