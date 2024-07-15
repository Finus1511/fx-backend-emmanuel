
<form method="GET" action="{{route('admin.user_products.index')}}">

    <div class="row">

          <div class="col-xs-6 col-sm-12 col-lg-3 col-md-12">

            <div class="input-group">

              <select class="form-control select2" name="is_outofstock">


                <option  class="select-color" value="">{{tr('select_availability_status')}}</option>

                <option class="select-color" value="{{OUT_OF_STOCK}}" @if(Request::get('is_outofstock') == OUT_OF_STOCK && Request::get('is_outofstock')!='' ) selected @endif>{{tr('no')}}</option>

                <option class="select-color" value="{{IN_STOCK}}" @if(Request::get('is_outofstock') == IN_STOCK && Request::get('is_outofstock')!='' ) selected @endif>{{tr('yes')}}</option>

            </select>

        </div>

    </div>

    <div class="col-xs-6 col-sm-12 col-lg-3 col-md-12">


            <select class="form-control select2" name="status">

                <option class="select-color" value="">{{tr('select_status')}}</option>

                <option class="select-color" value="{{SORT_BY_APPROVED}}" @if(Request::get('status') == SORT_BY_APPROVED && Request::get('status')!='' ) selected @endif>{{tr('approved')}}</option>

                <option class="select-color" value="{{SORT_BY_DECLINED}}" @if(Request::get('status') == SORT_BY_DECLINED && Request::get('status')!='' ) selected @endif>{{tr('declined')}}</option>

            </select>
        
        </div>

         <div class="col-xs-12 col-sm-12 col-lg-6 col-md-12">

            <div class="input-group">
               
                <input type="text" class="form-control" value="{{Request::get('search_key')}}" name="search_key"
                placeholder="{{tr('product_search_placeholder')}}"> <span class="input-group-btn">
                &nbsp

                <button type="submit" class="btn btn-default">
                   <a href=""><i class="fa fa-search" aria-hidden="true"></i></a>
                </button>
                
                <a href="{{route('admin.user_products.index')}}" class="btn btn-default reset-btn">
                    <i class="fa fa-eraser" aria-hidden="true"></i>
                </a>
                   
                </span>

            </div>
            
        </div>
    </div>
</form>
<br>