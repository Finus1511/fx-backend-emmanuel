<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PersonalizeProductStoreRequest extends FormRequest
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
            'name' => 'required|max:255',
            'description' => 'required',
            'personalized_request_id' => 'required|exists:personalized_requests,id,receiver_id,'.$this->input('id'),
            'personalized_product_id' => 'nullable|exists:personalized_products,id',
            'shipping_url' => ['required', 'url']
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'personalized_product_id.exists' => 'The selected personalized product is invalid.',
            'personalized_request_id.exists' => 'The selected personalized request is invalid.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => $validator->errors()->first(),
            'error_code' => 422
        ]));
    }
}
