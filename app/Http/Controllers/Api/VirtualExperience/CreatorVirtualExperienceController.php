<?php

namespace App\Http\Controllers\Api\VirtualExperience;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use Illuminate\Validation\Rule;

use App\Models\{User, VirtualExperience, VirtualExperienceBooking, VirtualExperienceFile};

use Carbon\Carbon;

use App\Repositories\VirtualExperienceRepository as VirtualExperienceRepo;

use App\Http\Resources\{VirtualExperienceResource, VirtualExperienceBookingsResource};

class CreatorVirtualExperienceController extends Controller
{
    protected $loginUser, $skip, $take;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));

        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

        $request->request->add(['timezone' => $this->timezone]);

    }

    /**
     * @method list()
     *
     * @uses To get list of the VH created by the creator
     *
     * @created Vithya R
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */

    public function list(Request $request) {

        try {

            $base_query = VirtualExperience::where('user_id', $request->id)->when($request->filled('status'), function ($query) use ($request) {
                             $query->where('status', $request->status);
                        })->when($request->filled('search_key'), function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('unique_id', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('price_per', "LIKE", "%" . $request->search_key . "%")
                                ->orWhereHas('user', function($query) use($request) {
                                    $query->where('name', "LIKE", "%{$request->search_key}%");
                                });
                            });
                        });

            $data['total'] = $base_query->count();

            $virtual_experiences = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['virtual_experiences'] = VirtualExperienceResource::collection($virtual_experiences);

            return $this->sendResponse('', '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method view()
     *
     * @uses To VH details created by the creator
     *
     * @created Vithya R
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */

    public function view(Request $request) {

        try {

            $virtual_experience = VirtualExperience::where(['unique_id' => $request->virtual_experience_unique_id])->first();

            throw_if(!$virtual_experience, new Exception(api_error(269), 269));

            $data['virtual_experience'] = new VirtualExperienceResource($virtual_experience);

            return $this->sendResponse('', '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method files_save()
     *
     * @uses store the images
     *
     * @created Vithya R
     *
     * @updated RA Shakthi
     *
     * @param Request $request
     *
     * @return JSON Response
     */
    public function files_save(Request $request) {

        try {
            $rules = [
                'files.*' => 'required|mimes:jpg,png,jpeg',
                'virtual_experience_id' => 'nullable|exists:virtual_experiences,id',
            ];

            $messages = [
                'files.*.mimes' => 'Each file must be a JPG, PNG, or JPEG image.',
            ];

            Helper::custom_validator($request->all(), $rules, $messages);

            $user = User::find($request->id);

            throw_if(!$user, new Exception(api_error(135), 135));

            if($request->has('files')) {

                $virtual_experience_file_ids = $virtual_experience_files = [];

                throw_if(count($request->files) <= 0, new Exception(api_error(278), 278));

                foreach($request->file('files') as $key => $file) {

                    $file_url = Helper::storage_upload_file($file, VIRTUAL_EXPERIENCE_PATH);

                    $file_type = $file->getClientOriginalExtension() ?: '';

                    $virtual_experience_file = new VirtualExperienceFile([
                        'user_id' => $user->id,
                        'file' => $file_url,
                        'file_type' => $file_type,
                    ]);

                    $virtual_experience_file->save();

                    $virtual_experience_files[] = $virtual_experience_file;

                    $virtual_experience_file_ids[] = $virtual_experience_file->id;
                }

                DB::commit();

                $data['virtual_experience_files'] = $virtual_experience_files ?? [];

                $data['virtual_experience_file_ids'] = $virtual_experience_file_ids;

                return $this->sendResponse(api_success(252), 252, $data);
            }

            throw new Exception(api_error(270), 270);

        } catch (Exception $e) {

            DB::rollBack();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method files_delete()
     *
     * @uses remove the selected file
     *
     * @created Vithya
     *
     * @updated 
     *
     * @param integer $virtual_experience_file_id
     *
     * @return JSON Response
     */
    public function files_delete(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'virtual_experience_file_id' => 'required|exists:virtual_experience_files,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            VirtualExperienceFile::where('id', $request->virtual_experience_file_id)->delete();

            DB::commit(); 

            $data['inputs'] = $request->all();

            return $this->sendResponse(api_success(253), 253, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method save()
     *
     * @uses remove the selected file
     *
     * @created Vithya
     *
     * @updated RA Shakthi
     *
     * @param integer $virtual_experience_file_id
     *
     * @return JSON Response
     */
    public function save(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'title' => 'required',
                'description' => 'required',
                'notes' => 'nullable',
                'price_per' => 'required|min:0',
                'file_ids' => 'required',
                'total_capacity' => 'required|min:1',
                'user_id' => 'nullable|exists:users,id',
                'scheduled_start' => 'required|date_format:Y-m-d H:i:s',
                'scheduled_end' => 'required|date_format:Y-m-d H:i:s',
            ];

           $custom_errors = ['price_per.required' => 'Please enter the amount'];

            $validated = Helper::custom_validator($request->all(), $rules, $custom_errors);

            $user = User::where(['id' => $request->id])->exists();

            throw_if(!$user, new Exception(api_error(135), 135));
            
            $scheduled_start = common_server_date($request->scheduled_start, $this->timezone, 'Y-m-d H:i:s');

            $scheduled_end = common_server_date($request->scheduled_end, $this->timezone, 'Y-m-d H:i:s');

            $virtual_experience_exists = VirtualExperience::where([
                'user_id' => $request->id,
                'scheduled_start' => $scheduled_start,
                'scheduled_end' => $scheduled_end,
            ])->exists();

            throw_if($virtual_experience_exists, new Exception(api_error(276)));
            
            $virtual_experience = VirtualExperience::create(['user_id' => $request->id, 'scheduled_start' => $scheduled_start,
                'scheduled_end' => $scheduled_end, 'used_capacity' => 0, 'remaning_capacity' => $request->total_capacity] + $validated);

            throw_if(!$virtual_experience, new Exception(api_error(271)));

            VirtualExperienceFile::whereIn('id', explode(',', $request->file_ids))->update(['virtual_experience_id' => $virtual_experience->id]);

            DB::commit();

            $data['virtual_experience'] = new VirtualExperienceResource($virtual_experience->refresh());

            return $this->sendResponse(api_success(254), 254, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method bookings_list()
     *
     * @uses To get list of the virtual experience bookings
     *
     * @created  RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function bookings_list(Request $request)
    {
        try {
           $base_query = VirtualExperienceBooking::where('user_id', $request->id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('search_key'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('unique_id', 'LIKE', "%{$request->search_key}%")
                        ->orWhere('payment_id', 'LIKE', "%{$request->search_key}%")
                        ->orWhere('price_per', 'LIKE', "%{$request->search_key}%")
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'LIKE', "%{$request->search_key}%"))
                        ->orWhereHas('virtualExperience', fn ($query) => $query->where('title', 'LIKE', "%{$request->search_key}%"))
                        ->orWhereHas('virtualExperienceUser', fn ($query) => $query->where('name', 'LIKE', "%{$request->search_key}%"));
                });
            });

            $data['total'] = $base_query->count();

            $data['virtual_experience_bookings'] = VirtualExperienceBookingsResource::collection(
                $base_query->latest()->skip($this->skip)->take($this->take)->get()
            );

            return $this->sendResponse('', '', $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

     /**
     * @method bookings_view()
     *
     * @uses To get the details by virtual experience bookings
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */

    public function bookings_view(Request $request) {

        try {

            $virtual_experience_booking = VirtualExperienceBooking::where('unique_id', $request->unique_id)->first();

            throw_if(!$virtual_experience_booking, new Exception(api_error(272), 272));

            $data['virtual_experience_booking'] = new VirtualExperienceBookingsResource($virtual_experience_booking);

            return $this->sendResponse('', '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }


    /**
     * @method bookings_cancel()
     *
     * @uses To cancel virtual experience bookings
     *
     * @created RA Shakthi
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function bookings_cancel(Request $request)
    {
        try {
            $virtual_experience_booking = VirtualExperienceBooking::where('unique_id', $request->unique_id)->first();

            throw_if(!$virtual_experience_booking, new Exception(api_error(272), 272));

            throw_if($virtual_experience_booking->status == VIRTUAL_EXPERIENCE_CANCELLED, new Exception(api_error(273), 273));

            $virtual_experience_booking->status = VIRTUAL_EXPERIENCE_CANCELLED;

            $virtual_experience_booking->save();

            return $this->sendResponse(api_success(255), 255, $data = []);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }
    /** 
     * @method start_virtual_experience()
     *
     * @uses to start the virtual experience
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */
       public function start_virtual_experience(Request $request) {
        try {
            $rules = [
                'virtual_experience_unique_id' => 'required|exists:virtual_experiences,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules);

            $virtual_experience = VirtualExperience::where(['unique_id' => $request->virtual_experience_unique_id])->first();

            throw_if(!$virtual_experience, new Exception(api_error(272), 272));

            $agora_app_id = Setting::get('agora_app_id');

            $app_certificate = Setting::get('agora_certificate_id');

            if (Setting::get('is_agora_configured') && ($request->device_type != DEVICE_WEB)) {

                $rules = ['virtual_id' => 'required'];

                Helper::custom_validator($request->all(), $rules, []);
            }

            throw_if(!$agora_app_id || !$app_certificate, new Exception(api_error(204), 204));

            $virtual_id = $request->virtual_id ?? md5(time());

            $token = '';

            if ($agora_configured = Setting::get('is_agora_configured')) {

                $uid = 0;

                $role = \RtcTokenBuilder::RoleAttendee;

                $expireTimeInSeconds = 3600;

                $privilegeExpiredTs = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp() + $expireTimeInSeconds;

                $token = \RtcTokenBuilder::buildTokenWithUid($agora_app_id, $app_certificate, $virtual_id, $uid, $role, $privilegeExpiredTs) ?? "";
            }

            $virtual_experience->update([
                'actual_start' => now(),
                'status' => VIRTUAL_EXPERIENCE_STARTED,
                'agora_token' => $token,
                'virtual_id' => $virtual_id 
            ]);

            $virtual_experience->refresh();

            $data['virtual_experience'] = new VirtualExperienceResource($virtual_experience);

            return $this->sendResponse(api_success(256), 256, $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /** 
     * @method end_virtual_experience()
     *
     * @uses to end the virtual experience
     *
     * @created RA Shakthi
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */
       public function end_virtual_experience(Request $request) {
        try {
            $rules = [
                'virtual_experience_unique_id' => 'required|exists:virtual_experiences,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules);

            $virtual_experience = VirtualExperience::where(['unique_id' => $request->virtual_experience_unique_id, 'status' => VIRTUAL_EXPERIENCE_STARTED])->first();

            throw_if(!$virtual_experience, new Exception(api_error(272), 272));

            $virtual_experience->update([
                'actual_end' => now(),
                'status' => VIRTUAL_EXPERIENCE_COMPLETED,
            ]);

            return $this->sendResponse(api_success(257), 257, []);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method creator_availabilities()
     *
     * @uses to get availability details of a creator for a specific date
     *
     * @created RA Shakthi
     *
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function creator_availabilities(Request $request) {
    try {
        $rules = [
            'user_id' => 'nullable|exists:users,id,' . $request->id,
            'date' => 'required|date|date_format:Y-m-d',
        ];

        Helper::custom_validator($request->all(), $rules);

        $user = User::where(['id' => $request->id])->exists();

        throw_if(!$user, new Exception(api_error(135), 135));

        $timezone = $this->loginUser->timezone ?? "America/New_York";

        $date = new \DateTime($request->date, new \DateTimeZone('UTC'));

        $check_date = $date->format('Y-m-d') < now()->format('Y-m-d');

        throw_if($check_date, new Exception(api_error(277), 277));

        if ($date->format('Y-m-d') == now()->format('Y-m-d')) {

            $current_date_time = now(new \DateTimeZone(DEFAULT_TIMEZONE));

            $start_date_time = $current_date_time->modify('+1 hour')->setTime($current_date_time->format('H'), 0, 0);

        } else {

            $start_date_time = new \DateTime($request->date . ' 00:00:00', new \DateTimeZone($timezone));
        }

        $end_date_time = clone $start_date_time;

        $end_date_time->setTime(23, 59, 59);

        $slots = [];

        while ($start_date_time < $end_date_time) {

            $start_time = $start_date_time->format('h:i A');

            $end_date_time_clone = clone $start_date_time;

            $end_date_time_clone->add(new \DateInterval('PT1H'));

            $end_time = $end_date_time_clone->format('h:i A');

            $start_date = $start_date_time->format('Y-m-d');

            $end_date = $end_date_time_clone->format('Y-m-d');

            $start_date_utc = $start_date.' '.$start_time;

            $start_date_format_utc = \DateTime::createFromFormat('Y-m-d h:i A', $start_date_utc);

            $formatted_start_date_time = $start_date_format_utc->format('Y-m-d H:i:s');

            $end_date_utc = $end_date.' '.$end_time;

            $end_date_format_utc = \DateTime::createFromFormat('Y-m-d h:i A', $end_date_utc);

            $end_format_time = $end_date_format_utc->format('H:i:s');

            $end_format_date = $end_date_format_utc->format('Y-m-d');

            $formatted_end_date_time = $end_format_time == '00:00:00' ? $start_date . ' 23:59:00' : $end_format_date . ' ' .$end_format_time; 

            $utc_end_date_time_format = common_server_date($formatted_end_date_time, $this->timezone, 'Y-m-d H:i:s');

            $utc_start_date_time_format = common_server_date($formatted_start_date_time, $this->timezone, 'Y-m-d H:i:s');
            
            $virtual_experience = VirtualExperience::where([
                'user_id' => $request->id,
                'scheduled_start' => $utc_start_date_time_format,
                'scheduled_end' => $utc_end_date_time_format,
            ])->exists();

            $end_time = ($end_time === '12:00 AM') ? '11:59 PM' : $end_time;

            $slots[] = [
                'start_time' => $start_time,
                'end_time' => $end_time,
                'utc_date' => $start_date_time->format('Y-m-d'),
                'is_available' => !$virtual_experience,
            ];

            $start_date_time->add(new \DateInterval('PT1H'));
        }

        $data['slots'] = $slots ?? [];

        return $this->sendResponse('', '', $data);

    } catch (Exception $e) {

        return $this->sendError($e->getMessage(), $e->getCode());
    }
}

   /**
 * @method virtual_experience_host_update()
 *
 * @uses To update the host id
 *
 * @created RA Shakthi
 *
 * @updated
 *
 * @param Request $request
 *
 * @return JSON response
 */
public function virtual_experience_host_update(Request $request) {
    try {
        DB::beginTransaction();

        $rules = [
            'virtual_experience_unique_id' => 'required|exists:virtual_experiences,unique_id',
            'host_id' => 'required'
        ];

        Helper::custom_validator($request->all(), $rules);

        $virtual_experience_details = VirtualExperience::where(['unique_id' => $request->virtual_experience_unique_id, 'status' => VIRTUAL_EXPERIENCE_STARTED])->first();

        throw_if(!$virtual_experience_details, new Exception(api_error(135), 135));

        $virtual_experience_details->update(['host_id' => $request->host_id]);

        DB::commit();

        $data['virtual_experience'] = new VirtualExperienceResource($virtual_experience_details);

        return $this->sendResponse(api_success(204), 204, $data);

    } catch (Exception $e) {

        DB::rollback();
        
        return $this->sendError($e->getMessage(), $e->getCode());
    }
}

}

