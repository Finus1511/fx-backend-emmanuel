<?php

namespace App\Http\Controllers\Api\VirtualExperience;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use Illuminate\Validation\Rule;

use App\Models\{User, VeVip, VeVipBooking, VeVipFile, VeVipQuestion, VeVipAnswer};

use Carbon\Carbon;

use App\Http\Resources\{VeVipResource, VeVipBookingsResource};

class VipCreatorVirtualExperienceController extends Controller
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

            $base_query = VeVip::where('user_id', $request->id)->when($request->filled('status'), function ($query) use ($request) {
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

            $data['virtual_experiences'] = VeVipResource::collection($virtual_experiences);

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

            $virtual_experience = VeVip::where(['unique_id' => $request->virtual_experience_unique_id])->first();

            throw_if(!$virtual_experience, new Exception(api_error(269), 269));

            $data['virtual_experience'] = new VeVipResource($virtual_experience);

            $data['questions'] = VeVipQuestion::where('ve_vip_id', $virtual_experience->id)->get();

            $data['booking'] = VeVipBooking::where(['ve_vip_id' => $virtual_experience->id, 'user_id' => $request->id])->first() ?? emptyObject();

            $data['is_answered'] = VeVipAnswer::where(['ve_vip_id' => $virtual_experience->id, 'user_id' => $request->id])->count() ? YES : NO;

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
                'virtual_experience_id' => 'nullable|exists:ve_vips,id',
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

                    $virtual_experience_file = new VeVipFile([
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
                'virtual_experience_file_id' => 'required|exists:ve_vip_files,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            VeVipFile::where('id', $request->virtual_experience_file_id)->delete();

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
                'questions' => 'required',
                'user_id' => 'nullable|exists:users,id',
                'scheduled_date' => 'required|date_format:Y-m-d'
            ];

           $custom_errors = ['amount.required' => 'Please enter the amount'];

            $validated = Helper::custom_validator($request->all(), $rules, $custom_errors);

            $user = User::where(['id' => $request->id])->exists();

            throw_if(!$user, new Exception(api_error(135), 135));

            $virtual_experience_exists = VeVip::where([
                'user_id' => $request->id,
                'scheduled_date' => $request->scheduled_date
            ])->exists();

            throw_if($virtual_experience_exists, new Exception(api_error(276)));

            $questions = json_decode($request->questions);

            throw_if(count($questions) <= 0, new Exception("Atleast one question is required.", 100));
            
            $virtual_experience = VeVip::create(['user_id' => $request->id] + $validated);

            throw_if(!$virtual_experience, new Exception(api_error(271)));

            foreach ($questions as $key => $question) {
                
                VeVipQuestion::create([
                    've_vip_user_id' => $request->id,
                    've_vip_id' => $virtual_experience->id,
                    'question' => $question->question,
                    'type' => $question->type
                ]);
            }

            VeVipFile::whereIn('id', explode(',', $request->file_ids))->update(['ve_vip_id' => $virtual_experience->id]);

            DB::commit();

            $data['virtual_experience'] = new VeVipResource($virtual_experience->refresh());

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
           $base_query = VeVipBooking::where('user_id', $request->id)
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

            $data['virtual_experience_bookings'] = VeVipBookingsResource::collection(
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

            $virtual_experience_booking = VeVipBooking::where('unique_id', $request->unique_id)->first();

            throw_if(!$virtual_experience_booking, new Exception(api_error(272), 272));

            $data['virtual_experience_booking'] = new VeVipBookingsResource($virtual_experience_booking);

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
            $virtual_experience_booking = VeVipBooking::where('unique_id', $request->unique_id)->first();

            throw_if(!$virtual_experience_booking, new Exception(api_error(272), 272));

            throw_if($virtual_experience_booking->status == VIP_VE_CANCELED, new Exception(api_error(273), 273));

            $virtual_experience_booking->status = VIP_VE_CANCELED;

            $virtual_experience_booking->save();

            return $this->sendResponse(api_success(255), 255, $data = $virtual_experience_booking);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method bookings_accept()
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
    public function bookings_accept(Request $request)
    {
        try {
            $virtual_experience_booking = VeVipBooking::where('unique_id', $request->unique_id)->first();

            throw_if(!$virtual_experience_booking, new Exception(api_error(272), 272));

            throw_if($virtual_experience_booking->status !=  VIP_VE_RAISED, new Exception(api_error(338), 338));

            $virtual_experience_booking->status = VIP_VE_ACCEPTED;

            if ($virtual_experience_booking->save()) {
                
                VeVipBooking::where('ve_vip_id', $virtual_experience_booking->ve_vip_id)->where('id', "!=", $virtual_experience_booking->id)->update(['status' => VIP_VE_CANCELED]);
            }

            return $this->sendResponse(api_success(255), 255, $data = $virtual_experience_booking);

        } catch (Exception $e) {

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
    public function update_answer(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'virtual_experience_id' => 'required|exists:ve_vips,id',
                'answers' => 'required'
            ];

            $validated = Helper::custom_validator($request->all(), $rules, []);

            $user = User::where(['id' => $request->id])->exists();

            throw_if(!$user, new Exception(api_error(135), 135));

            $virtual_experience = VeVip::find($request->virtual_experience_id);

            throw_if(!$virtual_experience, new Exception(api_error(271)));

            throw_if($virtual_experience->status == VIRTUAL_EXPERIENCE_PAID, new Exception(api_error(276)));

            $answers = json_decode($request->answers, true);

            throw_if(count($answers) <= 0, new Exception("Atleast one answer is required.", 100));

            $question_ids = VeVipQuestion::where('ve_vip_id', $virtual_experience->id)->pluck('id')->toArray();

            // Get the keys of the given object
            $objectKeys = array_keys($answers);

            // Ensure the keys are integers for proper comparison
            $objectKeys = array_map('intval', $objectKeys);
            
            // Compare the keys
            sort($objectKeys); // Sort the keys for comparison
            sort($question_ids); // Sort the answer IDs
            
            if ($objectKeys != $question_ids) {

                throw new Exception(api_error(337), 337);
            }

            foreach ($answers as $key => $answer) {
                
                $answer = VeVipAnswer::firstOrCreate(
                    ['ve_vip_question_id' => $key, 'user_id' => $request->id],          // Condition: Match the id with the key
                    ['answer' => $answer, 've_vip_user_id' => $virtual_experience->user_id, 've_vip_id' => $virtual_experience->id ]     // Fields to populate if the record doesn't exist
                );
            }

            $booking_data = [
                've_vip_id' => $virtual_experience->id,
                've_vip_user_id' => $virtual_experience->user_id,
                'user_id' => $request->id,
                'amount' => $virtual_experience->amount,
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $virtual_experience->amount, 
                'payment_id' => 'WPP-'.rand(),
                'status' => VIP_VE_RAISED,
            ];

            $virtual_experience_booking =VeVipBooking::create($booking_data);

            DB::commit();

            $data['virtual_experience'] = new VeVipResource($virtual_experience->refresh());

            return $this->sendResponse(api_success(261), 261, $data);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method received_bookings_list()
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
    public function received_bookings_list(Request $request)
    {
        try {
           $base_query = VeVipBooking::where('ve_vip_user_id', $request->id)
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

            $data['virtual_experience_bookings'] = VeVipBookingsResource::collection(
                $base_query->latest()->skip($this->skip)->take($this->take)->get()
            );

            return $this->sendResponse('', '', $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @method view_answer()
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
    public function view_answer(Request $request)
    {
        try {

            $rules = [
                'virtual_experience_id' => 'required|exists:ve_vips,id',
                'user_id' => 'required|exists:users,id'
            ];

            $validated = Helper::custom_validator($request->all(), $rules, []);

            $base_query = VeVipAnswer::where(['ve_vip_id' => $request->virtual_experience_id, 'user_id' => $request->user_id])->with('question');

            $data['total'] = $base_query->count();

            $data['answers'] = $base_query->get();

            return $this->sendResponse('', '', $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

}

