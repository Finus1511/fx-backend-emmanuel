<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor,Log;

use App\Jobs\SendEmailJob;

use App\Jobs\PublishPostJob;

use Carbon\Carbon;

use Excel;

use App\Models\{ChatMessage, User, Admin, ChatUser, ChatAsset, CustomTip};

use App\Exports\LiveVideoPaymentExport;

class AdminLiveVideoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

     /**
     * @method live_videos_index()
     *
     * @uses Display the Live Videos
     *
     * @created Ganesh
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function live_videos_index(Request $request) {

        $base_query = \App\Models\LiveVideo::orderBy('created_at','DESC');

        if($request->payment_status !='') {

            $base_query->where('payment_status',$request->payment_status);
        }

        if($request->video_type) {

            $base_query->where('type',$request->video_type);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $live_video_ids = \App\Models\LiveVideo::whereHas('user', function($q) use ($search_key) {

                return $q->Where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('live_videos.title','LIKE','%'.$search_key.'%')->pluck('id');

            $base_query = $base_query->whereIn('id',$live_video_ids);

        }

        $live_videos = $base_query->whereHas('user')->paginate(10);

        $live_videos->title = tr('live_videos');

        return view('admin.live_videos.index')
                ->with('page', 'live-videos')
                ->with('sub_page', 'live-videos-history')
                ->with('is_streaming', NO)
                ->with('live_videos', $live_videos);
    
    }


    /**
     * @method videos_index()
     *
     * @uses To list out LiveVideos
     *
     * @created Anjana H
     *
     * @updated Anjana H
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function live_videos_onlive(Request $request) {
        
        $base_query = \App\Models\LiveVideo::where('live_videos.status',VIDEO_STREAMING_ONGOING)->where('live_videos.is_streaming', IS_STREAMING_YES)
            ->orderBy('live_videos.created_at', 'desc');

        if($request->payment_status !='') {

            $base_query->where('live_videos.payment_status',$request->payment_status);
        }

        if($request->video_type) {

            $base_query->where('live_videos.type',$request->video_type);
        }

        if($request->search_key) {

            $search_key = $request->search_key;

            $live_video_ids = \App\Models\LiveVideo::whereHas('user', function($q) use ($search_key) {

                return $q->Where('users.name','LIKE','%'.$search_key.'%');

            })->orWhere('live_videos.title','LIKE','%'.$search_key.'%')->pluck('id');

            $base_query = $base_query->whereIn('id',$live_video_ids);

        }
        
        $live_videos = $base_query->paginate(10);
        
        $live_videos->title = tr('live_videos_history');

        return view('admin.live_videos.index')
                ->with('page', 'live-videos')
                ->with('sub_page', 'live-videos-live')
                ->with('is_streaming', YES)
                ->with('live_videos', $live_videos);
    }

    /**
     * @method live_videos_delete()
     *
     * To delete a live streaming video which is stopped by the user
     *
     * @created Ganesh
     *
     * @updated by - 
     *
     * @param integer $request - Video id
     *
     * @return repsonse of success/failure message
     */
    public function live_videos_delete(Request $request) {

        try {

            $live_video = \App\Models\LiveVideo::find($request->live_video_id);

            if(!$live_video){

                throw new Exception(tr('live_video_not_found'));

            }
            
            if($live_video->status== VIDEO_STREAMING_ONGOING){

                throw new Exception(tr('broadcast_video_delete_failure'));
            }

            DB::beginTransaction();

            if ($live_video) {                

                $live_video->delete();

                DB::commit();

                return back()->with('flash_success', tr('live_video_delete_success'));

            } 

           throw new Exception(tr('live_video_not_found'));
                

        } catch(Exception $e) {

            DB::rollback();

            return back()->with('flash_error', $e->getMessage());

        }

    } 


     /**
     * @method live_videos_view()
     *
     * @uses displays the specified live video details based on live video id
     *
     * @created Ganesh 
     *
     * @updated 
     *
     * @param object $request - post Id
     * 
     * @return View page
     *
     */
    public function live_videos_view(Request $request) {

        try {
            
            $live_video = \App\Models\LiveVideo::find($request->live_video_id);
            
            if(!$live_video) { 

                throw new Exception(tr('live_video_not_found'), 101);                
            }

            if(Setting::get('is_only_wallet_payment')){
                
                $live_video_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                    ->where('token', '>', 0)
                    ->sum('token');

                $user_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                    ->where('token', '>', 0)
                    ->sum('user_token');

                $admin_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                    ->where('token', '>', 0)
                    ->sum('admin_token');

            } else {
            
                $live_video_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                        ->where('amount', '>', 0)
                        ->sum('amount');

                $user_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                        ->where('amount', '>', 0)
                        ->sum('user_amount');

                $admin_amount = \App\Models\LiveVideoPayment::where('live_video_id', $request->live_video_id)
                        ->where('amount', '>', 0)
                        ->sum('admin_amount');

            }

            $live_video->live_video_amount = formatted_amount($live_video_amount ?? 0);

            $live_video->user_amount = formatted_amount($user_amount ?? 0);

            $live_video->admin_amount = formatted_amount($admin_amount ?? 0);

            return view('admin.live_videos.view')
                ->with('page', 'live-videos')
                ->with('sub_page', 'live-videos-history')
                ->with('live_video', $live_video);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method live_video_payments()
     *
     * @uses Display the lists of post payments
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function live_video_payments(Request $request) {

        $base_query = \App\Models\LiveVideoPayment::orderBy('created_at','DESC');

        $title = tr('live_video_payments');

        if($request->live_video_id) {

            $base_query = $base_query->where('live_video_id',$request->live_video_id);

            $live_video = \App\Models\LiveVideo::find($request->live_video_id);

            $title = tr('live_video_payments')." - ".$live_video->title;
        }

    if($request->search_key) {

    $live_video_payment_ids = \App\Models\LiveVideoPayment::when($request->user_id, function($query) use ($request){

                  return $query->where('user_id', $request->user_id);

              })->whereHas('user', function($query) use ($request) {

                   return $query->where('name', "LIKE", "%" . $request->search_key . "%");

              })->orWhereHas('videoDetails', function($query) use ($request) {

                   return $query->where('unique_id', "LIKE", "%" . $request->search_key . "%");

             })->orWhereHas('videoDetails', function($query) use ($request) {

                    return $query->where('title', "LIKE", "%" . $request->search_key . "%");

                })->orWhere('payment_id', "LIKE", "%" . $request->search_key . "%")

                 ->pluck('id');                

    $base_query = $base_query->whereIn('id', $live_video_payment_ids);
}

        $user = \App\Models\User::find($request->user_id) ?? '';

        if($request->user_id) {

            $base_query  = $base_query->where('user_id',$request->user_id)->orWhere('live_video_viewer_id',$request->user_id);
        }

        $live_video_payments = $base_query->whereHas('videoDetails')->has('user')->orderBy('created_at','DESC')->paginate(10);
        
        return view('admin.live_videos.payments')
                ->with('page','payments')
                ->with('sub_page','live-video-payments')
                ->with('user',$user)
                ->with('title', $title)
                ->with('live_video_payments',$live_video_payments);
    }


    /**
     * @method post_payments_view()
     *
     * @uses 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     */

    public function live_video_payments_view(Request $request) {

        try {

            $live_video_payment = \App\Models\LiveVideoPayment::where('id',$request->live_video_payment_id)->first();

            if(!$live_video_payment) {

                throw new Exception(tr('post_payment_not_found'), 1);
                
            }
           
            return view('admin.live_videos.payments_view')
                    ->with('page','payments')
                    ->with('sub_page','live-video-payments')
                    ->with('live_video_payment',$live_video_payment);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    public function live_video_payment_excel(Request $request) {

        try{
            $file_format = $request->file_format ?? '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new LiveVideoPaymentExport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.live_videos.payments')->with('flash_error' , $e->getMessage());

        }

    }

     /**
     * @method admin_chat_messages()
     * 
     * @uses - To get the media assets.
     *
     * @created RA Shakthi
     *
     * @updated 
     * 
     * @param 
     *
     * @return return response.
     *
     */
    public function admin_chat_messages(Request $request) {
        try {

            $admin = Auth::guard('admin')->user();

            $user_ids = User::query();

            if ($request->sort_by == "all-users") {

                $user_ids->where('status', APPROVED);

            } elseif ($request->sort_by == "content-creators") {

                $user_ids->where('is_content_creator', CONTENT_CREATOR);

            } else {

                $user_ids->where('is_content_creator', DEFAULT_USER);
            }

            $user_ids = $user_ids->pluck('id')->toArray();

            $admin_chat_messages = ChatMessage::whereIn('to_user_id', $user_ids)->where('admin_id', $admin->id)->orderByDesc('created_at')->get();

            $admin_chat_assets = ChatAsset::whereIn('to_user_id', $user_ids)->where('admin_id', $admin->id)->orderByDesc('created_at')->get();

            return view('admin.admin_chat_messages.index', [
                'page' => 'admin-chats',
                'admin_chat_messages' => $admin_chat_messages,
                'admin_chat_assets' => $admin_chat_assets,
                'admin' => $admin
            ]);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

      /**
     * @method send_bulk_message()
     * 
     * @uses - To send the bulk message to content creators,users.
     *
     * @created RA Shakthi
     *
     * @updated 
     * 
     * @param 
     *
     * @return return response.
     *
     */
    public function send_bulk_message(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'message' => 'required_without:file',
                'file' => 'nullable|mimes:jpeg,jpg,png,gif,svg,mp4,mkv',
            ];

            Helper::custom_validator($request->all(), $rules);

            Helper::custom_validator($request->all(),$rules);

            $admin = Auth::guard('admin')->user();

            $user_type = $request->user_type == CONTENT_CREATOR ? CONTENT_CREATOR : DEFAULT_USER;

            $user_ids = User::where('is_content_creator', $user_type)->pluck('id');

            $chat_assets_file_url = $file_type = '';

            if ($request->hasFile('file')) {

                $chat_assets_file_url = Helper::storage_upload_file($request->file('file'), COMMON_FILE_PATH);
            }

           $user_ids->map(function ($user_id) use ($request, $admin, $chat_assets_file_url, $file_type) {

                $chat_message = ChatUser::updateOrCreate([
                    'from_user_id' => $user_id,
                    'admin_id' => $admin->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $chat_message = ChatMessage::create([
                    'to_user_id' => $user_id,
                    'admin_id' => $admin->id,
                    'message' => $request->message,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($chat_assets_file_url) {

                    ChatAsset::create([
                        'to_user_id' => $user_id,
                        'admin_id' => $admin->id,
                        'chat_message_id' => $chat_message->id,
                        'file' => $chat_assets_file_url,
                        'file_type' => $file_type,
                        'is_paid' => PAID,
                    ]);
                }
            });

            DB::commit();

            return redirect()->route('admin.admin_chat_messages.index')->with('flash_success', tr('chat_message_updated_success'));
            
        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }

    /**
     * @method custom_tips_index()
     *
     * @uses To list out custom_tips details 
     *
     * @created
     *
     * @updated
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function custom_tips_index(Request $request) {

        $base_query = CustomTip::orderBy('updated_at','desc');

        $search_key = $request->search_key;

        if($request->search_key) {

            $base_query = $base_query->where('name','LIKE','%'.$search_key.'%');
        }

        $custom_tips = $base_query->paginate($this->take);

        return view('admin.custom_tips.index')
                ->with('page', 'custom_tips')
                ->with('sub_page' , 'custom_tips-view')
                ->with('custom_tips' , $custom_tips);
    }

    /**
     * @method custom_tips_create()
     *
     * @uses To create category details
     *
     * @created
     *
     * @updated
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function custom_tips_create() {

        $custom_tip = new CustomTip;

        return view('admin.custom_tips.create')
                ->with('page', 'custom_tips')
                ->with('sub_page', 'custom_tips-create')
                ->with('custom_tip', $custom_tip);           
    }

    /**
     * @method custom_tips_edit()
     *
     * @uses To display and update category details based on the sub category id
     *
     * @created
     *
     * @updated
     *
     * @param object $request - CustomTip Id 
     * 
     * @return redirect view page 
     *
     */
    public function custom_tips_edit(Request $request) {

        try {

            $custom_tip = CustomTip::find($request->custom_tip_id);

            if(!$custom_tip) { 

                throw new Exception(tr('custom_tip_not_found'), 101);
            }

            return view('admin.custom_tips.edit')
                ->with('page' , 'custom_tips')
                ->with('sub_page', 'custom_tips-view')
                ->with('custom_tip', $custom_tip); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.custom_tips.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method custom_tips_save()
     *
     * @uses To save the sub category details of new/existing sub category object based on details
     *
     * @created Akshata
     *
     * @updated Jeevan
     *
     * @param object request - CustomTip Form Data
     *
     * @return success message
     *
     */
    public function custom_tips_save(Request $request) {
        
        try {
            
            DB::begintransaction();

            $rules = [
                'title' => 'required|max:191',
                'picture' => 'mimes:jpg,png,jpeg',
                'discription' => 'max:199',
                'amount' => 'required|numeric'
            ];

            Helper::custom_validator($request->all(),$rules);

            $custom_tip = CustomTip::find($request->custom_tip_id) ?? new CustomTip;

            if($custom_tip->id) {

                $message = tr('custom_tip_updated_success'); 

            } else {

                $message = tr('custom_tip_created_success');

            }

            $custom_tip->title = $request->title ?: $custom_tip->title;

            $custom_tip->description = $request->description ?: '';

            $custom_tip->amount = $request->amount ?: $custom_tip->amount;

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->custom_tip_id) {

                    Helper::storage_delete_file($custom_tip->picture, CATEGORY_FILE_PATH); 
                    // Delete the old pic
                }

                $custom_tip->picture = Helper::storage_upload_file($request->file('picture'), CATEGORY_FILE_PATH);
            }

            if($custom_tip->save()) {

                DB::commit(); 

                return redirect(route('admin.custom_tips.view', ['custom_tip_id' => $custom_tip->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('custom_tip_save_failed'));
            
        } catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method custom_tips_view()
     *
     * @uses displays the specified category details based on category id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - category Id
     * 
     * @return View page
     *
     */
    public function custom_tips_view(Request $request) {
       
        try {
      
            $custom_tip = CustomTip::find($request->custom_tip_id);
            
            if(!$custom_tip) { 

                throw new Exception(tr('custom_tip_not_found'), 101);                
            }

            return view('admin.custom_tips.view')
                    ->with('page', 'custom_tips') 
                    ->with('sub_page', 'custom_tips-view')
                    ->with('custom_tip', $custom_tip);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method custom_tips_delete()
     *
     * @uses delete the sub category details based on category id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - CustomTip Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function custom_tips_delete(Request $request) {

        try {

            DB::begintransaction();

            $custom_tip = CustomTip::find($request->custom_tip_id);
            
            if(!$custom_tip) {

                throw new Exception(tr('custom_tip_not_found'), 101);                
            }

            if($custom_tip->delete()) {

                DB::commit();

                return redirect()->route('admin.custom_tips.index')->with('flash_success',tr('custom_tip_deleted_success'));   

            } 
            
            throw new Exception(tr('custom_tip_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method custom_tips_status
     *
     * @uses To update sub category status as DECLINED/APPROVED based on sub category id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - CustomTip Id
     * 
     * @return response success/failure message
     *
     **/
    public function custom_tips_status(Request $request) {

        try {

            DB::beginTransaction();

            $custom_tip = CustomTip::find($request->custom_tip_id);

            if(!$custom_tip) {

                throw new Exception(tr('custom_tip_not_found'), 101);
                
            }

            $custom_tip->status = $custom_tip->status ? DECLINED : APPROVED ;

            if($custom_tip->save()) {

                DB::commit();

                $message = $custom_tip->status ? tr('custom_tip_approve_success') : tr('custom_tip_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('custom_tip_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.custom_tips.index')->with('flash_error', $e->getMessage());

        }

    }
}
