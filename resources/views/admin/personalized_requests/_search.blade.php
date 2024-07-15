<form method="GET" action="{{route('admin.personalized_requests.index')}}">

    <input type="hidden" name="receiver_id" value="{{request('receiver_id')}}">
    <input type="hidden" name="sender_id" value="{{request('sender_id')}}">

    <div class="row justify-content-end">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
            <select class="form-control select2" name="status">

                <option class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{PERSONALIZE_USER_REQUESTED}}" @if(Request::get('status') == PERSONALIZE_USER_REQUESTED && Request::get('status')!='' ) selected @endif>{{tr('user_requested')}}</option>

                <option class="select-color" value="{{PERSONALIZE_CREATOR_ACCEPTED}}" @if(Request::get('status') == PERSONALIZE_CREATOR_ACCEPTED && Request::get('status')!='' ) selected @endif>{{tr('creator_accepted')}}</option>
                <option class="select-color" value="{{PERSONALIZE_CREATOR_REJECTED}}" @if(Request::get('status') == PERSONALIZE_CREATOR_REJECTED && Request::get('status')!='' ) selected @endif>{{tr('creator_rejected')}}</option>
                <option class="select-color" value="{{PERSONALIZE_USER_REJECTED}}" @if(Request::get('status') == PERSONALIZE_USER_REJECTED && Request::get('status')!='' ) selected @endif>{{tr('user_rejected')}}</option>

                <option class="select-color" value="{{PERSONALIZE_USER_PAID}}" @if(Request::get('status') == PERSONALIZE_USER_PAID && Request::get('status')!='' ) selected @endif>{{tr('user_paid')}}</option>

                <option class="select-color" value="{{PERSONALIZE_CREATOR_UPLOADED}}" @if(Request::get('status') == PERSONALIZE_CREATOR_UPLOADED && Request::get('status')!='' ) selected @endif>{{tr('creator_uploaded')}}</option>

            </select>
        
        </div>
         <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
            <select class="form-control select2" name="type">

                <option class="select-color" value="">{{tr('select_type')}}</option>

                <option class="select-color" value="{{PERSONALIZE_TYPE_IMAGE}}" @if(Request::get('type') == PERSONALIZE_TYPE_IMAGE && Request::get('type')!='' ) selected @endif>{{tr('image')}}</option>

                <option class="select-color" value="{{PERSONALIZE_TYPE_VIDEO}}" @if(Request::get('type') == PERSONALIZE_TYPE_VIDEO && Request::get('type')!='' ) selected @endif>{{tr('video')}}</option>

                <option class="select-color" value="{{PERSONALIZE_TYPE_PRODUCT}}" @if(Request::get('type') == PERSONALIZE_TYPE_PRODUCT && Request::get('type')!='' ) selected @endif>{{tr('product')}}</option>

                <option class="select-color" value="{{PERSONALIZE_TYPE_AUDIO}}" @if(Request::get('type') == PERSONALIZE_TYPE_AUDIO && Request::get('type')!='' ) selected @endif>{{tr('audio')}}</option>

            </select>
        
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-4 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('personalize_request_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.personalized_requests.index', ['receiver_id' => request('receiver_id'), 'sender_id' => request('sender_id')])}}" class="btn btn-default reset-btn">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>