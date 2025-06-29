<section class="content">

    <!-- Basic Forms -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">@yield('title')</h3>
            <h6 class="box-subtitle"> </a></h6>

            <div class="box-tools pull-right">
                <a href="{{route('admin.sub_admin.index') }}" class="btn btn-primary"><i class="ft-eye icon-left"></i>{{ tr('view_sub_admins') }}</a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col">
                    <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.sub_admin.save') }}" method="POST" enctype="multipart/form-data" role="form">

                        @csrf

                        <div class="form-body">

                            <div class="row">

                                <input type="hidden" name="sub_admin_id" id="sub_admin_id" value="{{ $sub_admin->id}}">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ tr('name') }}*</label>
                                        <input type="text" id="name" name="name" class="form-control" placeholder="{{ tr('name') }}" value="{{old('name') ?: $sub_admin->name}}" required onkeydown="return alphaOnly(event);">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">{{tr('email')}}*</label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="E-mail" value="{{ $sub_admin->email ?: old('email') }}" required pattern="^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$" oninvalid="this.setCustomValidity(&quot;{{ tr('email_validate') }}&quot;)" oninput="this.setCustomValidity('')">
                                    </div>
                                </div>

                            </div>

                            @if(!$sub_admin->id)

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="">{{ tr('password') }} *</label>
                                        <p class="text-muted mt-0 mb-0">{{tr('password_notes')}}</p>
                                        <input type="password" minlength="6" required name="password" class="form-control" id="password" placeholder="{{ tr('password') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm-password" class="">{{ tr('confirm_password') }} *</label>
                                        <p class="text-muted mt-0 mb-0">{{tr('confirm_password_notes')}}</p>
                                        <input type="password" minlength="6" required name="password_confirmation" class="form-control" id="confirm-password" placeholder="{{ tr('confirm_password') }}">
                                    </div>
                                </div>

                            </div>

                            @endif

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label>{{ tr('select_picture') }}</label>
                                        <p class="text-muted mt-0 mb-0">{{tr('image_validate')}}</p>

                                        <input type="file" class="form-control" name="picture" accept="image/*">

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="form-actions">

                            <div class="pull-right">

                                <button type="reset" class="btn btn-warning mr-1">
                                    <i class="ft-x"></i> {{ tr('reset') }}
                                </button>

                                <button type="submit" class="btn btn-primary" @if(Setting::get('is_demo_control_enabled')==YES) disabled @endif>{{ tr('submit') }}</button>

                            </div>

                            <div class="clearfix"></div>

                        </div>

                    </form>

                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

</section>
<!-- /.content