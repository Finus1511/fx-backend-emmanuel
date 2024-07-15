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

class OrderPaymentJob implements ShouldQueue
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

            $request = (object) $this->data['order_payments'];

            $title = Setting::get('site_name'); 

            $order_payments = \App\Models\OrderPayment::where('user_id',$request->id)->where('order_id',$request->order_id)->first();

            $from_user = User::find($order_payments->user_id);

            $paid_amount = $order_payments->total;

            $content = $from_user->name.push_messages(612, formatted_amount($paid_amount ?? 0.00));

            $message = tr('order_payments_message', formatted_amount($paid_amount ?? 0.00) )." ".$from_user->name ?? ''; 

            $order_product = \App\Models\OrderProduct::where('order_id', $request->order_id)->where('user_id', $request->id)->first();

            $data['from_user_id'] = $order_payments->user_id ?? '';

            $data['to_user_id'] = $order_product->userProductDetails->user_id ?? '';
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_TIPS');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_ORDER;

            $data['image'] = $order_payments->user->picture ?? asset('placeholder.jpeg');

            $data['order_id'] = $order_payments->order_id ?? '';

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user = User::where('id', $data['to_user_id'])->first();

            if (Setting::get('is_push_notification') == YES && $user) {

                if($user->is_push_notification == YES && ($user->device_token != '')) {

                    $push_data = [
                        'content_id' =>$order_payments->order_id ?? '',
                        'notification_type' => BELL_NOTIFICATION_TYPE_ORDER,
                        'content_unique_id' => $order_payments->payment_id ?? '',
                    ];

                    \Notification::send(
                        $user->id, 
                        new \App\Notifications\PushNotification(
                            $title , 
                            $content, 
                            json_encode($push_data), 
                            $user->device_token,
                            Setting::get('BN_USER_TIPS'),
                        )
                    );

                }
            } 

            if (Setting::get('is_email_notification') == YES && $user) {

                $email_data['subject'] = tr('order_payments_message', formatted_amount($order_payments->paid_amount ?? 0.00) )." ".$from_user->name ?? ''; 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.users.order_payment";

                $email_data['email'] = 'bhawya@codegama.com';

                $email_data['name'] = $user->name;

                $email_data['data'] = $user;

                dispatch(new SendEmailJob($email_data));

            }
            
            



        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
