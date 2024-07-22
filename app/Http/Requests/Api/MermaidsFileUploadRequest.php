<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class MermaidsFileUploadRequest extends FormRequest
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
        $rules = [
            'files' => 'required|array',
            'files.*' => 'file',
            'file_type' => ['required', Rule::in([FILE_TYPE_IMAGE, FILE_TYPE_VIDEO])],
            'preview_file' => 'required_if:file_type,' . FILE_TYPE_VIDEO . '|mimes:jpg,jpeg,png,gif',
        ];

        if ($this->input('file_type') === FILE_TYPE_IMAGE) {
            $rules['files.*'] .= '|mimes:jpg,jpeg,png,gif';
        } elseif ($this->input('file_type') === FILE_TYPE_VIDEO) {
            $rules['files.*'] .= '|mimes:mp4,avi,mov';
        }

        return $rules;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
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
