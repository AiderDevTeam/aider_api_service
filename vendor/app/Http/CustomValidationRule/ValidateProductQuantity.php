<?php

namespace App\Http\CustomValidationRule;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Route;

class ValidateProductQuantity implements Rule
{

    public function passes($attribute, $value): bool
    {
        return Route::input('product')->hasSufficientQuantity($value);
    }

    public function message(): string
    {
        return 'The product does not have enough :attribute to book';
    }
}
