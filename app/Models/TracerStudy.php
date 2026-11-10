<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class TracerStudy extends Model
{
    public const STATUS_UNVERIFIED = 'unverified';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_NEEDS_UPDATE = 'needs_update';
    public const STATUS_FOLLOW_UP = 'follow_up';
    public const STATUSES_NOT_VERIFIED = [
        self::STATUS_UNVERIFIED,
        self::STATUS_NEEDS_UPDATE,
        self::STATUS_FOLLOW_UP,
    ];

    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::addGlobalScope('response_record', fn (Builder $query) => $query->where('record_type', 'website'));
        static::creating(function (self $record): void {
            $record->record_type ??= 'website';
            $record->sumber_data ??= 'website';
        });
    }

    protected function casts(): array
    {
        return [
            'tahun_lulus' => 'integer',
            'tahun_masuk_perusahaan' => 'integer',
            'persetujuan_penggunaan_data' => 'boolean',
            'disetujui_pada' => 'datetime',
            'diverifikasi_pada' => 'datetime',
            'tahun_pencatatan' => 'integer',
            'riwayat_verifikasi' => 'encrypted:array',
            'tindak_lanjut_data' => 'encrypted:array',
            'data_sumber' => 'encrypted:array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(TracerStudyPeriode::class, 'periode_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function historyEntries(): Collection
    {
        $entries = collect($this->riwayat_verifikasi ?? [])->sortBy('created_at')->values();
        $actors = User::query()->whereKey($entries->pluck('user_id')->filter()->unique())->get()->keyBy('id');

        return $entries->map(fn (array $entry) => (object) [
            ...$entry,
            'created_at' => isset($entry['created_at']) ? Carbon::parse($entry['created_at']) : null,
            'updated_at' => isset($entry['updated_at']) ? Carbon::parse($entry['updated_at']) : null,
            'actor' => $actors->get($entry['user_id'] ?? null),
        ]);
    }

    public function getHistoriesAttribute(): Collection
    {
        return $this->historyEntries();
    }

    public function appendHistory(array $entry): void
    {
        $entries = $this->riwayat_verifikasi ?? [];
        $entries[] = [
            'id' => null,
            'user_id' => $entry['user_id'] ?? null,
            'ringkasan_perubahan' => $entry['ringkasan_perubahan'],
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
        $this->riwayat_verifikasi = $entries;
        $this->save();
    }

    public function followUpEntries(): Collection
    {
        $entries = collect($this->tindak_lanjut_data ?? [])->sortByDesc('created_at')->values();
        $admins = User::query()->whereKey($entries->pluck('admin_id')->filter()->unique())->get()->keyBy('id');

        return $entries->map(fn (array $entry) => (object) [
            ...$entry,
            'created_at' => isset($entry['created_at']) ? Carbon::parse($entry['created_at']) : null,
            'updated_at' => isset($entry['updated_at']) ? Carbon::parse($entry['updated_at']) : null,
            'diberitahukan_pada' => isset($entry['diberitahukan_pada']) ? Carbon::parse($entry['diberitahukan_pada']) : null,
            'admin' => $admins->get($entry['admin_id'] ?? null),
        ]);
    }

    public function getFollowUpsAttribute(): Collection
    {
        return $this->followUpEntries();
    }

    public function appendFollowUp(array $entry): void
    {
        $entries = $this->tindak_lanjut_data ?? [];
        $entries[] = [
            'id' => null,
            'admin_id' => $entry['admin_id'] ?? null,
            'jenis' => $entry['jenis'],
            'catatan_internal' => $entry['catatan_internal'] ?? null,
            'pesan_alumni' => $entry['pesan_alumni'] ?? null,
            'diberitahukan_pada' => $entry['diberitahukan_pada'] ?? null,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
        $this->tindak_lanjut_data = $entries;
        $this->save();
    }

    public function getNamaLengkapAttribute($value): ?string
    {
        if ($this->record_type !== 'historical' || ! $this->nama_lengkap_terenkripsi) {
            return $value;
        }

        try {
            return Crypt::decryptString($this->nama_lengkap_terenkripsi);
        } catch (\Throwable) {
            return null;
        }
    }

    public function setNamaLengkapAttribute(?string $value): void
    {
        if (($this->attributes['record_type'] ?? null) === 'historical') {
            $this->attributes['nama_lengkap'] = null;
            $this->attributes['nama_lengkap_terenkripsi'] = $value === null ? null : Crypt::encryptString($value);
            return;
        }

        $this->attributes['nama_lengkap'] = $value;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_verifikasi) {
            self::STATUS_VERIFIED => 'Terverifikasi',
            self::STATUS_NEEDS_UPDATE => 'Perlu Perbaikan',
            self::STATUS_FOLLOW_UP => 'Dalam Tindak Lanjut',
            default => 'Belum Diverifikasi',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_verifikasi) {
            self::STATUS_VERIFIED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_NEEDS_UPDATE => 'bg-rose-50 text-rose-700 border-rose-200',
            self::STATUS_FOLLOW_UP => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }
}
