<?php

namespace App\Services;

use App\Models\User;

class StudentIdentity
{
    private const ENVELOPE_FORMAT = 'parent-portal-identity-v2';

    public function encryptNisn(User $user, string $nisn): string
    {
        $legacyIdentity = $this->legacyIdentity($user);

        if ($legacyIdentity === null) {
            return encrypt($nisn);
        }

        return encrypt(json_encode([
            'format' => self::ENVELOPE_FORMAT,
            'nisn' => $nisn,
            'legacy_identity' => $legacyIdentity,
        ], JSON_THROW_ON_ERROR));
    }

    public function decryptNisn(?string $encrypted, ?string $identityType): ?string
    {
        if (blank($encrypted) || $identityType !== 'nisn') {
            return null;
        }

        try {
            $decrypted = decrypt($encrypted);
        } catch (\Throwable) {
            return null;
        }

        if (is_string($decrypted) && preg_match('/\A[0-9]{10}\z/', $decrypted)) {
            return $decrypted;
        }

        if (! is_string($decrypted)) {
            return null;
        }

        try {
            $payload = json_decode($decrypted, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        $nisn = $payload['nisn'] ?? null;

        return ($payload['format'] ?? null) === self::ENVELOPE_FORMAT
            && is_string($nisn)
            && preg_match('/\A[0-9]{10}\z/', $nisn)
                ? $nisn
                : null;
    }

    private function legacyIdentity(User $user): ?array
    {
        if ($user->student_identity_type === 'nik' && filled($user->student_identity_encrypted)) {
            return [
                'type' => 'nik',
                'encrypted_value' => $user->student_identity_encrypted,
                'hash' => $user->student_identity_hash,
            ];
        }

        if ($user->student_identity_type !== 'nisn' || blank($user->student_identity_encrypted)) {
            return null;
        }

        try {
            $decrypted = decrypt($user->student_identity_encrypted);
            $payload = is_string($decrypted) ? json_decode($decrypted, true, flags: JSON_THROW_ON_ERROR) : null;
        } catch (\Throwable) {
            return null;
        }

        if (($payload['format'] ?? null) !== self::ENVELOPE_FORMAT || ! is_array($payload['legacy_identity'] ?? null)) {
            return null;
        }

        return $payload['legacy_identity'];
    }
}
