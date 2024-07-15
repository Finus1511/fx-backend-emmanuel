<?php

namespace App\Http\Requests\Api\LiveStreamShopping;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

class LiveStreamStoreRequest extends FormRequest
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
            'title' => 'required|max:255',
            'stream_type' => ['required', Rule::in([STREAM_TYPE_PUBLIC, STREAM_TYPE_PRIVATE])],
            'payment_type' => ['required', Rule::in([PAYMENT_TYPE_FREE, PAYMENT_TYPE_PAID])],
            'amount' => [
                'required_if:payment_type,' . PAYMENT_TYPE_PAID,
                'numeric',
                $this->input('payment_type') == PAYMENT_TYPE_PAID ? 'min:1' : 'min:0',
            ],
            'schedule_type' => ['required', Rule::in([SCHEDULE_TYPE_NOW, SCHEDULE_TYPE_LATER])],
            'schedule_time' => ['required_if:schedule_type,' . SCHEDULE_TYPE_LATER, 'date'],
            'description' => 'required',
            'preview_file' => 'nullable|image|mimes:jpeg,png,gif,svg,webp',
            'user_product_ids.*' => 'required|exists:user_products,id',
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
