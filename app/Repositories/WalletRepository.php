<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\User;

class WalletRepository {

    /**
     * @method wallets_list_response()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function wallets_list_response($payments, $request) {

        $payments = $payments->map(function ($value, $key) use ($request) {

                        if($value->payment_type == WALLET_PAYMENT_TYPE_CREDIT) {

                            if($request->id == $value->user_id) {

                                $value->username = $value->ReceivedFromUser->name ?? "-";

                                unset($value->ReceivedFromUser);

                            }
                        } else {
                            $value->username = $value->toUser->name ?? "You";

                            unset($value->toUser);
                        }

                        $value->dispute_btn_status = in_array($value->status, [USER_WALLET_PAYMENT_PAID]) ? YES : NO;

                        $value->paid_date = common_date($value->paid_date, $request->timezone, 'd M Y');

                        return $value;
                    });


        return $payments;

    }

    /**
     * @method disputes_list_response()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $list
     */

    public static function disputes_list_response($user_disputes, $request) {

        foreach ($user_disputes as $key => $value) {

            $cancel_btn_status = NO;

            if($request->id == $value->user_id) { // Who raised the dispute

                $user = $value->DisputeReceiver;

                // cancel button status

                $cancel_btn_status = $value->status == DISPUTE_SENT ? YES : NO;

                unset($value->DisputeReceiver);

            } else {

                $user = $value->DisputeSender;
                
                unset($value->DisputeSender);

            }

            $value->username = $user->name ?? "-";

            $value->user_picture = $user->picture ?? asset('placeholder.jpeg');

            $value->cancel_btn_status = $cancel_btn_status; 

        }

        return $user_disputes;

    }

}