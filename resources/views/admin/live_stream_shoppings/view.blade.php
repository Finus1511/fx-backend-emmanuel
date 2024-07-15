@extends('layouts.admin')

@section('title', tr('live_stream_shoppings'))

@section('content-header', tr('live_stream_shoppings'))

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.live_stream_shoppings.index')}}">{{ tr('live_stream_shoppings') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_live_stream_shopping') }}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="box box-outline-purple">

                    <div class="box-body">

                        <div class="row">

                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>

                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('stream_id')}} </td>
                                            <td class="text-uppercase">{{ $live_stream_shopping->unique_id ?: tr('na')}}</td>
                                        </tr>

                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('payment_id')}} </td>
                                            <td class="text-uppercase">
                                               <a href="">
                                                    {!! $live_stream_shopping->lssPayment->payment_id ?? tr('n_a') !!}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('stream_title')}} </td>
                                            <td>{{ $live_stream_shopping->title ?: tr('n_a')}}</td>
                                        </tr>

                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('username')}} </td>
                                            <td>
                                                <a href="{{route('admin.users.view',['user_id' => $live_stream_shopping->user_id])}}">
                                                    {{ $live_stream_shopping->user->name ?? tr('n_a')}}
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('status')}} </td>
                                            <td><label class="{{ live_stream_status_badge_formatted($live_stream_shopping->status)}}">
                                                {{ $live_stream_shopping->status ? live_stream_status_formatted($live_stream_shopping->status) : tr('n_a')}}
                                            </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('stream_type')}} </td>
                                            <td>
                                                {{$live_stream_shopping->stream_type ?: tr('n_a')}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('payment_type') }}</td>
                                            <td>
                                            <label class="{{ $live_stream_shopping->payment_type == PAYMENT_TYPE_PAID ? 'label label-success' : 'label label-warning' }}">
                                            {{ $live_stream_shopping->payment_type == PAYMENT_TYPE_PAID ? tr('paid') : tr('free') }}
                                            </label>
                                        </td>
                                        </tr>
                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('streamed_at') }}</td>
                                            <td>{{common_date($live_stream_shopping->created_at, Auth::guard('admin')->user()->timezone)}}
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>

                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('start_time') }}</td>
                                            <td>{{common_date($live_stream_shopping->start_time,'','g:i A')}}</td>
                                        </tr>
                                        
                                        @if(!request()->live_video_id)
                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('end_time') }}</td>
                                            <td>{{common_date($live_stream_shopping->end_time, '','g:i A')}}</td>

                                        </tr>
                                        @endif
                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('description') }}</td>
                                            <td>{!! $live_stream_shopping->description ?: tr('n_a') !!}</td>
                                        </tr>

                                    </tbody>

                                </table>
                            </div>
                            @if($live_stream_shopping->amount > 0)
                            <div class="col-md-6">
                                <h3>{{tr('revenue_details')}}</h3>
                                <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                    <tbody>
                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('video_amount')}} </td>
                                            <td>{{ $live_stream_shopping->amount ? formatted_amount($live_stream_shopping->amount) : 0}}</td>
                                        </tr>
                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('user_commission')}} </td>
                                            <td>{{ $live_stream_shopping->user_amount ?: 0}}</td>
                                        </tr>
                                        <tr>
                                            <td class=" heading-text-weight">{{ tr('admin_commission') }}</td>
                                            <td>{{ $live_stream_shopping->admin_amount ?: 0}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                              </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection