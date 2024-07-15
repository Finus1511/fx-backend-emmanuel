@extends('layouts.admin') 

@section('content-header', tr('personalize_requests'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.personalized_requests.index' )}}">{{tr('personalize_requests')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_personalize_requests') }}</span>
    </li>
           
@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_personalize_requests') }}</h4>
                    
                </div>

                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>{{tr('personalize_requests_notes')}}</li>
                            </ul>
                        <p></p>
                    </div>
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">

                        @include('admin.personalized_requests._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('unique_id')}}</th>
                                    <th>{{tr('sender')}}</th>
                                    <th>{{tr('reciever')}}</th>
                                    <th>{{tr('type')}}</th>
                                    <th>{{tr('product_type')}}</th>
                                    <th>{{tr('amount')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>&nbsp;&nbsp;{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>


                                @foreach($personalized_requests as $i => $personalized_request)

                                    <tr>
                                        <td>{{$i+$personalized_requests->firstItem()}}</td>

                                        <td class="text-capitalize">{{$personalized_request->unique_id ?:  tr('na')}}</td>

                                        <td><a href="{{route('admin.users.view', ['user_id' => $personalized_request->sender_id])}}">{{$personalized_request->sender->name ?? tr('n_a')}}</a>
                                        </td>

                                        <td><a href="{{route('admin.users.view', ['user_id' => $personalized_request->receiver_id])}}">{{$personalized_request->receiver->name ?? tr('n_a')}}</a>
                                        </td>

                                        <td class="text-capitalize">{{$personalized_request->type ?: tr('na')}}</td>

                                        <td class="text-capitalize">{{$personalized_request->product_type ? product_type_formatted($personalized_request->product_type) : tr('na')}}</td>

                                        <td class="text-capitalize">{{$personalized_request->amount ? formatted_amount($personalized_request->amount) : '0.00'}}</td>

                                        <td>
                                            <span class="{{personalized_status_badge_formatted($personalized_request->status)}}">{{personalize_status_formatted($personalized_request->status)}}</span> 
                                        </td>

                                        <td>  

                                            <div class="btn-group" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">

                                                    <a class="dropdown-item" href="{{route('admin.personalized_requests.view',['personalized_request_id' => $personalized_request->id])}}">
                                                        {{tr('view')}}
                                                    </a>
                                                    
                                                    @if($personalized_request->product_type == PRODUCT_TYPE_PHYSICAL)
                                                    <a class="dropdown-item" href="{{route('admin.personalized_requests.products',['personalized_request_id' => $personalized_request->id])}}">
                                                    {{tr('products')}}
                                                    </a>
                                                    @endif
                                                </div>
                                                 
                                            </div>
                                        

                                        </td>
                                    
                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $personalized_requests->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection