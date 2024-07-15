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

class ChatAssetPaymentJob implements ShouldQueue
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

            $request = (object) $this->data['chat_asset_payments'];

            $title = $content = push_messages(606);

            $chat_asset_payments = \App\Models\ChatAssetPayment::where('from_user_id',$request->id)->where('chat_asset_payment_id',$request->chat_asset_payment_id)->first();

            $to_user = User::find($chat_asset_payment->from_user_id);

            $paid_amount = \Setting::get('is_only_wallet_payment') ? $chat_asset_payments->token : $chat_asset_payments->paid_amount;

            $message = tr('chat_asset_payments_message', formatted_amount($paid_amount ?? 0.00) )." ".$to_user->name ?? ''; 

            $data['to_user_id'] = $chat_asset_payments->from_user_id ?? '';

            $data['to_user_id'] = $chat_asset_payments->postDetails->from_user_id ?? '';
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_TIPS');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_CHAT_ASSET_PAYMENT;

            $data['image'] = $chat_asset_payments->user->picture ?? asset('placeholder.jpeg');

            $data['post_id'] = $chat_asset_payments->chat_asset_payment_id ?? '';

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user_details = User::where('id', $data['from_user_id'])->first();

            if (Setting::get('is_push_notification') == YES && $user_details) {

                if($user_details->is_push_notification == YES && ($user_details->device_token != '')) {

                    $push_data = [
                        'content_id' =>$chat_asset_payments->chat_asset_payment_id ?? '',
                        'notification_type' => BELL_NOTIFICATION_TYPE_CHAT_ASSET_PAYMENT,
                        'content_unique_id' => $chat_asset_payments->chatassetDetails->chat_asset_payment_unique_id ?? '',
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
               
                $email_data['subject'] = tr('chat_asset_payments_message', formatted_amount($chat_asset_payments->paid_amount ?? 0.00) )." ".$from_user->name ?? ''; 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.users.chat_asset_payment";

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