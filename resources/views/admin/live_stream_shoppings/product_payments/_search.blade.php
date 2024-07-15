<form method="GET" action="{{route('admin.live_stream_shoppings.product_payments')}}">

    <div class="row justify-content-end">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
          <input type="hidden" class="form-control" name="status" value="{{Request::get('status')}}">
          @if(request('live_stream_shopping_id'))
            <input type="hidden" name="live_stream_shopping_id" value="{{request('live_stream_shopping_id')}}}">
            @endif
            @if(request('user_product_id'))
            <input type="hidden" name="user_product_id" value="{{request('user_product_id')}}}">
            @endif
             @if(request('user_id'))
            <input type="hidden" name="user_id" value="{{request('user_id')}}}">
            @endif
            <select class="form-control select2" name="status">

                <option  class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{PAID}}" @if(Request::get('status') == PAID && Request::get('status')!='' ) selected @endif>{{tr('paid')}}</option>

                <option class="select-color" value="{{UNPAID}}" @if(Request::get('status') == UNPAID && Request::get('status')!='' ) selected @endif>{{tr('not_paid')}}</option>

            </select>

        </div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" value="{{Request::get('search_key')??''}}" name="search_key"
                placeholder="{{tr('lss_product_payments_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default reset-btn">
                  <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                
               <a class="btn btn-default reset-btn" href="{{route('admin.live_stream_shoppings.product_payments', ['live_stream_shopping_id' => request('live_stream_shopping_id'),'user_id' => request('user_id')])}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>
<br>