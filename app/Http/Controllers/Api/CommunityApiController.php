<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB, Setting;

use App\Helpers\Helper;

use App\Models\{User, Community, CommunityUser, CommunityMessage, CommunityAsset};

use App\Repositories\CommonRepository as CommonRepo;

class CommunityApiController extends Controller
{
    
    protected $loginUser;

    protected $skip, $take;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /** 
     * @method user_communities()
     *
     * @uses user_communities List
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function user_communities(Request $request) {

        try {

            $community_user = CommunityUser::where('user_id', $request->id)->pluck('community_id');

            $base_query = Community::whereIn('id', $community_user)->orderBy('updated_at', 'desc');

            if ($request->search_key) {
                
                $base_query = $base_query->where('name','LIKE','%'.$request->search_key.'%');
            }

            $data['total'] = $base_query->count() ?: 0;

            $communities = $base_query->skip($this->skip)->take($this->take)
                    ->get();

            foreach ($communities as $key => $community) {

                $community_messages = CommunityMessage::where('community_messages.community_id', $community->id)->latest()->first();

                $community->message = $community_messages->message ?? '....';

                $community->file_type = $community_messages->file_type ?? FILE_TYPE_TEXT;  

                if($community_messages) {

                    if ($community_messages->created_at->isToday()) {
                    
                        $community->time_formatted = common_date($community_messages->created_at, $this->timezone, 'h:i A');

                    } else {
                        $community->time_formatted = common_date($community_messages->created_at, $this->timezone, 'd M Y');  
                    }
                    
                } else {
                    
                    $community->time_formatted = common_date($community->created_at, $this->timezone, 'd M Y');  

                }   
                
            }

            $data['communities'] = $communities ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method community_messages_index()
     *
     * @uses community_messages_index List
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return JSON response
     *
     */
    public function community_messages_index(Request $request) {

        try {

            $rules = [
                'community_id' => 'required|exists:communities,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            $base_query = CommunityMessage::where('community_id', $request->community_id)->latest();

            $data['total'] = $base_query->count() ?: 0;

            $chat_messages = $base_query->skip($this->skip)->take($this->take)->orderBy('community_messages.updated_at', 'asc')->get();

            $chat_messages = CommonRepo::community_messages_response($chat_messages, $request);

            $data['messages'] = $chat_messages ?? [];

            $data['user'] = User::find($request->from_user_id);

            $data['is_block_user'] = Helper::is_block_user($request->id, $request->from_user_id);

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_community_assets()
     *
     * @uses To display all the chat media files
     *
     * @created Bhawya N
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function user_community_assets(Request $request) {

        try {

            $rules = [
                'community_id' => 'required|exists:communities,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            $base_query = $total_query = CommunityAsset::where('community_id',$request->community_id);

            if($request->file_type != POSTS_ALL) {

                $type = $request->file_type;

                if($type)

                    $base_query = $base_query->where('file_type', $type);
            }

            $data['total'] = $total_query->count() ?? 0;

            $base_query = $base_query->latest();

            $chat_assets = $base_query->skip($this->skip)->take($this->take)->get();

            $chat_assets = CommonRepo::community_assets_list_response($chat_assets, $request);

            $data['chat_assets'] = $chat_assets ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method community_asset_files_upload()
     *
     * @uses - To save the chat assets.
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param
     *
     * @return No return response.
     *
     */

    public function community_asset_files_upload(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'community_id' => 'required|exists:communities,id',
                'message' => 'nullable',
                'file' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);

            $data = $file_data = [];

            $chat_asset_file_id = $chat_asset_blur_file = '';

            $file_url = $file_name = [];

            if ($request->has('file')) {

                $files = $request->file;

                if(!is_array($files)) {

                    $file = $files; $chat_asset = emptyObject();

                    $filename = rand(1,1000000).'-chat_asset-'.($request->file_type ?? 'image').'.'.$file->getClientOriginalExtension();

                    $chat_assets_file_url = Helper::storage_upload_file($request->file, CHAT_ASSETS_PATH, $filename, NO);

                    if($chat_assets_file_url) {

                        $chat_asset = CommunityAsset::create([
                            'from_user_id' => $request->id,
                            'community_id' => $request->community_id,
                            'community_message_id' => 0,
                            'file' => $chat_assets_file_url,
                            'file_type' => $request->file_type ?? FILE_TYPE_IMAGE,
                        ]);

                    }

                    $chat_asset_file_id = $chat_asset->id ?? "";

                    $file_url[] = $chat_assets_file_url;

                    $file_name[] = basename($chat_assets_file_url);

                    $chat_asset->asset_file = $chat_asset->file ?? "";

                    $data['chat_asset'] = $chat_asset;

                } else {

                    foreach($files as $file){

                        $filename = rand(1,1000000).'-chat_asset-'.($request->file_type ?? 'image').'.'.$file->getClientOriginalExtension();

                        $chat_assets_file_url = Helper::storage_upload_file($file, CHAT_ASSETS_PATH, $filename, NO);

                        if($chat_assets_file_url) {

                            $chat_asset = CommunityAsset::create([
                                'from_user_id' => $request->id,
                                'community_id' => $request->community_id,
                                'community_message_id' => 0,
                                'file' => $chat_assets_file_url,
                                'file_type' => $request->file_type ?? FILE_TYPE_IMAGE,
                            ]);

                            $chat_asset_file_id != "" && $chat_asset_file_id .= ",";

                            $chat_asset_file_id .= $chat_asset->id;

                            $file_url[] = $chat_assets_file_url;

                            $file_name[] = basename($chat_assets_file_url);

                            $chat_asset_blur_file != "" && $chat_asset_blur_file .= ",";

                            $chat_asset_blur_file .= $chat_asset->blur_file;

                            $chat_asset->asset_file = $chat_asset->file;

                            array_push($file_data, $chat_asset);

                        }

                    }

                    $data['chat_asset'] = $file_data;
                }

            }

            DB::commit();

            $data['chat_asset_file_id'] = $chat_asset_file_id;

            $data['file'] = $file_url;

            $data['file_name'] = $file_name;

            $data['blur_file'] = $chat_asset_blur_file;

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method community_message_delete()
     *
     * @uses delete the chat assets
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param object $request
     *
     * @return JSON Response
     */
    public function community_message_delete(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'chat_message_id' => 'required_without:chat_reference_id|exists:community_messages,id',
                'chat_reference_id' => 'exists:community_messages,reference_id'
            ];

            Helper::custom_validator($request->all(),$rules);

            $chat_message = CommunityMessage::where('reference_id', $request->chat_reference_id ?: "")->orWhere('id', $request->chat_message_id ?: "")->first();;

            throw_if($chat_message->from_user_id != $request->id, new Exception(api_error(287), 287));

            $chat_message->delete();

            DB::commit();

            $request->merge([
                'community_id' => $chat_message->community_id,
            ]);

            $data['chat_messages'] = $this->community_messages_index($request)->getData();

            return $this->sendResponse(api_success(3000), 3000, $data);

        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method update_community()
     *
     * @uses To update the user details
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param objecct $request : User details
     *
     * @return json response with user details
     */
    public function update_community(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'community_id' => 'required|exists:communities,id,user_id,'.$request->id,
                'name' => 'nullable|max:255',
                'picture' => 'nullable|mimes:jpeg,jpg,bmp,png',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            
            $community = Community::find($request->community_id);

            $community->name = $request->name ?: $community->name;

            // Upload picture
            if($request->hasFile('picture') != "") {

                Helper::storage_delete_file($community->picture, COMMUNITY_FILE_PATH); // Delete the old pic

                $community->picture = Helper::storage_upload_file($request->file('picture'), COMMUNITY_FILE_PATH);
            
            }

            if($community->save()) {

                DB::commit();

                $community_messages = CommunityMessage::where('community_messages.community_id', $community->id)->latest()->first();

                $community->message = $community_messages->message ?? '....';

                $community->file_type = $community_messages->file_type ?? FILE_TYPE_TEXT;  

                if($community_messages) {

                    if ($community_messages->created_at->isToday()) {
                    
                        $community->time_formatted = common_date($community_messages->created_at, $this->timezone, 'h:i A');

                    } else {
                        $community->time_formatted = common_date($community_messages->created_at, $this->timezone, 'd M Y');  
                    }
                    
                } else {
                    
                    $community->time_formatted = common_date($community->created_at, $this->timezone, 'd M Y');  

                }

                $data['community'] = $community;

                return $this->sendResponse($message = api_success(262), $success_code = 262, $data);

            }

            throw new Exception(api_error(103), 103);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }
}
