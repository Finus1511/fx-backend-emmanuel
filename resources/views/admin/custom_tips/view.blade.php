@extends('layouts.admin')

@section('title', tr('view_custom_tips'))

@section('content-header', tr('custom_tips'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.custom_tips.index')}}">{{tr('custom_tips')}}</a>
    </li>
    <li class="breadcrumb-item active">{{tr('view_custom_tips')}}</a>
    </li>

@endsection

@section('content')

<section class="content">

        <div class="row">

            <div class="col-xl-12 col-lg-12">

                <div class="card user-profile-view-sec">

                    <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{tr('view_custom_tip')}}-{{$custom_tip->title ?: tr('n_a')}}</h4>

                    </div>

                    <div class="card-content">

                        <div class="col-md-12">

                            <div class="card profile-with-cover">

                                <div class="media profil-cover-details w-100">

                                    <div class="media-left pl-2 pt-2">

                                        <a class="profile-image">
                                             <img src="{{ $custom_tip->picture ?: asset('placeholder.jpg')}}" alt="{{ $custom_tip->title}}" class="img-thumbnail img-fluid img-border height-100"
                                        alt="Card image">
                                        </a>

                                    </div>

                                   
                                </div>

                            
                            </div>
                        
                        </div>

                        <hr>
                        <div class="user-view-padding">
                            <div class="row"> 

                                <div class=" col-xl-6 col-lg-6 col-md-12">
                                    <div class="table-responsive">

                                        <table class="table table-xl mb-0">
                                            <tr >
                                                <th>{{tr('title')}}</th>
                                                <td>{{$custom_tip->title ?: tr('n_a')}}</td>
                                            </tr>

                                            <tr >
                                                <th>{{tr('amount')}}</th>
                                                <td>{{formatted_amount($custom_tip->amount ?: 0)}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('status')}}</th>
                                                <td>
                                                    @if($custom_tip->status == APPROVED) 

                                                        <span class="badge badge-success">{{tr('approved')}}</span>

                                                    @else
                                                        <span class="badge badge-danger">{{tr('declined')}}</span>

                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('created_at')}} </th>
                                                <td>{{common_date($custom_tip->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('updated_at')}} </th>
                                                <td>{{common_date($custom_tip->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr> 

                                            <tr>
                                                <th>{{tr('description')}}</th>
                                                <td>{{$custom_tip->description ?: tr('n_a')}}</td>
                                            </tr> 
                                        </table>

                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-12">

                                    <div class="px-2 resp-marg-top-xs">

                                        <div class="card-title">{{tr('action')}}</div>

                                        <div class="row">

                                            @if(Setting::get('is_demo_control_enabled') == YES)

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-secondary btn-block btn-min-width mr-1 mb-1 " href="javascript:void(0)"> &nbsp;{{tr('edit')}}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{tr('delete')}}</a>

                                            </div>


                                            @else

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.custom_tips.edit', ['custom_tip_id'=>$custom_tip->id] )}}"> &nbsp;{{tr('edit')}}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('custom_tip_delete_confirmation' , $custom_tip->name)}}&quot;);" href="{{route('admin.custom_tips.delete', ['custom_tip_id'=> $custom_tip->id] )}}">&nbsp;{{tr('delete')}}</a>

                                            </div>

                                            @endif

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                 @if($custom_tip->status == APPROVED)
                                                     <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.custom_tips.status' ,['custom_tip_id'=> $custom_tip->id] )}}" onclick="return confirm(&quot;{{$custom_tip->name}} - {{tr('custom_tip_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                                                @else

                                                    <a  class="btn btn-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.custom_tips.status' , ['custom_tip_id'=> $custom_tip->id] )}}">&nbsp;{{tr('approve')}}</a> 
                                                @endif

                                            </div>

                                        </div>

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
