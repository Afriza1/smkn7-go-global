<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\UserAccountVerificationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        // Public registration allows only these 4 roles.
        // Kepala Sekolah and Admin Sekolah are STRICTLY forbidden from public registration.
        $allowedRoles = ['siswa', 'alumni', 'orang_tua', 'guru'];

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone' => ['required', 'string', 'max:25'],
            'role' => [
                'required',
                'string',
                Rule::in($allowedRoles),
            ],
            'password' => $this->passwordRules(),
        ], [
            'role.in' => 'Pilihan jenis akun tidak valid. Pendaftaran publik hanya diperuntukkan bagi Siswa, Alumni, Orang Tua, dan Guru.',
            'phone.required' => 'Nomor telepon wajib diisi untuk verifikasi akun sekolah.',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan login atau gunakan email lain.',
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'password' => Hash::make($input['password']),
                'role' => $input['role'],
                'status' => 'menunggu_verifikasi',
            ]);

            if ($user->role === 'siswa') {
                UserAccountVerificationRequest::create([
                    'user_id' => $user->id,
                    'submission_number' => 1,
                    'status' => UserAccountVerificationRequest::STATUS_PENDING,
                ]);
            }

            // Assign Spatie permission role safely.
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => $input['role'], 'guard_name' => 'web']);
                $user->syncRoles([$input['role']]);
            }

            return $user;
        });
    }
}
