@extends('layouts.admin') 

@section('title', tr('wallet_payments')) 

@section('content-header', tr('wallet_payments')) 

@section('breadcrumb')


    
<li class="breadcrumb-item">
    <a href="{{route('admin.wallet_payments.index')}}">{{ tr('wallet_payments') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('wallet_payment') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('wallet_payment') }}</h4>
                    
                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        <div class="row">
                            
                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                       
                                    <tbody>

                                        <tr>
                                            <td >{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $wallet_payment->unique_id ?: tr('na')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $wallet_payment->payment_id ?: tr('na')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $wallet_payment->payment_mode ?: tr('na')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('from_username')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $wallet_payment->received_from_user_id])}}">
                                                    {{ $wallet_payment->ReceivedFromUser->name ?? tr('na')}}</td>
                                                </a>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('to_username')}} </td>
                                            <td>
                                                <a href="{{ route('admin.users.view', ['user_id' => $wallet_payment->to_user_id])}}">
                                                {{ $wallet_payment->toUser->name ?? tr('na')}}
                                                </a>
                                            </td>
                                        </tr> 

                                        <tr>
                                            <td>{{ tr('payment_type')}} </td>
                                            <td>{{ $wallet_payment->payment_type ?: tr('na')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('amount_type')}} </td>
                                            <td>{{ $wallet_payment->amount_type ?: tr('na')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('requested_amount') }}</td>
                                            <td>{{formatted_amount($wallet_payment->requested_amount)}}</td>
                                        </tr> 
                                        <tr>
                                            <td>{{ tr('paid_amount') }}</td>
                                            <td>{{formatted_amount($wallet_payment->paid_amount)}}</td>
                                        </tr>
                                         <tr>
                                            <td>{{ tr('token') }}</td>
                                            <td>{{ formatted_amount($wallet_payment->token)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('currency') }}</td>
                                            <td>{{ $wallet_payment->currency ?: tr('na')}}</td>
                                        </tr>
                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">
                                
                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                       
                                    <tbody>
                                        <tr>
                                            <td>{{ tr('admin_amount') }}</td>
                                            <td>{{ $wallet_payment->admin_amount_formatted ?: 0.00}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('user_amount') }}</td>
                                            <td>{{ $wallet_payment->user_amount_formatted ?: 0.00}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('usage_type') }}</td>
                                            <td>{{ $wallet_payment->usage_type ?: tr('na')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($wallet_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('payment_status') }}</td>
                                            <td>
                                                @if($wallet_payment->status == PAID)

                                                    <span class="badge bg-success">{{tr('paid')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('not_paid')}}</span>

                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('is_admin_approved') }}</td>
                                            <td>
                                                @if($wallet_payment->is_admin_approved == YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('is_cancelled') }}</td>
                                            <td>
                                                @if($wallet_payment->is_cancelled ==YES)

                                                    <span class="badge bg-success">{{tr('yes')}}</span>

                                                @else 
                                                    <span class="badge bg-danger">{{tr('no')}}</span>

                                                @endif
                                            </td>
                                        </tr>

                                        @if($wallet_payment->is_cancelled ==YES)
                                        <tr>
                                            <td>{{ tr('cancelled_reason') }}</td>
                                            <td>{{ $wallet_payment->cancelled_reason ?: tr('na')}}</td>
                                        </tr>
                                        @endif

                                        <tr>
                                            <td>{{ tr('message') }}</td>
                                            <td>{{ $wallet_payment->message ?: tr('na')}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($wallet_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($wallet_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection