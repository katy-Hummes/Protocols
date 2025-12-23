<?php

namespace App\Rules;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;

class AgeRequirement implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Carbon::parse($value)->diffInYears(Carbon::now()) >= 16;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O contribuinte deve ter pelo menos 16 anos de idade.';
    }
}
