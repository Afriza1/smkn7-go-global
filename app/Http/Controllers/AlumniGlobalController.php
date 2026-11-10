<?php

namespace App\Http\Controllers;

use App\Models\AlumniGlobal;
use App\Models\KomunitasAlumniGlobal;
use App\Services\SchoolMonitoringStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniGlobalController extends Controller
{
    private const ACTIVE_MENTOR_NAMES = ['Elbim', 'Siti Rohma Jepang'];

    /**
     * Halaman Direktori Alumni Global & Komunitas (Siswa & Alumni).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Alumni profiles are separate from the curated mentor contact roster below.
        // Demo records remain stored but are not published as real alumni.
        $query = AlumniGlobal::aktif()->where('is_demo', false);

        // Pencarian Nama / Perusahaan / Kota
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('kota_wilayah', 'like', "%{$search}%")
                  ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%");
            });
        }

        // Filter Negara
        if ($request->filled('negara')) {
            $query->where('negara_bekerja', $request->negara);
        }

        // Filter Bidang Pekerjaan
        if ($request->filled('bidang')) {
            $query->where('bidang_pekerjaan', $request->bidang);
        }

        // Filter Status Mentor
        if ($request->filled('mentor') && $request->mentor == '1') {
            $query->where('is_mentor', true)
                ->whereIn('nama_lengkap', self::ACTIVE_MENTOR_NAMES);
        }

        $alumnis = $query->orderBy('is_mentor', 'desc')
            ->orderBy('tahun_kelulusan', 'desc')
            ->paginate(12)
            ->withQueryString();

        $alumnis->getCollection()->each->hidePrivateContactFromSerialization();

        // Data Pilihan Filter
        $bidangList = AlumniGlobal::aktif()->where('is_demo', false)->distinct()->pluck('bidang_pekerjaan')->filter()->values();
        $negaraList = AlumniGlobal::aktif()->where('is_demo', false)->distinct()->pluck('negara_bekerja')->filter()->values();

        // Komunitas Alumni WhatsApp Resmi
        $komunitas = KomunitasAlumniGlobal::first() ?? new KomunitasAlumniGlobal([
            'nama_komunitas' => 'Komunitas Alumni SMKN 7 Jember – Go Global Jepang',
            'deskripsi' => 'Informasi komunitas alumni resmi sedang dipersiapkan oleh BKK SMKN 7 Jember.',
            'tujuan_komunitas' => 'Jejaring silaturahmi alumni luar negeri.',
            'aturan_komunitas' => 'Patuhi norma dan etika komunikasi.',
            'is_aktif' => false,
        ]);

        $totalAlumniDiLuarNegeri = app(SchoolMonitoringStatistics::class)->alumniAktifJepang();

        // These two published contacts are maintained separately from alumni profiles.
        $mentorContacts = [
            [
                'name' => 'Elbim',
                'job' => 'Scaffolding di Jepang',
                'initials' => 'E',
                'whatsapp_url' => 'https://wa.me/6285602881235',
            ],
            [
                'name' => 'Siti Rohma Jepang',
                'job' => 'Caregiver di Jepang',
                'initials' => 'SR',
                'whatsapp_url' => 'https://wa.me/6288217092575',
            ],
        ];

        // Ringkasan Statistik
        $stats = [
            'total_alumni' => $totalAlumniDiLuarNegeri,
            'total_mentor' => count($mentorContacts),
        ];

        return view('dashboard.alumni-global.index', compact(
            'alumnis',
            'bidangList',
            'negaraList',
            'komunitas',
            'stats',
            'mentorContacts',
            'user'
        ));
    }

    /**
     * Halaman Detail Profil Mentor Alumni.
     */
    public function show(AlumniGlobal $alumni)
    {
        if (! $alumni->is_aktif || $alumni->is_demo) {
            abort(404, 'Profil alumni tidak ditemukan atau sedang dinonaktifkan.');
        }

        $alumni->hidePrivateContactFromSerialization();
        $isActiveMentor = $alumni->is_mentor && in_array($alumni->nama_lengkap, self::ACTIVE_MENTOR_NAMES, true);

        return view('dashboard.alumni-global.show', compact('alumni', 'isActiveMentor'));
    }
}
