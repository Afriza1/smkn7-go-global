<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontakLayananKonseling extends Model
{
    use HasFactory;

    protected $table = 'kontak_layanan_konselings';

    protected $fillable = [
        'nama_layanan',
        'nama_guru_bk',
        'kontak_resmi',
        'jam_layanan',
        'lokasi_ruangan',
        'email_layanan',
        'kontak_bantuan_eskalasi',
        'keterangan_tambahan',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'is_aktif' => 'boolean',
        ];
    }

    public static function getAktif(): self
    {
        return self::where('is_aktif', true)->first() ?? self::create([
            'nama_layanan' => 'Layanan Bimbingan Konseling & Karier Global SMKN 7 Jember',
            'nama_guru_bk' => 'Romi Afrilian, S.Pd. & Tim BKK SMKN 7 Jember',
            'kontak_resmi' => '(0331) 487722 / bkk@smkn7jember.sch.id',
            'jam_layanan' => 'Senin - Jumat, Pukul 07.30 - 15.30 WIB',
            'lokasi_ruangan' => 'Ruang Bimbingan & Konseling (Gedung A Lt. 1), SMKN 7 Jember',
            'email_layanan' => 'bk@smkn7jember.sch.id',
            'kontak_bantuan_eskalasi' => 'Sekretariat Kepala Sekolah SMKN 7 Jember (0331) 487720',
            'keterangan_tambahan' => 'Konsultasi bersifat privat, rahasia, dan ditujukan untuk mendukung kesiapan masa depan siswa.',
            'is_aktif' => true,
        ]);
    }
}
