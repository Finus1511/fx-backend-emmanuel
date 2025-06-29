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

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">

                                <div class="card profile-with-cover">

                                    &emsp;&emsp;{{tr('profile')}}

                                    <div class="media profil-cover-details w-100">

                                        <div class="media-left pl-2 pt-2">

                                            <a class="profile-image">
                                                <img src="{{ $sub_admin->picture}}" alt="{{ $sub_admin->name}}" class="img-thumbnail img-fluid img-border height-100" alt="Card image">
                                            </a>

                                        </div>
                                       
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="col-md-6">
                                <br>
                                <div class="row">
                                    @if(Setting::get('is_demo_control_enabled') == YES)
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-social btn-dropbox mr-1 mb-1" href="javascript:void(0)">
                                            <i class="fa fa-edit"></i>{{tr('edit')}}
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-social btn-twitter mr-1 mb-1" href="javascript:void(0)">
                                            <i class="fa fa-delete"></i>
                                            {{tr('delete')}}
                                        </a>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-social btn-dropbox mr-1 mb-1 " href="{{route('admin.sub_admin.edit', ['sub_admin_id'=>$sub_admin->id] )}}">
                                            <i class="fa fa-edit"></i>
                                            {{tr('edit')}}
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-social btn-google mr-1 mb-1" onclick="return confirm(&quot;{{tr('sub_admin_delete_confirmation' , $sub_admin->name)}}&quot;);" href="{{route('admin.sub_admin.delete', ['sub_admin_id'=> $sub_admin->id] )}}">
                                            <i class="fa fa-trash"></i>
                                            {{tr('delete')}}
                                        </a>
                                    </div>
                                    @endif

                                </div>  
                                
                                <div class="row">
                                     
                                     <div class="col-md-6">
                                         @if($sub_admin->status == APPROVED)
                                            <a class="btn btn-block btn-social btn-foursquare mr-1 mb-1" href="{{route('admin.sub_admin.status' ,['sub_admin_id'=> $sub_admin->id] )}}" onclick="return confirm(&quot;{{$sub_admin->name}} - {{tr('sub_admin_decline_confirmation')}}&quot;);">
                                                <i class="fa fa-user-times"></i>
                                                {{tr('decline')}}
                                            </a>
                                        @else

                                            <a class="btn btn-block btn-social btn-twitter mr-1 mb-1" href="{{route('admin.sub_admin.status' , ['sub_admin_id'=> $sub_admin->id] )}}">
                                                <i class="fa fa-user-check"></i>
                                                {{tr('approve')}}
                                            </a>
                                        @endif
                                    
                                    </div>
                                    <div class="col-md-6">

                                        <a href="{{route('admin.sub_admin.role_access', ['sub_admin_id' => $sub_admin->id])}}" class="btn btn-block btn-social btn-bitbucket mr-1 mb-2">
                                            <i class="fa fa-history"></i>
                                            {{tr('role_access')}}</a>

                                        </div>
                                </div>   
                            </div>
                        </div>
                       
                    </div>

                    <hr>
                    <div class="sub_admin-view-padding">
                        <div class="row"> 

                            <div class=" col-xl-6 col-lg-6 col-md-12">
                                <div class="table-responsive">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                                        <tr >
                                            <th style="border-top: 0">{{tr('name')}}</th>
                                            <td style="border-top: 0">{{$sub_admin->name}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('email')}}</th>
                                            <td>{{$sub_admin->email}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('status')}}</th>
                                            <td>
                                                @if($sub_admin->status == APPROVED)

                                                <span class="badge badge-success">{{tr('approved')}}</span>

                                                @else
                                                <span class="badge badge-danger">{{tr('declined')}}</span>

                                                @endif
                                            </td>
                                        </tr>                            

                                        <tr>
                                            <th>{{tr('created_at')}} </th>
                                            <td>{{common_date($sub_admin->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <th>{{tr('updated_at')}} </th>
                                            <td>{{common_date($sub_admin->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                    
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


@endsection

@section('scripts')

@endsection