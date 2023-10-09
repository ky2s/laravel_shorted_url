<?php

namespace App\Rules;

use App\Domain;
use App\Traits\UserFeaturesTrait;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ValidateDomainOwnershipRule implements Rule
{
    use UserFeaturesTrait;

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
        $user = Auth::user();

        if (empty($value)) {
            return true;
        }

        $userFeatures = $this->getFeatures($user);

        // Check if the domain is owned by the user
        // or if the domain is part of the global domains, and the user has access to them
        // or if there's a default global domain, and is the same as the selected domain
        if (
            Domain::where([['id', '=', $value], ['user_id', '=', $user->id]])
            ->when(($user->can('globalDomains', ['App\Link', $userFeatures['option_global_domains']]) || config('settings.short_domain') && config('settings.short_domain') == $value), function($query) {
                return $query->orWhere('user_id', '=', 0);
            })
            ->exists()
        ) {
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
