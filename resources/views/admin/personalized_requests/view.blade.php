@extends('layouts.admin') 

@section('title', tr('view_personalize_requests'))

@section('content-header',tr('personalize_requests'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.personalized_requests.index')}}">{{tr('personalize_requests')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_personalize_requests')}}</span>
    </li>
           
@endsection  

@section('content')

<section class="content">
    
    <div class="row match-height">
    
        <div class="col-lg-12 col-md-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{tr('view_personalize_requests')}} - {{$personalized_request->unique_id ?: tr('na')}}
                    </h4>
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">


                        <div class="card-group">

                            <div class="card card-margin-btm-zero">

                                <div class="card-body">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                        <tbody>

                                            <tr>
                                                <td>{{ tr('unique_id') }}</td>
                                                <td>{{$personalized_request->unique_id ?: tr('n_a')}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('sender') }}</td>
                                                <td><a href="{{route('admin.users.view', ['user_id' => $personalized_request->sender_id])}}">{{$personalized_request->sender->name ?? tr('n_a')}}</a></td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('sender') }}</td>
                                                <td><a href="{{route('admin.users.view', ['user_id' => $personalized_request->receiver_id])}}">{{$personalized_request->receiver->name ?? tr('n_a')}}</a></td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('type') }}</td>
                                                <td>
                                                   {{$personalized_request->type ?: tr('na')}}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('created_at') }}</td>
                                                <td>{{$personalized_request->created_at ? common_date($personalized_request->created_at,Auth::guard('admin')->user()->timezone) : tr('na')}}</td>
                                            </tr>

                                        </tbody>

                                    </table>
                                    
                                </div>

                            </div>
                          
                            <div class="card card-margin-btm-zero">

                                <div class="card-body">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                        <tbody>

                                            <tr>
                                                <td>{{ tr('product_type') }}</td>
                                                <td>
                                                   {{$personalized_request->product_type ? product_type_formatted($personalized_request->product_type) : tr('na')}}
                                                </td>
                                            </tr>

                                             <tr>
                                                <td>{{ tr('amount') }}</td>
                                                    <td class="text-capitalize">{{$personalized_request->amount ? formatted_amount($personalized_request->amount) : '0:00'}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('status') }}</td>
                                                <td>
                                                    <span class="{{personalized_status_badge_formatted($personalized_request->status)}}">{{personalize_status_formatted($personalized_request->status)}}</span> 
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('updated_at') }}</td>
                                                <td>{{$personalized_request->updated_at ?  ? common_date($personalized_request->updated_at,Auth::guard('admin')->user()->timezone) : tr('na')}}</td>
                                            </tr>

                                        </tbody>

                                    </table>

                                </div>
                                <!-- Card content -->

                            </div>

                            <!-- Card -->

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
