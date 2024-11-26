<?php

namespace App\Http\Controllers\Api\VirtualExperience;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use Illuminate\Validation\Rule;

use App\Models\{User, VeOneOnOne, VeOneOnOneBooking, VeOneOnOneFile};

use Carbon\Carbon;

use App\Http\Resources\{VeOneonOneResource, VeOneOnOneBookingsResource};

class OneonOneCreatorVirtualExperienceController extends Controller
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

            $base_query = VeOneOnOne::where('user_id', $request->id)->when($request->filled('status'), function ($query) use ($request) {
                             $query->where('status', $request->status);
                        })->when($request->filled('search_key'), function ($query) use ($request) {
                            $query->where(function ($query) use ($request) {
                                $query->where('title', "LIKE", "%" . $request->search_key . "%")
                                ->orWhere('unique_id', "LIKE", "%" . $request->search_key . "%")
                                ->orWhereHas('user', function($query) use($request) {
                                    $query->where('name', "LIKE", "%{$request->search_key}%");
                                });
                            });
                        });

            $data['total'] = $base_query->count();

            $virtual_experiences = $base_query->latest()->skip($this->skip)->take($this->take)->get();

            $data['virtual_experiences'] = VeOneonOneResource::collection($virtual_experiences);

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

            $virtual_experience = VeOneOnOne::where(['unique_id' => $request->virtual_experience_unique_id])->first();

            throw_if(!$virtual_experience, new Exception(api_error(269), 269));

            $data['virtual_experience'] = new VeOneonOneResource($virtual_experience);

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
                'virtual_experience_id' => 'nullable|exists:ve_one_on_ones,id',
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

                    $virtual_experience_file = new VeOneOnOneFile([
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
                'virtual_experience_file_id' => 'required|exists:ve_one_on_one_files,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            VeOneOnOneFile::where('id', $request->virtual_experience_file_id)->delete();

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
                'amount' => 'required|min:0',
                'file_ids' => 'required',
                'latitude' => 'required',
                'longitude' => 'required',
                'location' => 'required',
                'user_id' => 'nullable|exists:users,id',
                'scheduled_date' => 'required|date_format:Y-m-d'
            ];

           $custom_errors = ['amount.required' => 'Please enter the amount'];

            $validated = Helper::custom_validator($request->all(), $rules, $custom_errors);

            $user = User::where(['id' => $request->id])->exists();

            throw_if(!$user, new Exception(api_error(135), 135));

            $virtual_experience_exists = VeOneOnOne::where([
                'user_id' => $request->id,
                'scheduled_date' => $request->scheduled_date
            ])->exists();

            throw_if($virtual_experience_exists, new Exception(api_error(276)));
            
            $virtual_experience = VeOneOnOne::create(['user_id' => $request->id] + $validated);

            throw_if(!$virtual_experience, new Exception(api_error(271)));

            VeOneOnOneFile::whereIn('id', explode(',', $request->file_ids))->update(['ve_one_on_one_id' => $virtual_experience->id]);

            DB::commit();

            $data['virtual_experience'] = new VeOneonOneResource($virtual_experience->refresh());

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
           $base_query = VeOneOnOneBooking::where('user_id', $request->id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('search_key'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('unique_id', 'LIKE', "%{$request->search_key}%")
                        ->orWhere('payment_id', 'LIKE', "%{$request->search_key}%")
                        ->orWhere('amount', 'LIKE', "%{$request->search_key}%")
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'LIKE', "%{$request->search_key}%"))
                        ->orWhereHas('virtualExperience', fn ($query) => $query->where('title', 'LIKE', "%{$request->search_key}%"))
                        ->orWhereHas('virtualExperienceUser', fn ($query) => $query->where('name', 'LIKE', "%{$request->search_key}%"));
                });
            });

            $data['total'] = $base_query->count();

            $data['virtual_experience_bookings'] = VeOneOnOneBookingsResource::collection(
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

            $virtual_experience_booking = VeOneOnOneBooking::where('unique_id', $request->unique_id)->first();

            throw_if(!$virtual_experience_booking, new Exception(api_error(272), 272));

            $data['virtual_experience_booking'] = new VeOneOnOneBookingsResource($virtual_experience_booking);

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
            $virtual_experience_booking = VeOneOnOneBooking::where('unique_id', $request->unique_id)->first();

            throw_if(!$virtual_experience_booking, new Exception(api_error(272), 272));

            throw_if($virtual_experience_booking->status == VIRTUAL_EXPERIENCE_CANCELLED, new Exception(api_error(273), 273));

            $virtual_experience_booking->status = VIRTUAL_EXPERIENCE_CANCELLED;

            $virtual_experience_booking->save();

            return $this->sendResponse(api_success(255), 255, $data = []);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

}

