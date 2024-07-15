<form method="GET" action="{{route('admin.wallet_payments.index')}}">

    <div class="row">

        <input type="hidden" id="amount_type" name="amount_type" value="{{Request::get('amount_type') ?? ''}}">

        <input type="hidden" id="payment_mode" name="payment_mode" value="{{Request::get('payment_mode') ?? ''}}">
        
        <input type="hidden" id="payment_type" name="payment_type" value="{{Request::get('payment_type') ?? ''}}">

        <div class="col-xs-12 col-sm-12 col-lg-2 col-md-6 md-full-width resp-mrg-btm-md">

            <select class="form-control select2" name="amount_type">

                <option class="select-color" value="">{{tr('select_amount_type')}}</option>

                <option class="select-color" value="{{WALLET_PAYMENT_TYPE_ADD}}" @if(Request::get('amount_type') == WALLET_PAYMENT_TYPE_ADD && Request::get('amount_type')!='' ) selected @endif>{{tr('add')}}</option>

                <option class="select-color" value="{{WALLET_PAYMENT_TYPE_PAID}}" @if(Request::get('amount_type') == WALLET_PAYMENT_TYPE_PAID && Request::get('amount_type')!='' ) selected @endif>{{tr('paid')}}</option> 

                <option class="select-color" value="{{WALLET_PAYMENT_TYPE_CREDIT}}" @if(Request::get('amount_type') == WALLET_PAYMENT_TYPE_CREDIT && Request::get('amount_type')!='' ) selected @endif>{{tr('credit')}}</option> 

                <option class="select-color" value="{{WALLET_PAYMENT_TYPE_WITHDRAWAL}}" @if(Request::get('amount_type') == WALLET_PAYMENT_TYPE_WITHDRAWAL && Request::get('amount_type')!='' ) selected @endif>{{tr('withdrawal')}}</option>

            </select>
           
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-2 col-md-6 md-full-width resp-mrg-btm-md">


            <select class="form-control select2" name="payment_type">

                <option class="select-color" value="">{{tr('select_payment_type')}}</option>

                <option class="select-color" value="{{WALLET_PAYMENT_TYPE_ADD}}" @if(Request::get('payment_type') == WALLET_PAYMENT_TYPE_ADD && Request::get('payment_type')!='' ) selected @endif>{{tr('add')}}</option>

                <option class="select-color" value="{{WALLET_PAYMENT_TYPE_PAID}}" @if(Request::get('payment_type') == WALLET_PAYMENT_TYPE_PAID && Request::get('payment_type')!='' ) selected @endif>{{tr('paid')}}</option> 

                <option class="select-color" value="{{WALLET_PAYMENT_TYPE_CREDIT}}" @if(Request::get('payment_type') == WALLET_PAYMENT_TYPE_CREDIT && Request::get('payment_type')!='' ) selected @endif>{{tr('credit')}}</option> 

                <option class="select-color" value="{{WALLET_PAYMENT_TYPE_WITHDRAWAL}}" @if(Request::get('payment_type') == WALLET_PAYMENT_TYPE_WITHDRAWAL && Request::get('payment_type')!='' ) selected @endif>{{tr('withdrawal')}}</option>

            </select>
           
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">

            <select class="form-control select2" name="status">

                <option class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{PAID}}" @if(Request::get('status') == PAID && Request::get('status')!='' ) selected @endif>{{tr('paid')}}</option>

                <option class="select-color" value="{{UNPAID}}" @if(Request::get('status') == UNPAID && Request::get('status')!='' ) selected @endif>{{tr('not_paid')}}</option>

            </select>
        
        </div>

          <!-- <div class="col-xs-12 col-sm-12 col-lg-3 col-md-2 md-full-width resp-mrg-btm-md">

             <select class="form-control select2" name="payment_mode">

                <option class="select-color" value="">{{tr('select_payment_mode')}}</option>

                <option class="select-color" value="{{COD}}" @if(Request::get('payment_mode') == COD && Request::get('payment_mode')!='' ) selected @endif>{{tr('cod')}}</option>

                <option class="select-color" value="{{CARD}}" @if(Request::get('payment_mode') == CARD && Request::get('payment_mode')!='' ) selected @endif>{{tr('card')}}</option>

                <option class="select-color" value="{{BANK_TRANSFER}}" @if(Request::get('payment_mode') == BANK_TRANSFER && Request::get('payment_mode')!='' ) selected @endif>{{tr('bank_transfer')}}</option>

                <option class="select-color" value="{{PAYMENT_OFFLINE}}" @if(Request::get('payment_mode') == PAYMENT_OFFLINE && Request::get('payment_mode')!='' ) selected @endif>{{tr('payment_offline')}}</option>

                <option class="select-color" value="{{PAYMENT_MODE_WALLET}}" @if(Request::get('payment_mode') == PAYMENT_MODE_WALLET && Request::get('payment_mode')!='' ) selected @endif>{{tr('payment_wallet')}}</option>

                <option class="select-color" value="{{CCBILL}}" @if(Request::get('payment_mode') == CCBILL && Request::get('payment_mode')!='' ) selected @endif>{{tr('ccbill')}}</option>

                <option class="select-color" value="{{COINPAYMENT}}" @if(Request::get('payment_mode') == COINPAYMENT && Request::get('payment_mode')!='' ) selected @endif>{{tr('coinpayment')}}</option>

                <option class="select-color" value="{{INAPP_PURCHASE}}" @if(Request::get('payment_mode') == INAPP_PURCHASE && Request::get('payment_mode')!='' ) selected @endif>{{tr('inapp_purchase')}}</option>

            </select>
        
        </div> -->

        <div class="col-xs-12 col-sm-12 col-lg-4 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('wallet_payments_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.wallet_payments.index',['amount_type'=>Request::get('amount_type')??'','payment_type'=>Request::get('payment_type')??'','payment_mode'=>Request::get('payment_mode')??''])}}" class="btn btn-default reset-btn">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>