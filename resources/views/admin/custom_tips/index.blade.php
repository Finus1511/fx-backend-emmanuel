@extends('layouts.admin') 

@section('content-header', tr('custom_tips')) 

@section('breadcrumb')

<li class="breadcrumb-item">
    <a href="{{route('admin.custom_tips.index')}}">{{tr('custom_tips')}}</a>
</li>

<li class="breadcrumb-item active">{{ tr('view_custom_tips') }}</li>

@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_custom_tips') }}</h4>

                    <div class="heading-elements">
                        <a href="{{ route('admin.custom_tips.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_custom_tip') }}</a>
                    </div>
                    
                </div>

                <div class="box box-outline-purple">

                    <div class="box-body">

                        @include('admin.custom_tips._search')
                        
                        <div class="table-responsive">

                            <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                                <thead>
                                    <tr>
                                        <th>{{ tr('s_no') }}</th>
                                        <th>{{ tr('picture') }}</th>
                                        <th>{{ tr('title') }}</th>
                                        <th>{{tr('amount')}}</th>
                                        <th>{{ tr('status') }}</th>
                                        <th>{{ tr('action') }}</th>
                                    </tr>
                                </thead>
                               
                                <tbody>

                                    @foreach($custom_tips as $i => $custom_tip)
                                    <tr>
                                        <td>{{ $i+$custom_tips->firstItem() }}</td>

                                        <td><img src="{{$custom_tip->picture ?: asset('placeholder.jpg')}}" class="category-image"></td>

                                        <td>
                                            <a href="{{  route('admin.custom_tips.view' , ['custom_tip_id' => $custom_tip->id] )  }}">
                                            {{ $custom_tip->title ?: tr('n_a')}}
                                            </a>
                                        </td>

                                        <td>
                                            {{formatted_amount($custom_tip->amount ?? 0)}}
                                        </td>
                                        
                                        <td>
                                            @if($custom_tip->status == APPROVED)

                                                <span class="btn btn-success btn-sm">{{ tr('approved') }}</span> 
                                            @else

                                                <span class="btn btn-warning btn-sm">{{ tr('declined') }}</span> 
                                            @endif
                                        </td>

                                        <td>
                                        
                                            <div class="btn-group" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu dropdown-sm-scroll" aria-labelledby="btnGroupDrop1">

                                                    <a class="dropdown-item" href="{{ route('admin.custom_tips.view', ['custom_tip_id' => $custom_tip->id] ) }}">&nbsp;{{ tr('view') }}</a> 

                                                    @if(Setting::get('is_demo_control_enabled') == YES)

                                                        <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

                                                        <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a> 

                                                    @else

                                                        <a class="dropdown-item" href="{{ route('admin.custom_tips.edit', ['custom_tip_id' => $custom_tip->id] ) }}">&nbsp;{{ tr('edit') }}</a>

                                                        <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('category_delete_confirmation' , $custom_tip->title) }}&quot;);" href="{{ route('admin.custom_tips.delete', ['custom_tip_id' => $custom_tip->id] ) }}">&nbsp;{{ tr('delete') }}</a>

                                                    @endif

                                                    @if($custom_tip->status == APPROVED)

                                                        <a class="dropdown-item" href="{{  route('admin.custom_tips.status' , ['custom_tip_id' => $custom_tip->id] )  }}" onclick="return confirm(&quot;{{ $custom_tip->title }} - {{ tr('category_decline_confirmation') }}&quot;);">&nbsp;{{ tr('decline') }}
                                                    </a> 

                                                    @else

                                                        <a class="dropdown-item" href="{{ route('admin.custom_tips.status' , ['custom_tip_id' => $custom_tip->id] ) }}">&nbsp;{{ tr('approve') }}</a> 

                                                    @endif

                                                </div>

                                            </div>

                                        </td>

                                    </tr>

                                    @endforeach

                                </tbody>
                        
                            </table>

                            <div class="pull-right" id="paglink">{{ $custom_tips->appends(request()->input())->links('pagination::bootstrap-4') }}</div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection
