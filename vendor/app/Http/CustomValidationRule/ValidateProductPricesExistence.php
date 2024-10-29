<?php

namespace App\Http\CustomValidationRule;


use App\Models\PriceStructure;
use Illuminate\Contracts\Validation\Rule;

class ValidateProductPricesExistence implements Rule
{
    public function passes($attribute, $value): bool
    {
        return collect($value)->every(function ($price) {
            $priceSet = json_decode($price, true);
            return PriceStructure::where('id', $priceSet['priceStructureId'])->exists();
        });
    }

    public function message(): string
    {
        return 'Invalid price(id) selected';
    }
}
