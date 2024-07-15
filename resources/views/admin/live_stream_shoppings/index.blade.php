@extends('layouts.admin')

@section('title', tr('live_stream_shoppings'))

@section('content-header', tr('live_stream_shoppings'))

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.live_stream_shoppings.index',['status' => request()->status == LIVE_STREAM_SHOPPING_ONGOING ? LIVE_STREAM_SHOPPING_ONGOING : null])}}">{{ tr('live_stream_shoppings') }}</a>
</li>

<li class="breadcrumb-item active">
    {{ request()->status == LIVE_STREAM_SHOPPING_ONGOING ? tr('live_stream_shoppings_ongoing') : tr('live_stream_shoppings_history')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card view-post-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{request()->status == LIVE_STREAM_SHOPPING_ONGOING ? tr('on_live') : tr('history')}}
                        @if(request('user_id'))
                          <a href="{{route('admin.users.view', ['user_id' =>$user->id])}}"> - {{$user->name ?: tr('na')}}</a>
                        @endif
                    </h4>

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">
                        @include('admin.live_stream_shoppings._search')
                        <br>
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            <thead>
                                <tr>

                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('title') }}</th>
                                    <th>{{ tr('user_name') }}</th>
                                    <th>{{ tr('stream_type') }}</th>
                                    <th>{{ tr('amount') }}</th>
                                    <th>{{ tr('payment_type') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('streamed_at') }}</th>
                                    <th>&nbsp;&nbsp;{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($live_stream_shoppings as $i => $live_stream_shopping)
                                <tr>

                                    <td>{{ $i+$live_stream_shoppings->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.live_stream_shoppings.view' , ['live_stream_shopping_id' => $live_stream_shopping->id] )  }}">
                                            {{ $live_stream_shopping->title ?: tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $live_stream_shopping->user_id] )  }}">
                                            {{ $live_stream_shopping->user->name ?? tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>{{ $live_stream_shopping->stream_type ?: tr('n_a')}}</td>

                                    <td>
                                        {{ $live_stream_shopping->amount ? formatted_amount($live_stream_shopping->amount) : 0}}
                                    </td>

                                    <td>
                                        <label class="{{ $live_stream_shopping->payment_type == PAYMENT_TYPE_PAID ? 'label label-success' : 'label label-warning' }}">
                                            {{ $live_stream_shopping->payment_type == PAYMENT_TYPE_PAID ? tr('paid') : tr('free') }}
                                        </label>
                                    </td>

                                    <td><label class="{{ live_stream_status_badge_formatted($live_stream_shopping->status)}}">
                                        {{ $live_stream_shopping->status ? live_stream_status_formatted($live_stream_shopping->status) : tr('n_a')}}
                                    </label>
                                    </td>
                                     <td>{{common_date($live_stream_shopping->created_at, Auth::guard('admin')->user()->timezone)}}</td>
                                    <td>
                                        
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                               <a class="dropdown-item" href="{{ route('admin.live_stream_shoppings.view', ['live_stream_shopping_id' => $live_stream_shopping->id, 'status' => request()->status == LIVE_STREAM_SHOPPING_ONGOING ? LIVE_STREAM_SHOPPING_ONGOING : null]) }}">
                                                    &nbsp;{{ tr('view') }}
                                                </a>
                                                <a class="dropdown-item" href="{{route('admin.live_stream_shoppings.payments',['live_stream_shopping_id' => $live_stream_shopping->id])}}">
                                                    &nbsp;{{ tr('lss_payments') }}
                                                </a>
                                                 <a class="dropdown-item" href="{{route('admin.live_stream_shoppings.products',['live_stream_shopping_id' => $live_stream_shopping->id])}}">
                                                    &nbsp;{{ tr('lss_products') }}
                                                </a>
                                                 <a class="dropdown-item" href="{{route('admin.live_stream_shoppings.product_payments',['live_stream_shopping_id' => $live_stream_shopping->id])}}">
                                                    &nbsp;{{ tr('lss_product_payments') }}
                                                 </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $live_stream_shoppings->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

  </div>

</section>

@endsection