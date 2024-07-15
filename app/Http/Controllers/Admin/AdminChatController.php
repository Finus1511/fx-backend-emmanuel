<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CommonRepository as CommonRepo;
use App\Models\{ChatMessage, ChatAsset, User};
use App\Helpers\Helper;
use DB, Exception, Auth;
use Carbon\Carbon;

class AdminChatController extends Controller
{

     /**
     * @method admin_chat_messages()
     *
     * @uses - To get the media assets.
     *
     * @created Sulabh Nepal
     *
     * @param
     *
     * @return return response.
     *
     */
    public function admin_chat_messages(Request $request) {
        try {

            if(!$request->sort_by){

               return redirect()->route('admin.admin_chat_messages.index', ['sort_by' => "all-users"]);
            }

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

            $admin_chat_messages = ChatMessage::leftJoin('chat_assets', 'chat_messages.id', '=', 'chat_assets.chat_message_id')
            ->whereIn('chat_messages.to_user_id', $user_ids)
            ->where('chat_messages.admin_id', $admin->id)
            ->orderByDesc('chat_messages.created_at')
            ->groupBy('chat_messages.created_at')
            ->select('chat_messages.message','chat_messages.created_at', 'chat_assets.*', 'chat_messages.created_at as message_created_at')
            ->get();

            return view('admin.admin_chat_messages.index', [
                'page' => 'admin-chats',
                'admin_chat_messages' => $admin_chat_messages,
                'admin' => $admin
            ]);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }


    /**
     * @method send_bulk_message()
     *
     * @uses - To send bulk message to users.
     *
     * @created Sulabh Nepal
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
                'admin_id' => 'required|exists:admins,id',
                'user_type' => 'required|in:'.implode(',', [ALL_USERS_NUMBER, DEFAULT_USER, CONTENT_CREATOR])
            ];

            Helper::custom_validator($request->all(), $rules);

            DB::beginTransaction();

            $chat_assets_file_url = $file_type = '';

            if($request->user_type == ALL_USERS_NUMBER) {

                $recieving_users = \App\Models\User::pluck('id')->toArray(); //all users

            } else if($request->user_type == CONTENT_CREATOR) {

                $recieving_users = \App\Models\User::where('is_content_creator', CONTENT_CREATOR)->pluck('id')->toArray(); // to content creators only

            } else{

                $recieving_users = \App\Models\User::where('is_content_creator', DEFAULT_USER)->pluck('id')->toArray(); // to default users only

            }

            $timestamp = Carbon::now();

            if($request->hasFile('file')) {

                $file = $request->file('file');

                $file_type = $file->getClientOriginalExtension();

                $filename = rand(1, 1000000) . '-chat_asset_file.'.$file_type;

                $chat_assets_file_url = Helper::storage_upload_file($file, CHAT_ASSETS_PATH, $filename);

                foreach($recieving_users as $to_user_id){

                    CommonRepo::chat_user_update(0, $to_user_id, $request->admin_id);

                    $chat_message = ChatMessage::create([
                        'admin_id' => $request->admin_id,
                        'from_user_id' => 0,
                        'to_user_id' => $to_user_id,
                        'message' => $request->message ?? '',
                        'is_file_uploaded' => YES,
                        'is_broadcast' => YES,
                        'broadcasted_to' => $request->user_type,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);

                    $data = ChatAsset::create([
                        'from_user_id' => $request->admin_id,
                        'to_user_id' => $to_user_id,
                        'chat_message_id' => $chat_message->id,
                        'file' => $chat_assets_file_url,
                        'file_type' => $file_type,
                        'is_paid' => PAID,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);

                }
            }else{

                foreach($recieving_users as $to_user_id){

                    CommonRepo::chat_user_update(0, $to_user_id, $request->admin_id);

                    CommonRepo::chat_user_update($to_user_id, 0, $request->admin_id); // to display the message in the user's chat list

                    $data = ChatMessage::create([
                        'admin_id' => $request->admin_id,
                        'from_user_id' => 0,
                        'to_user_id' => $to_user_id,
                        'message' => $request->message ?? '',
                        'is_file_uploaded' => NO,
                        'is_broadcast' => YES,
                        'broadcasted_to' => $request->user_type,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);
                }
            }

            DB::commit();

            return redirect()->back()->with('flash_success', 'Message sent successfully.')->with('receivers', $recieving_users);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    }
}
