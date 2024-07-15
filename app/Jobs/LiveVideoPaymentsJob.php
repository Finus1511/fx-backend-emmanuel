 <?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;
use Log, Auth;
use Setting, Exception;
use App\Helpers\Helper;
use App\Models\User;

class LiveVideoPaymentsJob implements ShouldQueue
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

            $request = (object) $this->data['live_video_payments'];

            $title = $content = push_messages(606);

            $live_video_payments = \App\Models\LiveVideoPayment::where('user_id',$request->id)->where('live_video_payment_id',$request->live_video_payment_id)->first();

            $to_user = User::find($live_video_payment->user_id);

            $paid_amount = \Setting::get('is_only_wallet_payment') ? $live_video_payments->token : $live_video_payments->paid_amount;

            $message = tr('live_vedio_payments_message', formatted_amount($paid_amount ?? 0.00) )." ".$to_user->name ?? ''; 

            $data['user_id'] = $live_video_payments->user_id ?? '';

            $data['user_id'] = $live_video_payments->postDetails->user_id ?? '';
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_TIPS');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_LIVE_VIDEO_PAYMENT;

            $data['image'] = $live_video_payments->user->picture ?? asset('placeholder.jpeg');

            $data['live_video_id'] = $live_video_payments->live_video_payment_id ?? '';

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user_details = User::where('id', $data['user_id'])->first();

            if (Setting::get('is_push_notification') == YES && $user_details) {

                if($user_details->is_push_notification == YES && ($user_details->device_token != '')) {

                    $push_data = [
                        'content_id' =>$live_video_payments->live_video_payment_id ?? '',
                        'notification_type' => BELL_NOTIFICATION_TYPE_LIVE_VIDEO_PAYMENT,
                        'content_unique_id' => $live_video_payments->chatassetDetails->live_video_payment_unique_id ?? '',
                    ];

                    \Notification::send(
                        $user_details->id, 
                        new \App\Notifications\PushNotification(
                            $title , 
                            $content, 
                            json_encode($push_data), 
                            $user_details->device_token,
                            Setting::get('BN_USER_TIPS'),
                        )
                    );

                }
            } 

            if (Setting::get('is_email_notification') == YES && $user_details) {
               
                $email_data['subject'] = tr('chat_asset_payments_message', formatted_amount($live_video_payments->paid_amount ?? 0.00) )." ".$from_user->name ?? ''; 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.users.live_video_payments_invoice";

                $email_data['email'] = $user_details->email;

                $email_data['name'] = $user_details->name;

                $email_data['data'] = $user_details;

                dispatch(new SendEmailJob($email_data));

            }
            
            



        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}