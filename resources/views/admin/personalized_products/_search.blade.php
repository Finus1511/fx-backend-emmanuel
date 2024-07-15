<form method="GET" action="{{route('admin.personalized_requests.products')}}">

    <input type="hidden" name="personalized_request_id" value="{{request('personalized_request_id')}}">

    <div class="row justify-content-end">

        <div class="col-xs-12 col-sm-12 col-lg-3 col-md-6 md-full-width resp-mrg-btm-md">
            <select class="form-control select2" name="status">

                <option class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{APPROVED}}" @if(Request::get('status') == APPROVED && Request::get('status')!='' ) selected @endif>{{tr('approved')}}</option>

                <option class="select-color" value="{{DECLINED}}" @if(Request::get('status') == DECLINED && Request::get('status')!='' ) selected @endif>{{tr('declined')}}</option>

            </select>
        
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-4 col-md-12">

            <div class="input-group">

                <input type="text" class="form-control" name="search_key" value="{{Request::get('search_key')??''}}" placeholder="{{tr('personalize_products_search_placeholder')}}"> 

                <span class="input-group-btn">
                    &nbsp

                    <button type="submit" class="btn btn-default reset-btn">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <a href="{{route('admin.personalized_requests.products', ['personalized_request_id' => request('personalized_request_id')])}}" class="btn btn-default reset-btn">
                        <i class="fa fa-eraser" aria-hidden="true"></i>
                    </a>

                </span>

            </div>

        </div>

    </div>

</form>
<br>