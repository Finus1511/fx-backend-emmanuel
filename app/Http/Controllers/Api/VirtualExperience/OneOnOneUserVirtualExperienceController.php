<?php

namespace App\Http\Controllers\Api\VirtualExperience;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use Illuminate\Validation\Rule;

use App\Models\{User, VeOneOnOne, UserWallet, VeOneOnOneBooking};

use Carbon\Carbon;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Http\Resources\{VeOneonOneResource, VeOneOnOneBookingsResource};

class OneOnOneUserVirtualExperienceController extends Controller
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

             $base_query = VeOneOnOne::where('user_id', $request->id)->when($request->filled('status'), function ($query) use ($request) {
                             $query->where('status', $request->status);
                        })->when($request->filled('search_key'), function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('unique_id', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('amount', "LIKE", "%" . $request->search_key . "%")
                                ->orWhereHas('user', function($query) use($request) {
                                    $query->where('name', "LIKE", "%{$request->search_key}%");
                                });
                            });
                        });

            $data['total'] = $base_query->count();

            $virtual_experiences = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['virtual_experiences'] = VeOneOnOneResource::collection($virtual_experiences);

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

             $virtual_experience = VeOneOnOne::where(['unique_id' => $request->virtual_experience_unique_id])->first();

            throw_if(!$virtual_experience, new Exception(api_error(269), 269));

            $data['virtual_experience'] = new VeOneOnOneResource($virtual_experience);

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
                    'user_unique_id' => 'required|exists:users,unique_id'
                    ];

            Helper::custom_validator($request->all(), $rules);

            $user = User::firstWhere('unique_id', $request->user_unique_id);

            throw_if(!$user, new Exception(api_error(135), 135));

            $base_query = VeOneOnOne::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->when($request->filled('status'), function ($query) use ($request) {
                             $query->where('status', $request->status);
                        })->when($request->filled('search_key'), function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('unique_id', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('amount', "LIKE", "%" . $request->search_key . "%")
                                ->orWhereHas('user', function($query) use($request) {
                                    $query->where('name', "LIKE", "%{$request->search_key}%");
                                });
                            });
                        });

            $data['total'] = $base_query->count() ?? 0;

            $virtual_experiences = $base_query->skip($this->skip)->take($this->take)->get();

            $data['virtual_experiences'] = VeOneOnOneResource::collection($virtual_experiences);

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
                'virtual_experience_id' => 'required|exists:ve_one_on_ones,id',
            ];

            $custom_errors = ['virtual_experience_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $virtual_experience = VeOneOnOne::find($request->virtual_experience_id);

            throw_if(!$virtual_experience, new Exception(api_error(135), 135));

            $virtual_experience_payment = VeOneOnOneBooking::where(['ve_one_on_one_id' => $request->virtual_experience_id, 'user_id' => $request->id, 'status' => VIRTUAL_EXPERIENCE_PAID])->first();

            throw_if($virtual_experience_payment, new Exception(api_error(279), 279));

            $virtual_experience_amount = $virtual_experience->amount;

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

                $booking_data = [
                    've_one_on_one_id' => $virtual_experience->id,
                    've_one_on_one_user_id' => $virtual_experience->user_id,
                    'user_id' => $request->id,
                    'amount' => $virtual_experience->amount,
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'total' => $virtual_experience_amount, 
                    'payment_id' => 'WPP-'.rand(),
                    'status' => VIRTUAL_EXPERIENCE_PAID,
                ];

                $virtual_experience_booking =VeOneOnOneBooking::create($booking_data);

                } else {

                    throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                }

                $virtual_experience->refresh();

               $data['virtual_experience'] = new VeOneOnOneResource($virtual_experience);

            DB::commit();

            return $this->sendResponse(api_success(140), 140, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

}


