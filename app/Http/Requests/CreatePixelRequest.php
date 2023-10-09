<?php

namespace App\Http\Requests;

use App\Rules\PixelLimitGateRule;
use App\Rules\ValidatePixelIDRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CreatePixelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'name' => ['required', 'max:32', 'unique:pixels,name,null,id,user_id,'.$request->user()->id, new PixelLimitGateRule()],
            'type' => ['required', 'in:' . implode(',', array_keys(config('pixels')))],
            'pixel_id' => ['required', 'alpha_dash', 'max:255', new ValidatePixelIDRule()]
        ];
    }
}
