<form action="{{route('admin.live_stream_shoppings.index') }}" method="GET" role="search">
    <div class="row">
        <input type="hidden" name="status" value="{{request('status')}}}">
            <div class="col-xs-12 col-sm-12 col-lg-3 col-md-3 md-full-width resp-mrg-btm-md">
              <select class="form-control select2" name="payment_type">
                    <option  class="select-color" value="">{{tr('payment_type')}}</option>
                    <option value="{{PAYMENT_TYPE_FREE}}" @if(Request::get('payment_type')==PAYMENT_TYPE_FREE && Request::get('payment_type')!='' ) selected @endif>{{tr('free_videos')}}</option>
                    <option value="{{PAYMENT_TYPE_PAID}}" @if(Request::get('payment_type')==PAYMENT_TYPE_PAID ) selected @endif>{{tr('paid_videos')}}</option>
                </select>
            </div>

            <div class="col-xs-12 col-sm-12 col-lg-3 col-md-3 md-full-width resp-mrg-btm-md">
                <select class="form-control select2" name="stream_type">

                    <option  class="select-color" value="">{{tr('select_stream_type')}}</option>
                    <option value="{{STREAM_TYPE_PUBLIC}}" @if(Request::get('stream_type')==STREAM_TYPE_PUBLIC) selected @endif>{{tr('public_videos')}}</option>
                    <option value="{{STREAM_TYPE_PRIVATE}}" @if(Request::get('stream_type')==STREAM_TYPE_PRIVATE) selected @endif>{{tr('private_videos')}}</option>
                </select>
            </div>

            <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

                <div class="input-group">
                    <input type="text" class="form-control" value="{{Request::get('search_key')??''}}" name="search_key"
                    placeholder="{{tr('live_search_placeholder')}}"> <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                    
                    <a class="btn btn-default reset-btn" href="{{route('admin.live_stream_shoppings.index', ['status' => request('status')])}}"><i class="fa fa-eraser" aria-hidden="true"></i>
                    </a>  
                </span>
            </div>
                
        </div>

     </div>

</form>