<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\{PersonalizeStoreRequest, UpdateStatusRequest, PersonalizeProductStoreRequest, UpdateAmountRequest};
use Validator, Log, Hash, Setting, DB, Exception, File;
use App\Models\{User, PersonalizedRequest, UserWallet, PersonalizedProduct, PersonalizedDeliveryAddress, PersonalizedProductFile};
use App\Helpers\Helper;
use App\Http\Resources\{PersonalizedRequestResource, PersonalizedProductResource, PersonalizedRequestFileResource};
use App\Repositories\PaymentRepository as PaymentRepo;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PersonalizedRequestController extends Controller
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
     * @method store()
     *
     * @uses To store the personalized requests
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param App\Http\Requests\Api\PersonalizeStoreRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PersonalizeStoreRequest $request) {

        try {

            DB::beginTransaction();

            $received_user = User::firstWhere(['id' => $request->receiver_id, 'is_content_creator' => CONTENT_CREATOR]);

            throw_if(!$received_user, new Exception(api_error(289), 289));

            $personalized_request = PersonalizedRequest::create(['sender_id' => $request->id]+ $request->validated());

            throw_if(!$personalized_request, new Exception(api_error(290), 290));

            DB::commit();

            $data['personalized_requests'] = new PersonalizedRequestResource($personalized_request->refresh());

            return $this->sendResponse(api_success(817), 817, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

     /**
     * @method personalized_request_sent()
     *
     * @uses To get list of the Personalized requests created by the user
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function personalized_request_sent(Request $request)
    {
        try {
            $base_query = PersonalizedRequest::where('sender_id', $request->id)
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->filled('search_key'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                          ->orWhereHas('receiver', function ($query) use ($request) {
                                $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                            });
                    });
                });

            $data['total'] = $base_query->count();

            $personalized_requests = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['personalized_requests'] = PersonalizedRequestResource::collection($personalized_requests);

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @method personalized_request_received()
     *
     * @uses To retrieve a list of the creator's received requests.
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function personalized_request_received(Request $request)
    {
        try {

            $base_query = PersonalizedRequest::where('receiver_id', $request->id)
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->filled('search_key'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                            ->orWhereHas('sender', function ($query) use ($request) {
                                $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                            });
                       });
                });

            $data['total'] = $base_query->count();

            $recieved_requests = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['personalized_creator_requests'] = PersonalizedRequestResource::collection($recieved_requests);

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @method personalized_request_update_by_creator()
     *
     * @uses To update the amount, description, or status to accept or reject.
     *
     * @created RA Shakthi
     *
     * @param App\Http\Requests\Api\UpdateStatusRequest $request
     *
     * @return JSON Response
     */
    public function personalized_request_update_by_creator(UpdateAmountRequest $request) {

        try {

        DB::beginTransaction();

        $personalized_request = PersonalizedRequest::where(['unique_id' => $request->unique_id, 'status' => PERSONALIZE_USER_REQUESTED])->first();

        throw_if(!$personalized_request, new Exception(api_error(291), 291));

        if($request->amount != $personalized_request->amount){

            $personalized_request->update(['is_amount_update' => YES]);
        }
        
        $personalized_request->update($request->validated());

        throw_if(!$personalized_request, new Exception(api_error(292), 292));

        DB::commit();

        $data['personalized_requests'] = new PersonalizedRequestResource($personalized_request);

        return $this->sendResponse(api_success(824), 824, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        } 
    }

    /**
     * @method personalized_request_status_update_by_creator()
     *
     * @uses To update the status to accept or reject.
     *
     * @created RA Shakthi
     *
     * @param App\Http\Requests\Api\Request $request
     *
     * @return JSON Response
     */
    public function personalized_request_status_update_by_creator(UpdateStatusRequest $request) {

        try {

        DB::beginTransaction();

        $personalized_request = PersonalizedRequest::where(['unique_id' => $request->unique_id, 'status' => PERSONALIZE_USER_REQUESTED])->first();

        throw_if(!$personalized_request, new Exception(api_error(291), 291));

        $personalized_request->update($request->validated());

        throw_if(!$personalized_request, new Exception(api_error(304), 304));

        $code = $personalized_request->status == PERSONALIZE_CREATOR_ACCEPTED ? 819 : 818;

        DB::commit();

        $data['personalized_requests'] = new PersonalizedRequestResource($personalized_request);

        return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        } 
    }

     /**
     * @method personalized_request_payment_by_wallet()
     * 
     * @uses send money to other user
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return  JSON Response
     */
    public function personalized_request_payment_by_wallet(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'personalized_request_unique_id' => 'required|exists:personalized_requests,unique_id,sender_id,'.$request->id,
                'personalized_delivery_address_id' => 'nullable|exists:personalized_delivery_addresses,id,user_id,'.$request->id,
            ];

            Helper::custom_validator($request->all(), $rules);

            $personalized_request = PersonalizedRequest::firstWhere(['unique_id' => $request->personalized_request_unique_id, 'status' => PERSONALIZE_CREATOR_ACCEPTED]);

            throw_if(!$personalized_request, new Exception(api_error(291), 291));

            if ($personalized_request->product_type == PRODUCT_TYPE_PHYSICAL && !$request->personalized_delivery_address_id) {

                $rules += [
                    'name' => ['required', 'string', 'max:255'],
                    'address' => ['required'],
                    'pincode' => ['required', 'max:15'],
                    'city' => ['required', 'max:50'],
                    'state' => ['required', 'max:50'],
                    'country' => ['required', 'max:50'],
                    'country_code' => ['required', 'max:50'],
                    'landmark' => ['required', 'max:50'],
                    'contact_number' => ['required', 'digits_between:6,13']
                ];

                $validated_data = Helper::custom_validator($request->all(), $rules);

                $personalized_delivery_address = PersonalizedDeliveryAddress::create(['user_id' => $request->id]+ $validated_data);

                throw_if(!$personalized_delivery_address, new Exception(api_error(298), 298));
            }

            $personalized_request->update(['personalized_delivery_address_id' => ($request->personalized_delivery_address_id ? $request->personalized_delivery_address_id : (isset($personalized_delivery_address) ? $personalized_delivery_address->id : 0))]);

            $total = $user_pay_amount = $personalized_request->amount ?: 0;

            // need to confirm if the promo code is applicable for personalized request
            // $user_pay_amount = Helper::apply_promo_code($request, $total, LIVE_VIDEO_PAYMENTS, $personalized_request->receiver_id);

            $user_wallet = UserWallet::where('user_id', $request->id)->first();

            throw_if(!$user_wallet, new Exception(api_error(282), 282));

            $remaining = $user_wallet->remaining ?: 0;

            if (Setting::get('is_referral_enabled')) {

                $remaining += $user_wallet->referral_amount ?: 0;
            }

            throw_if($remaining < $user_pay_amount, new Exception(api_error(147), 147));

            if ($user_pay_amount > 0) {

                $request->merge([
                    'to_user_id' => $personalized_request->receiver_id,
                    'total' => $user_pay_amount * Setting::get('token_amount'),
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount * Setting::get('token_amount'),
                    'payment_id' => 'AC-' . rand(),
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'usage_type' => USAGE_TYPE_PERSONALIZE_REQUEST,
                    'tokens' => $user_pay_amount,
                    // 'promo_discount' => $total - $user_pay_amount
                ]);

                $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

                if ($wallet_payment_response->success) {

                    $personalized_request->update(['status' => PERSONALIZE_USER_PAID]);

                    DB::commit();

                    $data['personalized_request'] = new PersonalizedRequestResource($personalized_request);

                    return $this->sendResponse(api_success(820), 820, $data ?? []);

                } else {

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
     * @method request_reject_by_user()
     * 
     * @uses To reject the personalized request by user
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function request_reject_by_user(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'personalized_request_unique_id' => 'required|exists:personalized_requests,unique_id,sender_id,'.$request->id,
                'cancel_reason' => 'required'
            ];

            $custom_errors = ['personalized_request_unique_id' => api_error(214)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $personalized_request = PersonalizedRequest::firstWhere(['unique_id' => $request->personalized_request_unique_id, 'status' => PERSONALIZE_CREATOR_ACCEPTED]);

            throw_if(!$personalized_request, new Exception(api_error(296), 296));

            $personalized_request->update(['status' => PERSONALIZE_USER_REJECTED]);

            DB::commit();

            $data['personalized_requests'] = new PersonalizedRequestResource($personalized_request);

            return $this->sendResponse(api_success(821), 821, $data);
                    
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @method create_product_for_personalize_request()
     *
     * @uses To store the personalized user products
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param App\Http\Requests\Api\PersonalizeProductStoreRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_product_for_personalize_request(PersonalizeProductStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $personalized_request = PersonalizedRequest::firstWhere(['id' => $request->personalized_request_id, 'status' => PERSONALIZE_USER_PAID]);

            throw_if(!$personalized_request, new Exception(api_error(300), 300));

            $personalized_product = PersonalizedProduct::updateOrCreate(['id' => $request->personalized_product_id], $request->validated());

            throw_if(!$personalized_product, new Exception(api_error(290), 290));

            $personalized_request->update(['status' => PERSONALIZE_CREATOR_UPLOADED]);

            $personalized_request->refresh();

            PersonalizedProductFile::whereIn('id', explode(',', $request->file_ids))->update(['personalized_product_id' => $personalized_product->id]);

            DB::commit();

            $data['personalized_product'] = new PersonalizedProductResource($personalized_product->refresh());

            $code = $personalized_product->wasRecentlyCreated ? 822 : 823;

            return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

   /**
     * @method personalize_product_files_save()
     *
     * @uses To store the images, videos, audios
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param Request $request
     *
     * @return JSON Response
     */
    public function personalize_product_files_save(Request $request)
    {
        try {

            $rules = [

                'files.*' => 'required|file|mimes:jpg,png,jpeg,mp4,mov,mkv,ogg',
            ];

            $custom_errors = [
                'files.*.mimes' => 'Each file must be a JPG, PNG, JPEG, MP4, or MP3.',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $personalize_product_file_ids = $personalize_product_files = [];

            if ($request->has('files')) {

                throw_if(count($request->files) <= 0, new Exception(api_error(278), 278));

                foreach ($request->file('files') as $key => $file) {

                    $file_url = Helper::storage_upload_file($file, PERSONALIZE_PRODUCT_PATH);

                    $file_type = $file->getClientOriginalExtension() ?: '';

                    $personalize_product_file = new PersonalizedProductFile([
                        'file' => $file_url,
                        'file_type' => $file_type,
                    ]);

                    $personalize_product_file->save();

                    $personalize_product_files[] = $personalize_product_file;

                    $personalize_product_file_ids[] = $personalize_product_file->id;
                }

                DB::commit();

                $data['personalize_product_files'] = $personalize_product_files ?? [];

                $data['personalize_product_file_ids'] = $personalize_product_file_ids;

                return $this->sendResponse(api_success(252), 252, $data);
            }

            throw new Exception(api_error(299), 299);

        } catch (Exception $e) {

            DB::rollBack();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @method personalize_product_file_delete()
     *
     * @uses remove the selected file
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param integer $personalized_product_file_id
     *
     * @return JSON Response
     */
       public function personalize_product_file_delete(Request $request) {
        try {
            DB::beginTransaction();

            $rules = [
                'file' => 'required',
                'personalized_product_file_id' => 'required|exists:personalized_product_files,id',
            ];

            Helper::custom_validator($request->all(), $rules);

            $personalized_product_file = PersonalizedProductFile::firstWhere('file', $request->file);

            $personalized_product_file_ids = explode(',', $request->personalized_product_file_id);

            if($personalized_product_file) {

                $personalized_request_product = array_search($personalized_product_file->id, $personalized_product_file_ids);

                unset($personalized_product_file_ids[$personalized_request_product]);

                $personalized_product_file->delete();

                DB::commit(); 

            }

            $personalized_product_files = PersonalizedProductFile::whereIn('id',$personalized_product_file_ids)->pluck('file');

            $personalized_product_file_ids = $personalized_product_file_ids ? implode(',', $personalized_product_file_ids) : '';
            
            $data['personalized_product_file_ids'] = $personalized_product_file_ids ?? '';

            $data['personalized_product_files'] = $personalized_product_files ?? '';

            return $this->sendResponse(api_success(152), 152, $data = $data);

        } catch (\Exception $e) { 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        } 
    }

     /**
     * @method personalize_request_product_response()
     * 
     * @uses To get the selected the personalized request
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return JSON Response
     */
     public function personalize_request_product_response(Request $request) {

        try {

            DB::beginTransaction();

           $rules = [
                'personalized_request_unique_id' => 'required|exists:personalized_requests,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules);

            $personalized_request = PersonalizedRequest::firstWhere(['unique_id' => $request->personalized_request_unique_id]);

            throw_if(!$personalized_request, new Exception(api_error(291), 291));

             if($personalized_request->type != PERSONALIZE_TYPE_PRODUCT && $personalized_request->product_type != PRODUCT_TYPE_PHYSICAL){

                $rules = [

                  'password' => ['required', 'min:6', 'exclude'],

                ];

                Helper::custom_validator($request->all(), $rules);

                throw_if(Str::contains($request->password, ' '), new Exception(tr('space_not_allowed')));

                $admin_password = $this->loginUser->password ?? '';

                throw_if(!Hash::check($request->password, $admin_password), new Exception(api_error(108), 108));

            }

            DB::commit();

            if($personalized_request->product_type == PRODUCT_TYPE_PHYSICAL){

                $personalized_product = PersonalizedProduct::firstWhere('personalized_request_id', $personalized_request->id);

                throw_if(!$personalized_product, new Exception(api_error(302), 302));

                $data['personalized_product'] = new PersonalizedProductResource($personalized_product);
            }

            $data['personalized_requests'] = new PersonalizedRequestFileResource($personalized_request);

            return $this->sendResponse('', '', $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }


    /**
     * @method creator_update_request_file()
     *
     * @uses To update the request file for creator
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param Request $request
     *
     * @return JSON Response
     */
    public function creator_update_request_file(Request $request)
    {
        try {
            $rules = [
                'personalized_request_unique_id' => 'required|exists:personalized_requests,unique_id,receiver_id,'.$request->id,
                'previrew_file' => 'nullable|mimes:jpg,png,jpeg,gif',
            ];

            $custom_errors = [
                'personalized_request_unique_id' => api_error(214),
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $personalized_request = PersonalizedRequest::firstWhere(['unique_id' => $request->personalized_request_unique_id, 'status' => PERSONALIZE_USER_PAID]);

            throw_if(!$personalized_request, new Exception(api_error(300), 300));

            throw_if($personalized_request->product_type == PRODUCT_TYPE_PHYSICAL, new Exception(api_error(301), 301));

            if ($personalized_request->type != PERSONALIZE_TYPE_PRODUCT) {

                $rules['file'] = ['required'];

                switch ($personalized_request->type) {

                    case PERSONALIZE_TYPE_IMAGE:
                        $rules['file'][] = 'mimes:jpg,png,jpeg,gif';
                        $preview_placeholder = asset('images/image_placeholder.png');
                        break;

                    case PERSONALIZE_TYPE_VIDEO:
                        $rules['file'][] = 'mimes:mp4,mov,mkv,ogg';
                        $preview_placeholder = asset('images/video_placeholder.png');
                        break;
                        
                    case PERSONALIZE_TYPE_AUDIO:
                        $rules['file'][] = 'mimes:mp3';
                        $preview_placeholder = asset('images/audio_placeholder.jpg');
                        break;
                }

            }elseif($personalized_request->product_type == PRODUCT_TYPE_DIGITAL) {

                $rules['file'] = ['required', 'mimes:pdf,xls,xlsx,csv'];

                $preview_placeholder = asset('images/file_placeholder.jpg');
            }

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            if ($request->hasFile('file')) {

                $file = $request->file('file');

                $file_url = Helper::storage_upload_file($file, PERSONALIZE_FILE_PATH);

                $file_type = $file->getClientOriginalExtension() ?: '';

                if($request->file('preview_file')){

                    $preview_file = $request->file('preview_file');

                    $preview_file_url = Helper::storage_upload_file($preview_file, PERSONALIZE_FILE_PATH);
                }

                $personalized_request->update(['file' => $file_url, 'preview_file' => isset($preview_file_url) ? $preview_file_url : $preview_placeholder,'file_type' => $file_type, 'status' => PERSONALIZE_CREATOR_UPLOADED]);
            }

            DB::commit();

            $data['personalized_request'] = new PersonalizedRequestResource($personalized_request);

            return $this->sendResponse(api_success(252), 252, $data);

        } catch (Exception $e) {

            DB::rollBack();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method personalized_delivery_address_list()
     *
     * @uses To get list of the Personalized delivery address lists created by the user
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
     public function personalized_delivery_address_list(Request $request)
     {
        try {
            $base_query = PersonalizedDeliveryAddress::where('user_id', $request->id);

            $data = [
                'total' => $base_query->count(),
                'delivery_addresses' => $base_query->get(),
            ];

            return $this->sendResponse('', '', $data);

        } catch (\Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method request_cancel_by_user()
     * 
     * @uses To cancel the personalized request by user
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function request_cancel_by_user(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'personalized_request_unique_id' => 'required|exists:personalized_requests,unique_id,sender_id,'.$request->id,
                'cancel_reason' => 'required'
            ];

            $custom_errors = ['personalized_request_unique_id' => api_error(214)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $personalized_request = PersonalizedRequest::firstWhere(['unique_id' => $request->personalized_request_unique_id, 'status' => PERSONALIZE_USER_REQUESTED]);

            throw_if(!$personalized_request, new Exception(api_error(305), 305));

            $personalized_request->update(['status' => PERSONALIZE_USER_CANCELLED, 'cancel_reason' => $request->cancel_reason]);

            DB::commit();

            $data['personalized_requests'] = new PersonalizedRequestResource($personalized_request);

            return $this->sendResponse(api_success(825), 825, $data);
                    
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

}
