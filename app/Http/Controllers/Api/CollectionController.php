<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\{Collection,User, CollectionFile, CollectionPayment, UserWallet};

use Carbon\Carbon;

use App\Helpers\Helper;
 
use App\Http\Requests\Api\{CollectionStoreRequest, CollectionFileUploadRequest, CollectionFileGetRequest, 
CollectionGetRequest, CollectionPaymentGetRequest};
 
use App\Http\Resources\{CollectionResource, CollectionFileResource, CollectionPaymentResource};

use App\Repositories\PaymentRepository as PaymentRepo;

use Illuminate\Support\Facades\Storage;

use App\Jobs\GenerateThumbnail;

class CollectionController extends Controller
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
     * @uses To display all the collections
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function index(Request $request) {

     try {

        $user_id = $request->id;

        if ($request->user_unique_id) {
            
            $user_id = User::firstWhere('unique_id', $request->user_unique_id)->id;
        }

          $base_query = Collection::query()->with('collectionFiles')->where('user_id', $user_id)
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

            $collections = $base_query->skip($this->skip)->take($this->take)->get();

            $data['collections'] = CollectionResource::collection($collections);

            return $this->sendResponse($message = '', $code = '', $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
     }

    /**
     * @method store()
     *
     * @uses To save the collections
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param $request
     *
     * @return JSON Response
     */
    public function store(CollectionStoreRequest $request) {

        try {

            DB::beginTransaction();

            $user = User::where(['id' => $request->id, 'is_content_creator' => CONTENT_CREATOR])->first();

            throw_if(!$user, new Exception(api_error(289), 289));

            $collection = Collection::updateOrCreate(['id' => $request->collection_id, 'user_id' => $request->id], $request->validated());

             if($request->hasFile('thumbnail')) {

                Helper::storage_delete_file($collection->thumbnail, COLLECTION_FOLDER_PATH); 

                $collection->update(['thumbnail' => Helper::storage_upload_file($request->file('thumbnail'), COLLECTION_FOLDER_PATH) ?? asset('placeholder.jpeg')]);
            }

            throw_if(!$collection, new Exception(api_error(327), 327));

            $success_code = $collection->wasRecentlyCreated ? 837: 838;

            $collection->load('collectionFiles');

            DB::commit();

            $data['collection'] = CollectionResource::make($collection->refresh());

            return $this->sendResponse(api_success($success_code), $success_code, $data);

        } catch(Exception $e) {

            DB::rollBack();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method files_upload()
     *
     * @uses To upload collections files
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param $request
     *
     * @return JSON Response
     */
    public function files_upload(CollectionFileUploadRequest $request)
    {  
    try {

        DB::beginTransaction();

        $user = User::where(['id' => $request->id, 'is_content_creator' => CONTENT_CREATOR])->first();

        throw_if(!$user, new Exception(api_error(135), 135));

        $collection = Collection::firstWhere('unique_id', $request->collection_unique_id);

        throw_if(!$collection, new Exception(api_error(327), 327));

        $collection_files = [];

       if($request->hasFile('files')) {

            $files = $request->file('files');

            throw_if(count($files) <= 0, new Exception(api_error(278), 278));

            $data = $thumbnails = [];

            foreach ($files as $file) {

                $file_url = Helper::storage_upload_file($file, COLLECTION_FILE_PATH);

                $file_extension = $file->getClientOriginalExtension() ?: '';

                $file_type = in_array($file_extension, ['mp4', 'avi', 'mov']) ? 'video' : 'image';

                $unique_id  = "CF-".rand();
                
                $data[] = [
                    'unique_id' => $unique_id,
                    'collection_id' => $collection->id,
                    'user_id' => $request->id,
                    'file' => $file_url,
                    'file_type' => $file_type,
                    'preview_file' => asset('placeholder.jpeg'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $thumbnails[] = [
                    'unique_id' => $unique_id,
                    'file_url' => $file_url
                ]; 

            }

            CollectionFile::insert($data);

            DB::commit();

            if(!empty($thumbnails)){

              GenerateThumbnail::dispatch($thumbnails);
            }

            $collection_files = CollectionFile::whereIn('unique_id', array_column($data, 'unique_id'))->get();

            $collection_files = CollectionFileResource::collection($collection_files);

            return $this->sendResponse(api_success(252), 252, ['collection_files' => $collection_files ?? []]);
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
 * @created RA Shakthi
 *
 * @updated
 *
 * @param integer $collection_file_id
 *
 * @return JSON Response
 */
 public function files_remove(CollectionFileGetRequest $request) {

    try {
        
        DB::begintransaction();

        $collection_file = CollectionFile::firstWhere('unique_id', $request->collection_file_unique_id);

        $collection_file->delete();

        DB::commit();

        return $this->sendResponse(api_success(839), 839, $request->collection_file_id ?? 0);
       
    } catch(Exception $e){ 

        DB::rollback();

        return $this->sendError($e->getMessage(), $e->getCode());

    } 

  }

   /**
     * @method destroy()
     *
     * @uses To delete creators collection folder
     *
     * @created RA Shakthi
     *
     * @updated  
     *
     * @param
     * 
     * @return response of details
     *
     */
    public function destroy(CollectionGetRequest $request) {

        try {

            DB::begintransaction();

            $collection = Collection::firstWhere('unique_id', $request->collection_unique_id);

            $collection->delete();

            DB::commit();

            $data['collection_id'] = $request->collection_unique_id;

            return $this->sendResponse(api_success(840), 840, $data);
            
        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }       
         
    }

     /**
     * @method collections_payment_by_wallet()
     * 
     * @uses send money to other user
     *
     * @created RA Shakthi 
     *
     * @updated 
     *
     * @param object $request
     *
     * @return json with boolean output
     */
    public function collections_payment_by_wallet(CollectionPaymentGetRequest $request) {

        try {

            DB::beginTransaction();

            $collection = Collection::firstWhere('unique_id', $request->collection_unique_id);

            throw_if(!$collection, new Exception(api_error(327), 327));

            throw_if($request->id == $collection->user_id, new Exception(api_error(171), 171));

            $check_payment = CollectionPayment::firstWhere(['user_id' => $request->id, 'collection_id' => $collection->id]);

            throw_if($check_payment, new Exception(api_error(328), 328));

            $user_wallet = UserWallet::firstWhere('user_id', $request->id);

            throw_if(!$user_wallet, new Exception(api_error(282), 282));

            $remaining = $user_wallet->remaining ?: 0;

            if (Setting::get('is_referral_enabled')) {

                $remaining += $user_wallet->referral_amount ?: 0;
            }

            $user_pay_amount = $request->promo_code ? Helper::apply_promo_code($request, $collection->amount, COLLECTION_PAYMENTS, $collection->user_id) : $collection->amount;

            throw_if($remaining < $user_pay_amount, new Exception(api_error(147), 147));

            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $user_pay_amount * Setting::get('token_amount'), 
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount * Setting::get('token_amount'),
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-' . rand(),
                'usage_type' => USAGE_TYPE_COLLECTION,
                'tokens' => $user_pay_amount,
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $collection_payment = CollectionPayment::create([
                    'user_id' => $request->id,
                    'collection_id' => $collection->id,
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_id' => $request->payment_id,
                    'amount' =>  $user_pay_amount * Setting::get('token_amount'),
                    'user_amount' =>  $user_pay_amount * Setting::get('token_amount'),
                    'admin_amount' =>  $user_pay_amount * Setting::get('token_amount'),
                    'status' => PAID,
                ]);

                if ($collection_payment->status == PAID) {

                    $wallet_payment_response = PaymentRepo::collection_payment_wallet_update($request, $collection, $collection_payment);

                    throw_if(!$wallet_payment_response->success, new Exception(api_error(329), 329)); 
                }

                DB::commit();

                $data['collection_payment'] = new CollectionPaymentResource($collection_payment->refresh());

                return $this->sendResponse(api_success(841), 841, $data);

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

     /**
     * @method view()
     *
     * @uses To view collections using unique id
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param object $request
     *
     * @return  JSON Response
     */
    public function view(CollectionPaymentGetRequest $request) {

        try {

            DB::beginTransaction();

            $collection = Collection::firstWhere(['unique_id' => $request->collection_unique_id]);

            throw_if(!$collection, new Exception(api_error(327), 327));

            DB::commit();

            $data['collection'] = new CollectionResource($collection);

            return $this->sendResponse('', '', $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

     /**
     * @method files_list()
     *
     * @uses To display all the collections files
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request collection unique id
     *
     * @return JSON Response
     */
    public function files_list(CollectionGetRequest $request) {

     try {

          $collection = Collection::firstWhere('unique_id', $request->collection_unique_id);

          $base_query = CollectionFile::query()->where(['user_id' => $request->id, 'collection_id' => $collection->id])
            ->when($request->filled('search_key'), function($query) use($request) {
                $query->where(function($query) use($request){
                    $query->where('unique_id', "LIKE", "%" . $request->search_key . "%");
                });
            })->when($request->filled('status'), function($query) use($request) {
                $query->where(function($query) use($request){
                    $query->where('status', $request->status);
                });
            });

            $data['total'] = $base_query->count() ?? 0;

            $collection_files = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['collection'] = new CollectionResource($collection);

            $data['collection_files'] = CollectionFileResource::collection($collection_files);

            return $this->sendResponse($message = '', $code = '', $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method files_list_for_others()
     *
     * @uses To display all the collections files
     *
     * @created
     *
     * @updated
     *
     * @param request collection unique id
     *
     * @return JSON Response
     */
    public function files_list_for_others(Request $request) {

     try {

            $rules = [
                'collection_unique_id' => 'required|exists:collections,unique_id'
            ];

            Helper::custom_validator($request->all(), $rules);

          $collection = Collection::firstWhere('unique_id', $request->collection_unique_id);

          $base_query = CollectionFile::query()->where(['collection_id' => $collection->id])
            ->when($request->filled('search_key'), function($query) use($request) {
                $query->where(function($query) use($request){
                    $query->where('unique_id', "LIKE", "%" . $request->search_key . "%");
                });
            })->when($request->filled('status'), function($query) use($request) {
                $query->where(function($query) use($request){
                    $query->where('status', $request->status);
                });
            });

            $data['total'] = $base_query->count() ?? 0;

            $collection_files = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['collection'] = new CollectionResource($collection);

            $data['collection_files'] = collection_post_user_needs_to_pay($collection->id, $request->id) ? [] : CollectionFileResource::collection($collection_files);

            return $this->sendResponse($message = '', $code = '', $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }
}
