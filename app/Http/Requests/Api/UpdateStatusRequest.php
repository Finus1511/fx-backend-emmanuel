<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
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
            'unique_id' => ['required', 'exists:personalized_requests,unique_id,receiver_id,'.$this->input('id')],
            'status' => ['required' , Rule::in([PERSONALIZE_CREATOR_REJECTED,PERSONALIZE_CREATOR_ACCEPTED])],
            'cancel_reason' => ['nullable', 'required_if:status,' . PERSONALIZE_CREATOR_REJECTED],
        ];
    }

    /**
     * Custom Validation Errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'unique_id.exists' => api_error(291),
            'cancel_reason.required_if' => api_error(293),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'error'   => $validator->errors()->first(),
            'error_code' => 422
        ]));
    }
}
