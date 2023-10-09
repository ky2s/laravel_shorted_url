<?php

namespace App\Http\Requests;

use App\Link;
use App\Rules\LinkDisabledGateRule;
use App\Rules\LinkDomainGateRule;
use App\Rules\LinkExpirationGateRule;
use App\Rules\LinkPixelGateRule;
use App\Rules\LinkTargetingGateRule;
use App\Rules\LinkPasswordGateRule;
use App\Rules\LinkSpaceGateRule;
use App\Rules\ValidateAliasRule;
use App\Rules\ValidateBadWordsRule;
use App\Rules\ValidateDeepLinkRule;
use App\Rules\ValidateCountryKeyRule;
use App\Rules\ValidateDomainOwnershipRule;
use App\Rules\ValidateLanguageKeyRule;
use App\Rules\ValidatePixelOwnersipRule;
use App\Rules\ValidatePlatformKeyRule;
use App\Rules\ValidateSpaceOwnershipRule;
use App\Rules\ValidateUrlRule;
use App\Traits\UserFeaturesTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateLinkRequest extends FormRequest
{
    use UserFeaturesTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // If the request is to edit a link as a specific user
        // And the user is not an admin
        if (request()->has('user_id') && Auth::user()->role == 0) {
            return false;
        }

        // Check if the link to be edited exists under that user
        if (request()->has('user_id')) {
            Link::where([['id', '=', request()->route('id')], ['user_id', '=', request()->input('user_id')]])->firstOrFail();
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userFeatures = $this->getFeatures(Auth::user());

        return [
            'url' => ['sometimes', 'required', new ValidateUrlRule(), 'max:2048', new ValidateBadWordsRule()],
            'alias' => ['sometimes', 'alpha_dash', 'max:255', new ValidateAliasRule()],
            'password' => ['sometimes', 'nullable', 'string', 'min:1', 'max:128', new LinkPasswordGateRule($userFeatures)],
            'space' => ['nullable', 'integer', new ValidateSpaceOwnershipRule(), new LinkSpaceGateRule($userFeatures)],
            'domain' => ['nullable', 'integer', new ValidateDomainOwnershipRule(), new LinkDomainGateRule($userFeatures)],
            'pixels' => ['nullable', new ValidatePixelOwnersipRule(), new LinkPixelGateRule($userFeatures)],
            'disabled' => ['nullable', 'boolean', new LinkDisabledGateRule($userFeatures)],
            'privacy' => ['nullable', 'integer', 'between:0,2'],
            'privacy_password' => [(in_array(request()->input('privacy'), [0, 1]) ? 'nullable' : 'sometimes'), 'string', 'min:1', 'max:128'],
            'expiration_url' => ['nullable', new ValidateUrlRule(), 'max:2048', new ValidateBadWordsRule(), new LinkExpirationGateRule($userFeatures)],
            'expiration_date' => ['nullable', 'required_with:expiration_time', 'date_format:Y-m-d', new LinkExpirationGateRule($userFeatures)],
            'expiration_time' => ['nullable', 'required_with:expiration_date', 'date_format:H:i', new LinkExpirationGateRule($userFeatures)],
            'expiration_clicks' => ['nullable', 'integer', 'min:0', 'digits_between:0,9', new LinkExpirationGateRule($userFeatures)],
            'target_type' => ['nullable', 'integer', 'min:0', 'max:4'],
            'country.*.key' => ['nullable', 'required_with:country.*.value', new ValidateCountryKeyRule(), new LinkTargetingGateRule($userFeatures)],
            'country.*.value' => ['nullable', 'required_with:country.*.key', 'max:2048', new ValidateUrlRule(), new ValidateBadWordsRule(), new ValidateDeepLinkRule($userFeatures)],
            'platform.*.key' => ['nullable', 'required_with:platform.*.value', new ValidatePlatformKeyRule(), new LinkTargetingGateRule($userFeatures)],
            'platform.*.value' => ['nullable', 'required_with:platform.*.key', 'max:2048', new ValidateUrlRule(), new ValidateBadWordsRule(), new ValidateDeepLinkRule($userFeatures)],
            'language.*.key' => ['nullable', 'required_with:language.*.value', new ValidateLanguageKeyRule(), new LinkTargetingGateRule($userFeatures)],
            'language.*.value' => ['nullable', 'required_with:language.*.key', 'max:2048', new ValidateUrlRule(), new ValidateBadWordsRule(), new ValidateDeepLinkRule($userFeatures)],
            'rotation.*.value' => ['nullable', 'max:2048', new ValidateUrlRule(), new ValidateBadWordsRule(), new ValidateDeepLinkRule($userFeatures), new LinkTargetingGateRule($userFeatures)]
        ];
    }

    public function attributes()
    {
        return [
            'url' => __('Link'),
            'alias' => __('Alias'),
            'password' => __('Password'),
            'space' => __('Space'),
            'domain' => __('Domain'),
            'pixels' => __('Pixels'),
            'disabled' => __('Disabled'),
            'privacy' => __('Stats'),
            'privacy_password' => __('Password'),
            'expiration_url' => __('Expiration link'),
            'expiration_date' => __('Expiration date'),
            'expiration_time' => __('Expiration time'),
            'expiration_clicks' => __('Expiration clicks'),
            'country.*.key' => __('Country'),
            'country.*.value' => __('Link'),
            'platform.*.key' => __('Platform'),
            'platform.*.value' => __('Link'),
            'language.*.key' => __('Language'),
            'language.*.value' => __('Link'),
            'rotation.*.value' => __('Link')
        ];
    }
}
