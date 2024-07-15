 <form method="GET" action="{{route('admin.order.payments')}}">

<input type="hidden" id="user_id" name="user_id" value="{{Request::get('user_id') ?? ''}}">


    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12"></div>

        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}"
                placeholder="{{tr('order_payment_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default">
                   <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                </button>
                
                <button class="btn btn-default"><a  href="{{route('admin.order.payments')}}"><i class="fa fa-eraser" aria-hidden="true"></i></button>
                </a>
                   
                </span>

            </div>
            
        </div>

    </div>

</form>