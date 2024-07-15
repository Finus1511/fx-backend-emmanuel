<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Log, Hash, Validator, Exception, Setting;
use Illuminate\Validation\Rule;
use App\Models\{ User, Post, LiveVideo, LiveVideoPayment, VideoCallRequest, VideoCallPayment, AudioCallRequest, AudioCallPayment, ChatMessage, ChatAsset,  UserSubscription, UserSubscriptionPayment, PromoCode, PostPayment};
use App\Jobs\{ TipPaymentJob};
use App\Helpers\Helper;
use App\Repositories\PaymentRepository as PaymentRepo;

class InAppPurchaseController extends Controller
{

    protected $loginUser;

    protected $skip, $take;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /** 
     * @method tips_payment_by_iap()
     *
     * @uses tip payment to user
     *
     * @created Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function tips_payment_by_iap(Request $request) {

        try {

            DB::beginTransaction();

            throw_if($request->id == $request->user_id, new Exception(api_error(154), 154));

            $rules = [
                    'post_id' => 'nullable|exists:posts,id',
                    'user_id' => 'required|exists:users,id',
                    'amount' => 'required|numeric|min:1',
                    'payment_id' =>'required',
                    'status' => ['required', Rule::in(PAID, UNPAID)],
                ];

            $custom_errors = ['post_id' => api_error(139), 'user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            $user = User::Approved()->firstWhere('id',  $request->user_id);

            throw_if(!$user, new Exception(api_error(135), 135));

            $user_pay_amount = $request->amount ?: 1;
            
            $request->request->add([
                'to_user_id' => $request->user_id,
                'tokens' => $user_pay_amount,
                'payment_mode' => INAPP_PURCHASE,
                'total' => $user_pay_amount, 
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => $request->payment_id,
                'usage_type' => USAGE_TYPE_TIP,
                'status' => $request->status,
            ]);
           
            $payment_response = PaymentRepo::tips_payment_save($request)->getData();

            if($payment_response->success) {
            
            DB::commit();
            
            $job_data['user_tips'] = $request->all();

            $job_data['timezone'] = $this->timezone;

            TipPaymentJob::dispatch($job_data);

            return $this->sendResponse(api_success(146), 146, $payment_response->data);

            } else {
              
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }
    
    /** 
     * @method live_videos_payment_by_iap()
     *
     * @uses Live video payment by in app purchase
     *
     * @created Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function live_videos_payment_by_iap(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'live_video_id' => 'required|exists:live_videos,id',
                'payment_id' => 'required',
                'amount' => 'required|numeric|min:1',
                'status' => ['required', Rule::in(PAID, UNPAID)],

            ];

            $custom_errors = ['live_video_id' => api_error(150)];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            $live_video = LiveVideo::where('id',  $request->live_video_id)->CurrentLive()->first();

            throw_if(!$live_video, new Exception(api_error(201), 201));

            $live_video_payment = LiveVideoPayment::where(['live_video_viewer_id' => $request->id, 'live_video_id' => $request->live_video_id, 'status' => DEFAULT_TRUE])->count();

            if($live_video->payment_status == NO || $live_video_payment) {

                $code = 140;

                goto successReponse;
                
            }

            $total = $user_pay_amount = $request->amount ?? 0.00;

            $request->request->add(['payment_mode' => INAPP_PURCHASE, 'paid_amount' => $total, 'tokens' => $total, 'status' => $request->status,
            ]);

            $payment_response = PaymentRepo::live_videos_payment_save($request, $live_video)->getData();

            if($payment_response->success) {
                
                DB::commit();

                $code = 118;

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
            }          

            successReponse:

            $data['live_video_id'] = $request->live_video_id;

            $data['live_video_unique_id'] = $live_video->unique_id;

            $data['payment_mode'] = INAPP_PURCHASE;

            return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }
    /** 
     * @method video_call_payment_by_iap()
     *
     * @uses video call payment to user
     *
     * @created Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function video_call_payment_by_iap(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'video_call_request_id' => 'required|exists:video_call_requests,id,user_id,'.$request->id,
                'payment_id' => 'required',
                'amount' => 'required|numeric|min:1',
                'status' => ['required', Rule::in(PAID, UNPAID)],
            ];

            $custom_errors = ['video_call_request_id' => api_error(214)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            $video_call_request = VideoCallRequest::find($request->video_call_request_id);

            $video_call_payment = VideoCallPayment::PaidApproved()->firstWhere('video_call_request_id',  $request->video_call_request_id);

            throw_if($video_call_payment, new Exception(api_error(213), 213));
        
            $total = $user_pay_amount = $request->amount ?: 1;
            
            $request->request->add([
                'video_call_request_id'=>$video_call_request->id,
                'paid_amount' => $total,
                'payment_mode' => INAPP_PURCHASE,
                'payment_id' => $request->payment_id,
                'model_id' => $video_call_request->model_id,
                'tokens' => $total,
                'status' => $request->status,
            ]);

            $payment_response = PaymentRepo::video_call_payments_save($request)->getData();

            if($payment_response->success) {
                
                DB::commit();
                
                return $this->sendResponse(api_success(212), 212, $payment_response->data);

            } else {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

        /** 
     * @method audio_call_payment_by_iap()
     *
     * @uses audio call payment to user
     *
     * @created Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function audio_call_payment_by_iap(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'audio_call_request_id' => 'required|exists:audio_call_requests,id,user_id,'.$request->id,
                'payment_id' => 'required',
                'amount' => 'required|numeric|min:1',
                'status' => ['required', Rule::in(PAID, UNPAID)]
            ];

            $custom_errors = ['audio_call_request_id' => api_error(230)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            $audio_call_request = AudioCallRequest::find($request->audio_call_request_id);

            $audio_call_payment = AudioCallPayment::PaidApproved()->firstWhere('audio_call_request_id',  $request->audio_call_request_id);

            throw_if($audio_call_payment, new Exception(api_error(231), 231));
        
            $total = $user_pay_amount = $request->amount ?: 1;
            
            $request->request->add([
                'audio_call_request_id'=>$audio_call_request->id,
                'paid_amount' => $total,
                'payment_mode' => INAPP_PURCHASE,
                'payment_id' => $request->payment_id,
                'model_id' => $audio_call_request->model_id,
                'tokens' => $total,
                'status' => $request->status,
            ]);

            $payment_response = PaymentRepo::audio_call_payments_save($request)->getData();

            if($payment_response->success) {
                
                DB::commit();
                
                return $this->sendResponse(api_success(230), 230, $payment_response->data);

            } else {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

        /**
     * @method chat_assets_payment_by_iap()
     * 
     * @uses Chat Asset payment by in app purchase
     *
     * @created Shakthi
     *
     * @updated 
     *
     * @param object $request - Chat message id
     *
     * @return json with boolean output
     */

