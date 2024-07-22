<?php

namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PromoCodeValidateRequest extends FormRequest
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
        $types = [SUBSCRIPTION_PAYMENTS, USER_TIPS, POST_PAYMENTS, VIDEO_CALL_PAYMENTS,AUDIO_CALL_PAYMENTS, CHAT_ASSET_PAYMENTS, ORDER_PAYMENTS, LIVE_VIDEO_PAYMENTS, TOTAL_PAYMENTS, ALL_PAYMENTS];
        return [
            'promo_code' => 'required|string',
            'platform' => ['required', Rule::in($types)],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($this->platform === SUBSCRIPTION_PAYMENTS) {
            $validator->sometimes('subscription_id', 'required|exists:subscriptions,id', function ($input) {
                return true;
            });
        }elseif ($this->platform === POST_PAYMENTS) {
            $validator->sometimes('post_id', 'required|exists:posts,id', function ($input) {
                return true;
            });
        } elseif ($this->platform === VIDEO_CALL_PAYMENTS) {
            $validator->sometimes('video_call_request_id', 'required|exists:video_call_requests,id', function ($input) {
                return true;
            });
        } elseif ($this->platform === AUDIO_CALL_PAYMENTS) {
            $validator->sometimes('audio_call_request_id', 'required|exists:audio_call_requests,id', function ($input) {
                return true;
            });
        } elseif ($this->platform === CHAT_ASSET_PAYMENTS) {
            $validator->sometimes('chat_asset_id', 'required|exists:chat_assets,id', function ($input) {
                return true;
            });
        } elseif ($this->platform === LIVE_VIDEO_PAYMENTS) {
            $validator->sometimes('live_video_id', 'required|exists:live_videos,id', function ($input) {
                return true;
            });
        }
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