<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\User, App\Models\Subscription, App\Models\SubscriptionPayment;

use App\Repositories\PaymentRepository as PaymentRepo;

class SubscriptionApiController extends Controller
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
     * @method subscriptions_index()
     *
     * @uses To display all the subscription plans
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function subscriptions_index(Request $request) {

        try {

            $base_query = Subscription::where('subscriptions.status' , APPROVED)->where('user_id', $request->user_id);

            $data['total_subscriptions'] = $base_query->count();

            $data['subscriptions'] = $base_query->latest()->get();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method subscriptions_view()
     *
     * @uses get the selected subscription details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function subscriptions_view(Request $request) {

        try {

            $subscription = Subscription::where('subscriptions.status' , APPROVED)->firstWhere('subscriptions.id', $request->subscription_id);

            if(!$subscription) {
                throw new Exception(api_error(129), 129);   
            }

            return $this->sendResponse($message = '' , $code = '', $subscription);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method subscriptions_store()
     *
     * @uses get the selected subscription details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function subscriptions_store(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'subscription_id' => 'nullable|exists:subscriptions,id,user_id,'.$request->id,
                'title'=>'required',
                'description'=>'nullable',
                'amount'=>'required|numeric',
                'plan'=>'required|numeric|min:1',
                'plan_type'=>'required|in:days,months,years',
                'discount'=>'nullable',
                'picture' => 'nullable|mimes:jpeg,jpg,gif,png,svg|exclude',
            ];

            $validated = Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $subscription = Subscription::updateOrCreate(
                ['id' => $request->subscription_id, 'user_id' => $request->id],
                $validated
            );

            if($request->hasFile('picture')) {

                $subscription->picture = Helper::storage_upload_file($request->file('picture') , PROFILE_PATH_USER);

                $subscription->save();

            }

            $subscription->wasRecentlyCreated && User::where('id', $request->id)
                    ->update([
                        'user_account_type' => USER_PREMIUM_ACCOUNT, 
                        'is_content_creator' => CONTENT_CREATOR, 
                        'content_creator_step' => CONTENT_CREATOR_APPROVED
                    ]);

            DB::commit();

            $code = $subscription->wasRecentlyCreated ? '258' : '259' ;

            $data['subscription'] = $subscription->refresh();

            return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method subscriptions_delete()
     *
     * @uses get the selected subscription details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function subscriptions_delete(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'subscription_id' => 'required|exists:subscriptions,id,user_id,'.$request->id
            ];

            $validated = Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $subscription = Subscription::find($request->subscription_id);

            if($subscription->delete()) {

                DB::commit();

                $data['subscription'] = $subscription;

                return $this->sendResponse(api_success(260), 260, $data);

            }

            throw new Exception(api_error(336), 336);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /** 
     * @method subscriptions_payment_by_card()
     *
     * @uses pay for subscription using paypal
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function subscriptions_payment_by_card(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'subscription_id' => 'required|exists:subscriptions,id',
                'promo_code' => 'nullable|exists:promo_codes,promo_code'
            ];

            $custom_errors = ['subscription_id' => api_error(129)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $subscription = Subscription::Approved()->firstWhere('id',  $request->subscription_id);

            if(!$subscription) {

                throw new Exception(api_error(129), 129);
                
            }

            $is_user_subscribed_free_plan = $this->loginUser->one_time_subscription ?? NO;

            if($subscription->amount <= 0 && $is_user_subscribed_free_plan) {

                throw new Exception(api_error(130), 130);
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $total = $subscription->amount ?? 0.00;

            $user_pay_amount = Helper::apply_promo_code($request, $total, SUBSCRIPTION_PAYMENTS, 000);

            if($user_pay_amount > 0) {

                $user_card = \App\Models\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }
                
                $request->request->add([
	                'total' => $total, 
	                'customer_id' => $user_card->customer_id,
                    'card_token' => $user_card->card_token,
	                'user_pay_amount' => $user_pay_amount,
	                'paid_amount' => $user_pay_amount,
                    'promo_discount' => $total - $user_pay_amount
	            ]);


                $card_payment_response = PaymentRepo::subscriptions_payment_by_stripe($request, $subscription)->getData();
               	
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

            $payment_response = PaymentRepo::subscriptions_payment_save($request, $subscription)->getData();

            if($payment_response->success) {
                
                DB::commit();

                $code = 118;

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
     * @method subscriptions_history()
     *
     * @uses get the selected subscription details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function subscriptions_history(Request $request) {

        try {

            $subscription_payments = SubscriptionPayment::BaseResponse()->where('user_id' , $request->id)->skip($this->skip)->take($this->take)->orderBy('subscription_payments.id', 'desc')->get();

            foreach ($subscription_payments as $key => $value) {

                $value->plan_text = formatted_plan($value->plan ?? 0);

                $value->expiry_date = common_date($value->expiry_date, $this->timezone, 'M, d Y');

                $value->show_autorenewal_options = 
                $value->show_autorenewal_pause_btn = 
                $value->show_autorenewal_enable_btn = HIDE;

                if($key == 0) {

                    $value->show_autorenewal_options = ($value->status && $value->subscription_amount > 0)? SHOW : HIDE;

                    if($value->show_autorenewal_options == SHOW) {

                        $value->show_autorenewal_pause_btn = $value->is_cancelled == AUTORENEWAL_ENABLED ? HIDE : SHOW;

                        $value->show_autorenewal_enable_btn = $value->show_autorenewal_pause_btn ? NO : YES;
                    }

                }
            
            }

            return $this->sendResponse($message = '' , $code = '', $subscription_payments);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method subscriptions_autorenewal_status
     *
     * @uses To prevent automatic subscriptioon, user have option to cancel subscription
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param 
     *
     * @return json reponse
     */
    public function subscriptions_autorenewal_status(Request $request) {

        try {

            DB::beginTransaction();

            $user_subscription = SubscriptionPayment::where('subscription_payments.id', $request->user_subscription_id)->where('status', DEFAULT_TRUE)->firstWhere('user_id', $request->id);

            if(!$user_subscription) {

                throw new Exception(api_error(152), 152);   

            }

            // Check the subscription is already cancelled

            if($user_subscription->is_cancelled == AUTORENEWAL_CANCELLED) {

                $user_subscription->is_cancelled = AUTORENEWAL_ENABLED;

                $user_subscription->cancel_reason = $request->cancel_reason ?? '';

            } else {

                $user_subscription->is_cancelled = AUTORENEWAL_CANCELLED;

                $user_subscription->cancel_reason = $request->cancel_reason ?? '';

            }

            $user_subscription->save();

            DB::commit();

            $data['user_subscription_id'] = $request->user_subscription_id;

            $data['is_autorenewal_status'] = $user_subscription->is_cancelled;

            $code = $user_subscription->is_cancelled == AUTORENEWAL_CANCELLED ? 120 : 119;

            return $this->sendResponse(api_success($code) , $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_subscriptions_payment_by_wallet()
     * 
     * @uses send money to other user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function subscriptions_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'subscription_id' => 'required|exists:subscriptions,id',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user_subscription = Subscription::find($request->subscription_id);

            if(!$user_subscription || $user_subscription->user_id == $request->id) {

                throw new Exception(api_error(155), 155);   
            }

            $check_user_payment = \App\Models\UserSubscriptionPayment::UserPaid($request->id, $user_subscription->user_id)->first();

            if($check_user_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $user_subscription->amount ?? 0;

            $total = $subscription_amount ?? 0.00;

            $user_pay_amount = Helper::apply_promo_code($request, $total, SUBSCRIPTION_PAYMENTS, $user_subscription->user_id);// Check the user has enough balance 

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }            

            if($remaining < $user_pay_amount) {

                throw new Exception(api_error(147), 147);
            }
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $subscription_amount, 
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'to_user_id' => $user_subscription->user_id,
                'payment_id' => 'WPP-'.rand(),
                'tokens' => $user_pay_amount,
                'usage_type' => USAGE_TYPE_SUBSCRIPTION,
                'promo_discount' => $total - $user_pay_amount
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                DB::commit();

                $code = $subscription_amount > 0 ? 140 : 235;

                return $this->sendResponse(api_success($code), $code, $payment_response->data ?? []);

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

}