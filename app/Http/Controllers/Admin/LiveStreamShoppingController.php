<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{LiveStreamShopping, LssPayment, LssProductPayment, LssProduct, UserProduct, User};
use Setting, DB, Exception;

class LiveStreamShoppingController extends Controller
{
   /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method live_stream_shoppings()
     *
     * @uses To get list of the live stream shopping created by the user
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function live_stream_shoppings(Request $request)
    {
        try {

           $user = $sub_page = '';

           $base_query = LiveStreamShopping::with('lssPayment')->when($request->status == LIVE_STREAM_SHOPPING_ONGOING, function ($query) {
                $query->where(['is_streaming' => YES, 'status' => LIVE_STREAM_SHOPPING_ONGOING]);
            })->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })->when($request->filled('stream_type'), function ($query) use ($request) {
                $query->where('stream_type', $request->stream_type);
            })->when($request->filled('payment_type'), function ($query) use ($request) {
                $query->where('payment_type', $request->payment_type);
            })->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            })->when($request->filled('search_key'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                    ->orWhere('title', 'LIKE', '%' . $request->search_key . '%')
                    ->orWhereHas('user', function ($query) use ($request) {
                        $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                    });
                });
            });

           if($request->user_id){

             $user = User::find($request->user_id);

           }

           $live_stream_shoppings = $base_query->paginate($this->take);

           $sub_page = $request->status == LIVE_STREAM_SHOPPING_ONGOING ? 'view_live_stream_ongoing' : 'view_live_stream_history';

           return view('admin.live_stream_shoppings.index')
                        ->with('page', 'live_stream_shoppings')
                        ->with(compact(['live_stream_shoppings', 'user', 'sub_page'])); 

        }catch(Exception $e) {

            return redirect()->route('admin.live_stream_shoppings.index')->with('flash_error', $e->getMessage());
        }
    }

     /**
     * Display the specified resource.
     *
     * Created RA Shakthi
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function live_stream_shoppings_view(Request $request) {
       
        try {

            $live_stream_shopping = LiveStreamShopping::with('lssPayment')->find($request->live_stream_shopping_id);

            throw_if(!$live_stream_shopping, new Exception(tr('live_stream_shopping_not_found')));

            $sub_page = $request->status == LIVE_STREAM_SHOPPING_ONGOING ? 'view_live_stream_ongoing' : 'view_live_stream_history';
            
            return view('admin.live_stream_shoppings.view')
            ->with('page', 'live_stream_shoppings') 
            ->with(compact(['live_stream_shopping', 'sub_page']));
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method live_stream_shopping_payments()
     *
     * @uses To get list of the live stream shopping payments
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function live_stream_shopping_payments(Request $request)
    {
        try {

          $live_stream_shopping = $user = '';

          $base_query = LssPayment::with('lssStreamShopping')->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            })->when($request->filled('live_stream_shopping_id'), function ($query) use ($request) {
                $query->where('live_stream_shopping_id', $request->live_stream_shopping_id);
            })->when($request->filled('search_key'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                          ->orWhere('payment_id', 'LIKE', '%' . $request->search_key . '%')->orWhereHas('user', function ($query) use ($request) {
                              $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                        })->orWhereHas('lssStreamShopping', function ($query) use ($request) {
                              $query->where('title', 'LIKE', '%' . $request->search_key . '%');
                        });
                  });
            });

            if($request->live_stream_shopping_id){

                $live_stream_shopping = LiveStreamShopping::find($request->live_stream_shopping_id);

            }

            if($request->user_id){

                $user = User::find($request->user_id);

            }

           $lss_payments = $base_query->paginate($this->take);

           return view('admin.live_stream_shoppings.payments.index')
                        ->with('page', 'payments')
                        ->with('sub_page', 'lss-payments')
                        ->with(compact(['lss_payments', 'live_stream_shopping', 'user'])); 

        }catch(Exception $e) {

            return redirect()->route('admin.live_stream_shoppings.index')->with('flash_error', $e->getMessage());
        }
    }

    /**
     * @method lss_product_payments()
     *
     * @uses To get list of the live stream shopping product payments
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function lss_product_payments(Request $request)
    {
        try {

          $live_stream_shopping = $user = '';

          $base_query = LssProductPayment::when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->user_id);
            })->when($request->filled('live_stream_shopping_id'), function ($query) use ($request) {
                $query->where('live_stream_shopping_id', $request->live_stream_shopping_id);
            })->when($request->filled('user_product_id'), function ($query) use ($request) {
                $query->where('user_product_id', $request->user_product_id);
            })->when($request->filled('search_key'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                          ->orWhere('payment_id', 'LIKE', '%' . $request->search_key . '%')->orWhereHas('user', function ($query) use ($request) {
                              $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                        })->orWhereHas('lssStreamShopping', function ($query) use ($request) {
                              $query->where('title', 'LIKE', '%' . $request->search_key . '%');
                        });
                  });
            });

            if($request->live_stream_shopping_id){

                $live_stream_shopping = LiveStreamShopping::find($request->live_stream_shopping_id);

            }
            if($request->user_id){

                $user = User::find($request->user_id);

            }

           $lss_product_payments = $base_query->paginate($this->take);

           return view('admin.live_stream_shoppings.product_payments.index')
                        ->with('page', 'payments')
                        ->with('sub_page', 'lss-product-payments')
                        ->with(compact(['lss_product_payments', 'live_stream_shopping', 'user'])); 

        }catch(Exception $e) {

            return redirect()->route('admin.live_stream_shoppings.index')->with('flash_error', $e->getMessage());
        }
    }

     /**
     * @method lss_products()
     *
     * Display the specified resource.
     *
     * Created RA Shakthi
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lss_products(Request $request) {
       
        try {

            $live_stream_shopping = $user = '';

           $user_product_ids = LssProduct::where('live_stream_shopping_id', $request->live_stream_shopping_id)->pluck('user_product_id')->toArray();

            $base_query = UserProduct::whereIn('id', $user_product_ids)
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->filled('user_id'), function ($query) use ($request) {
                    $query->where('user_id', $request->user_id);
                })
                ->when($request->filled('search_key'), function ($query) use ($request) {
                    $search_key = $request->search_key;
                    $query->where(function ($query) use ($search_key) {
                        $query->where('unique_id', 'LIKE', '%' . $search_key . '%')
                              ->orWhere('name', 'LIKE', '%' . $search_key . '%');
                    });
                });

            if($request->live_stream_shopping_id){

                $live_stream_shopping = LiveStreamShopping::find($request->live_stream_shopping_id);

            }

           $lss_products = $base_query->paginate($this->take);

           return view('admin.live_stream_shoppings.products_index')
                        ->with('page', 'live_stream_shoppings')
                        ->with('sub_page', 'view_live_stream_history')
                        ->with(compact(['lss_products', 'live_stream_shopping', 'user']));

        }catch(Exception $e) {

            return redirect()->route('admin.live_stream_shoppings.index')->with('flash_error', $e->getMessage());
        }
    
    }
}