    public function chat_assets_payment_by_iap(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'chat_message_id' => 'required|numeric',
                'payment_id' => 'required',
                'amount' => 'required|numeric|min:1',
                'status' => ['required', Rule::in(PAID, UNPAID)]
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $chat_message = ChatMessage::firstWhere('id',$request->chat_message_id);

            $chat_asset = ChatAsset::firstWhere('chat_message_id',$request->chat_message_id);

            throw_if(!$chat_message || !$chat_asset, new Exception(api_error(3000), 3000));

            $total = $user_pay_amount = $request->amount ?: 0.00;

            $request->request->add([
                'payment_mode' => INAPP_PURCHASE,
                'paid_amount'=> $total, 
                'usage_type' => USAGE_TYPE_CHAT, 
                'user_pay_amount' => $total,
                'paid_status' => PAID_STATUS,
                'tokens' => $total,
                'status' => $request->status
            ]);
            
            $payment_response = PaymentRepo::chat_assets_payment_save($request, $chat_message)->getData();

            if($payment_response->success) {
                
                DB::commit();

                $data['chat_message'] = $chat_message;
                
                return $this->sendResponse(api_success(118), 118, $data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /** 
     * @method user_subscriptions_payment_by_iap()
     *
     * @uses user subscription payment by iap
     *
     * @created Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function user_subscriptions_payment_by_iap(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'payment_id' => 'required',
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
                'amount' => 'required|numeric|min:1',
                'status' => ['required', Rule::in(PAID, UNPAID)]
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::where('unique_id', $request->user_unique_id)->first();

            throw_if(!$user, new Exception(api_error(135), 135));

            $user_subscription = $user->userSubscription;

            if(!$user_subscription) {
                
                if($request->is_free == YES) {

                    $user_subscription = new UserSubscription;

                    $user_subscription->user_id = $user->id;

                    $user_subscription->save();
                    
                } else {

                    throw new Exception(api_error(155), 155);   
 
                }

            }
           
            $check_user_payment = UserSubscriptionPayment::UserPaid($request->id, $user->id)->first();

            throw_if($check_user_payment, new Exception(api_error(268), 268));

            $user_pay_amount = $request->amount ?: 0.00;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($user_pay_amount,$request);

                    $user_pay_amount = $user_pay_amount - $promo_amount;
                }

            }

            $request->request->add([
                'payment_mode'=> INAPP_PURCHASE,
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount, 
                'payment_id' => $request->payment_id, 
                'tokens' => $user_pay_amount, 
                'paid_status' => $request->status
            ]);

            $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription, $promo_amount)->getData();

            if($payment_response->success) {

                DB::commit();

                $code = $user_pay_amount > 0 ? 140 : 235;

                return $this->sendResponse(api_success($code), $code, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

     /** 
     * @method post_payment_by_iap()
     *
     * @uses post payment by iap
     *
     * @created Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function post_payment_by_iap(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'payment_id' => 'required',
                'post_id' => 'required|exists:posts,id',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
                'amount' => 'required|numeric|min:1',
                'status' => ['required', Rule::in(PAID, UNPAID)]
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $post = Post::PaidApproved()->firstWhere('id',  $request->post_id);

            throw_if(!$post, new Exception(api_error(146), 146));

            throw_if($request->id == $post->user_id, new Exception(api_error(171), 171));

            $check_post_payment = PostPayment::UserPaid($request->id, $request->post_id)->first();

            throw_if($check_post_payment, new Exception(api_error(145), 145));

            $post_amount = $request->amount;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($post_amount,$request);

                    $post_amount = $post_amount - $promo_amount;
                }

            }

            $user_pay_amount = $post_amount ?: 0.00;
           
             $request->request->add([
                'payment_mode'=> INAPP_PURCHASE,
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount, 
                'payment_id' => $request->payment_id, 
                'tokens' => $user_pay_amount, 
                'paid_status' => $request->status,
                'usage_type' => USAGE_TYPE_PPV
            ]);

            $payment_response = PaymentRepo::post_payments_save($request, $post, $promo_amount)->getData();

           if($payment_response->success) {
                    
                DB::commit();

                return $this->sendResponse(api_success(140), 140, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
            }  
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }
}
