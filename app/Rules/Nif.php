<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Nif implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = trim($value);
        $digits = str_split($value);

        $validFirstDigits = [1, 2, 3, 5, 6, 7, 8, 9];

        if (is_numeric($value) && strlen($value) == 9 && in_array($digits[0], $validFirstDigits)) {
            $checkDigit = 0;
            for ($i = 0; $i < 8; $i++) {
                $checkDigit += $digits[$i] * (10 - $i - 1);
            }
            $checkDigit = 11 - ($checkDigit % 11);
            $checkDigit = $checkDigit >= 10 ? 0 : $checkDigit;
            if ($checkDigit == $digits[8]) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid NIF format.';
    }
}
