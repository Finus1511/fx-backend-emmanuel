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

class VideoCallPaymentJob implements ShouldQueue
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

            $video_call_request = (object) $this->data['video_call_request'];

            $video_call_payments = \App\Models\VideoCallPayment::where('user_id',$video_call_request->user_id)->where('model_id',$video_call_request->model_id)->where('video_call_request_id',$video_call_request->id)->first();

            $paid_amount = \Setting::get('is_only_wallet_payment') ? $video_call_payments->token : $video_call_payments->paid_amount;

            $title = $content = push_messages(616,formatted_amount($paid_amount ?? 0.00)) ." ".$video_call_request->user->name ?? '';

            $message = tr('video_call_payments_message')." ".$video_call_request->user->name ?? ''; 

            $data['from_user_id'] = $video_call_request->user_id;

            $data['to_user_id'] = $video_call_request->model_id;
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_VIDEO_CALL');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_VIDEO_CALL_PAYMENT;

            $data['image'] = $video_call_request->user->picture ?? asset('placeholder.jpeg');

            $data['video_call_request_id'] = $video_call_request->id ?? '';

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $model = User::where('id', $video_call_request->model_id)->first();

            if (Setting::get('is_push_notification') == YES && $model) {

                if($model->is_push_notification == YES && ($model->device_token != '')) {

                    $push_data = [
                        'content_id' => $video_call_request->id,
                        'notification_type' => BELL_NOTIFICATION_TYPE_VIDEO_CALL_PAYMENT,
                        'content_unique_id' => $video_call_request->unique_id,
                    ];

                    \Notification::send(
                        $model->id, 
                        new \App\Notifications\PushNotification(
                            $title , 
                            $content, 
                            json_encode($push_data), 
                            $model->device_token,
                            Setting::get('BN_USER_VIDEO_CALL'),
                        )
                    );

                }
            } 

            if (Setting::get('is_email_notification') == YES && $model) {
               
                $email_data['subject'] = tr('video_call_payments_title'); 
               
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