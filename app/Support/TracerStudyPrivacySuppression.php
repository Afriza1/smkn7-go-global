<?php

namespace App\Support;

use Illuminate\Support\Collection;

final class TracerStudyPrivacySuppression
{
    public static function isSmallGroup(int $uniqueAlumni): bool
    {
        return $uniqueAlumni > 0 && $uniqueAlumni < 10;
    }

    public static function shouldSuppressComparison(int $firstGroup, int $secondGroup): bool
    {
        return self::isSmallGroup($firstGroup) || self::isSmallGroup($secondGroup);
    }

    /**
     * Hide small groups and, when exactly one is hidden, hide one safe group
     * as complementary suppression so its count cannot be recovered by subtraction.
     *
     * @return array{visible: Collection, suppressed: bool}
     */
    public static function filterGroups(Collection $groups, string $privacyCountKey = 'alumni_unik'): array
    {
        $groups = $groups->values();
        $smallIndexes = $groups
            ->keys()
            ->filter(fn (int $index): bool => self::isSmallGroup((int) data_get($groups->get($index), $privacyCountKey)))
            ->values();

        $hiddenIndexes = $smallIndexes->all();

        if ($smallIndexes->count() === 1) {
            $secondaryIndex = $groups
                ->keys()
                ->reject(fn (int $index): bool => in_array($index, $hiddenIndexes, true))
                ->sortBy(fn (int $index): int => (int) data_get($groups->get($index), $privacyCountKey))
                ->first();

            if ($secondaryIndex !== null) {
                $hiddenIndexes[] = $secondaryIndex;
            }
        }

        $visible = $groups
            ->reject(fn ($group, int $index): bool => in_array($index, $hiddenIndexes, true))
            ->values();

        return [
            'visible' => $visible,
            'suppressed' => $hiddenIndexes !== [],
        ];
    }
}
