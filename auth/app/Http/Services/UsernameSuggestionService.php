<?php

namespace App\Http\Services;

use App\Models\User;

class UsernameSuggestionService
{
    public static function generateUsername(string $firstName, string $lastName): array
    {
        $firstName = str_replace(' ', '', $firstName);
        $lastName = str_replace(' ', '', $lastName);

        $existingUsernames = User::query()->pluck('username')->toArray();
        $suggestedUsernames = [];

        $baseNames = self::getPossibleUsernames(strtolower($firstName), strtolower($lastName));

        for ($i = 0; $i < 3; $i++) {

            //handles first and last names <= 2
            if (empty(array_diff($baseNames, $suggestedUsernames))) {
                $suggestedUsernames[] = self::addSuffixToUsername($firstName . $lastName, $existingUsernames);
                continue;
            }

            $basename = empty($suggestedUsernames) ? $baseNames[array_rand($baseNames)] :
                $baseNames[array_rand(array_diff($baseNames, $suggestedUsernames))];

            if (!in_array($basename, $existingUsernames)) {
                $suggestedUsernames[] = $basename;
                continue;
            }

            $suggestedUsernames[] = self::addSuffixToUsername($basename, $existingUsernames);
        }
        return $suggestedUsernames;
    }

    private static function addSuffixToUsername($basename, $existingUsernames): string
    {
        $suffix = mt_rand(1, 9999);
        while (in_array($basename . $suffix, $existingUsernames)) {
            $suffix = mt_rand(1, 9999);
        }
        return $basename . $suffix;
    }

    private static function getPossibleUsernames(string $firstName, string $lastName): array
    {
        $firstNamePart = substr($firstName, 0, mt_rand(3, 4));
        $lastNamePart = substr($lastName, 0, mt_rand(3, 4));

        return [
            $firstName . $lastName,
            $firstNamePart . $lastName,
            $firstName . $lastNamePart,
            $firstNamePart . $lastNamePart,
            $lastNamePart . $firstNamePart,

            $firstName . '_' . $lastName,
            $firstNamePart . '_' . $lastName,
            $firstName . '_' . $lastNamePart,
            $firstNamePart . '_' . $lastNamePart,
            $lastNamePart . '_' . $firstNamePart
        ];
    }
}
