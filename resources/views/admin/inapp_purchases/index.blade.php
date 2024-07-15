@extends('layouts.admin') 

@section('title', tr('inapp_purchases')) 

@section('content-header', tr('inapp_purchases')) 

@section('breadcrumb')
    
<li class="breadcrumb-item active">
    <a href="{{route('admin.inapp_purchases.index')}}">{{ tr('inapp_purchases') }}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_inapp_purchases') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_inapp_purchases') }}</h4>

                    <div class="heading-elements">
                        <a href="{{ route('admin.inapp_purchases.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_inapp_purchase') }}</a>
                    </div>
                    
                </div>
                 <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>
                                    {{tr('inapp_purchase_notes')}}
                                </li>
                            </ul>
                        <p></p>
                    </div>
                </div>
                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('reference_name') }}</th>
                                    <th>{{ tr('apple_id') }}</th>
                                    <th>{{ tr('product_id') }}</th>
                                    <th>{{ tr('amount') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>&nbsp;&nbsp;{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>

                                @foreach($inapp_purchases as $i => $inapp_purchase)
                                <tr>
                                    <td>{{ $i + $inapp_purchases->firstItem() }}</td>

                                    <td>
                                        <a href="{{  route('admin.inapp_purchases.view' , ['inapp_purchase_id' => $inapp_purchase->id] )  }}">
                                        {{ $inapp_purchase->reference_name  ?: tr('n_a')}}
                                        </a>
                                    </td>

                                    <td>
                                        {{ $inapp_purchase->apple_id}}
                                    </td>

                                    <td>
                                        {{ $inapp_purchase->product_id}}
                                    </td>

                                    <td>
                                        {{ $inapp_purchase->amount}}
                                    </td>

                                    <td>
                                        @if($inapp_purchase->status == APPROVED)

                                            <span class="btn btn-success btn-sm">{{ tr('approved') }}</span>
                                        @else

                                            <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                        @endif
                                    </td>

                                    <td>
                                    
                                        <div class="btn-group" role="group">

                                            <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                                <a class="dropdown-item" href="{{ route('admin.inapp_purchases.view', ['inapp_purchase_id' => $inapp_purchase->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                @if(Setting::get('is_demo_control_enabled') == YES)

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.inapp_purchases.edit', ['inapp_purchase_id' => $inapp_purchase->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                    <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('inapp_purchase_delete_confirmation' , $inapp_purchase->reference_name) }}&quot;);" href="{{ route('admin.inapp_purchases.delete', ['inapp_purchase_id' => $inapp_purchase->id,'page'=>request()->input('page')] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                @endif

                                                @if($inapp_purchase->status == APPROVED)

                                                    <a class="dropdown-item" href="{{  route('admin.inapp_purchases.status' , ['inapp_purchase_id' => $inapp_purchase->id] )  }}" onclick="return confirm(&quot;{{ $inapp_purchase->reference_name }} - {{ tr('inapp_purchase_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                </a> 

                                                @else

                                                    <a class="dropdown-item" href="{{ route('admin.inapp_purchases.status' , ['inapp_purchase_id' => $inapp_purchase->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                @endif

                                            </div>

                                        </div>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $inapp_purchases->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection