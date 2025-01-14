<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Repositories\PaymentRepository as PaymentRepo;

use Log, Validator, Exception, DB, Setting;

use Illuminate\Http\Request;

use App\Models\{User, UserSubscriptionPayment, Community, CommunityUser};

class SubscriptionPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $request)
    {
        //
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {
        //

        try {

            $current_timestamp = \Carbon\Carbon::now()->toDateTimeString();

            Log::info("current_timestamp ".print_r($current_timestamp, true));

            $subscription_payments = UserSubscriptionPayment::where('is_current_subscription',YES)->where('expiry_date','<', $current_timestamp)->get();

            if($subscription_payments->isEmpty()) {

                throw new Exception(api_error(129), 129);

            }

            DB::beginTransaction();

            foreach ($subscription_payments as $subscription_payment){

                $community = Community::where('user_id', $subscription_payment->to_user_id)->first();

                if($community) {

                    CommunityUser::where(['community_id' => $community->id, 'user_id' => $subscription_payment->from_user_id])->delete();
                }

                UserSubscriptionPayment::where('id', $subscription_payment->id)->update(['is_current_subscription' => NO]);
            }

            DB::commit();

        }catch (Exception $e){

            DB::rollback();

            Log::info("SubscriptionPaymentJob Error".print_r($e->getMessage(), true));
        }
    }
}
