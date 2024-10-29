<?php

namespace App\Http\CustomValidationRule;

use Illuminate\Contracts\Validation\Rule;

class ValidateProductAddress implements Rule
{

    public function passes($attribute, $value): bool
    {
        $address = json_decode($value, true);

        if (!isset($address['city']) || !isset($address['originName']) ||
            !isset($address['country']) || !isset($address['countryCode']) ||
            !isset($address['longitude']) || !isset($address['latitude'])) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return 'Product address is invalid.';
    }
}
