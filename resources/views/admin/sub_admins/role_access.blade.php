@extends('layouts.admin')

@section('title', tr('sub_admins'))

@section('content-header', tr('sub_admins'))

@section('breadcrumb')


<li class="breadcrumb-item"><a href="{{route('admin.sub_admin.index')}}">{{tr('sub_admins')}}</a>
</li>
<li class="breadcrumb-item active">{{tr('view_sub_admin')}}</a>
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-xl-12 col-lg-12">

            <div class="card sub_admin-profile-view-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('view_sub_admins')}}</h4>

                </div>

                <div class="card-content">

                    <div class="sub_admin-view-padding">
                        <form class="form-horizontal" action="{{ (Setting::get('is_demo_control_enabled') == YES) ? '#' : route('admin.sub_admin.update_role_access') }}" method="POST" enctype="multipart/form-data" role="form">

                        @csrf

                        <input type="hidden" name="sub_admin_id" id="sub_admin_id" value="{{ $sub_admin_id ?? 0}}">

                        <div class="row"> 

                            <div class=" col-xl-6 col-lg-6 col-md-12">
                                <div class="table-responsive">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                        <tr >
                                            <th style="border-top: 0">{{tr('account_management')}}</th>
                                            <td style="border-top: 0">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="account_management" class="faChkRnd chk-box-inner-left" id="account_management" value="1" {{ in_array('account_management', $permissions ?? []) ? 'checked' : '' }}>
                                                    <label for="account_management"></label>                
                                                </div>
                                            </td>
                                        </tr>
                                        <tr >
                                            <th style="border-top: 0">{{tr('post_management')}}</th>
                                            <td style="border-top: 0">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="post_management" class="faChkRnd chk-box-inner-left" id="post_management" value="1" {{ in_array('post_management', $permissions ?? []) ? 'checked' : '' }}>
                                                    <label for="post_management"></label>                
                                                </div>
                                            </td>
                                        </tr>
                                        <tr >
                                            <th style="border-top: 0">{{tr('products_management')}}</th>
                                            <td style="border-top: 0">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="products_management" class="faChkRnd chk-box-inner-left" id="products_management" value="1" {{ in_array('products_management', $permissions ?? []) ? 'checked' : '' }}>
                                                    <label for="products_management"></label>                
                                                </div>
                                            </td>
                                        </tr>
                                        <tr >
                                            <th style="border-top: 0">{{tr('video_management')}}</th>
                                            <td style="border-top: 0">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="video_management" class="faChkRnd chk-box-inner-left" id="video_management" value="1" {{ in_array('video_management', $permissions ?? []) ? 'checked' : '' }}>
                                                    <label for="video_management"></label>                
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class=" col-xl-6 col-lg-6 col-md-12">
                                <div class="table-responsive">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                        <tr >
                                            <th style="border-top: 0">{{tr('revenue_management')}}</th>
                                            <td style="border-top: 0">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="revenue_management" class="faChkRnd chk-box-inner-left" id="revenue_management" value="1" {{ in_array('revenue_management', $permissions ?? []) ? 'checked' : '' }}>
                                                    <label for="revenue_management"></label>                
                                                </div>
                                            </td>
                                        </tr>
                                        <tr >
                                            <th style="border-top: 0">{{tr('personalize_requests')}}</th>
                                            <td style="border-top: 0">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="personalize_requests" class="faChkRnd chk-box-inner-left" id="personalize_requests" value="1" {{ in_array('personalize_requests', $permissions ?? []) ? 'checked' : '' }}>
                                                    <label for="personalize_requests"></label>                
                                                </div>
                                            </td>
                                        </tr>
                                        <tr >
                                            <th style="border-top: 0">{{tr('lookups_management')}}</th>
                                            <td style="border-top: 0">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="lookups_management" class="faChkRnd chk-box-inner-left" id="lookups_management" value="1" {{ in_array('lookups_management', $permissions ?? []) ? 'checked' : '' }}>
                                                    <label for="lookups_management"></label>                
                                                </div>
                                            </td>
                                        </tr>
                                        <tr >
                                            <th style="border-top: 0">{{tr('setting_management')}}</th>
                                            <td style="border-top: 0">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="setting_management" class="faChkRnd chk-box-inner-left" id="setting_management" value="1" {{ in_array('setting_management', $permissions ?? []) ? 'checked' : '' }}>
                                                    <label for="setting_management"></label>                
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
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

                </div>

            </div>

        </div>

    </div>

</section>


@endsection
