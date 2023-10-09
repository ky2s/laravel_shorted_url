<?php

namespace App\Rules;

use App\Pixel;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ValidatePixelOwnersipRule implements Rule
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
        if (empty(array_filter($value))) {
            return true;
        }

        if (Pixel::where('user_id', '=', request()->input('user_id') ?? Auth::user()->id)->whereIn('id', array_filter($value))->exists()) {
            return true;
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
        return __('Invalid value.');
    }
}
