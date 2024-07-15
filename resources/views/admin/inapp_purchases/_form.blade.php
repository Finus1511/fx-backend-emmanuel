<section class="content">
           
    <div class="box">    
        <div class="box-header with-border">

            <h3 class="box-title">{{$inapp_purchase->id ? tr('edit_inapp_purchase') : tr('add_inapp_purchase') }}</h3>
            <h6 class="box-subtitle"></h6>

            <div class="box-tools pull-right">
                <a href="{{route('admin.inapp_purchases.index') }}" class="btn btn-primary"><i class="ft-file"></i>{{ tr('view_inapp_purchases') }}</a>
            </div>

        </div>
         <div class="box-body">
            <div class="callout bg-pale-secondary">
                <h4>{{tr('notes')}}</h4>
                <p>
                    </p><ul>
                        <li>
                            {{tr('add_inapp_purchase_notes')}}
                        </li>
                    </ul>
                <p></p>
            </div>
        </div>
        <div class="card-content collapse show">

            <div class="card-body">
            
                <div class="card-text">

                </div>

                <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.inapp_purchases.save') }}" method="POST" enctype="multipart/form-data" role="form">
                   
                    @csrf
                  
                    <div class="form-body">

                        <div class="row">

                            <input type="hidden" name="inapp_purchase_id" id="inapp_purchase_id" value="{{ $inapp_purchase->id}}">

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="reference_name">{{ tr('reference_name') }}*</label>
                                    <input type="text" id="reference_name" name="reference_name" class="form-control" placeholder="{{ tr('reference_name') }}" value="{{ $inapp_purchase->reference_name ?: old('reference_name') }}" required>
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="amount">{{ tr('amount') }}*</label>
                                    <input type="text" id="amount" name="amount" class="form-control" placeholder="{{ tr('amount') }}" value="{{ $inapp_purchase->amount ?: old('amount') }}" required>
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="product_id">{{ tr('product_id') }}*</label>
                                    <input type="text" id="product_id" name="product_id" class="form-control" placeholder="{{ tr('product_id') }}" value="{{ $inapp_purchase->product_id ?: old('product_id') }}" required>
                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="apple_id">{{ tr('apple_id') }}*</label>
                                    <input type="text" id="apple_id" name="apple_id" class="form-control" placeholder="{{ tr('apple_id') }}" value="{{ $inapp_purchase->apple_id ?: old('apple_id') }}" required>
                                </div>

                            </div>

                        </div>

                    </div><br>
                  
                    <div class="form-actions">

                        <div class="pull-right">
                        
                            <button type="reset" class="btn btn-warning mr-1">
                                <i class="ft-x"></i> {{ tr('reset') }} 
                            </button>

                            <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif ><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>
                        
                        </div>

                        <div class="clearfix"></div>

                    </div>

                </form>
                
            </div>
        
        </div>
    </div>

</section>

