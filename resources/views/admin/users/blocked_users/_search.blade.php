<form method="GET" action="{{route('admin.block_users.index')}}">

    @if(request()->user_id) <input type="hidden" name="user_id" value="{{ request()->user_id ? : '' }}"> @endif
    

    <div class="row">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 resp-mrg-btm-md">
        </div>

      
        <div class="col-md-3"></div>
        <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">

                <input type="hidden" id="user_id" name="user_id" value="{{Request::get('user_id') ?? ''}}">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('blocked_user_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.block_users.index',['user_id'=>$user->id??''])}}" class="btn btn-default reset-btn">
                        <span> <i class="fa fa-eraser" aria-hidden="true"></i>
                        </span>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>