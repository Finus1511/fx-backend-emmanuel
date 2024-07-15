<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator, Log, Hash, Setting, DB, Exception, File;

use Carbon\Carbon;

use App\Models\{User, FileArchive};

class FileArchiveController extends Controller
{
    protected $loginUser;

    protected $skip, $take, $timezone, $currency, $device_type;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->currency = Setting::get('currency', '$');

        $this->loginUser = User::CommonResponse()->find($request->id);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

        $request->request->add(['timezone' => $this->timezone]);

        $this->device_type = $this->loginUser->device_type ?? DEVICE_WEB;

    }

    /** 
     * @method file_archives_index()
     *
     * @uses to get the files uploaded by the user (all and based on the origin)
     * 
     * @created Vithya R
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function file_archives_index(Request $request) {

       try {

            $base_query = $total_query = FileArchive::where('user_id',$request->id)->orderBy('file_archives.created_at', 'desc');

            if($request->has('file_type')) {

                $base_query = $total_query = $base_query->where('file_type', $request->file_type);
            }

            if($request->has('sort_by')) {

                $sort_by = $request->sort_by == "old" ? 'asc' : 'desc';

                $base_query = $total_query = $base_query->orderBy('file_archives.created_at', $sort_by);

            }

            if($request->has('from') && $request->has('to')) {

                $from_date = date('Y-m-d H:i:s', strtotime($request->from));

                $to_date = date('Y-m-d H:i:s', strtotime($request->to));

                $base_query = $total_query = $base_query->whereBetween(DB::raw('DATE(file_archives.created_at)'), [$from_date, $to_date]);
            }

            $holds['all'] = FileArchive::where('user_id',$request->id)->count();

            $holds['image'] = FileArchive::where('user_id',$request->id)->where('file_type', FILE_TYPE_IMAGE)->count();

            $holds['video'] = FileArchive::where('user_id',$request->id)->where('file_type', FILE_TYPE_VIDEO)->count();

            $holds['audio'] = FileArchive::where('user_id',$request->id)->where('file_type', FILE_TYPE_AUDIO)->count();

            $holds['document'] = FileArchive::where('user_id',$request->id)->where('file_type', FILE_TYPE_DOCUMENT)->count();


            $file_archives = $base_query->CommonResponse()->skip($this->skip)->take($this->take)->get();

            foreach($file_archives as $file_archive) {
                $file_archive->created = common_date($file_archive->created_at, $this->timezone, 'M d');
            }


            $data['total'] = $total_query->count() ?? 0;

            $data['file_archives'] = $file_archives ?? emptyObject();

            $data['holds'] = $holds;

            $data['inputs'] = $request->all();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method file_archives_view()
     *
     * @uses to get the single file information
     * 
     * @created Vithya R
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function file_archives_view(Request $request) {

       try {

            $file_archive = FileArchive::where('user_id',$request->id)->where('file_archives.id', $request->file_archive_id)->first();

            $data['file_archive'] = $file_archive ?? emptyObject();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


}
