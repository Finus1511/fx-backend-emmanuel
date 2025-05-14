<?php

namespace App\Http\Controllers\Api\VirtualExperience;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use Illuminate\Validation\Rule;

use App\Models\{User, VirtualExperience, UserWallet, VirtualExperienceBooking};

use Carbon\Carbon;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Http\Resources\{VirtualExperienceResource, VirtualExperienceBookingsResource};

use App\Repositories\VirtualExperienceRepository as VirtualExperienceRepo;

class UserVirtualExperienceController extends Controller
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
     * @method list()
     *
     * @uses user virtual experience List 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function list(Request $request) {
        
        try {

             $base_query = VirtualExperience::where('user_id', $request->id)->when($request->filled('status'), function ($query) use ($request) {
                             $query->where('status', $request->status);
                        })->when($request->filled('search_key'), function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('unique_id', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('price_per', "LIKE", "%" . $request->search_key . "%")
                                ->orWhereHas('user', function($query) use($request) {
                                    $query->where('name', "LIKE", "%{$request->search_key}%");
                                });
                            });
                        });

            $data['total'] = $base_query->count();

            $virtual_experiences = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['virtual_experiences'] = VirtualExperienceResource::collection($virtual_experiences);

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }    
    }

    /** 
     * @method view()
     *
     * @uses user virtual experience view 
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function view(Request $request) {
        
        try {

             $virtual_experience = VirtualExperience::where(['unique_id' => $request->virtual_experience_unique_id])->first();

            throw_if(!$virtual_experience, new Exception(api_error(269), 269));

            $data['virtual_experience'] = new VirtualExperienceResource($virtual_experience);

            return $this->sendResponse('', '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

     /**
     * @method other_user_virtual_experience_list()
     *
     * @uses To display all the virtual experience of a creator
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function other_user_virtual_experience_list(Request $request) {

        try {

            $rules = [
                    'user_unique_id' => 'nullable|exists:users,unique_id'
                    ];

            Helper::custom_validator($request->all(), $rules);

            $base_query = VirtualExperience::where('user_id', '!=', $request->id)->orderBy('created_at', 'desc')
                ->when($request->filled('status'), function ($query) use ($request) {
                             $query->where('status', $request->status);
                        })->when($request->filled('search_key'), function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('unique_id', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('price_per', "LIKE", "%" . $request->search_key . "%")
                                ->orWhereHas('user', function($query) use($request) {
                                    $query->where('name', "LIKE", "%{$request->search_key}%");
                                });
                            });
                        });

            if ($request->user_unique_id) {

                $user = User::firstWhere('unique_id', $request->user_unique_id);

                throw_if(!$user, new Exception(api_error(135), 135));

                $base_query = $base_query->where('user_id', $user->id);
            }

            $data['total'] = $base_query->count() ?? 0;

            $virtual_experiences = $base_query->skip($this->skip)->take($this->take)->get();

            $data['virtual_experiences'] = VirtualExperienceResource::collection($virtual_experiences);

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }


    /**
     * @method virtual_experience_booking()
     * 
     * @uses sends money to another user
     * 
     * @created  RA Shakthi
     *
     * @updated
     * 
     * @param Request $request
     * 
     * @return JSON with boolean output
     */
    public function virtual_experience_booking(Request $request) {
        try {
            DB::beginTransaction();

            $rules = [
                'virtual_experience_id' => 'required|exists:virtual_experiences,id',
                 'book_all' => ['nullable', Rule::in([YES, NO])]
            ];

            $custom_errors = ['virtual_experience_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $virtual_experience = VirtualExperience::find($request->virtual_experience_id);

            throw_if(!$virtual_experience, new Exception(api_error(135), 135));

            $virtual_experience_payment = VirtualExperienceBooking::where(['virtual_experience_id' => $request->virtual_experience_id, 'user_id' => $request->id, 'status' => VIRTUAL_EXPERIENCE_PAID])->first();

            throw_if($virtual_experience_payment, new Exception(api_error(279), 279));

            $request->book_all == YES ? throw_if($virtual_experience->used_capacity > 0, new Exception(api_error(280), 280)) : throw_if($virtual_experience->remaning_capacity <= 0, new Exception(api_error(281), 281));

            $total_capacity = $request->book_all ==YES ? $virtual_experience->total_capacity : 1;

            $virtual_experience_amount = $virtual_experience->price_per * $total_capacity;

            $user_wallet = UserWallet::where('user_id', $request->id)->first();

            throw_if(!$user_wallet, new Exception(api_error(282), 282));

            throw_if($user_wallet->remaining < $virtual_experience_amount, new Exception(api_error(142), 142));

            $request->request->add([
                    'user_id' => $virtual_experience->user_id,
                    'to_user_id' => $request->id,
                    'received_from_user_id' => $request->id,
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'total' => $virtual_experience_amount * Setting::get('token_amount'), 
                    'user_pay_amount' => $virtual_experience_amount,
                    'paid_amount' => $virtual_experience_amount * Setting::get('token_amount'),
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'payment_id' => 'WPP-'.rand(),
                    'usage_type' => USAGE_TYPE_PPV,
                    'tokens' => $virtual_experience_amount,
                ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();


            if($wallet_payment_response->success) {

                $request->merge([
                    'user_id' => $virtual_experience->user_id,
                    'to_user_id' => $request->id,
                    'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                    'payment_id' => 'WPP-'.rand()
                ]);

                $wallet_payment_response = PaymentRepo::user_wallets_payment_to_other_save($request)->getData();

                $booking_data = [
                    'virtual_experience_id' => $virtual_experience->id,
                    'virtual_experience_user_id' => $virtual_experience->user_id,
                    'user_id' => $request->id,
                    'price_per' => $virtual_experience->price_per,
                    'total_capacity' => $request->book_all ==YES ? $virtual_experience->total_capacity : 1,
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'total' => $virtual_experience_amount, 
                    'payment_id' => 'WPP-'.rand(),
                    'status' => VIRTUAL_EXPERIENCE_PAID,
                ];

                $virtual_experience_booking =VirtualExperienceBooking::create($booking_data);

                 $remaining_capacity = $virtual_experience->remaning_capacity - $virtual_experience_booking->total_capacity;

                 $used_capacity = $virtual_experience->used_capacity + $virtual_experience_booking->total_capacity;


                 $virtual_experience->update(['remaning_capacity' => $remaining_capacity, 'used_capacity' => $used_capacity]);

                } else {

                    throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                }

                $virtual_experience->refresh();

               $data['virtual_experience'] = new VirtualExperienceResource($virtual_experience);

            DB::commit();

            return $this->sendResponse(api_success(140), 140, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

}


