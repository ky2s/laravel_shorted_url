<?php

namespace App\Http\Requests\API;

use App\Http\Requests\CreatePixelRequest as BaseCreatePixelRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreatePixelRequest extends BaseCreatePixelRequest
{
    /**
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => $validator->errors(),
                'status' => 422
            ], 422));
    }
}

