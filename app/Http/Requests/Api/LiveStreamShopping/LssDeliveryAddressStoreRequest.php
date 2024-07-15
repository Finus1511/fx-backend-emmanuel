<?php

namespace App\Http\Requests\Api\LiveStreamShopping;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class LssDeliveryAddressStoreRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required'],
            'pincode' => ['required', 'max:15'],
            'city' => ['required', 'max:50'],
            'region_code' => ['required'],
            'state' => ['required', 'max:50'],
            'country' => ['required', 'max:50'],
            'country_code' => ['required', 'max:3'],
            'landmark' => ['required', 'max:50'],
            'contact_number' => ['required', 'digits_between:6,13'],
            'lss_delivery_address_id' => 'nullable|exists:lss_delivery_addresses,id',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     */
    protected function failedValidation($validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => $validator->errors()->first(),
            'error_code' => 422
        ]));
    }
}

