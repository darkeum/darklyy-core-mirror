<?php

namespace Boot\Contracts\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule as LaravelValidationRule;

interface ValidationRule extends LaravelValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void;
}
