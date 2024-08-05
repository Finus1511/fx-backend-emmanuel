<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Collection, CollectionFile};
use Setting, DB, Exception;
class CollectionController extends Controller
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
     * @method collections()
     *
     * @uses To get list of the collections created by the user
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function collections(Request $request)
    {
        try {
           $user = '';
           $base_query = Collection::when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })->when($request->filled('search_key'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('unique_id', 'LIKE', '%' . $request->search_key . '%')
                    ->orWhere('name', 'LIKE', '%' . $request->search_key . '%')
                    ->orWhereHas('user', function ($query) use ($request) {
                        $query->where('name', 'LIKE', '%' . $request->search_key . '%');
                    });
                });
            });
           if($request->user_id){
             $user = User::find($request->user_id);
           }
           $collections = $base_query->paginate($this->take);
           return view('admin.collections.index')
                        ->with('page', 'collections')
                        ->with(compact(['collections', 'user'])); 
        }catch(Exception $e) {
            return redirect()->route('admin.collections.index')->with('flash_error', $e->getMessage());
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
    public function collections_view(Request $request) {
       
        try {
            $collection = Collection::with('collectionFiles')->find($request->collection_id);
            throw_if(!$collection, new Exception(tr('collection_not_found')));
            return view('admin.collections.view')
            ->with('page', 'collections') 
            ->with(compact(['collection']));
            
        } catch (Exception $e) {
            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }
       /**
     * @method collection_file_delete()
     *
     * @uses delete the collection file details based on collection file id
     *
     * @created RA Shakthi 
     *
     * @updated  
     *
     * @param object $request - Collection File Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function collection_file_delete(Request $request) {
        try {
            DB::begintransaction();
            $collection_file = CollectionFile::find($request->collection_file_id);
            throw_if(!$collection_file, new Exception(tr('collection_file_not_found'), 101));
            if($collection_file->delete()) {
                DB::commit();
                return back()->with('flash_success',tr('collection_file_deleted_success'));   
            } 
            
            throw new Exception(tr('collection_file_delete_failed'));
            
        } catch(Exception $e){
            DB::rollback();
            return redirect()->back()->with('flash_error', $e->getMessage());
        }       
         
    }
}