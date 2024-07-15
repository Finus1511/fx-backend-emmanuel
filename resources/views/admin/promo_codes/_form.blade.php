<section class="content">

    <div class="row flex-grow">

        <div class="col-12 grid-margin">

            <div class="card">

                @if(Setting::get('is_demo_control_enabled') == NO )

                    <form class="forms-sample" action="{{ route('admin.promo_codes.save') }}" method="POST" enctype="multipart/form-data" role="form">

                @else

                    <form class="forms-sample" role="form">

                @endif 

                @csrf
                    @if($promo_code)
                        <input type="hidden" name="promo_code_id" id="promo_code_id" value="{{$promo_code->id}}">
                    @endif
                    <div class="card-header bg-card-header">

                        <h4 class="">@yield('title')</h4>


                        <div class="heading-elements">
                            <a class="btn btn-primary" href="{{route('admin.promo_codes.index')}}">
                                <i class="ft-user icon-left"></i> {{tr('view_promo_codes')}}
                            </a>
                        </div>
                        
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="title"> {{ tr('platform') }} <span class="admin-required">*</span></label>

                                <select id ="platform" name="platform" class="form-control select2" required>
                                    <option value="{{ ALL_PAYMENTS }}" {{ $promo_code->platform == ALL_PAYMENTS ?'selected="selected"':'' }}>{{ tr('all_payments') }}</option>
                                    <option value="{{ SUBSCRIPTION_PAYMENTS }}" {{ $promo_code->platform == SUBSCRIPTION_PAYMENTS ?'selected="selected"':'' }}>{{ tr('subscription_payments') }}</option>
                                    <option value="{{ USER_TIPS }}" {{ $promo_code->platform == USER_TIPS ?'selected="selected"':'' }}>{{ tr('user_tips') }}</option>
                                    <option value="{{ POST_PAYMENTS }}" {{ $promo_code->platform == POST_PAYMENTS ?'selected="selected"':'' }}>{{ tr('post_payments') }}</option>
                                    <option value="{{ VIDEO_CALL_PAYMENTS }}" {{ $promo_code->platform == VIDEO_CALL_PAYMENTS ?'selected="selected"':'' }}>{{ tr('video_call_payments') }}</option>
                                    <option value="{{ AUDIO_CALL_PAYMENTS }}" {{ $promo_code->platform == AUDIO_CALL_PAYMENTS ?'selected="selected"':'' }}>{{ tr('audio_call_payments') }}</option>
                                    <option value="{{ CHAT_ASSET_PAYMENTS }}" {{ $promo_code->platform == CHAT_ASSET_PAYMENTS ?'selected="selected"':'' }}>{{ tr('chat_asset_payments') }}</option>
                                    <option value="{{ ORDER_PAYMENTS }}" {{ $promo_code->platform == ORDER_PAYMENTS ?'selected="selected"':'' }}>{{ tr('order_payments') }}</option>
                                    <option value="{{ LIVE_VIDEO_PAYMENTS }}" {{ $promo_code->platform == LIVE_VIDEO_PAYMENTS ?'selected="selected"':'' }}>{{ tr('live_video_payments') }}</option>
                                </select>

                            </div>

                            <div class="form-group col-md-6">

                                <label for="promo_code">{{ tr('promo_code') }} <span class="admin-required">*</span></label>
                                
                                <input type="text" class="form-control" name="promo_code" placeholder="{{ tr('promo_code') }}" value="{{ old('promo_code') ?: $promo_code->promo_code}}" required pattern="[A-Z0-9]{1,10}"><p class="help-block"> {{ tr('note') }} : {{ tr('coupon_code_note') }}</p>

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="radius">{{ tr('amount_type') }} <span class="admin-required">*</span></label>

                                <select onchange="checkType(this.value);" id ="amount_type" name="amount_type" class="form-control select2" required>
                                    <option value="{{ PERCENTAGE }}" {{ $promo_code->amount_type == 0 ?'selected="selected"':'' }}>{{ tr('percentage_amount') }}</option>
                                    <option value="{{ ABSOULTE }}" {{ $promo_code->amount_type == 1 ?'selected="selected"':'' }}>{{ tr('absoulte_amount') }}</option>
                                </select>
                            </div>  

                            <div class="form-group col-md-6">

                                <label class="percentage_amount" style="display: block;">{{tr('percentage_amount')}}(in %) <span class="admin-required">*</span></label>

                                <label class="absoulte_amount" style="display: none;">{{tr('absoulte_amount')}} <span class="admin-required">*</span></label>

                                <input type="number" min="1" max="100" pattern="[0-9]{6,13}" class="form-control" id="amount" name="amount" placeholder="{{ tr('amount') }}" value="{{ old('amount') ?: $promo_code->amount}}" required>

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="no_of_users_limit">{{ tr('no_of_users_limit') }} <span class="admin-required">*</span></label>

                                <input type="number" min="1" pattern="[0-9]" name="no_of_users_limit" class="form-control" placeholder="{{ tr('no_of_users_limit') }}" value="{{old('no_of_users_limit') ?: $promo_code->no_of_users_limit}}" required title="{{ tr('no_of_users_limit_notes') }}">
                            </div>  

                            <div class="form-group col-md-6">

                                <label>{{tr('per_users_limit')}} <span class="admin-required">*</span></label>

                                <input type="number" min="1" pattern="[0-9]" name="per_users_limit" class="form-control" placeholder="{{ tr('per_users_limit') }}" value="{{old('per_users_limit') ?: $promo_code->per_users_limit }}" required title="{{ tr('per_users_limit_notes') }}">

                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="start_date">{{ tr('start_date') }} <span class="admin-required">*</span></label>

                                <input type="text" id="start_date" name="start_date"  class="form-control datetimepicker" placeholder="{{ tr('start_date') }}" value="{{ old('start_date') ?: $promo_code->start_date }}" required>
                            </div> 

                            <div class="form-group col-md-6">

                                <label for="expiry_date">{{ tr('expiry_date') }} <span class="admin-required">*</span></label>

                                <input type="text" id="expiry_date" name="expiry_date"  class="form-control datetimepicker" placeholder="{{ tr('expiry_date') }}" value="{{ old('expiry_date') ?: $promo_code->expiry_date }}" required>
                            </div>  

                        </div>

                        <div class="row">

                            <div class="form-group col-md-6">

                                <label for="radius">{{ tr('content_creator_label') }} <span class="admin-required">*</span></label>

                                <select id ="user_id" name="user_id" class="form-control select2" required>
                                    <option value="">{{ tr('select_content_creator') }}</option>

                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $promo_code->user_id == $user->id ?'selected="selected"':'' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="form-actions">

                        <div class="pull-right">

                            <button type="reset" class="btn btn-warning mr-1">
                                <i class="ft-x"></i> {{ tr('reset') }}
                            </button>

                            <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled')==YES) disabled @endif><i class="fa fa-check-square-o"></i>{{ tr('submit') }}</button>

                        </div>

                        <div class="clearfix"></div>
                    
                    </div><br>



                </form>

            </div>

        </div>

    </div>
    
</section>

@section('scripts')

<script type="text/javascript">

    function checkType(val){
        
        if(val == 0){
            $('.percentage_amount').css("display", "block");
            $('.absoulte_amount').css("display", "none");
            $("#amount").attr("max", 100);
        }else{
            $('.percentage_amount').css("display", "none");
            $('.absoulte_amount').css("display", "block");
            $("#amount").removeAttr("max");       
        }
        
    }

</script>

@endsection

