@extends('layouts.admin') 
@section('title', tr('revenue_management')) 
@section('content-header', tr('payments')) 
@section('breadcrumb')
    
<li class="breadcrumb-item"><a href="">{{ tr('payments') }}</a></li>
<li class="breadcrumb-item active">{{ tr('message_payments') }}</li>
@endsection 
@section('content')
<section class="content">
    <div class="row ">
        <div class="col-12 ">
            <div class="card post-payment-sec">
                <div class="card-header border-bottom border-gray">
                    <h4 class="card-title">{{ tr('message_payments') }} 
                   </h4>
                </div>
                 <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('message_payments')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">
                <div class="box-body">
                    @include('admin.users.chat._message_payments_search')
                    
                    <div class="table-responsive">                        
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('from_user') }}</th>
                                    <th>{{ tr('to_user')}}</th>
                                    <th >{{ tr('payment_id') }}</th>
                                    <th>{{ tr('paid_amount') }}</th>
                                    <th>{{ tr('admin_amount') }}</th>
                                    <th>{{ tr('user_amount') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>
                                @foreach($chat_message_payments as $i => $message_payment)
                                <tr>
                                    <td>{{ $i+$chat_message_payments->firstItem() }}</td>
                                    <td>
                                        <a href="{{$message_payment->fromUser->name ? route('admin.users.view',['user_id' => $message_payment->user_id] ?: 0) : '#'}}" class="{{ $message_payment->fromUser->name ? '' : 'link-disabled'}}">
                                        {{ $message_payment->fromUser->name ?? tr('na') }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{$message_payment->toUser->name ? route('admin.users.view',['user_id' => $message_payment->to_user_id]  ?: 0) : '#'}}" class="{{ $message_payment->toUser->name ? '' : 'link-disabled' }}">
                                        {{ $message_payment->toUser->name ?? tr('na') }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $message_payment->payment_id  ?: tr('na')}}
                                        <br>
                                        <br>
                                        <span class="text-gray">{{tr('date')}}: {{common_date($message_payment->paid_date, Auth::user()->timezone)}}</span>
                                    </td>
                                    <td>
                                        {{$message_payment->amount ? formatted_amount($message_payment->amount) : 0.00}}
                                    </td>
                                    <td>
                                        {{ $message_payment->admin_amount ? formatted_amount($message_payment->admin_amount) : 0.00}}
                                    </td>
                                    <td>
                                        {{ $message_payment->user_amount ? formatted_amount($message_payment->user_amount) : 0.00}}
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                        {{ $message_payment->payment_mode ?: tr('na')}}
                                        </span>
                                    </td>
                                        
                                    <td class="flex payments-action-left">
                                       <a href="{{route('admin.chat_message_payments.view',['chat_message_payment_id' => $message_payment->id])}}" class="btn btn-primary">{{tr('view')}}</a>&nbsp;
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pull-right resp-float-unset" id="paglink">{{ $chat_message_payments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>
                    </div>
                </div>
              </div>
           </div>
        </div>
    </div>
</section>
@endsection
@section('styles')
<style>
    .table th, .table td {
    padding: 0.75rem 1.5rem !important;
}
</style>
@endsection