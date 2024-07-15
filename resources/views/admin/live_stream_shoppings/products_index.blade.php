@extends('layouts.admin')

@section('title', tr('lss_products'))

@section('content-header', tr('lss_products'))

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.live_stream_shoppings.index',['status' => request()->status == LIVE_STREAM_SHOPPING_ONGOING ? LIVE_STREAM_SHOPPING_ONGOING : null])}}">{{ tr('live_stream_shoppings') }}</a>
</li>

<li class="breadcrumb-item active">
    {{tr('lss_products_history')}}
</li>

@endsection

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card view-post-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">

                        {{tr('lss_products')}}@if(request('live_stream_shopping_id'))
                        - <a href="{{route('admin.live_stream_shoppings.view',['live_stream_shopping_id' => $live_stream_shopping->id])}}">{{$live_stream_shopping->title ?: tr('na')}}</a>@endif
                    </h4>

                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">
                         @include('admin.live_stream_shoppings.products_search')
                        <br>
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('user_name') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('price') }}</th>
                                    <th>{{ tr('quantity') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($lss_products as $i => $lss_product)
                                <tr>

                                    <td>{{ $i+$lss_products->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.users.view' , ['user_id' => $lss_product->user_id])  }}">
                                            {{ $lss_product->user->name ?? tr('n_a') }}
                                        </a>
                                    </td>

                                    <td>{{ $lss_product->name ?: tr('n_a')}}</td>

                                    <td>{{ $lss_product->quantity ?: 0}}</td>

                                    <td>
                                        {{ $lss_product->price ? formatted_amount($lss_product->price) : 0}}
                                    </td>

                                    <td>
                                       <label class="{{ $lss_product->status ? 'label label-success' : 'label label-warning' }}">
                                        {{ $lss_product->status ? tr('approved') : tr('declined') }}
                                        </label>
                                    </td>

                                    <td>
                                        
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                                 <a class="dropdown-item" href="{{route('admin.live_stream_shoppings.product_payments',['user_product_id' => $lss_product->user_product_id])}}">
                                                    &nbsp;{{ tr('lss_product_payments') }}
                                                 </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $lss_products->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

            </div>

        </div>

    </div>

  </div>

</section>

@endsection