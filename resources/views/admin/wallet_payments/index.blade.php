@extends('layouts.admin') 

@section('title', tr('wallet_payments')) 

@section('content-header', tr('wallet_payments')) 

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.wallet_payments.index')}}">{{ tr('wallet_payments') }}</a>
</li>

<li class="breadcrumb-item ">{{tr('wallet_payments')}}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card user-wallet-sec">
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('wallet_payments')}}</h4>
                     <div class="heading-elements">

                        <a href="{{ route('admin.wallet_payments.excel',['user_id'=>Request::get('user_id'),'to_user_id'=>Request::get('to_user_id'),'status'=>Request::get('status'),'amount_type'=>Request::get('amount_type'),'payment_type'=>Request::get('payment_type'),'user_id'=>Request::get('user_id'),'received_from_user_id'=>Request::get('received_from_user_id'),'search_key'=>Request::get('search_key'),'file_format'=>'.csv']) }}" class="btn btn-primary resp-mrg-btm-xs">{{tr('export_to_csv')}}</a>

                        <a href="{{ route('admin.wallet_payments.excel',['user_id'=>Request::get('user_id'),'to_user_id'=>Request::get('to_user_id'),'status'=>Request::get('status'),'amount_type'=>Request::get('amount_type'),'payment_type'=>Request::get('payment_type'),'user_id'=>Request::get('user_id'),'received_from_user_id'=>Request::get('received_from_user_id'),'search_key'=>Request::get('search_key'),'file_format'=>'.xls']) }}" class="btn btn-primary resp-mrg-btm-xs">{{tr('export_to_xls')}}</a>

                        <a href="{{ route('admin.wallet_payments.excel',['user_id'=>Request::get('user_id'),'to_user_id'=>Request::get('to_user_id'),'status'=>Request::get('status'),'amount_type'=>Request::get('amount_type'),'payment_type'=>Request::get('payment_type'),'user_id'=>Request::get('user_id'),'received_from_user_id'=>Request::get('received_from_user_id'),'search_key'=>Request::get('search_key'),'file_format'=>'.xlsx']) }}" class="btn btn-primary resp-mrg-btm-xs">{{tr('export_to_xlsx')}}</a>
                        
                    </div>
                    
                </div>


                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('wallet_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">

                        @include('admin.wallet_payments._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('from_user') }}</th>
                                    <th>{{ tr('to_user') }}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('payment_type') }}</th>
                                    <th>{{ tr('amount_type') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($wallet_payments as $i => $wallet_payment)
                                <tr>
                                    <td>{{ $i+$wallet_payments->firstItem() }}</td>

                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $wallet_payment->user_id] )  }}">
                                        {{ $wallet_payment->ReceivedFromUser->name ?? tr('na')}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{route('admin.users.view' , ['user_id' => $wallet_payment->user_id] )  }}">
                                        {{ $wallet_payment->toUser->name ?? tr('na')}}
                                        </a>
                                    </td>

                                    <td>{{ $wallet_payment->payment_id ?: tr('na')}}
                                        <br><br>
                                         <span class="text-gray"> Paid Date: {{common_date($wallet_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</span>                                  
                                     </td>

                                     <td>{{formatted_amount($wallet_payment->paid_amount)}}
                                            <br><br>
                                            <span class="text-gray"> Admin: {{$wallet_payment->admin_amount_formatted ?: 0.00}}</span>
                                            <span class="text-gray"> User: {{$wallet_payment->user_amount_formatted ?: 0.00}}</span>
                                    </td>

                                    <td>
                                        {{ $wallet_payment->payment_type ?: tr('na')}}
                                    </td>

                                    <td>
                                        {{ $wallet_payment->amount_type ?: tr('na')}}
                                    </td>

                                    <td>
                                        {{ $wallet_payment->payment_mode ?: tr('na')}}
                                    </td>
                                     <td>

                                        @if($wallet_payment->status == PAID)

                                        <span class="badge bg-success">{{ tr('paid') }} </span>

                                        @else

                                        <span class="badge bg-danger">{{ tr('not_paid') }} </span>

                                        @endif

                                    </td>

                                    <td>
                                    
                                        <a class="btn btn-info" href="{{route('admin.wallet_payments.view', ['user_wallet_payment_id' => $wallet_payment->id])}}">&nbsp;{{ tr('view') }}</a> 

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $wallet_payments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection