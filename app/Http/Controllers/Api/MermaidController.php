<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Log, Hash, Validator, Exception, Setting;
use App\Models\{Mermaid,User, MermaidFile, MermaidPayment, UserWallet};
use Carbon\Carbon;
use App\Helpers\Helper;
 
use App\Http\Requests\Api\{MermaidStoreRequest, MermaidsFileUploadRequest, MermaidFileGetRequest, MermaidGetRequest, MermaidPaymentGetRequest};
 
use App\Http\Resources\{MermaidResource, MermaidFileResource, MermaidPaymentResource};

use App\Repositories\PaymentRepository as PaymentRepo;

class MermaidController extends Controller
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
     * @method index()
     *
     * @uses To display all the mermaids
     *
     * @created 
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function index(Request $request) {
     try {
          $base_query = Mermaid::query()->with('mermaidFiles')->where('user_id', $request->id)
            ->when($request->filled('search_key'), function($query) use($request) {
                $query->where(function($query) use($request){
                    $query->where('unique_id', "LIKE", "%" . $request->search_key . "%")
                    ->orWhere('name', "LIKE", "%" . $request->search_key . "%");
                });
            })->when($request->filled('status'), function($query) use($request) {
                $query->where(function($query) use($request){
                    $query->where('status', $request->status);
                });
            });
            $data['total'] = $base_query->count() ?? 0;
            $mermaids = $base_query->skip($this->skip)->take($this->take)->get();
            $data['mermaids'] = MermaidResource::collection($mermaids);
            return $this->sendResponse($message = '', $code = '', $data);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode());
        }
     }
    /**
     * @method store()
     *
     * @uses To save the mermaids
     *
     * @created 
     *
     * @updated 
     *
     * @param $request
     *
     * @return JSON Response
     */
    public function store(MermaidStoreRequest $request) {
        try {
            DB::beginTransaction();
            $user = User::where(['id' => $request->id, 'is_content_creator' => CONTENT_CREATOR])->first();
            throw_if(!$user, new Exception(api_error(135), 135));
            $mermaid = Mermaid::updateOrCreate(['id' => $request->mermaid_id, 'user_id' => $request->id], $request->validated());
             if($request->hasFile('thumbnail')) {
                Helper::storage_delete_file($mermaid->thumbnail, MERMAID_FOLDER_PATH); 
                $mermaid->update(['thumbnail' => Helper::storage_upload_file($request->file('thumbnail'), MERMAID_FOLDER_PATH) ?? asset('placeholder.jpeg')]);
            }
            throw_if(!$mermaid, new Exception(api_error(327), 327));
            MermaidFile::whereIn('id', explode(',', $request->file_ids))->update(['mermaid_id' => $mermaid->id]);
            $success_code = $mermaid->wasRecentlyCreated ? 837: 838;
            $mermaid->load('mermaidFiles');
            DB::commit();
            $data['mermaid'] = MermaidResource::make($mermaid->refresh());
            return $this->sendResponse(api_success($success_code), $success_code, $data);
        } catch(Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }
    /**
     * @method files_upload()
     *
     * @uses To upload mermaids files
     *
     * @created 
     *
     * @updated 
     *
     * @param $request
     *
     * @return JSON Response
     */
    public function files_upload(MermaidsFileUploadRequest $request)
    {  
    try {
        DB::beginTransaction();
        $user = User::where(['id' => $request->id, 'is_content_creator' => CONTENT_CREATOR])->first();
        throw_if(!$user, new Exception(api_error(135), 135));
        $mermaid_file_ids = $mermaid_files = [];
        if ($request->hasFile('files')) {
            $files = $request->file('files');
            throw_if(count($files) <= 0, new Exception(api_error(278), 278));
            foreach ($files as $file) {
                $file_url = Helper::storage_upload_file($file, MERMAID_FILE_PATH);
                $file_type = $file->getClientOriginalExtension() ?: '';
                $mermaid_file = new MermaidFile([
                    'user_id' => $request->id,
                    'file' => $file_url,
                    'file_type' => $request->file_type,
                    'preview_file' => $request->file_type == FILE_TYPE_VIDEO ? Helper::storage_upload_file($request->file('preview_file'), MERMAID_FILE_PATH): "",
                ]);
                $mermaid_file->save();
                $mermaid_files[] = $mermaid_file;
                $mermaid_file_ids[] = $mermaid_file->id;
            }
            DB::commit();
            $data['mermaid_files'] = $mermaid_files ?? [];
            $data['mermaid_file_ids'] = MermaidFileResource::make($mermaid_file->refresh());
            return $this->sendResponse(api_success(252), 252, $data);
        }
        throw new Exception(api_error(299), 299);
    } catch (Exception $e) {
        DB::rollBack();
        return $this->sendError($e->getMessage(), $e->getCode());
    }
 }
 /**
 * @method files_remove()
 *
 * @uses remove the selected file
 *
 * @created 
 *
 * @updated
 *
 * @param integer $mermaid_file_id
 *
 * @return JSON Response
 */
 public function files_remove(MermaidFileGetRequest $request) {
    try {
        
        DB::begintransaction();

        $mermaid_file = MermaidFile::firstWhere('unique_id', $request->mermaid_file_unique_id);
        $mermaid_file->delete();
        DB::commit();
        return $this->sendResponse(api_success(839), 839, $request->mermaid_file_id ?? 0);
       
    } catch(Exception $e){ 
        DB::rollback();
        return $this->sendError($e->getMessage(), $e->getCode());
    } 
  }
   /**
     * @method destroy()
     *
     * @uses To delete creators mermaid folder
     *
     * @created 
     *
     * @updated  
     *
     * @param
     * 
     * @return response of details
     *
     */
    public function destroy(MermaidGetRequest $request) {
        try {
            DB::begintransaction();

            $mermaid = Mermaid::firstWhere('unique_id', $request->mermaid_unique_id);
            $mermaid->delete();
            DB::commit();
            $data['mermaid_id'] = $request->mermaid_unique_id;
            return $this->sendResponse(api_success(840), 840, $data);
            
        } catch(Exception $e){
            DB::rollback();
            return $this->sendError($e->getMessage(), $e->getCode());
        }       
         
    }

    public function mermaids_payment_by_wallet(MermaidPaymentGetRequest $request) {
        try {
            DB::beginTransaction();
            
            $mermaid = Mermaid::firstWhere('unique_id', $request->mermaid_unique_id);
            throw_if(!$mermaid, new Exception(api_error(327), 327));
            throw_if($request->id == $mermaid->user_id, new Exception(api_error(171), 171));
            $check_payment = MermaidPayment::firstWhere(['user_id' => $request->id, 'mermaid_id' => $mermaid->id]);
            throw_if($check_payment, new Exception(api_error(328), 328));
            $user_wallet = UserWallet::firstWhere('user_id', $request->id);
            throw_if(!$user_wallet, new Exception(api_error(282), 282));
            $remaining = $user_wallet->remaining ?: 0;
            if (Setting::get('is_referral_enabled')) {
                $remaining += $user_wallet->referral_amount ?: 0;
            }
            throw_if($remaining < $mermaid->amount, new Exception(api_error(147), 147));
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $mermaid->amount * Setting::get('token_amount'), 
                'user_pay_amount' => $mermaid->amount,
                'paid_amount' => $mermaid->amount * Setting::get('token_amount'),
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-' . rand(),
                'usage_type' => USAGE_TYPE_MERMAID,
                'tokens' => $mermaid->amount,
            ]);
            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
            if($wallet_payment_response->success) {
                $mermaid_payment = MermaidPayment::create([
                    'user_id' => $request->id,
                    'mermaid_id' => $mermaid->id,
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_id' => $request->payment_id,
                    'amount' => $mermaid->amount * Setting::get('token_amount'),
                    'user_amount' => $mermaid->amount * Setting::get('token_amount'),
                    'admin_amount' => $mermaid->amount * Setting::get('token_amount'),
                    'status' => PAID,
                ]);
                if ($mermaid_payment->status == PAID) {
                    $wallet_payment_response = PaymentRepo::mermaid_payment_wallet_update($request, $mermaid, $mermaid_payment);
                    throw_if(!$wallet_payment_response->success, new Exception(api_error(329), 329)); 
                }
                DB::commit();
                $data['mermaid_payment'] = new MermaidPaymentResource($mermaid_payment->refresh());
                return $this->sendResponse(api_success(840), 840, $data);
            } else {
                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
            }
        } catch(Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }
    
    public function view(MermaidPaymentGetRequest $request) {
        try {
            DB::beginTransaction();
            $mermaid = Mermaid::firstWhere(['unique_id' => $request->mermaid_unique_id]);
            throw_if(!$mermaid, new Exception(api_error(327), 327));
            DB::commit();
            $data['mermaid'] = new MermaidResource($mermaid);
            return $this->sendResponse('', '', $data);
        } catch(Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }
}

