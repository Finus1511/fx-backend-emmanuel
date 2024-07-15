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

class PostNotificationToFollower implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $logged_in_user_id;

    protected $post;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($logged_in_user_id, $post)
    {
        //
        $this->logged_in_user_id = $logged_in_user_id;

        $this->post = $post;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $user = \App\Models\User::find($this->logged_in_user_id);

            $user_subscribed = \App\Models\UserSubscriptionPayment::where('to_user_id',$this->logged_in_user_id)->where('is_current_subscription',YES)->pluck('from_user_id')->toArray();

            $base_query = \App\Models\Follower::leftJoin('users', 'users.id', '=', 'followers.user_id')
                ->select('followers.*')
                ->where('user_id', $this->logged_in_user_id)
                ->where('users.status', APPROVED)
                ->whereIn('follower_id',$user_subscribed)
                ->where('users.is_email_verified', YES)
                ->orderBy('followers.created_at', 'desc');

       
            $base_query->chunk(30,function($followers) use ($user) {

                $title = Setting::get('site_name');

                $content = tr('user_new_post_message', $user->name ?? '');

                $message = tr('user_new_post_message', $user->name ?? ''); 

                Log::info("User ".print_r($user->email, true));
                
                foreach ($followers as $key => $value) {

                    Log::info("User ".print_r($value->user_id, true));

                    $data['from_user_id'] = $value->follower_id;

                    $data['to_user_id'] = $value->user_id;
                  
                    $data['message'] = $message;
        
                    $data['action_url'] =  Setting::get('BN_USER_LIKE');
        
                    $data['image'] = $value->user->picture ?? asset('placeholder.jpeg');
            
                    $data['notification_type'] = BELL_NOTIFICATION_TYPE_NEW_POST;

                    $data['subject'] = $content;
        
                    dispatch(new BellNotificationJob($data));
    
                    $user = User::where('id', $value->follower_id)->first();

                    if (Setting::get('is_push_notification') == YES && $user) {

                        if($user->is_push_notification == YES && ($user->device_token != '')) {
        
                            $push_data = [
                                'content_id' => $this->post->id,
                                'notification_type' => BELL_NOTIFICATION_TYPE_NEW_POST,
                                'content_unique_id' => $this->post->unique_id,
                            ];
                    
                            \Notification::send(
                                $user->id, 
                                new \App\Notifications\PushNotification(
                                    $title , 
                                    $content, 
                                    json_encode($push_data), 
                                    $user->device_token,
                                    Setting::get('BN_USER_LIKE'),
                                )
                            );
        
                        }
                    }

                    if (Setting::get('is_email_notification') == YES && $user) {
               
                        $email_data['subject'] = tr('new_post_message');
                       
                        $email_data['message'] = $message;
        
                        $email_data['page'] = "emails.users.post_notification";
        
                        $email_data['email'] = $user->email;
        
                        $email_data['name'] = $user->name;
        
                        $email_data['data'] = $user;
        
                        dispatch(new SendEmailJob($email_data));
        
                    }
    
    
                }
            });

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
        

       
    }
}
