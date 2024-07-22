<?php

namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class PromoCodeStoreRequest extends FormRequest
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
    public function rules(Request $request)
    {
         return [
            'promo_code' => $request->promo_code_id ? 'required|max:10|min:4|unique:promo_codes,promo_code,'.$request->promo_code_id.',id' : 'required|min:4|max:10|unique:promo_codes,promo_code,NULL,id',
            'amount_type' => 'required|in:'.PERCENTAGE.','.ABSOULTE,
            'amount' => 'required|numeric|min:0'.($request->amount_type == PERCENTAGE ? '|max:100' : ''),
            'start_date' => 'required|after:now',
            'expiry_date' => 'nullable|after:start_date',
            'no_of_users_limit' => 'required|int',
            'per_users_limit' => 'required|int',
            'platform' => 'required|in:'.PLATFORMS,
            'status' => ['required', Rule::in([APPROVED, DECLINED])],
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
            'error_code' => 422,
        ]));
    }
}