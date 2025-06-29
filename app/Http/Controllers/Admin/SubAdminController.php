<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

use Excel;

use App\Models\{Admin, RoleAccess};

use Carbon\Carbon;

use Illuminate\Support\Arr;

class SubAdminController extends Controller
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
     * @method sub_admins_index()
     *
     * @uses To list out sub_admins details 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function sub_admin_index(Request $request) {

        $base_query = Admin::where('role', SUB_ADMIN)->orderBy('created_at','desc');   

        if($request->search_key) {

            $search_user_ids = Admin::where('name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('email','LIKE','%'.$request->search_key.'%')
                    ->pluck('id');

            $base_query = $base_query->whereIn('id',$search_user_ids);
        }

        if($request->status) {

            $base_query = $base_query->where('status',$request->status);

        }

        $sub_admins = $base_query->paginate($this->take);

        $page = 'sub_admins'; $sub_page = 'sub_admins-view';

        $title = tr('view_sub_admins');

        return view('admin.sub_admins.index')
                    ->with('page', $page)
                    ->with('sub_page', $sub_page)
                    ->with('sub_admins', $sub_admins);
    
    }

    /**
     * @method sub_admins_create()
     *
     * @uses To create sub_admin details
     *
     * @created  Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function sub_admin_create() {

        $sub_admin = new Admin;

        return view('admin.sub_admins.create')
                    ->with('page', 'sub_admins')
                    ->with('sub_page','sub_admins-create')
                    ->with('sub_admin', $sub_admin);        
   
    }

    /**
     * @method sub_admins_edit()
     *
     * @uses To display and update sub_admin details based on the sub_admin id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - sub_admin Id
     * 
     * @return redirect view page 
     *
     */
    public function sub_admin_edit(Request $request) {

        try {

            $sub_admin = Admin::find($request->sub_admin_id);

            if(!$sub_admin) { 

                throw new Exception(tr('sub_admin_not_found'), 101);
            }

            return view('admin.sub_admins.edit')
                    ->with('page', 'sub_admins')
                    ->with('sub_page', 'sub_admins-view')
                    ->with('sub_admin', $sub_admin); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.sub_admin.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method sub_admins_save()
     *
     * @uses To save the sub_admins details of new/existing sub_admin object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - sub_admin Form Data
     *
     * @return success message
     *
     */
    public function sub_admin_save(Request $request) {

        try {

            DB::begintransaction();

            $rules = [   
                'name' => 'required|max:191',
                'email' => 'email|unique:admins,email,'.$request->id.'|max:255',
                'email' => $request->sub_admin_id ? 'required|email|max:191|unique:admins,email,'.$request->sub_admin_id.',id' : 'required|email|max:191|unique:admins,email,NULL,id',
                'password' => $request->sub_admin_id ? "" : 'required|min:6|confirmed',
                'picture' => 'mimes:jpg,png,jpeg',
                'sub_admin_id' => 'exists:admins,id|nullable',
            ];

            $custom_errors = [ 'regex' => api_error(265) ];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $sub_admin = $request->sub_admin_id ? Admin::find($request->sub_admin_id) : new Admin;

            $is_new_sub_admin = NO;

            if($sub_admin->id) {

                $message = tr('sub_admin_updated_success'); 

            } else {

                $is_new_sub_admin = YES;

                $sub_admin->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('sub_admin_created_success');

            }

            // dd($request->all(), $request->name);

            $sub_admin->name = $request->name ?? "";
            $sub_admin->email = $request->email ?? "";
            $sub_admin->about = $request->about ?? "";
            $sub_admin->role = SUB_ADMIN;
            
            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->sub_admin_id) {

                    Helper::storage_delete_file($sub_admin->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $sub_admin->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
          
            }

            if($sub_admin->save()) {

                DB::commit(); 

                return redirect(route('admin.sub_admin.view', ['sub_admin_id' => $sub_admin->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('sub_admin_save_failed'));
            
        } 
        catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method sub_admins_view()
     *
     * @uses Display the specified sub_admin details based on sub_admin_id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - sub_admin Id
     * 
     * @return View page
     *
     */
    public function sub_admin_view(Request $request) {
       
        try {
      
            $sub_admin = Admin::find($request->sub_admin_id);

            if(!$sub_admin) { 

                throw new Exception(tr('sub_admin_not_found'), 101);                
            }

            return view('admin.sub_admins.view')
                        ->with('page', 'sub_admins') 
                        ->with('sub_page','sub_admins-view') 
                        ->with('sub_admin' , $sub_admin);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method sub_admins_delete()
     *
     * @uses delete the sub_admin details based on sub_admin id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - sub_admin Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function sub_admin_delete(Request $request) {

        try {

            DB::begintransaction();

            $sub_admin = Admin::find($request->sub_admin_id);
            
            if(!$sub_admin) {

                throw new Exception(tr('sub_admin_not_found'), 101);                
            }

            if($sub_admin->delete()) {

                DB::commit();

                return redirect()->route('admin.sub_admin.index',['page'=>$request->page])->with('flash_success',tr('sub_admin_deleted_success'));   

            } 
            
            throw new Exception(tr('sub_admin_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method sub_admins_status
     *
     * @uses To update sub_admin status as DECLINED/APPROVED based on sub_admins id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - sub_admin Id
     * 
     * @return response success/failure message
     *
     **/
    public function sub_admin_status(Request $request) {

        try {

            DB::beginTransaction();

            $sub_admin = Admin::find($request->sub_admin_id);

            if(!$sub_admin) {

                throw new Exception(tr('sub_admin_not_found'), 101);
                
            }

            $sub_admin->status = $sub_admin->status ? DECLINED : APPROVED ;

            if($sub_admin->save()) {

                DB::commit();

                $message = $sub_admin->status ? tr('sub_admin_approve_success') : tr('sub_admin_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('sub_admin_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.sub_admin.index')->with('flash_error', $e->getMessage());

        }

    }

    public function sub_admin_role_access(Request $request) {

        try {

            $role_access = RoleAccess::where('admin_id', $request->sub_admin_id)->first();

            $permissions = $role_access && $role_access->roles ? explode(',', $role_access->roles) : [];

            // dd($role_access->toArray(), explode(',', $role_access->roles), in_array('personalize_requests', $permissions ?? []) ? 'checked' : '');

            return view('admin.sub_admins.role_access')
                        ->with('page', 'sub_admins')
                        ->with('sub_page','sub_admins-view')
                        ->with('sub_admin_id',$request->sub_admin_id)
                        ->with('permissions' , $permissions);

        } catch(Exception $e) {

            return redirect()->route('admin.sub_admin.index')->with('flash_error', $e->getMessage());

        }

    }

    public function sub_admin_update_role_access(Request $request) {

        try {

            $data = $request->all();

            $data = Arr::except($data, ['_token', 'sub_admin_id']);

            $keys = array_keys($data);

            $role_access_string = implode(',', $keys);

            DB::beginTransaction();

            $role_access_update = RoleAccess::updateOrCreate(
                ['admin_id' => $request->sub_admin_id],  // Search by admin_id
                ['roles' => $role_access_string] // Update/create with these values
            );

            if($role_access_update) {

                DB::commit();

                return redirect()->back()->with('flash_success', tr('role_access_update_success'));
            }
            
            throw new Exception(tr('sub_admin_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.sub_admin.index')->with('flash_error', $e->getMessage());

        }

    }

}
