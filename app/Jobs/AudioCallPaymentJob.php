<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

use Log, Auth;

use Setting, Exception;

use App\Helpers\Helper;

class AudioCallPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        try {

            $audio_call_request = (object) $this->data['audio_call_request'];

            $audio_call_payments = \App\Models\AudioCallPayment::where('user_id',$audio_call_request->user_id)->where('model_id',$audio_call_request->model_id)->where('audio_call_request_id',$audio_call_request->id)->first();

            $paid_amount = \Setting::get('is_only_wallet_payment') ? $audio_call_payments->token : $audio_call_payments->paid_amount;

            $title = $content = push_messages(617,formatted_amount($paid_amount ?? 0.00)) ." ".$audio_call_request->user->name ?? '';

            $message = tr('audio_call_payments_message')." ".$audio_call_request->user->name ?? ''; 

            $data['from_user_id'] = $audio_call_request->user_id;

            $data['to_user_id'] = $audio_call_request->model_id;
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_AUDIO_CALL');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_AUDIO_CALL_PAYMENT;

            $data['image'] = $audio_call_request->user->picture ?? asset('placeholder.jpeg');

            $data['audio_call_request_id'] = $audio_call_request->id ?? '';

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $model = User::where('id', $audio_call_request->model_id)->first();

            if (Setting::get('is_push_notification') == YES && $model) {

                if($model->is_push_notification == YES && ($model->device_token != '')) {

                    $push_data = [
                        'content_id' =>$audio_call_request->id ?? '',
                        'notification_type' => BELL_NOTIFICATION_TYPE_AUDIO_CALL_PAYMENT,
                        'content_unique_id' => $audio_call_request->unique_id ?? '',
                    ];

                    \Notification::send(
                        $model->id, 
                        new \App\Notifications\PushNotification(
                            $title , 
                            $content, 
                            json_encode($push_data), 
                            $model->device_token,
                            Setting::get('BN_USER_AUDIO_CALL'),
                        )
                    );

                }
            } 

            if (Setting::get('is_email_notification') == YES && $user_details) {
               
                $email_data['subject'] = tr('audio_call_payments_title'); 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.users.video-call-status";

                $email_data['email'] = $model->email;

                $email_data['name'] = $model->name;

                $email_data['data'] = $model;

                dispatch(new SendEmailJob($email_data));

            }
            
            



        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}