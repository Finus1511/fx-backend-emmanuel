<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{PersonalizedRequest, PersonalizedProduct};
use Setting, Exception, DB;

class AdminPersonalizedRequestController extends Controller
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
     * @method index()
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
    public function index(Request $request)
    {
        try {
            $base_query = PersonalizedRequest::when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })->when($request->filled('type'), function ($query) use ($request) {
                    $query->where('type', $request->type);
                })->when($request->filled('receiver_id'), function ($query) use ($request) {
                    $query->where('receiver_id', $request->receiver_id);
                })->when($request->filled('sender_id'), function ($query) use ($request) {
                    $query->where('sender_id', $request->sender_id);
                })->when($request->filled('search_key'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                          ->orWhereHas('receiver', function ($query) use ($request) {
                                $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                            });
                    });
                });

           $personalized_requests = $base_query->paginate($this->take);

           return view('admin.personalized_requests.index')
                        ->with('page', 'personalized_requests')
                        ->with('sub_page', 'view_personalized_requests')
                        ->with('personalized_requests',$personalized_requests); 

        } catch(Exception $e) {

            return redirect()->route('admin.vod_videos.index')->with('flash_error', $e->getMessage());
        }
    }

     /**
     * @method view()
     *
     * @uses display the specified personalized request details based on personalized request id
     *
     * @created RA Shakthi 
     *
     * @updated 
     *
     * @param object $request - Personalized Request Id
     * 
     * @return View page
     *
     */
    public function view(Request $request) {
       
        try {
      
            $personalized_request = PersonalizedRequest::find($request->personalized_request_id);

            throw_if(!$personalized_request, new Exception(tr('personalized_request_not_found')));

            return view('admin.personalized_requests.view')
                        ->with('page', 'personalized_requests') 
                        ->with('sub_page', 'view_personalized_requests') 
                        ->with('personalized_request', $personalized_request);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method products()
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
    public function products(Request $request)
    {
        try {

            $base_query = PersonalizedProduct::where('personalized_request_id', $request->personalized_request_id)->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);

                })->when($request->filled('search_key'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                        ->orWhere('name', 'LIKE', '%' . $request->search_key . '%');
                    });
                });

           $personalized_products = $base_query->paginate($this->take);

           return view('admin.personalized_products.index')
                        ->with('page', 'personalized_requests')
                        ->with('sub_page', 'view_personalized_requests')
                        ->with('personalized_products', $personalized_products); 

        } catch(Exception $e) {

            return redirect()->route('admin.vod_videos.index')->with('flash_error', $e->getMessage());
        }
    }

    /**
     * @method products_view()
     *
     * @uses display the specified personalized product details based on personalized product id
     *
     * @created RA Shakthi 
     *
     * @updated 
     *
     * @param object $request - Personalized Product Id
     * 
     * @return View page
     *
     */
    public function products_view(Request $request) {
       
        try {
      
            $personalized_product = PersonalizedProduct::with('personalizedProductFiles')->find($request->personalized_product_id);

            throw_if(!$personalized_product, new Exception(tr('personalized_product_not_found')));

            return view('admin.personalized_products.view')
                        ->with('page', 'personalized_requests') 
                        ->with('sub_page', 'view_personalized_requests') 
                        ->with('personalized_product', $personalized_product);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }
}
