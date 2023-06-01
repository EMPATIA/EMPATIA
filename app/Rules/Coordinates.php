<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Coordinates implements Rule
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
        if(!empty($value)){
            $coordinates = explode(',', $value);

            if (count($coordinates) !== 2) {
                return false;
            }

            foreach ($coordinates as $coordinate) {
                if (!is_numeric($coordinate)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('cbs.topics.form.error.parameter_geolocation.coordinates');
    }

}
