<?php

namespace App\Http\CustomValidationRule;

use App\Models\PriceStructure;
use Illuminate\Contracts\Validation\Rule;

class EnsureAllPriceStructuresAreSelectedValidation implements Rule
{

    public function passes($attribute, $value): bool
    {
        $selectedPriceStructureIds = collect($value)->map(function ($item) {
            return json_decode($item, true)['priceStructureId'];
        });

        return PriceStructure::pluck('id')->every(function ($id) use ($selectedPriceStructureIds) {
            return $selectedPriceStructureIds->contains($id);
        });

    }

    public function message(): string
    {
        return 'Provide prices for all ranges (Daily, 7+ Days, 30+ Days)';
    }
}
