<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB, Setting;

use App\Helpers\Helper;

use App\Http\Resources\{ChatMessageResource, ChatMessagePaymentResource};

use App\Http\Requests\Api\ChatMessagePaymentRequest;

use App\Models\{Follower, ChatMessage, ChatAsset, User, UserWallet, ChatMessagePayment};

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\CommonRepository as CommonRepo;

class ChatApiController extends Controller
{

    protected $skip, $take;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method chat_assets_save()
     *
     * @uses - To save the chat assets.
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param
     *
     * @return No return response.
     *
     */

     public function chat_assets_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',
                'message' => 'nullable',
                'amount' => 'nullable|numeric|min:1',
                'file' => 'required'
            ];

            Helper::custom_validator($request->all(),$rules);

            $message = $request->message;

            $from_chat_user_inputs = ['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id];

            $from_chat_user = \App\Models\ChatUser::updateOrCreate($from_chat_user_inputs);

            $to_chat_user_inputs = ['from_user_id' => $request->to_user_id, 'to_user_id' => $request->from_user_id];

            $to_chat_user = \App\Models\ChatUser::updateOrCreate($to_chat_user_inputs);

            $chat_message = new \App\Models\ChatMessage;

            $chat_message->from_user_id = $request->from_user_id;

            $chat_message->to_user_id = $request->to_user_id;

            $chat_message->message = $request->message ?? '';

            $chat_message->is_file_uploaded = YES;

            $amount = $request->amount ?? 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $chat_message->token = $amount;

                $chat_message->amount = $chat_message->token * Setting::get('token_amount');

            } else {

                $chat_message->amount = $amount;

            }

            $chat_message->is_paid = $chat_message->amount > 0 ? YES : NO;

            if ($chat_message->save()) {


                if ($request->has('file')) {

                    $files = $request->file;

                    if(!is_array($files)) {

                        $chat_asset = new \App\Models\ChatAsset;

                        $chat_asset->from_user_id = $request->from_user_id;

                        $chat_asset->to_user_id = $request->to_user_id;

                        $chat_asset->chat_message_id = $chat_message->chat_message_id;

                        $filename = rand(1,1000000).'-chat_asset-'.$request->file_type ?? 'image';

                        $chat_asset_file_url = Helper::storage_upload_file($request->file, CHAT_ASSETS_PATH, $filename);

                        $chat_asset->file = $chat_asset_file_url;

                        if($chat_asset_file_url) {

                            // File Archive start

                            $archive_request = new Request(['user_id' => $request->id,'file' => $chat_asset_file_url, 'origin' => FILE_ORIGIN_CHAT_ASSET, 'file_type' => $request->file_type ?? FILE_TYPE_IMAGE, "amount" => $chat_message->amount ?? 0.00, 'is_paid' => $chat_message->amount > 0 ? YES : NO, 'chat_asset_id' => $chat_asset->id]);

                            Helper::update_file_archive($archive_request);

                            // File Archive end

                            $chat_asset->file_type = $request->file_type ?? FILE_TYPE_IMAGE;

                            $chat_asset->token = $chat_message->token ?? 0.00;

                            $chat_asset->amount = $chat_message->amount ?? 0.00;

                            $chat_asset->blur_file = $request->file_type == FILE_TYPE_IMAGE ? \App\Helpers\Helper::generate_chat_blur_file($chat_asset->file, $request->file) : Setting::get('post_video_placeholder');

                            $chat_asset->save();

                        }
                    } else {

                        foreach($files as $file){

                            $chat_asset = new \App\Models\ChatAsset;

                            $chat_asset->from_user_id = $request->from_user_id;

                            $chat_asset->to_user_id = $request->to_user_id;

                            $chat_asset->chat_message_id = $chat_message->chat_message_id;

                            $filename = rand(1,1000000).'-chat_asset-'.$request->file_type ?? 'image';

                            $chat_asset_file_url = Helper::storage_upload_file($file, CHAT_ASSETS_PATH, $filename);

                            $chat_asset->file = $chat_asset_file_url;

                            if($chat_asset_file_url) {

                                // File Archive start

                                $archive_request = new Request(['user_id' => $request->id,'file' => $chat_asset_file_url, 'origin' => FILE_ORIGIN_CHAT_ASSET, 'file_type' => $request->file_type ?? FILE_TYPE_IMAGE, "amount" => $chat_message->amount ?? 0.00, 'is_paid' => $chat_message->amount > 0 ? YES : NO, 'chat_asset_id' => $chat_asset->id]);

                                Helper::update_file_archive($archive_request);

                                // File Archive end

                                $chat_asset->file_type = $request->file_type ?? FILE_TYPE_IMAGE;

                                $chat_asset->token = $chat_message->token ?? 0.00;

                                $chat_asset->amount = $chat_message->amount ?? 0.00;

                                $chat_asset->blur_file = $request->file_type == FILE_TYPE_IMAGE ? \App\Helpers\Helper::generate_chat_blur_file($chat_asset->file, $file) : Setting::get('post_video_placeholder');

                                $chat_asset->save();
                            }
                        }

                    }
                }

                DB::commit();
            }

            $chat_message = \App\Repositories\CommonRepository::chat_messages_single_response($chat_message, $request);

            $data['chat_message'] = $chat_message;

            $data['chat_asset'] = $chat_asset;

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


    /**
     * @method chat_assets_index()
     *
     * @uses - To get the media assets.
     *
     * @created Arun
     *
     * @updated Vithya R
     *
     * @param
     *
     * @return return response.
     *
     */

    public function chat_assets_index(Request $request) {

        try {

            $base_query = $total_query = \App\Models\ChatAsset::where(function($query) use ($request){
                        $query->where('chat_assets.from_user_id', $request->from_user_id);
                        $query->where('chat_assets.to_user_id', $request->to_user_id);
                    })->orWhere(function($query) use ($request){
                        $query->where('chat_assets.from_user_id', $request->to_user_id);
                        $query->where('chat_assets.to_user_id', $request->from_user_id);
                    })
                    ->latest();

            $chat_assets = $base_query->skip($this->skip)->take($this->take)->get();

            $data['chat_assets'] = $chat_assets ?? emptyObject();

            $data['total'] = $total_query->count() ?? [];

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method chat_assets_payment_by_stripe()
     *
     * @uses chat_assets_payment_by_stripe based on Chat message id
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param object $request - Chat message id
     *
     * @return json with boolean output
     */

    public function chat_assets_payment_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'chat_message_id' => 'required|numeric',
                'promo_code' => 'nullable|exists:promo_codes,promo_code'
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            $chat_message = \App\Models\ChatMessage::firstWhere('id',$request->chat_message_id);

            $chat_asset = \App\Models\ChatAsset::firstWhere('chat_message_id',$request->chat_message_id);

            if(!$chat_message || !$chat_asset) {

                throw new Exception(api_error(3000), 3000);

            }

            $request->request->add(['payment_mode' => CARD, 'usage_type' => USAGE_TYPE_CHAT]);

            $total = $chat_message->amount ?: 0.00;

            $user_pay_amount = Helper::apply_promo_code($request, $total, CHAT_ASSET_PAYMENTS, $chat_message->from_user_id);

            if($user_pay_amount > 0) {

                // Check the user have the cards

                $user_card = \App\Models\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120);

                }

                $request->request->add([
                    'total' => $total,
                    'user_card_id' => $user_card->id,
                    'customer_id' => $user_card->customer_id,
                    'card_token' => $user_card->card_token,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                    'promo_code'=> $request->promo_code,
                    'promo_discount' => $total - $user_pay_amount
                ]);

                $card_payment_response = PaymentRepo::chat_assets_payment_by_stripe($request, $chat_message)->getData();

                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);

                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);


            }

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
     * @method chat_assets_payment_by_wallet()
     *
     * @uses chat_assets_payment_by_wallet based on Chat message id
     *
     * @created Bhawya
     *
     * @updated
     *
     * @param object $request - Chat message id
     *
     * @return json with boolean output
     */

    public function chat_assets_payment_by_wallet(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'chat_message_id' => 'required|numeric',
                'promo_code' => 'nullable|exists:promo_codes,promo_code'
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            $chat_message = \App\Models\ChatMessage::firstWhere('id',$request->chat_message_id);

            $chat_asset = \App\Models\ChatAsset::firstWhere('chat_message_id',$request->chat_message_id);

            if(!$chat_message || !$chat_asset) {

                throw new Exception(api_error(3000), 3000);

            }

            $total = Setting::get('is_only_wallet_payment') ? $chat_message->token : $chat_message->amount;

            $user_pay_amount = Helper::apply_promo_code($request, $total, CHAT_ASSET_PAYMENTS, $chat_message->from_user_id);

            // Check the user has enough balance

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;

            }

            if($remaining < $total) {
                throw new Exception(api_error(147), 147);
            }

            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $user_pay_amount * Setting::get('token_amount'),
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount * Setting::get('token_amount'),
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'CMP-'.rand(),
                'usage_type' => USAGE_TYPE_CHAT,
                'tokens' => $user_pay_amount,
                'promo_code'=> $request->promo_code,
                'promo_discount' => $total-$user_pay_amount
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $request->request->add([
                    'from_user_id' => $request->id,
                    'to_user_id' => $request->user_id,
                ]);

                $payment_response = PaymentRepo::chat_assets_payment_save($request, $chat_message)->getData();

                if($payment_response->success) {

                    DB::commit();

                    $chat_message = \App\Repositories\CommonRepository::chat_messages_asset_single_response($chat_message, $request);

                    $data['chat_message'] = $chat_message;

                    return $this->sendResponse(api_success(118), 118, $data);

                } else {

                    throw new Exception($payment_response->error, $payment_response->error_code);

                }

            }

            throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method chat_assets_payment_by_paypal()
     *
     * @uses chat_assets_payment_by_paypal based on Chat message id
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param object $request - Chat message id
     *
     * @return json with boolean output
     */

    public function chat_assets_payment_by_paypal(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'chat_message_id' => 'required|numeric',
                'payment_id' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            $chat_message = \App\Models\ChatMessage::firstWhere('id',$request->chat_message_id);

            $chat_asset = \App\Models\ChatAsset::firstWhere('chat_message_id',$request->chat_message_id);

            if(!$chat_message || !$chat_asset) {

                throw new Exception(api_error(3000), 3000);

            }

            $total = $chat_message->amount ?: 0.00;

            $user_pay_amount = Helper::apply_promo_code($request, $total, CHAT_ASSET_PAYMENTS, $chat_message->from_user_id);

            $request->request->add([
                'payment_mode' => PAYPAL,
                'paid_amount'=>$user_pay_amount,
                'usage_type' => USAGE_TYPE_CHAT, 
                'user_pay_amount' => $user_pay_amount,
                'paid_status' => PAID_STATUS,
                'promo_code' => $request->promo_code,
                'promo_discount' => $total - $user_pay_amount
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
     * @method chat_assets_delete()
     *
     * @uses delete the chat assets
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function chat_assets_delete(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'chat_message_id' => 'required_without:chat_reference_id|exists:chat_messages,id',
                'chat_reference_id' => 'exists:chat_messages,reference_id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $chat_message = \App\Models\ChatMessage::where('reference_id', $request->chat_reference_id ?: "")->orWhere('id', $request->chat_message_id ?: "")->first();;

            throw_if($chat_message->from_user_id != $request->id, new Exception(api_error(287), 287));

            $chat_message->delete();

            DB::commit();

            $request->merge([
                'from_user_id' => $chat_message->from_user_id,
                'to_user_id' => $chat_message->to_user_id
            ]);

            $data['chat_messages'] = $this->chat_messages_index($request)->getData();

            return $this->sendResponse(api_success(3000), 3000, $data);

        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method chat_assets_payments_list()
     *
     * @uses To display the chat_assets_payments list based on user  id
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param object $request
     *
     * @return json response with user details
     */

    public function chat_assets_payments_list(Request $request) {

        try {

            $base_query = $total_query = \App\Models\ChatAssetPayment::where('from_user_id',$request->id);

            $chat_assets_payments = $base_query->skip($this->skip)->take($this->take)->get();

            $data['chat_assets_payments'] = $chat_assets_payments;

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method chat_assets_payments_view()
     *
     * @uses get the selected chat_assets_payments request
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function chat_assets_payments_view(Request $request) {

        try {

            $rules = ['chat_asset_payments_id' => 'required|exists:chat_asset_payments,id'];

            Helper::custom_validator($request->all(),$rules);

            $chat_asset_payment = \App\Models\ChatAssetPayment::with('chatMessage')->with('chatAssets')->firstWhere('id',$request->chat_asset_payments_id);

            if(!$chat_asset_payment) {

                throw new Exception(api_error(167), 167);

            }

            $data['chat_asset_payment'] = $chat_asset_payment;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


    /**
     * @method chat_users_search()
     *
     * @uses
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     *
     * @return JSON response
     *
     */
    public function chat_users_search(Request $request) {

        try {

            // validation start

            $rules = ['search_key' => 'required'];

            $custom_errors = ['search_key.required' => 'Please enter the username'];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $search_key = $request->search_key;

            $base_query = $total_query = \App\Models\ChatUser::where('from_user_id', $request->id)
                    ->whereHas('toUser',function($query) use($search_key) {
                        return $query->where('users.name','LIKE','%'.$search_key.'%');
                    })
                    ->orderBy('chat_users.updated_at', 'desc');

            $chat_users = $base_query->skip($this->skip)->take($this->take)->get();

            $data['users'] = $chat_users ?? [];

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method chat_messages_search()
     *
     * @uses
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return JSON response
     *
     */
    public function chat_messages_search(Request $request) {

        try {

            $rules = ['search_key' => 'required'];

            $custom_errors = ['search_key.required' => 'Please enter the message'];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $search_key = $request->search_key;

            $base_query = $total_query = \App\Models\ChatMessage::where(function($query) use ($request){
                        $query->where('chat_messages.from_user_id', $request->from_user_id);
                        $query->where('chat_messages.to_user_id', $request->to_user_id);
                    })
                    ->where('chat_messages.message', 'like', "%".$search_key."%")
                    ->orderBy('chat_messages.updated_at', 'asc');

            $chat_messages = $base_query->skip($this->skip)->take($this->take)->get();

            $data['messages'] = $chat_messages ?? [];

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method chat_asset_broadcast_files_upload()
     *
     * @uses - To save the chat assets during broadcast.
     *
     * @created Sulabh
     *
     * @param
     *
     * @return No return response.
     */

    public function chat_asset_broadcast_files_upload(Request $request){

        try{

            DB::beginTransaction();

            $rules = [
                'from_user_id' => 'required_without:admin_id|exists:users,id',
                'admin_id' => 'exists:admins,id',
                'user_type' => 'required_with:admin_id',
                'message' => 'nullable',
                'amount' => 'nullable|numeric|min:1',
                'file' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);

            if($request->filled('admin_id')){

                $recieving_users = \App\Models\User::where('user_type', $request->user_type)->pluck('id')->toArray(); //users of the selected type

            }else{    
                $recieving_users = \App\Models\Follower::where('follower_id', $request->from_user_id)->pluck('user_id')->toArray(); //following users
            }

            $data = $file_data = [];

            $chat_asset_file_id = $chat_asset_blur_file = '';

            $file_url = $file_name = [];

            if ($request->has('file')) {

                $files = $request->file;

                $file = $files[0];

                // $filename = str_replace(' ', '-', $file->getClientOriginalName());

                $filename = rand(1,1000000).'-chat_asset-'.($request->file_type ?? 'image').'.'.$file->getClientOriginalExtension();

                $chat_assets_file_url = Helper::storage_upload_file($file, CHAT_ASSETS_PATH, $filename, NO);

                if($chat_assets_file_url) {

                    $blur_file = $request->file_type == FILE_TYPE_IMAGE ? \App\Helpers\Helper::generate_chat_blur_file($chat_assets_file_url, $file) : Setting::get('post_video_placeholder');

                    foreach($recieving_users as $to_user_id){

                        $chat_asset = new \App\Models\ChatAsset;

                        $chat_asset->from_user_id = $request->from_user_id ?? 0;

                        $chat_asset->admin_id = $request->admin_id ?? 0;

                        $chat_asset->to_user_id = $to_user_id;

                        $chat_asset->chat_message_id = 0;

                        $chat_asset->file = $chat_assets_file_url;

                        $chat_asset->file_type = $request->file_type ?? FILE_TYPE_IMAGE;

                        // $chat_asset->token = $chat_message->token ?? 0.00;

                        // $chat_asset->amount = $chat_message->amount ?? 0.00;

                        $chat_asset->blur_file = $blur_file;

                        $chat_asset->save();

                        $file_data['chat_asset'] = $chat_asset;

                        $chat_asset_file_id = $chat_asset->id;

                        $file_url[] = $chat_assets_file_url;

                        $file_name[] = basename($chat_assets_file_url);

                        $chat_asset_blur_file != "" && $chat_asset_blur_file .= ",";

                        $chat_asset_blur_file .= $chat_asset->blur_file;

                        $chat_asset->asset_file = $chat_asset->file;
                    }

                }

            }

            DB::commit();

            $data['chat_asset_file_id'] = $chat_asset_file_id;

            $data['file'] = $file_url;

            $data['file_name'] = $file_name;

            $data['blur_file'] = $chat_asset_blur_file;

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method chat_assets_save()
     *
     * @uses - To save the chat assets.
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param
     *
     * @return No return response.
     *
     */

    public function chat_asset_files_upload(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',
                'message' => 'nullable',
                'amount' => 'nullable|numeric|min:1',
                'file' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);

            $data = $file_data = [];

            $chat_asset_file_id = $chat_asset_blur_file = '';

            $file_url = $file_name = [];

            if ($request->has('file')) {

                $files = $request->file;

                if(!is_array($files)) {

                    $file = $files;

                    $chat_asset = new \App\Models\ChatAsset;

                    $chat_asset->from_user_id = $request->from_user_id;

                    $chat_asset->to_user_id = $request->to_user_id;

                    $chat_asset->chat_message_id = 0;

                    // $filename = str_replace(' ', '-', $file->getClientOriginalName());

                    $filename = rand(1,1000000).'-chat_asset-'.($request->file_type ?? 'image').'.'.$file->getClientOriginalExtension();

                    // $filename = rand(1,1000000).'-chat_asset-'.$request->file_type ?? 'image';

                    $chat_assets_file_url = Helper::storage_upload_file($request->file, CHAT_ASSETS_PATH, $filename, NO);

                    $chat_asset->file = $chat_assets_file_url;

                    if($chat_assets_file_url) {

                        $chat_asset->file_type = $request->file_type ?? FILE_TYPE_IMAGE;

                        // $chat_asset->token = $chat_message->token ?? 0.00;

                        // $chat_asset->amount = $chat_message->amount ?? 0.00;

                        $chat_asset->blur_file = $request->file_type == FILE_TYPE_IMAGE ? \App\Helpers\Helper::generate_chat_blur_file($chat_asset->file, $request->file) : Setting::get('post_video_placeholder');

                        $chat_asset->save();

                    }

                    $file_data['chat_asset'] = $chat_asset;

                    $chat_asset_file_id != "" && $chat_asset_file_id .= ",";

                    $chat_asset_file_id .= $chat_asset->id;

                    $file_url[] = $chat_assets_file_url;

                    $file_name[] = basename($chat_assets_file_url);

                    $chat_asset_blur_file != "" && $chat_asset_blur_file .= ",";

                    $chat_asset_blur_file .= $chat_asset->blur_file;

                    $chat_asset->asset_file = $chat_asset->file;

                    $data['chat_asset'] = $chat_asset;

                } else {

                    foreach($files as $file){

                        $chat_asset = new \App\Models\ChatAsset;

                        $chat_asset->from_user_id = $request->from_user_id;

                        $chat_asset->to_user_id = $request->to_user_id;

                        $chat_asset->chat_message_id = 0;

                        // $filename = str_replace(' ', '-', $file->getClientOriginalName());

                        $filename = rand(1,1000000).'-chat_asset-'.($request->file_type ?? 'image').'.'.$file->getClientOriginalExtension();

                        // $filename = rand(1,1000000).'-chat_asset-'.$request->file_type ?? 'image';

                        $chat_assets_file_url = Helper::storage_upload_file($file, CHAT_ASSETS_PATH, $filename, NO);

                        $chat_asset->file = $chat_assets_file_url;

                        if($chat_assets_file_url) {

                            $chat_asset->file_type = $request->file_type ?? FILE_TYPE_IMAGE;

                            $chat_asset->blur_file = $request->file_type == FILE_TYPE_IMAGE ? \App\Helpers\Helper::generate_chat_blur_file($chat_asset->file, $file) : ($request->file_type == FILE_TYPE_DOCUMENT ? Setting::get('asset_placeholder') : Setting::get('post_video_placeholder'));

                            $chat_asset->save();
                        }

                        $chat_asset_file_id != "" && $chat_asset_file_id .= ",";

                        $chat_asset_file_id .= $chat_asset->id;

                        $file_url[] = $chat_assets_file_url;

                        $file_name[] = basename($chat_assets_file_url);

                        $chat_asset_blur_file != "" && $chat_asset_blur_file .= ",";

                        $chat_asset_blur_file .= $chat_asset->blur_file;

                        $chat_asset->asset_file = $chat_asset->file;

                        array_push($file_data, $chat_asset);

                    }

                    $data['chat_asset'] = $file_data;

                }

            }

            DB::commit();

            $data['chat_asset_file_id'] = $chat_asset_file_id;

            $data['file'] = $file_url;

            $data['file_name'] = $file_name;

            $data['blur_file'] = $chat_asset_blur_file;

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method chat_asset_files_remove()
     *
     * @uses remove the selected file
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param integer $post_file_id
     *
     * @return JSON Response
     */
    public function chat_asset_files_remove(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'file' => 'nullable',
                // 'file_type' => 'required',
                // 'blur_file' => 'required_if:file_type,==,'.POSTS_IMAGE,
                // 'preview_file' => 'required_if:file_type,==,'.POSTS_VIDEO,
                // 'post_file_id' => 'nullable|exists:post_files,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            if($request->file) {

                $chat_asset = \App\Models\ChatAsset::where('file', $request->file)->first();

            } else {

                $chat_asset = \App\Models\ChatAsset::where('id', $request->chat_asset_id)->first();

            }

            $chat_asset_ids = explode(',', $request->chat_asset_id);

            if($chat_asset) {

                $pos = array_search($chat_asset->id, $chat_asset_ids);

                unset($chat_asset_ids[$pos]);

                $chat_asset->delete();

                DB::commit();

            }

            $chat_assets = \App\Models\ChatAsset::whereIn('id',$chat_asset_ids)->pluck('file');

            $chat_asset_ids = $chat_asset_ids ? implode(',', $chat_asset_ids) : '';

            $data['chat_asset_id'] = $chat_asset_ids ?? '';

            $data['chat_asset'] = $chat_assets ?? '';

            return $this->sendResponse(api_success(152), 152, $data = $data);


        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method chat_messages()
     *
     * @uses chat_messages List
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return JSON response
     *
     */
    public function chat_messages_index(Request $request) {

        try {

            $rules = [
                'admin_id' => 'nullable|exists:admins,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            if ($request->admin_id) {

                $base_query = ChatMessage::where('admin_id', $request->admin_id)
                                         ->where('to_user_id', $request->id);

            } else {
                $base_query = ChatMessage::where(function($query) use ($request) {
                        $query->where('from_user_id', $request->from_user_id)
                              ->where('to_user_id', $request->to_user_id);
                    })
                    ->orWhere(function($query) use ($request) {
                        $query->where('from_user_id', $request->to_user_id)
                              ->where('to_user_id', $request->from_user_id);
                    });
            }

            $data['total'] = $base_query->count() ?: 0;

            $base_query = $base_query->latest();

            $chat_message = \App\Models\ChatMessage::where('chat_messages.to_user_id', $request->from_user_id)->where('status', NO)->update(['status' => YES]);

            $chat_messages = $base_query->skip($this->skip)->take($this->take)->orderBy('chat_messages.updated_at', 'asc')->get();

            $chat_messages = \App\Repositories\CommonRepository::chat_messages_response($chat_messages, $request);

            if($request->device_type == DEVICE_WEB) {

                // $chat_messages = array_reverse($chat_messages->toArray());

            }

            $data['messages'] = $chat_messages ?? [];

            $data['user'] = $request->admin_id ? \App\Models\Admin::find($request->admin_id) : \App\Models\User::find($request->to_user_id);

            $data['is_block_user'] = Helper::is_block_user($request->id, $request->to_user_id);

            $data['is_admin'] = $request->admin_id ? YES : NO;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method chat_assets_save()
     *
     * @uses - To save the chat assets.
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param
     *
     * @return No return response.
     *
     */

    public function chat_messages_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',
                'message' => 'nullable',
                'amount' => 'nullable|numeric|min:1',
                'chat_asset_file_id' => 'nullable',
            ];

            Helper::custom_validator($request->all(),$rules);

            $message = $request->message;

            $from_chat_user_inputs = ['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id];

            $from_chat_user = \App\Models\ChatUser::updateOrCreate($from_chat_user_inputs);

            $to_chat_user_inputs = ['from_user_id' => $request->to_user_id, 'to_user_id' => $request->from_user_id];

            $to_chat_user = \App\Models\ChatUser::updateOrCreate($to_chat_user_inputs);

            $chat_message_payment = ChatMessagePayment::where(['user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id, 'status' => PAID])->latest()->first();
            
            if($chat_message_payment){
              $current_date = now();
              throw_if($chat_message_payment->expiry_date && $current_date->isAfter($chat_message_payment->expiry_date), new Exception(api_error(333), 333));
            }

            $chat_message = new \App\Models\ChatMessage;

            $chat_message->from_user_id = $request->from_user_id;

            $chat_message->to_user_id = $request->to_user_id;

            $chat_message->message = $request->message ?? '';

            $chat_message->is_file_uploaded = YES;

            $amount = $request->amount ?? 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $chat_message->token = $amount;

                $chat_message->amount = $chat_message->token * Setting::get('token_amount');

            } else {

                $chat_message->amount = $amount;

            }

            $chat_message->is_paid = $chat_message->amount > 0 ? YES : NO;

            if ($chat_message->save()) {

                if($request->chat_asset_file_id) {

                    $chat_asset_file_ids = explode(',',$request->chat_asset_file_id);

                    foreach($chat_asset_file_ids as $chat_asset_file_id) {

                        $chat_asset = \App\Models\ChatAsset::find($chat_asset_file_id);

                        $chat_asset->chat_message_id = $chat_message->id;

                        $chat_asset->token = $chat_message->token;

                        $chat_asset->amount = $chat_message->amount;

                        $chat_asset->save();

                    }
                }

                // If the files are uploaded form file archives

                if($request->file_archives) {

                    $file_archives = explode(',',$request->file_archives);

                    foreach($file_archives as $file_archive_id) {

                        $file_archive = \App\Models\FileArchive::find($file_archive_id);

                        if($file_archive) {

                            $chat_asset = new \App\Models\ChatAsset;

                            $chat_asset->from_user_id = $chat_message->from_user_id;

                            $chat_asset->to_user_id = $chat_message->to_user_id;

                            $chat_asset->chat_message_id = $chat_message->id;

                            $filename = rand(1,1000000).'-chat_asset.'.get_extension_from_path($file_archive->file);

                            $new_folder_path = CHAT_ASSETS_PATH.$filename;

                            $old_file_path = str_replace("storage/", '',parse_url($file_archive->file, PHP_URL_PATH));

                            \Storage::copy("public/".$old_file_path, "public/".$new_folder_path);

                            $chat_asset->file = $chat_asset_file_url = asset(\Storage::url($new_folder_path));

                            if($chat_asset_file_url) {

                                // File Archive start

                                $archive_request = new Request(['user_id' => $request->id,'file' => $chat_asset_file_url, 'origin' => FILE_ORIGIN_CHAT_ASSET, 'file_type' => $file_archive->file_type ?? FILE_TYPE_IMAGE, "amount" => $chat_message->amount ?? 0.00, 'is_paid' => $chat_message->amount > 0 ? YES : NO, 'chat_asset_id' => $chat_asset->id]);

                                Helper::update_file_archive($archive_request);

                                // File Archive end

                                $chat_asset->file_type = $file_archive->file_type ?? FILE_TYPE_IMAGE;

                                $chat_asset->token = $chat_message->token ?? 0.00;

                                $chat_asset->amount = $chat_message->amount ?? 0.00;

                                $chat_asset->blur_file = Setting::get('post_video_placeholder');

                                $chat_asset->save();
                            }


                        }

                    }


                }

                DB::commit();
            }

            $chat_message = \App\Repositories\CommonRepository::chat_messages_asset_single_response($chat_message, $request);

            $to_user = User::firstWhere(['id' => $request->to_user_id, 'is_content_creator' => CONTENT_CREATOR]);

            $chat_message->is_user_needs_pay = $to_user ? ($chat_message_payment || $to_user->chat_message_amount == 0 ? NO : YES) : NO;

            $data['chat_message'] = $chat_message ?? emptyObject();

            $data['chat_asset'] = $chat_asset ?? emptyObject();

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_chat_assets()
     *
     * @uses To display all the chat media files
     *
     * @created Bhawya N
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function user_chat_assets(Request $request) {

        try {

            if($request->admin_id){

                 $chat_message_ids = ChatMessage::where('admin_id', $request->admin_id)
                                         ->where('to_user_id', $request->id)->pluck('id')->toArray();

            }else{

                $to_chat_message_ids = \App\Models\ChatMessage::where(function($query) use ($request){
                        $query->where('chat_messages.from_user_id', $request->from_user_id);
                        $query->where('chat_messages.to_user_id', $request->to_user_id);
                    })
                ->pluck('id')
                ->toArray();

                $from_chat_message_ids = \App\Models\ChatMessage::where(function($query) use ($request){
                            $query->where('chat_messages.to_user_id', $request->from_user_id);
                            $query->where('chat_messages.from_user_id', $request->to_user_id);
                        })
                    ->pluck('id')
                    ->toArray();

                $chat_message_ids = array_merge($to_chat_message_ids, $from_chat_message_ids);

            }

            $base_query = $total_query = \App\Models\ChatAsset::whereIn('chat_message_id',$chat_message_ids);

            if($request->file_type != POSTS_ALL) {

                $type = $request->file_type;

                if($type)

                    $base_query = $base_query->where('chat_assets.file_type', $type);
            }

            $data['total'] = $total_query->count() ?? 0;

            $base_query = $base_query->latest();

            $chat_assets = $base_query->skip($this->skip)->take($this->take)->get();

            $chat_assets = \App\Repositories\CommonRepository::chat_assets_list_response($chat_assets, $request);

            $data['chat_assets'] = $chat_assets ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method send_bulk_message()
     *
     * @uses - To send bulk message to follwers.
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param
     *
     * @return No return response.
     *
     */
    public function send_bulk_message(Request $request) {

        try {

            $rules = [
                'message' => 'required_without:file',
                'file' => 'nullable|mimes:jpeg,jpg,png,gif,svg,mp4,mkv',
                'to_user_id' => 'nullable|exists:users,id',
            ];

            Helper::custom_validator($request->all(), $rules);

            DB::beginTransaction();

            $chat_assets_file_url = $file_type = '';

            $blocked_user_ids = blocked_users($request->id);

            if($request->hasFile('file')) {

                $file = $request->file('file');

                $file_type = $file->getClientOriginalExtension();

                $filename = rand(1, 1000000) . '-chat_asset_file.'.$file_type;

                $chat_assets_file_url = Helper::storage_upload_file($file, CHAT_ASSETS_PATH, $filename);
            }

            $followers = Follower::whereNotIn('follower_id', $blocked_user_ids)->whereHas('follower')->where(['user_id' => $request->id, 'status' => FOLLOWER_ACTIVE])->chunk(30, function ($followers) use ($request, $chat_assets_file_url, $file_type) {

                        foreach ($followers as $follower) {

                            CommonRepo::chat_user_update($request->id, $follower->follower_id);

                            $chat_message = ChatMessage::create([
                                'from_user_id' => $request->id,
                                'to_user_id' => $follower->follower_id,
                                'message' => $request->message ?? '',
                                'is_file_uploaded' => $chat_assets_file_url ? YES : NO,
                            ]);

                            if ($chat_assets_file_url) {

                                ChatAsset::create([
                                    'from_user_id' => $request->id,
                                    'to_user_id' => $follower->follower_id,
                                    'chat_message_id' => $chat_message->id,
                                    'file' => $chat_assets_file_url,
                                    'file_type' => $file_type,
                                    'is_paid' => PAID,
                            ]);
                        }
                    }
                });

            $data['chat_message'] = [
                'message' => $request->message,
                'file' => $chat_assets_file_url ?? '',
                'file_type' => $file_type ?? ''
            ];

            if($request->to_user_id){

                $chat_message = ChatMessage::with('chatAssets')->firstWhere(['to_user_id' => $request->to_user_id]);
            }

            $data['user_chat_message'] = $request->to_user_id ? new ChatMessageResource($chat_message) : [];

            DB::commit();

            return $this->sendResponse(api_success(814), "", $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method chat_message_payment_by_wallet()
     *
     * @uses Send money to other user
     *
     * @created
     *
     * @updated
     *
     * @param object $request
     *
     * @return JSON response
     */
    public function chat_message_payment_by_wallet(ChatMessagePaymentRequest $request) {
        try {
            DB::beginTransaction();
            $to_user = User::firstWhere(['id' => $request->to_user_id, 'is_content_creator' => CONTENT_CREATOR]);
            throw_if(!$to_user, new Exception(api_error(289), 289));
            $total = Setting::get('is_only_wallet_payment') ? $to_user->chat_message_token : $to_user->chat_message_amount;
            $user_pay_amount = $request->promo_code ? Helper::apply_promo_code($request, $total, CHAT_MESSAGE_PAYMENTS, $to_user->id) : $total;
            throw_if($to_user->id == $request->id, new Exception(api_error(332), 332));
            $chat_message_payment = ChatMessagePayment::where(['user_id' => $request->id])->latest()->first();

            $current_date = now();

            if($chat_message_payment){
                
               throw_if(!$current_date->isAfter($chat_message_payment->expiry_date), new Exception(api_error(335), 335));
            }
            if($user_pay_amount > 0) {
                $user_wallet = UserWallet::where('user_id', $request->id)->first();
                throw_if(!$user_wallet, new Exception(api_error(282), 282));
                $remaining = $user_wallet->remaining ?? 0;
                if(Setting::get('is_referral_enabled')) {
                    $remaining = $remaining + $user_wallet->referral_amount;
                }
               throw_if($remaining < $total, new Exception(api_error(147), 147));
                $request->request->add([
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'total' => $user_pay_amount * Setting::get('token_amount'),
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount * Setting::get('token_amount'),
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'payment_id' => 'WPP-'.rand(),
                    'usage_type' => USAGE_TYPE_CHAT_MESSAGE,
                    'tokens' => $user_pay_amount,
                    'promo_code'=> $request->promo_code,
                    'promo_discount' => $request->promo_code ? $total-$user_pay_amount : 0,
                ]);
               $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
            if($wallet_payment_response->success) {
                $chat_message_payment = ChatMessagePayment::create([
                    'user_id' => $request->id,
                    'to_user_id' => $request->to_user_id,
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_id' => $request->payment_id,
                    'amount' => $user_pay_amount * Setting::get('token_amount'),
                    'user_amount' => $user_pay_amount * Setting::get('token_amount'),
                    'paid_date' => now(),
                    'expiry_date' => now()->addDays(30)->setTimezone('UTC'),
                    'admin_amount' => $user_pay_amount * Setting::get('token_amount'),
                    'status' => PAID,
                ]);
                if($chat_message_payment->status == PAID) {
                    $wallet_payment_response = PaymentRepo::chat_message_payment_wallet_update($request, $chat_message_payment);
                    throw_if(!$wallet_payment_response->success ?? "", new Exception(api_error(329), 329)); 
                }
                DB::commit();
                $data['chat_message_payment'] = new ChatMessagePaymentResource($chat_message_payment->refresh());
                return $this->sendResponse(api_success(844), 844, $chat_message_payment);
            } else {
                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
            }
        }
        return $this->sendError(api_error(303), 303, '');
        } catch(Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

}
