<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class DataPrazoRule implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function passes($attribute, $value)
    {
        try {
            $selectedDate = Carbon::parse($value)->startOfDay();
            $thirtyDaysAgo = Carbon::now()->subDays(30)->startOfDay();
            $today = Carbon::now()->startOfDay();
            return $selectedDate->between($thirtyDaysAgo, $today);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function message()
    {
        return 'The validation error message.';
    }
}
