@extends('layouts.admin') 
@section('title', tr('collections')) 
@section('content-header', tr('collections')) 
@section('breadcrumb')
<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('users') }}</a>
</li>
    
<li class="breadcrumb-item">{{ tr('view_collections') }}</li>
@endsection 
@section('content')
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom border-gray">
                        <h4 class="card-title">{{ tr('collections') }} @if($user)- <a href="{{ route('admin.users.view',['user_id'=>$user->id ?? '']) }}">{{$user->name}}</a> 
                        @endif
                    </h4>
                    
                </div>
                <div class="box box-outline-purple">
                <div class="box-body">
                    <div class="table-responsive">
                        @include('admin.collections._search')
                        
                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            
                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('unique_id') }}</th>
                                    <th>{{ tr('user') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('amount') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>
                           
                            <tbody>
                                @foreach($collections as $i => $collection)
                                <tr>
                                    <td>{{ $i + $collections->firstItem() }}</td>
                                    
                                    <td>{{$collection->unique_id ?: tr('na')}}</td>
                                    
                                    <td>
                                       <a href="{{$collection->user->name ? route('admin.users.view',['user_id' => $collection->user_id] ?: 0) : '#'}}" class="{{ $collection->user->name ? '' : 'link-disabled'}}">
                                        {{ $collection->user->name ?? tr('na') }}
                                        </a>
                                    </td>
                                    <td>
                                        {{$collection->name ?: tr('na')}}
                                    </td>
                                    <td>
                                        {{$collection->amount ? formatted_amount($collection->amount) : 0.00}}
                                    </td>
                                    <td>
                                        <span class="btn {{ $collection->status ? 'btn-success' : 'btn-danger'}} btn-sm">{{$collection->status ? tr('approved') : tr('declined')}}</span>
                                   
                                    <td>
                                    
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-outline-success dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                              <a class="dropdown-item" href="{{ route('admin.collections.view', ['collection_id' => $collection->id] ) }}">&nbsp;{{ tr('view') }}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        
                        </table>
                        <div class="pull-right" id="paglink">{{ $collections->appends(request()->input())->links('pagination::bootstrap-4') }}</div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection