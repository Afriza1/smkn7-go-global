<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is Kepala Sekolah.
     */
    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    /**
     * Check if user is Admin Sekolah.
     */
    public function isAdminSekolah(): bool
    {
        return $this->role === 'admin_sekolah';
    }

    /**
     * Check if user is Siswa.
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Check if user is Alumni.
     */
    public function isAlumni(): bool
    {
        return $this->role === 'alumni';
    }

    /**
     * Check if user is Orang Tua.
     */
    public function isOrangTua(): bool
    {
        return $this->role === 'orang_tua';
    }

    /**
     * Check if user is Guru.
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Karier Jepang registrations relation.
     */
    public function pendaftaranKariers()
    {
        return $this->hasMany(\App\Models\PendaftaranKarierJepang::class)->latest();
    }

    /**
     * Current active Karier Jepang registration.
     */
    public function pendaftaranKarierAktif()
    {
        return $this->hasOne(\App\Models\PendaftaranKarierJepang::class)->latestOfMany();
    }

    /**
     * Financial simulation history relation.
     */
    public function riwayatSimulasis()
    {
        return $this->hasMany(\App\Models\RiwayatSimulasiFinansial::class)->latest();
    }

    /**
     * Digital counseling consultations (as participant: siswa/alumni).
     */
    public function konselingDigitals()
    {
        return $this->hasMany(\App\Models\KonselingDigital::class, 'user_id')->latest();
    }

    /**
     * Digital counseling consultations (as counselor: guru BK).
     */
    public function konselingSebagaiGuru()
    {
        return $this->hasMany(\App\Models\KonselingDigital::class, 'guru_id')->latest();
    }

    /**
     * Permohonan program bantuan sosial & pembiayaan (siswa/alumni).
     */
    public function pengajuanBantuans()
    {
        return $this->hasMany(\App\Models\PengajuanBantuan::class)->latest();
    }

    /**
     * Profil Alumni Global jika pengguna adalah alumni terdaftar.
     */
    public function alumniGlobal()
    {
        return $this->hasOne(\App\Models\AlumniGlobal::class);
    }

    /**
     * Annual Tracer Study responses owned by this account.
     */
    public function tracerStudies()
    {
        return $this->hasMany(TracerStudy::class)->latest();
    }

    public function parentStudentLink()
    {
        return $this->hasOne(ParentStudentLink::class, 'parent_id')->latestOfMany();
    }

    public function studentParentLink()
    {
        return $this->hasMany(ParentStudentLink::class, 'student_id');
    }

    public function accountVerificationRequests()
    {
        return $this->hasMany(UserAccountVerificationRequest::class)->orderByDesc('submission_number');
    }

    /**
     * Check if user is active.
     */
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Check if user is pending verification.
     */
    public function isMenungguVerifikasi(): bool
    {
        return $this->status === 'menunggu_verifikasi';
    }

    /**
     * Human readable role label.
     */
    public function getRoleDisplayName(): string
    {
        return match ($this->role) {
            'kepala_sekolah' => 'Kepala Sekolah',
            'admin_sekolah' => 'Admin Sekolah',
            'siswa' => 'Siswa',
            'alumni' => 'Alumni',
            'orang_tua' => 'Orang Tua / Wali',
            'guru' => 'Guru / Tenaga Pendidik',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

    /**
     * Human readable status label.
     */
    public function getStatusDisplayName(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'ditolak' => 'Perlu Perbaikan / Ditolak',
            'dinonaktifkan' => 'Dinonaktifkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
