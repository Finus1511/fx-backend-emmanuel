<section class="content">
    
    <div class="row match-height">
    
        <div class="col-lg-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{$custom_tip->id ? tr('edit_custom_tip') : tr('add_custom_tip')}}</h4>

                    <div class="heading-elements">
                        <a href="{{route('admin.custom_tips.index') }}" class="btn btn-primary"><i class="ft-user icon-left"></i>{{ tr('view_custom_tips') }}</a>
                    </div>

                </div>

                <div class="card-content collapse show">

                    <div class="card-body">
                    
                        <div class="card-text">

                        </div>

                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.custom_tips.save') }}" method="POST" enctype="multipart/form-data" role="form">
                           
                            @csrf
                          
                            <div class="form-body">

                                <div class="row">

                                    <input type="hidden" name="custom_tip_id" id="custom_tip_id" value="{{ $custom_tip->id}}">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_name">{{ tr('custom_tip_name') }}*</label>
                                            <input type="text" id="title" name="title" class="form-control" placeholder="{{ tr('title') }}" value="{{ $custom_tip->title ?: old('title') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amount">{{ tr('amount') }}*</label>
                                            <input type="number" min="1" id="amount" name="amount" class="form-control" placeholder="{{ tr('amount') }}" value="{{ $custom_tip->amount ?: old('amount') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ tr('select_picture') }}</label>
                                            <input type="file" class="form-control"  id="picture" name="picture" accept="image/png,image/jpeg" src="{{ $custom_tip->picture ? $custom_tip->picture : asset('placeholder.png') }}">
                                        </div>
                                    </div>

                                </div>
                                
                            </div>

                            <div class="row">

                                <div class="col-md-12"> 

                                    <div class="form-group">

                                        <label for="description">{{tr('description')}}</label>

                                        <textarea id="summernote" rows="5" class="form-control" name="description" placeholder="{{ tr('description') }}">{{old('description') ?: $custom_tip->description}}</textarea>

                                    </div>

                                </div>

                            </div>
                          
                            <div class="form-actions">

                                <div class="pull-right">
                                
                                    <button type="reset" class="btn btn-warning mr-1">
                                        <i class="ft-x"></i> {{ tr('reset') }} 
                                    </button>

                                    <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled') == YES) disabled @endif >{{ tr('submit') }}</button>
                                
                                </div>

                                <div class="clearfix"></div>

                            </div>

                        </form>
                        
                    </div>
                
                </div>

            </div>
        
        </div>
    
    </div>

</section>

