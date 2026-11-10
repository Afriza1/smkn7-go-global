<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KonselingDigital extends Model
{
    use HasFactory;

    protected $table = 'konseling_digitals';

    protected $fillable = [
        'nomor_konseling',
        'user_id',
        'guru_id',
        'kategori',
        'metode',
        'alasan_konseling',
        'tanggal_usulan',
        'waktu_usulan',
        'status',
        'tanggal_jadwal',
        'jam_mulai',
        'jam_selesai',
        'tempat_tatap_muka',
        'catatan_jadwal',
        'alasan_penolakan',
        'alasan_pembatalan',
        'sesi_dimulai_pada',
        'sesi_berakhir_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_usulan' => 'date',
            'tanggal_jadwal' => 'date',
            'sesi_dimulai_pada' => 'datetime',
            'sesi_berakhir_pada' => 'datetime',
        ];
    }

    // ==========================================
    // KONSTANTA & DAFTAR LABEL
    // ==========================================
    public const KATEGORI_PERSIAPAN_JEPANG = 'persiapan_jepang';
    public const KATEGORI_PENDIDIKAN_KARIER = 'pendidikan_karier';
    public const KATEGORI_PERSIAPAN_KEBERANGKATAN = 'persiapan_keberangkatan';
    public const KATEGORI_PRIBADI_SOSIAL = 'pribadi_sosial';
    public const KATEGORI_LAINNYA = 'lainnya';

    public const METODE_ONLINE = 'online';
    public const METODE_TATAP_MUKA = 'tatap_muka';

    public const STATUS_MENUNGGU_KONFIRMASI = 'menunggu_konfirmasi';
    public const STATUS_DIJADWALKAN = 'dijadwalkan';
    public const STATUS_BERLANGSUNG = 'berlangsung';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public static function getKategoriList(): array
    {
        return [
            self::KATEGORI_PERSIAPAN_JEPANG => 'Persiapan kerja ke Jepang',
            self::KATEGORI_PENDIDIKAN_KARIER => 'Pendidikan dan karier',
            self::KATEGORI_PERSIAPAN_KEBERANGKATAN => 'Persiapan keberangkatan',
            self::KATEGORI_PRIBADI_SOSIAL => 'Permasalahan pribadi atau sosial',
            self::KATEGORI_LAINNYA => 'Konsultasi lainnya',
        ];
    }

    public function getKategoriLabel(): string
    {
        return self::getKategoriList()[$this->kategori] ?? ucfirst(str_replace('_', ' ', $this->kategori));
    }

    public function getMetodeLabel(): string
    {
        return match ($this->metode) {
            self::METODE_ONLINE => 'Online via Website (Chat Real-Time)',
            self::METODE_TATAP_MUKA => 'Tatap Muka di Sekolah',
            default => ucfirst(str_replace('_', ' ', $this->metode)),
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU_KONFIRMASI => 'Menunggu Konfirmasi BK',
            self::STATUS_DIJADWALKAN => 'Telah Dijadwalkan',
            self::STATUS_BERLANGSUNG => 'Sesi Sedang Berlangsung',
            self::STATUS_SELESAI => 'Konsultasi Selesai',
            self::STATUS_DITOLAK => 'Pengajuan Ditolak',
            self::STATUS_DIBATALKAN => 'Dibatalkan Peserta',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU_KONFIRMASI => 'bg-amber-50 text-amber-800 border-amber-200',
            self::STATUS_DIJADWALKAN => 'bg-blue-50 text-[#155EAA] border-blue-200',
            self::STATUS_BERLANGSUNG => 'bg-emerald-50 text-emerald-800 border-emerald-300 animate-pulse',
            self::STATUS_SELESAI => 'bg-slate-100 text-slate-700 border-slate-300',
            self::STATUS_DITOLAK => 'bg-rose-50 text-rose-800 border-rose-200',
            self::STATUS_DIBATALKAN => 'bg-slate-100 text-slate-500 border-slate-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    public function bisaBatal(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_KONFIRMASI;
    }

    public function isOnline(): bool
    {
        return $this->metode === self::METODE_ONLINE;
    }

    public function isBerlangsung(): bool
    {
        return $this->status === self::STATUS_BERLANGSUNG;
    }

    public function isSelesai(): bool
    {
        return $this->status === self::STATUS_SELESAI;
    }

    public function bisaKirimPesan(): bool
    {
        return $this->isOnline() && $this->isBerlangsung();
    }

    public static function generateNomorKonseling(): string
    {
        $prefix = 'BK-' . date('Ym') . '-';
        $last = self::where('nomor_konseling', 'like', $prefix . '%')->latest('id')->first();
        if ($last) {
            $num = (int) substr($last->nomor_konseling, -4) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    // ==========================================
    // RELASI ELOQUENT
    // ==========================================
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function pesans(): HasMany
    {
        return $this->hasMany(PesanKonseling::class, 'konseling_id')->oldest();
    }

    public function catatanPrivat(): HasOne
    {
        return $this->hasOne(CatatanPrivatKonseling::class, 'konseling_id');
    }

    public function riwayatStatuses(): HasMany
    {
        return $this->hasMany(RiwayatStatusKonseling::class, 'konseling_id')->latest();
    }
}
