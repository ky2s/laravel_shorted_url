<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidatePixelIDRule implements Rule
{
    private $message;

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
        if (request()->input('type') == 'adroll') {
            if (mb_strpos($value, '-') == false) {
                $this->message = __('The :attribute must be in :format format.', ['attribute' => $attribute, 'format' => 'ADVID-PIXID']);
                return false;
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
        return $this->message;
    }
}
