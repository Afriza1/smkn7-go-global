<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Define all roles in Spatie Permission
        $roles = [
            'admin_sekolah',
            'kepala_sekolah',
            'siswa',
            'alumni',
            'orang_tua',
            'guru',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Privileged demo accounts are only seeded outside production.
        // In production this also leaves any existing privileged accounts untouched.
        if (! app()->environment('production')) {
            // 1. Kepala Sekolah (Hanya dibuat oleh proses berwenang, bukan registrasi publik)
            $kepalaSekolah = User::updateOrCreate(
                ['email' => 'kepala.sekolah@smkn7jember.sch.id'],
                [
                    'name' => 'Drs. H. M. Zainal Arifin, M.Pd.',
                    'phone' => '081234567890',
                    'password' => Hash::make('password123'),
                    'role' => 'kepala_sekolah',
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                ]
            );
            $kepalaSekolah->syncRoles(['kepala_sekolah']);

            // 2. Admin Sekolah
            $admin = User::updateOrCreate(
                ['email' => 'admin@smkn7jember.sch.id'],
                [
                    'name' => 'Administrator Go Global',
                    'phone' => '081234567891',
                    'password' => Hash::make('password123'),
                    'role' => 'admin_sekolah',
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                ]
            );
            $admin->syncRoles(['admin_sekolah']);
        }

        // 3. Siswa (Contoh akun aktif)
        $siswa = User::updateOrCreate(
            ['email' => 'siswa@smkn7jember.sch.id'],
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567892',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
        $siswa->syncRoles(['siswa']);

        // 4. Alumni
        $alumni = User::updateOrCreate(
            ['email' => 'alumni@smkn7jember.sch.id'],
            [
                'name' => 'Bayu Pratama',
                'phone' => '081234567893',
                'password' => Hash::make('password123'),
                'role' => 'alumni',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
        $alumni->syncRoles(['alumni']);

        // 5. Orang Tua
        $orangTua = User::updateOrCreate(
            ['email' => 'orangtua@smkn7jember.sch.id'],
            [
                'name' => 'H. Suryadi',
                'phone' => '081234567894',
                'password' => Hash::make('password123'),
                'role' => 'orang_tua',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
        $orangTua->syncRoles(['orang_tua']);

        // 6. Guru / Tenaga Pendidik
        $guru = User::updateOrCreate(
            ['email' => 'guru@smkn7jember.sch.id'],
            [
                'name' => 'Romi Afrilian, S.Pd.',
                'phone' => '081234567895',
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'status' => 'aktif',
                'email_verified_at' => now(),
            ]
        );
        $guru->syncRoles(['guru']);
    }
}
