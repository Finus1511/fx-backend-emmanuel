<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PersonalizeStoreRequest extends FormRequest
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
            'sender_id' => 'nullable|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'type' => ['required', Rule::in([PERSONALIZE_TYPE_IMAGE, PERSONALIZE_TYPE_VIDEO, PERSONALIZE_TYPE_AUDIO, PERSONALIZE_TYPE_PRODUCT])],
            'description' => 'required',
            'product_type' => [
                'required_if:type,' . PERSONALIZE_TYPE_PRODUCT,
                Rule::in([PRODUCT_TYPE_DIGITAL, PRODUCT_TYPE_PHYSICAL])
            ],
            'amount' => 'required',
            'personalized_request_id' => 'nullable|exists:personalized_requests,id',
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
            'personalized_request_id.exists' => 'The selected personalized request is invalid.',
            'product_type.required_if' => 'The product type field is required when the type is a product.',
            'product_type.in' => 'The product type must be digital or physical.',
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
