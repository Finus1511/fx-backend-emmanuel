<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;
use App\Models\UserWalletPayment;
  
class UserWalletPaymentExport implements FromView 
{

    public function __construct(Request $request)
    {
        $this->search_key = $request->search_key;
        $this->amount_type = $request->amount_type;
        $this->payment_type = $request->payment_type;
        $this->status = $request->status;
        $this->to_user_id = $request->to_user_id;
        $this->user_id = $request->user_id;
        $this->from_user_id = $request->recieved_from_user_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View {

        $base_query = UserWalletPayment::orderBy('created_at','DESC');

        if($this->to_user_id) {

            $base_query = $base_query->where('to_user_id',$this->to_user_id);
        }

        if($this->search_key) {

            $search_key = $this->search_key;

            $base_query = $base_query
                            ->whereHas('toUser',function($query) use($search_key){

                                return $query->where('name','LIKE','%'.$search_key.'%');
                                
                            })->orwhereHas('ReceivedFromUser',function($query) use($search_key){

                                return $query->where('name','LIKE','%'.$search_key.'%');

                            })->orWhere('payment_id','LIKE','%'.$search_key.'%');
        }

        if($this->user_id != '') {

            $base_query  = $base_query->where('user_id',$this->user_id);
        }

        if($this->from_user_id != '') {

            $base_query  = $base_query->where('recieved_from_user_id',$this->from_user_id);
        }

        if($this->amount_type != '') {

            $base_query  = $base_query->where('amount_type',$this->amount_type);
        }

        if($this->status != '') {

            $base_query  = $base_query->where('status',$this->status);
        }

        if($this->payment_type != '') {

            $base_query  = $base_query->where('payment_type',$this->payment_type);
        }

        $wallet_payments = $base_query->get()->chunk(50);

        return view('exports.wallet_payments', [
            'data' => $wallet_payments
        ]);

    }

}