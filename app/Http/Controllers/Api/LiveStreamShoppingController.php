<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Api\LiveStreamShopping\{LiveStreamStoreRequest, LssDeliveryAddressStoreRequest};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\{LiveStreamResource, LiveStreamProductOrderResource, LiveStreamPaymentResource, LssDeliveryAddressResource, UserProductResource};
use App\Models\{User, LiveStreamShopping, LssProduct, UserProduct, LssProductPayment, UserWallet, LssPayment, LssDeliveryAddress, Follower};
use Log, Setting, DB, Exception;
use App\Helpers\Helper;
use App\Repositories\PaymentRepository as PaymentRepo;

class LiveStreamShoppingController extends Controller
{
    protected $loginUser, $skip, $take;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));

        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

        $request->request->add(['timezone' => $this->timezone]);

    }

    /**
     * @method create_live_stream()
     *
     * @uses To Create the live stream
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param App\Http\Requests\Api\LiveStreamStoreRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_live_stream(LiveStreamStoreRequest $request) {

        try {

            DB::beginTransaction();

            $user = User::firstWhere(['id' => $request->id, 'is_content_creator' => CONTENT_CREATOR]);

            throw_if(!$user, new Exception(api_error(289), 289));

            $live_stream_shopping_data = [
                'user_id' => $request->id,
                'schedule_time' => $request->schedule_type == SCHEDULE_TYPE_LATER ?common_server_date($request->schedule_time, $request->timezone, 'Y-m-d H:i:s') : now(),
                'is_streaming' => $request->schedule_type == SCHEDULE_TYPE_LATER ? NO : YES,
                'start_time' => $request->schedule_type == SCHEDULE_TYPE_LATER ? '' : now(),
                'status' =>  $request->schedule_type == SCHEDULE_TYPE_LATER ? LIVE_STREAM_SHOPPING_CREATED : LIVE_STREAM_SHOPPING_ONGOING,
            ];

            $live_stream_shopping = LiveStreamShopping::create($live_stream_shopping_data +$request->validated());

            throw_if(!$live_stream_shopping, new Exception(api_error(306), 306));

            if($request->hasFile('preview_file')) {

                $file = $request->file('preview_file');

                $file_url = Helper::storage_upload_file($file, LIVE_STREAM_SHOPPING_FILE_PATH);

                $file_type = $file->getClientOriginalExtension() ?: '';

                $live_stream_shopping->update(['preview_file' => $file_url, 'preview_file_type' => $file_type]);
            }else{

                $live_stream_shopping->update(['preview_file' => asset('images/live-streaming.jpeg')]);
            }

           if ($request->user_product_ids) {

                $user_product_ids = explode(',', $request->user_product_ids);

                $lss_products = [];

                foreach ($user_product_ids as $user_product_id) {
                    
                    $lss_products[] = [
                        'unique_id' => "LSSP-".uniqid(),
                        'user_id' => $request->id,
                        'live_stream_shopping_id' => $live_stream_shopping->id,
                        'user_product_id' => $user_product_id
                    ];
                }

                LssProduct::insert($lss_products);
            }

            DB::commit();

            $data['live_stream_shopping'] = new LiveStreamResource($live_stream_shopping->refresh());

            return $this->sendResponse(api_success(829), 829, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }
    
    /** 
     * @method live_stream_shoppings_view()
     *
     * @uses To get the current live streaming shoppings
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function live_stream_shoppings_view(Request $request) {

        try {

            $rules = ['live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            $live_stream_shopping = LiveStreamShopping::firstWhere(['unique_id' => $request->live_stream_shopping_unique_id]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $agora_app_id = Setting::get('agora_app_id');

            $app_certificate = Setting::get('agora_certificate_id');

            if (Setting::get('is_agora_configured') && ($request->device_type != DEVICE_WEB)) {

                $rules = ['virtual_id' => 'required'];

                Helper::custom_validator($request->all(), $rules, []);
            }

            throw_if(!$agora_app_id || !$app_certificate, new Exception(api_error(204), 204));

            $virtual_id = $request->virtual_id ?? md5(time());

            $token = '';

            if ($agora_configured = Setting::get('is_agora_configured')) {

                $uid = 0;

                $role = \RtcTokenBuilder::RoleAttendee;

                $expireTimeInSeconds = 3600;

                $privilegeExpiredTs = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp() + $expireTimeInSeconds;

                $token = \RtcTokenBuilder::buildTokenWithUid($agora_app_id, $app_certificate, $virtual_id, $uid, $role, $privilegeExpiredTs) ?? "";
            }

            if($request->id != $live_stream_shopping->user_id){

              throw_if($live_stream_shopping->is_streaming != YES && $live_stream_shopping->status != LIVE_STREAM_SHOPPING_ONGOING, new Exception(api_error(316), 316));

              if ($request->id != $live_stream_shopping->user_id) {

                 $live_stream_shopping->increment('viewer_count');
              }

            }else{

                $live_stream_shopping->update([
                    'start_time' => now(),
                    'is_streaming' => YES,
                    'status' => LIVE_STREAM_SHOPPING_ONGOING,
                ]);
            }

            $live_stream_shopping->update(['agora_token' => $token, 'virtual_id' => $virtual_id]);

            $data['live_stream_shopping'] = new LiveStreamResource($live_stream_shopping->refresh());

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

     /** 
     * @method live_stream_shopping_products()
     *
     * @uses To get the current live streaming shopping products
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */
        public function live_stream_shopping_products(Request $request) {

        try {

            $rules = [
                'live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id'
            ];

            Helper::custom_validator($request->all(), $rules);

            $live_stream_shopping = LiveStreamShopping::firstWhere([
                'unique_id' => $request->live_stream_shopping_unique_id,
                'is_streaming' => YES
            ]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $user_product_ids = LssProduct::where('live_stream_shopping_id', $live_stream_shopping->id)->pluck('user_product_id')->toArray();

           if(!empty($user_product_ids)){

                $base_query =  UserProduct::whereIn('id', $user_product_ids)->where('status', APPROVED)->when($request->filled('status'), function ($query) use ($request) {
                        $query->where('status', $request->status);

                    })->when($request->filled('search_key'), function ($query) use ($request) {
                        $query->where(function ($query) use ($request) {
                            $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')->orWhere('name', 'LIKE', '%' . $request->search_key . '%');
                           });
                    });
            }

            $user_products = $base_query->get();

            $data = [
                'available_user_products_count' => $user_products->where('quantity', '>', 0)->count(),
                'sold_products_count' => $user_products->where('quantity', '=', 0)->count()
            ];

            $data['user_products'] = UserProductResource::collection($user_products);

            return $this->sendResponse('', '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    }

     /**
 * @method ongoing_live_stream_shopping()
 *
 * @uses To get list of the Live Stream shopping onlive
 *
 * @created RA Shakthi
 *
 * @updated
 *
 * @param request id
 *
 * @return JSON Response
 */
public function ongoing_live_stream_shopping(Request $request)
{
    try {
        DB::beginTransaction();

        $base_query = LiveStreamShopping::where([['user_id', '!=', $request->id],'is_streaming' => YES,'status' => LIVE_STREAM_SHOPPING_ONGOING,'stream_type' => STREAM_TYPE_PUBLIC]);

        $follower_ids = Follower::where('follower_id', $request->id)->pluck('user_id')->toArray();

        $live_stream_shoppings_private_videos_query = LiveStreamShopping::where([['user_id', '!=', $request->id],'is_streaming' => YES,'status' => LIVE_STREAM_SHOPPING_ONGOING,'stream_type' => STREAM_TYPE_PRIVATE])->whereIn('user_id', $follower_ids);

        if ($live_stream_shoppings_private_videos_query->exists()) {

            $live_stream_shoppings_private_videos = $live_stream_shoppings_private_videos_query->get();

            $live_stream_shoppings = $base_query->get();

            $paid_live_streams = $live_stream_shoppings->filter(function ($live_stream_shopping) {
                return LssPayment::where('live_stream_shopping_id', $live_stream_shopping->id)->exists();
            });

            $unpaid_live_streams = $live_stream_shoppings->filter(function ($live_stream_shopping) {
                return !LssPayment::where('live_stream_shopping_id', $live_stream_shopping->id)->exists();
            });

            $sorted_live_stream_shoppings = $paid_live_streams->sortByDesc('payment_type')->sortByDesc('created_at')->merge($unpaid_live_streams)->values();

            $merged_live_stream_shoppings = $live_stream_shoppings_private_videos->merge($sorted_live_stream_shoppings);

            $paged_live_stream_shoppings = $merged_live_stream_shoppings->skip($this->skip)->take($this->take);
             
            $data['total'] = $merged_live_stream_shoppings->count();

            $data['live_stream_shoppings_onlive'] = LiveStreamResource::collection($paged_live_stream_shoppings);

        } else {

            $data['total'] = $base_query->count();

            $live_stream_shoppings = $base_query->get();

            $paid_live_streams = $live_stream_shoppings->filter(function ($live_stream_shopping) {
                return LssPayment::where('live_stream_shopping_id', $live_stream_shopping->id)->exists();
            });

            $unpaid_live_streams = $live_stream_shoppings->filter(function ($live_stream_shopping) {
                return !LssPayment::where('live_stream_shopping_id', $live_stream_shopping->id)->exists();
            });

            $sorted_live_stream_shoppings = $paid_live_streams->sortByDesc('payment_type')->sortByDesc('created_at')->merge($unpaid_live_streams)->skip($this->skip)->take($this->take);

            $data['live_stream_shoppings_onlive'] = LiveStreamResource::collection($sorted_live_stream_shoppings);
        }

        DB::commit();

        return $this->sendResponse('', '', $data);

    } catch (\Exception $e) {

        DB::rollBack();

        return $this->sendError($e->getMessage(), $e->getCode());
    }
}


    /**
     * @method scheduled_live_stream_shoppings()
     *
     * @uses To get list of the Live Stream shopping onlive
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function scheduled_live_stream_shoppings(Request $request)
    {
        try {

            DB::beginTransaction();

            $base_query = LiveStreamShopping::where([['user_id', '!=', $request->id],'schedule_type' => SCHEDULE_TYPE_LATER, 'stream_type' => STREAM_TYPE_PUBLIC,['schedule_time', '>=', now()]]);

             $follower_ids = Follower::where('follower_id', $request->id)->pluck('user_id')->toArray();

            $scheduled_lss_private_videos_query = LiveStreamShopping::where([['user_id', '!=', $request->id], 'schedule_type' => SCHEDULE_TYPE_LATER,'stream_type' => STREAM_TYPE_PRIVATE],['schedule_time', '>=', now()])->whereIn('user_id', $follower_ids);

             if ($scheduled_lss_private_videos_query->exists()) {

                $scheduled_lss_private_videos = $scheduled_lss_private_videos_query->skip($this->skip)->take($this->take)->orderByDesc('payment_type')->orderByDesc('created_at')->get();

                $schedule_live_stream_shoppings = $base_query->skip($this->skip)->take($this->take)->orderByDesc('payment_type')->orderByDesc('created_at')->get();

                $merged_schedule_live_stream_shoppings = $scheduled_lss_private_videos->merge($schedule_live_stream_shoppings);

                $data['total'] = $merged_schedule_live_stream_shoppings->count();

                $data['scheduled_live_stream_shoppings'] = LiveStreamResource::collection($merged_schedule_live_stream_shoppings);

            }else{

                $data['total'] = $base_query->count();                

                $schedule_live_stream_shoppings = $base_query->skip($this->skip)->take($this->take)->orderByDesc('payment_type')->orderByDesc('created_at')->get();

                $data['scheduled_live_stream_shoppings'] = LiveStreamResource::collection($schedule_live_stream_shoppings);
            }

            DB::commit();

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {
            
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @method creator_live_stream_shoppings()
     *
     * @uses To get list of creator's Live Stream shopping
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function creator_live_stream_shoppings(Request $request)
    {
        try {

            DB::beginTransaction();

            $base_query = LiveStreamShopping::where('user_id', $request->id);

            $data['total'] = $base_query->count();

            $live_stream_shoppings = $base_query->latest()->skip($this->skip)->take($this->take)->orderByDesc('created_at')->get();

            $data['creator_live_stream_shoppings'] = LiveStreamResource::collection($live_stream_shoppings);

            DB::commit();

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {
            
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }



    /**
     * @method lss_product_orders_list()
     *
     * @uses To get list of the Live Stream shopping onlive
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function lss_product_orders_list(Request $request)
    {
        try {

            DB::beginTransaction();

            $base_query = LssProductPayment::where('user_id', $request->id)
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);

                })->when($request->filled('search_key'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')->orWhereHas('user', function ($query) use ($request) {
                                $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                        })->orWhereHas('userProduct', function ($query) use ($request) {
                            $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                        });
                    });
                });

            $data['total'] = $base_query->count();

            $live_stream_shopping_orders = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['live_stream_shopping_orders'] = LiveStreamProductOrderResource::collection($live_stream_shopping_orders);

            DB::commit();

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {
            
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


     /**
     * @method lss_product_payment_by_wallet()
     * 
     * @uses To pay the live stream shopping product
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return  JSON Response
     */
    public function lss_product_payment_by_wallet(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'user_product_unique_id' => 'required|exists:user_products,unique_id',
                'live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id',
                'lss_delivery_address_id' => 'required|exists:lss_delivery_addresses,id,user_id,' . $request->id,
                'promo_code' => 'nullable|exists:promo_codes,promo_code'
            ];

            Helper::custom_validator($request->all(), $rules);

            $user_product = UserProduct::firstWhere(['unique_id' => $request->user_product_unique_id]);

            throw_if(!$user_product, new Exception(api_error(133), 133));

            throw_if($user_product->user_id == $request->id, new Exception(api_error(286), 286));

            throw_if(!$user_product->quantity > 0, new Exception(api_error(311), 311));

            $live_stream_shopping = LiveStreamShopping::firstWhere(['unique_id' => $request->live_stream_shopping_unique_id, 'is_streaming' => YES]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $user_wallet = UserWallet::firstWhere(['user_id' => $request->id]);

            throw_if(!$user_wallet, new Exception(api_error(282), 282)); 

            $remaining = $user_wallet->remaining ?: 0;

            if (Setting::get('is_referral_enabled')) {

                $remaining += $user_wallet->referral_amount ?: 0;
            }

            $total = $user_product->price;

            $user_product_price = Helper::apply_promo_code($request, $total, LIVE_VIDEO_PAYMENTS, $user_product->user_id);

            throw_if($remaining < $user_product_price, new Exception(api_error(147), 147));
            
            if($user_product_price > 0) {

                $request->merge([
                    'to_user_id' => $user_product->user_id,
                    'total' => $user_product_price * Setting::get('token_amount'),
                    'user_pay_amount' => $user_product_price,
                    'paid_amount' => $user_product_price * Setting::get('token_amount'),
                    'payment_id' => 'LSSPP-' . rand(),
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'usage_type' => USAGE_TYPE_LIVE_STREAM_SHOPPING,
                    'tokens' => $user_product_price,
                    'promo_discount' => $total - $user_product_price
                ]);

                $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

                if($wallet_payment_response->success){

                     $lss_product_payment = LssProductPayment::create([
                        'user_id' => $request->id,
                        'live_stream_shopping_id' => $live_stream_shopping->id,
                        'lss_delivery_address_id' => $request->lss_delivery_address_id,
                        'user_product_id' => $user_product->id,
                        'payment_mode' => PAYMENT_MODE_WALLET,
                        'payment_id' => $request->payment_id,
                        'amount' => $user_product_price * Setting::get('token_amount'),
                        'user_amount' => $user_product_price * Setting::get('token_amount'),
                        'admin_amount' => $user_product_price * Setting::get('token_amount'),
                        'promo_code' => $request->promo_code ?? '',
                        'promo_discount' => $total - $user_product_price,
                        'status' => PAID,
                    ]);

                    if($lss_product_payment->status == PAID) {

                       $wallet_payment_response = PaymentRepo::lss_product_payment_wallet_update($request, $user_product, $lss_product_payment);

                        throw_if(!$wallet_payment_response->success, new Exception(api_error(317), 317)); 
                    }

                    DB::commit();

                    $user_product->update(['quantity' => $user_product->quantity-1]);

                    $data['lss_product_payment'] = new LiveStreamProductOrderResource($lss_product_payment->refresh());

                    return $this->sendResponse(api_success(826), 826, $data ?? []);

                }else{

                    throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                }
            }

            return $this->sendError(api_error(303), 303, '');

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @method creator_update_request_url()
     *
     * @uses To update the request shipping url
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return  JSON Response
     */
    public function creator_update_shipping_url(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'unique_id' => 'required|exists:lss_product_payments,unique_id',
                'live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id,user_id,' . $request->id,
                'shipping_url' => 'required|url'
            ];
 
            Helper::custom_validator($request->all(), $rules);

            $live_stream_shopping = LiveStreamShopping::firstWhere(['unique_id' => $request->live_stream_shopping_unique_id, 'user_id' => $request->id]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $live_stream_shopping_payment = LssProductPayment::firstWhere(['unique_id' => $request->unique_id, 'status' => PAID, 'live_stream_shopping_id' => $live_stream_shopping->id]);

            throw_if(!$live_stream_shopping_payment, new Exception(api_error(308), 308));

            $live_stream_shopping_payment->update(['shipping_url' => $request->shipping_url]);
            
            throw_if(!$live_stream_shopping_payment, new Exception(api_error(309), 309));

            DB::commit();

            $data['live_stream_shopping_payment'] = new LiveStreamProductOrderResource($live_stream_shopping_payment);

            return $this->sendResponse(api_success(827), 827, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

     /**
     * @method lss_order_products_view()
     *
     * @uses To view order details using unique id
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return  JSON Response
     */
    public function lss_order_products_view(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'unique_id' => 'required|exists:lss_product_payments,unique_id',
            ];
 
            Helper::custom_validator($request->all(), $rules);

            $lss_order_product = LssProductPayment::firstWhere(['unique_id' => $request->unique_id]);

            throw_if(!$lss_order_product, new Exception(api_error(308), 308));

            DB::commit();

            $data['lss_order_product'] = new LiveStreamProductOrderResource($lss_order_product);

            return $this->sendResponse('', '', $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /** 
     * @method live_stream_shopping_end()
     *
     * @uses To End the live streaming shopping videos 
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function live_stream_shopping_end(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id,user_id,' . $request->id,
            ];

            Helper::custom_validator($request->all(), $rules);

            $live_stream_shopping = LiveStreamShopping::firstWhere(['unique_id' => $request->live_stream_shopping_unique_id, 'is_streaming' => YES, 'status' => LIVE_STREAM_SHOPPING_ONGOING]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $live_stream_shopping->update(['is_streaming' => NO, 'status' => LIVE_STREAM_SHOPPING_COMPLETED, 'end_time' => now()]);

            DB::commit();

            $data['live_stream_shopping'] = new LiveStreamResource($live_stream_shopping->refresh());

            return $this->sendResponse(api_success(828), 828, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /**
     * @method live_stream_shoppings_history()
     *
     * @uses To get history of the Live Stream shopping
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function live_stream_shoppings_history(Request $request)
    {
        try {

            DB::beginTransaction();

            $base_query = LiveStreamShopping::where('user_id', $request->id)
               ->when($request->filled('status'), function ($query) use ($request) {
                 return $query->where('status', $request->status);
               })->when($request->filled('search_key'), function ($query) use ($request) {
                return $query->where(function ($query) use ($request) {
                $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                    ->orWhere('title', 'LIKE', '%' . $request->search_key . '%');
                });
            });

            $data['total'] = $base_query->count();

            $live_stream_shoppings_history = $base_query->skip($this->skip)->take($this->take)->orderByDesc('created_at')->latest()->get();

            $data['live_stream_shoppings_history'] = LiveStreamResource::collection($live_stream_shoppings_history);

            DB::commit();

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {
            
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

      /**
     * @method live_stream_shopping_payment_by_wallet()
     * 
     * @uses To pay the live stream shopping
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return  JSON Response
     */
    public function live_stream_shopping_payment_by_wallet(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules);

            $live_stream_shopping = LiveStreamShopping::firstWhere(['unique_id' => $request->live_stream_shopping_unique_id, 'payment_type' => PAYMENT_TYPE_PAID]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $user_wallet = UserWallet::firstWhere(['user_id' => $request->id]);

            throw_if(!$user_wallet, new Exception(api_error(282), 282)); 

            $remaining = $user_wallet->remaining ?: 0;

            if (Setting::get('is_referral_enabled')) {

                $remaining += $user_wallet->referral_amount ?: 0;
            }

            $total = $live_stream_shopping->amount;

            $live_stream_shopping_amount = Helper::apply_promo_code($request, $total, LIVE_VIDEO_PAYMENTS, $live_stream_shopping->user_id);

            throw_if($remaining < $live_stream_shopping_amount, new Exception(api_error(147), 147));

            $lss_payment_exists = LssPayment::where(['live_stream_shopping_id' => $live_stream_shopping->id, 'user_id' => $request->id])->exists();

            throw_if($lss_payment_exists, new Exception(api_error(310, $live_stream_shopping->title), 310));
            
            if($live_stream_shopping_amount > 0) {

                $request->merge([
                    'to_user_id' => $live_stream_shopping->user_id,
                    'total' => $live_stream_shopping_amount * Setting::get('token_amount'),
                    'user_pay_amount' => $live_stream_shopping_amount,
                    'paid_amount' => $live_stream_shopping_amount * Setting::get('token_amount'),
                    'payment_id' => 'AC-' . rand(),
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'usage_type' => USAGE_TYPE_LIVE_STREAM_SHOPPING,
                    'tokens' => $live_stream_shopping_amount,
                    'promo_code' => $request->promo_code,
                    'promo_discount' => $total - $live_stream_shopping_amount
                ]);

                $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

                if($wallet_payment_response->success){

                    $lss_payment = LssPayment::create([
                        'user_id' => $request->id,
                        'live_stream_shopping_id' => $live_stream_shopping->id,
                        'payment_mode' => PAYMENT_MODE_WALLET,
                        'payment_id' => 'LSSPP-'.rand(),
                        'amount' => $live_stream_shopping_amount * Setting::get('token_amount'),
                        'user_amount' => $live_stream_shopping_amount * Setting::get('token_amount'),
                        'promo_code' => $request->promo_code,
                        'promo_discount' => $total - $live_stream_shopping_amount,
                        'status' => PAID,
                    ]);

                    if($lss_payment->status == PAID) {

                       $wallet_payment_response = PaymentRepo::lss_payment_wallet_update($request, $live_stream_shopping, $lss_payment);

                        throw_if(!$wallet_payment_response->success, new Exception(api_error(318), 318)); 
                    }

                    DB::commit();

                    $data['lss_payment'] = new LiveStreamPaymentResource($lss_payment->refresh());

                    return $this->sendResponse(api_success(829), 829, $data ?? []);

                }else{

                    throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                }
            }

            return $this->sendError(api_error(303), 303, '');

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method lss_product_orders_recieved_list()
     *
     * @uses To get list of lss product orders recieved list
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function lss_product_orders_recieved_list(Request $request)
    {
        try {

            DB::beginTransaction();

            $rules = [
                'live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id,user_id,' . $request->id,
            ];

            Helper::custom_validator($request->all(), $rules);

            $live_stream_shopping = LiveStreamShopping::firstWhere(['unique_id' => $request->live_stream_shopping_unique_id, 'user_id' => $request->id]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $base_query = LssProductPayment::where('live_stream_shopping_id', $live_stream_shopping->id)->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);

                })->when($request->filled('search_key'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')->orWhereHas('user', function ($query) use ($request) {
                                $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                        })->orWhereHas('userProduct', function ($query) use ($request) {
                            $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                        });
                    });
                });

            $data['total'] = $base_query->count();

            $lss_product_orders = $base_query->skip($this->skip)->take($this->take)->get();

            $data['lss_products_recieved_order_details'] = LiveStreamProductOrderResource::collection($lss_product_orders);

            DB::commit();

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {
            
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method lss_delivery_address_list()
     * 
     * Get the list of lss delivery addresses.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lss_delivery_address_list(Request $request)
    {
        try {

            DB::beginTransaction();

            $base_query = LssDeliveryAddress::where('user_id', $request->id)
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);

                })->when($request->filled('search_key'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                            ->orWhere('name', 'LIKE', '%' . $request->search_key . '%')
                            ->orWhere('address', 'LIKE', '%' . $request->search_key . '%');
                    });
                });

            $total_records = $base_query->count();

            $lss_delivery_addresses = $base_query->skip($this->skip)->take($this->take)->get();

            $data = [
                'total' => $total_records,
                'lss_delivery_address_details' => LssDeliveryAddressResource::collection($lss_delivery_addresses),
            ];

            DB::commit();

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

     /**
     * @method lss_delivery_address_store()
     * 
     * Store or update the delivery address for live stream shopping.
     *
     * @param  App\Http\Requests\Api\LiveStreamShopping\LssDeliveryAddressStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lss_delivery_address_store(LssDeliveryAddressStoreRequest $request) {

        try {
            DB::beginTransaction();

            $lss_delivery_address = LssDeliveryAddress::updateOrCreate(
                ['id' => $request->lss_delivery_address_id, 'user_id' => $request->id],
                $request->validated()
            );

            if($lss_delivery_address->wasRecentlyCreated) {

                $is_default = LssDeliveryAddress::where(['user_id' => $lss_delivery_address->user_id, 'is_default' => YES])->exists() ? NO : YES;

                $lss_delivery_address->update(['is_default' => $is_default]);
            }

            DB::commit();

            $code = $lss_delivery_address->wasRecentlyCreated ? '830' : '831' ;

            $data['lss_delivery_address_details'] = new LssDeliveryAddressResource($lss_delivery_address->refresh());

            return $this->sendResponse(api_success($code), $code, $data);

        } catch (\Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


     /**
     * @method lss_delivery_addrees_make_as_default
     *
     * @uses To change default live stream shopping delivery address 
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function lss_delivery_addrees_make_as_default(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'lss_delivery_address_unique_id' => 'required|exists:lss_delivery_addresses,unique_id,user_id,' . $request->id,
            ];

            Helper::custom_validator($request->all(), $rules);

            $lss_delivery_address = LssDeliveryAddress::firstWhere(['unique_id' => $request->lss_delivery_address_unique_id]);

            throw_if(!$lss_delivery_address, new Exception(api_error(312), 312));

            throw_if($lss_delivery_address->is_default == YES, new Exception(api_error(313), 313));

            LssDeliveryAddress::where('user_id', $lss_delivery_address->user_id)->update(['is_default' => NO]);

            $lss_delivery_address->update(['is_default' => YES]);

            DB::commit();

            $data['lss_delivery_address_details'] = new LssDeliveryAddressResource($lss_delivery_address->refresh());

            return $this->sendResponse(api_success(832), 832, $data);

        } catch (\Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method lss_delivery_addrees_delete()
     *
     * @uses to delete the Live stream shopping address
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function lss_delivery_addrees_delete(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'lss_delivery_address_unique_id' => 'required|exists:lss_delivery_addresses,unique_id,user_id,' . $request->id,
            ];

            Helper::custom_validator($request->all(), $rules);

            $lss_delivery_address = LssDeliveryAddress::firstWhere(['unique_id' => $request->lss_delivery_address_unique_id]);

            throw_if(!$lss_delivery_address, new Exception(api_error(312), 312));

            if($lss_delivery_address->is_default == YES){

                $lss_delivery_address_update = LssDeliveryAddress::firstWhere(['user_id' => $lss_delivery_address->user_id, 'is_default' => NO]);

                if($lss_delivery_address_update){

                    $lss_delivery_address_update->update(['is_default' => YES]);
                } 
            }

            $lss_delivery_address->delete();

            DB::commit();

             $data['lss_delivery_address_id'] = $lss_delivery_address->id ?: 0;

            return $this->sendResponse(api_success(833), 833, $data);

        } catch (\Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method update_shipping_status()
     *
     * @uses To update the shipping status
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function update_shipping_status(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'lss_product_payment_unique_id' => 'required|exists:lss_product_payments,unique_id',
                'live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id,user_id,' . $request->id,
            ];
 
            Helper::custom_validator($request->all(), $rules);

            $live_stream_shopping = LiveStreamShopping::firstWhere(['unique_id' => $request->live_stream_shopping_unique_id, 'user_id' => $request->id]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $live_stream_shopping_payment = LssProductPayment::firstWhere(['unique_id' => $request->lss_product_payment_unique_id, 'status' => PAID, 'live_stream_shopping_id' => $live_stream_shopping->id]);

            throw_if(!$live_stream_shopping_payment, new Exception(api_error(308), 308));

            throw_if($live_stream_shopping_payment->is_shipped == YES, new Exception(api_error(314), 314));

            $live_stream_shopping_payment->update(['is_shipped' => YES]);
            
            throw_if(!$live_stream_shopping_payment, new Exception(api_error(315), 315));

            DB::commit();

            $data['live_stream_shopping_payment'] = new LiveStreamProductOrderResource($live_stream_shopping_payment);

            return $this->sendResponse(api_success(834), 834, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


    /** 
     * @method lss_update_viewer_count()
     *
     * @uses To End the live streaming shopping videos by user
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function lss_update_viewer_count(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'live_stream_shopping_unique_id' => 'required|exists:live_stream_shoppings,unique_id',
                'viewer_count' => 'required|numeric',
            ];

            Helper::custom_validator($request->all(), $rules);

            $live_stream_shopping = LiveStreamShopping::firstWhere(['unique_id' => $request->live_stream_shopping_unique_id, 'is_streaming' => YES, 'status' => LIVE_STREAM_SHOPPING_ONGOING]);

            throw_if(!$live_stream_shopping, new Exception(api_error(307), 307));

            $live_stream_shopping->update(['viewer_count' => $request->viewer_count]);

            DB::commit();

            $data =  $live_stream_shopping->unique_id ?? tr('not_available');

            return $this->sendResponse(api_success(835), 835, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }
}
