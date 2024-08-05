@extends('layouts.admin')
@section('content-header', tr('chat_message_payment'))
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.chat_message_payments.index') }}">{{ tr('payments') }}</a>
</li>
<li class="breadcrumb-item active" aria-current="page">
    <span>{{ tr('view_chat_message_payment') }}</span>
</li>
@endsection
@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom border-gray">
                    <h4 class="card-title">{{ tr('view_chat_message_payment') }}</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered table-striped tab-content">
                                    <tbody>
                                        <tr>
                                            <td>{{ tr('unique_id')}} </td>
                                            <td class="text-uppercase">{{ $chat_message_payment->unique_id ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('from_username')}} </td>
                                            <td>
                                                <a href="{{$chat_message_payment->fromUser->name ? route('admin.users.view',['user_id' => $chat_message_payment->user_id] ?: 0) : '#'}}" class="{{ $chat_message_payment->fromUser->name ? '' : 'link-disabled'}}">
                                                    {{ $chat_message_payment->fromUser->name ?? tr('not_available')}}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('to_username')}} </td>
                                            <td>
                                                <a href="{{$chat_message_payment->toUser->name ? route('admin.users.view',['user_id' => $chat_message_payment->to_user_id]  ?: 0) : '#'}}" class="{{ $chat_message_payment->toUser->name ? '' : 'link-disabled' }}">
                                                    {{ $chat_message_payment->toUser->name ?? tr('not_available')}}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('payment_id')}} </td>
                                            <td>{{ $chat_message_payment->payment_id ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('paid_amount')}} </td>
                                            <td>{{ $chat_message_payment->amount ? formatted_amount($chat_message_payment->amount): 0.00}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('admin_amount')}} </td>
                                            <td>{{ $chat_message_payment->admin_amount ? formatted_amount($chat_message_payment->admin_amount): 0.00}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered table-striped tab-content">
                                    <tbody>
                                        <tr>
                                            <td>{{ tr('user_amount')}} </td>
                                            <td>{{ $chat_message_payment->user_amount ? formatted_amount($chat_message_payment->user_amount): 0.00}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('payment_mode')}} </td>
                                            <td>{{ $chat_message_payment->payment_mode ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('paid_date') }}</td>
                                            <td>{{common_date($chat_message_payment->paid_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('expiry_date') }}</td>
                                            <td>{{common_date($chat_message_payment->expiry_date , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('created_at') }}</td>
                                            <td>{{common_date($chat_message_payment->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('updated_at') }}</td>
                                            <td>{{common_date($chat_message_payment->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
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