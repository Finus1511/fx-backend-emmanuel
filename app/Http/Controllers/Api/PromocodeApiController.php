<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\PromoCode, App\Models\User, App\Models\UserPromoCode;

use Carbon\Carbon;

use App\Helpers\Helper;

use App\Services\{ PromoCodeService };

use App\Http\Requests\Api\{PromoCodeValidateRequest,PromoCodeStoreRequest, PromoCodeGetRequest};

use App\Http\Resources\PromoCodeResource;

class PromocodeApiController extends Controller
{
    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method promo_code_index()
     *
     * @uses To display all the promocode
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function promo_code_index(Request $request) {

        try {

            $date_filter = '';
            if($request->from_date) {
                $date_filter .= $request->from_date;
                $date_filter .= $request->to_date ? " - $request->to_date" : (" - " . now($this->user_timezone)->format('d-m-Y'));
            }

            $base_query = PromoCode::query()->where('user_id', $request->id)
            ->when($request->filled('search_key'), function($query) use($request) {
                $query->where(function($query) use($request){
                    $query->where('unique_id', "LIKE", "%" . $request->search_key . "%")
                    ->orWhere('promo_code', "LIKE", "%" . $request->search_key . "%");
                });
            })->when($request->filled('amount_type'), function($query) use($request) {
                $query->where(function($query) use($request){
                    $query->where('amount_type', $request->amount_type);
                });
            })->when($request->filled('sort_by_days'), function ($query) use ($request) {
                $query->where(function($query) use($request){
                    $query->where('created_at', '>=', (new Helper)->filter_with_days($request->sort_by_days));
                });
            })->when($date_filter, function($query) use($date_filter) {
                $query->where(function($query) use($date_filter) {
                    $query->whereBetween('created_at', (new Helper)->get_date_filter_keys($date_filter, true, $this->user_timezone));
                });
            });

            $data['total'] = $base_query->count() ?? 0;

            $promocode = $base_query->skip($this->skip)->take($this->take)->latest()->get();

            $data['promocode'] = PromoCodeResource::collection($promocode);

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method promo_code_save()
     *
     * @uses To save the promocode
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id, promo code
     *
     * @return JSON Response
     */
    public function promo_code_save(PromoCodeStoreRequest $request) {

         try {

            DB::beginTransaction();

            $user = User::where(['id' => $request->id, 'is_content_creator' => CONTENT_CREATOR])->first();

            throw_if(!$user, new Exception(api_error(135), 135));

            $promo_code = PromoCode::updateOrCreate(['id' => $request->promo_code_id, 'user_id' => $request->id], $request->validated());

            throw_if(!$promo_code, new Exception(api_error(326), 326));

            $success_code = $promo_code->wasRecentlyCreated ? 233 : 232;

            DB::commit();

            $data['promo_code'] = PromoCodeResource::make($promo_code);

            return $this->sendResponse(api_success($success_code), $success_code, $data);

        } catch(Exception $e) {

            DB::rollBack();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method promo_code_delete()
     *
     * @uses To display all the promocode
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function promo_code_delete(PromoCodeGetRequest $request) {

        try {

            DB::beginTransaction();

            $promo_code = PromoCode::firstWhere(['unique_id' => $request->promo_code_unique_id, 'user_id' => $request->id]);

            $promo_code->delete();

            DB::commit();

            return $this->sendResponse(api_success(234), 234);

        } catch(Exception $e) {

            DB::rollBack();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method promo_code_validate()
     *
     * @uses To validate and calculate promocode amount
     *
     * @created 
     *
     * @updated 
     *
     * @param request id, promo code
     *
     * @return JSON Response
     */
    public function promo_code_validate(PromoCodeValidateRequest $request) {
 
        try {

        DB::beginTransaction();
        
        $promo_code = PromoCode::firstWhere(['promo_code' => $request->promo_code]);

        throw_if(!$promo_code || ($promo_code->platform != $request->platform && $promo_code->platform != ALL_PAYMENTS), new Exception(api_error(291), 291));
        
        $user = User::find($request->id);

        throw_if(!$user, new Exception(api_error(135), 135));

        $no_of_times_used = UserPromoCode::where('promo_code', $promo_code->promo_code)->sum('no_of_times_used');

        $current_date = now($this->timezone);

        throw_if($current_date->isBefore($promo_code->start_date), new Exception(api_error(324), 324));

        throw_if($promo_code->expiry_date && $current_date->isAfter($promo_code->expiry_date), new Exception(api_error(320), 320));

        throw_if($no_of_times_used >= $promo_code->no_of_users_limit, new Exception(api_error(321), 321));

        throw_if($request->id == $promo_code->user_id, new Exception(api_error(322), 322));

        $promo_amount = $request_amount = (new PromoCodeService)->handle($request->platform, $request)->getData()->data ?? 0.00;

        if ($request->promo_code && !empty($promo_code)) {
            $discount = $promo_code->amount_type == PERCENTAGE ? amount_convertion($promo_code->amount, $promo_amount) : $promo_code->amount;
            $promo_amount = $promo_amount - $discount;
        }

        $coupon_applied_amount = $request_amount - $promo_amount;
        
        $data['coupon_code_validate'] = [
            'request_amount' => $request_amount,
            'promo_code' => $request->promo_code ?? "",
            'coupon_type' => $promo_code->amount_type ? tr('amount') : tr('percentage'),
            'coupon_code_amount' => $promo_code->amount_type == ABSOULTE ? $promo_code->amount : 0.00,
            'coupon_code_percentage' => $promo_code->amount_type != ABSOULTE ? $promo_code->amount . '%' : null,
            'coupon_applied_amount' => $coupon_applied_amount,
        ];
        
        DB::commit();

        return $this->sendResponse(api_success(836), 836, $data);
 
        } catch(Exception $e) {
 
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    public function promo_code_status_update(PromoCodeGetRequest $request) {
        try {
            DB::beginTransaction();
            $promo_code = PromoCode::firstWhere(['unique_id' => $request->promo_code_unique_id, 'user_id' => $request->id]);
            $promo_code->update(['status' => !$promo_code->status]);
            $success_code = $promo_code->status ? 841 : 842;
            DB::commit();
            $data['promo_code'] = new PromoCodeResource($promo_code->refresh());
            return $this->sendResponse(api_success($success_code), $success_code, $data);
        } catch(Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

}
