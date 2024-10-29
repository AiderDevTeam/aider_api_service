<?php

namespace App\Http\CustomValidationRule;

use App\Models\PriceStructure;
use Illuminate\Contracts\Validation\Rule;

class ValidateProductPrices implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (!is_array($value))
            return false;

        foreach ($value as $prices) {
            $price = json_decode($prices, true);
            if (!isset($price['price']) || !isset($price['priceStructureId']) ||
                !is_numeric($price['price']) || !is_numeric($price['priceStructureId'])) {
                return false;
            }
        }
        return true;
    }

    public function message(): string
    {
        return 'The :attribute objects must contain price and priceStructureId keys.';
    }
}
