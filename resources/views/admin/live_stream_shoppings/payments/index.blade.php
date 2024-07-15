@extends('layouts.admin')

@section('title', tr('live_stream_shopping_payments'))

@section('content-header', tr('live_stream_shopping_payments'))

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.live_stream_shoppings.index')}}">{{ tr('live_stream_shoppings') }}</a>
</li>

<li class="breadcrumb-item active">
    {{tr('live_stream_shopping_payments')}}
</li>

@endsection

@section('content')

<section class="content" style="height: 100vh;">

    <div class="row">

        <div class="col-12">

            <div class="card view-post-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{tr('lss_payments')}} 
                        @if(request('live_stream_shopping_id'))
                        - <a href="{{route('admin.live_stream_shoppings.view',['live_stream_shopping_id' => $live_stream_shopping->id])}}">{{$live_stream_shopping->title ?: tr('na')}}</a>@endif
                        @if(request('user_id'))
                          <a href="{{route('admin.users.view', ['user_id' =>$user->id])}}"> - {{$user->name ?: tr('na')}}</a>
                        @endif
                    </h4>

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">
                        @include('admin.live_stream_shoppings.payments._search')
                        <br>
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('title') }}</th>
                                    <th>{{ tr('user_name') }}</th>
                                    <th>{{ tr('payment_id') }}</th>
                                    <th>{{ tr('payment_mode') }}</th>
                                    <th>{{ tr('amount') }}</th>
                                    <th>{{ tr('currency') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('created_at') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($lss_payments as $i => $lss_payment)
                                <tr>

                                    <td>{{ $i+$lss_payments->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.live_stream_shoppings.view' , ['live_stream_shopping_id' => $lss_payment->live_stream_shopping_id] )  }}">
                                            {{ $lss_payment->lssStreamShopping->title ?? tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $lss_payment->user_id] )  }}">
                                            {{ $lss_payment->user->name ?? tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $lss_payment->payment_id ?: tr('n_a') }}
                                    </td>

                                    <td>
                                        {{ $lss_payment->payment_mode ?: tr('n_a') }}
                                    </td>

                                    <td>
                                        {{ $lss_payment->amount ? formatted_amount($lss_payment->amount) : 0}}
                                    </td>

                                    <td>
                                        {{ $lss_payment->currency ?: tr('n_a') }}
                                    </td>

                                    <td><label class="{{ $lss_payment->status == PAID ? 'label label-success' : 'label label-warning'}}">
                                        {{ $lss_payment->status == PAID ? tr('paid') : tr('not_paid')}}
                                    </label>
                                    </td>
                                    <td>{{common_date($lss_payment->created_at, Auth::guard('admin')->user()->timezone ?? DEFAULT_TIMEZONE)}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                      </table>
                        <div class="pull-right resp-float-unset" id="paglink">{{ $lss_payments->appends(request()->input())->links('pagination::bootstrap-4') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</section>
@endsection