@extends('layouts.admin')

@section('title', tr('view_inapp_purchases'))

@section('content-header', tr('view_inapp_purchases'))

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.inapp_purchases.index')}}">{{tr('inapp_purchases')}}</a>
</li>

<li class="breadcrumb-item active">{{tr('view_inapp_purchases')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="card">

        <div class="card-header">

            <h4 id="basic-forms" class="card-title">{{$inapp_purchase->reference_name ?: tr('n_a')}}</h4>

        </div>

        <div class="card-content collapse show" aria-expanded="true">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-5">

                        <div class="card-title text-center"><h4><b>{{tr('action')}}</b></h4></div><br>

                         @if(Setting::get('admin_delete_control') == YES )

                            <a href="javascript:;" class="btn btn-warning mb-2" title="{{tr('edit')}}"><b>{{tr('edit')}}</b></a>

                            <a onclick="return confirm(&quot;{{ tr('inapp_purchase_delete_confirmation', $inapp_purchase->reference_name ) }}&quot;);" href="javascript:;" class="btn btn-danger" title="{{tr('delete')}}"><b>{{tr('delete')}}</b>
                                </a>

                        @else
                            <a href="{{ route('admin.inapp_purchases.edit' , ['inapp_purchase_id' => $inapp_purchase->id] ) }}" class="btn btn-warning btn-min-width ml-2 mb-1" title="{{tr('edit')}}"><b>{{tr('edit')}}</b></a>  
                                                        
                            <a onclick="return confirm(&quot;{{ tr('inapp_purchase_delete_confirmation', $inapp_purchase->reference_name ) }}&quot;);" href="{{ route('admin.inapp_purchases.delete', ['inapp_purchase_id' => $inapp_purchase->id] ) }}" class="btn btn-danger btn-min-width mb-1 ml-2" title="{{tr('delete')}}"><b>{{tr('delete')}}</i></b>
                                </a>
                        @endif

                        @if($inapp_purchase->status == APPROVED)

                            <a class="btn btn-info btn-min-width mb-1 ml-2" title="{{tr('decline')}}" href="{{ route('admin.inapp_purchases.status', ['inapp_purchase_id' => $inapp_purchase->id]) }}" onclick="return confirm(&quot;{{$inapp_purchase->reference_name}} - {{tr('inapp_purchase_decline_confirmation')}}&quot;);" >
                                <b>{{tr('decline')}}</b>
                            </a>

                        @else
                            
                            <a class="btn btn-success btn-min-width mr-1 mb-1" title="{{tr('approve')}}" href="{{ route('admin.inapp_purchases.status', ['inapp_purchase_id' => $inapp_purchase->id]) }}">
                                <b>{{tr('approve')}}</b> 
                            </a>
                               
                        @endif
                           
                    </div>

                    <div class="col-lg-7">

                        <div class="card-title text-center"><h4><b>{{tr('inapp_purchases')}}</b></h4></div><br>

                        <p><strong>{{tr('reference_name')}}</strong>

                            <span class="pull-right">{{$inapp_purchase->reference_name ?: tr('n_a')}}
                            </span>
                            
                        </p>
                        <hr>

                        <p><strong>{{tr('amount')}}</strong>

                            <span class="pull-right">{{$inapp_purchase->amount ?: tr('n_a')}}
                            </span>
                            
                        </p>
                        <hr>

                        <p><strong>{{tr('product_id')}}</strong>

                            <span class="pull-right">{{$inapp_purchase->product_id ?: tr('n_a')}}
                            </span>
                            
                        </p>
                        <hr>

                        <p><strong>{{tr('apple_id')}}</strong>

                            <span class="pull-right">{{$inapp_purchase->apple_id ?: tr('n_a')}}
                            </span>
                            
                        </p>
                        <hr>

                       <p><strong>{{tr('status')}}</strong>

                            @if($inapp_purchase->status == APPROVED)
                                <span class="badge bg-success pull-right">{{tr('approved')}}</span>
                            @else
                                <span class="badge bg-danger pull-right">{{tr('declined')}}</span>
                            @endif

                        </p>
                        <hr>

                        <p><strong>{{tr('created_at')}} </strong>
                            <span class="pull-right">{{common_date($inapp_purchase->created_at , Auth::guard('admin')->user()->timezone)}}</span>
                        </p>
                        <hr>

                        <p><strong>{{tr('updated_at')}} </strong>
                            <span class="pull-right">{{common_date($inapp_purchase->updated_at , Auth::guard('admin')->user()->timezone)}}
                            </span>
                        </p>
                        <hr>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
  
@endsection

