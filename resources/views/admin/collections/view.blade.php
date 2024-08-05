@extends('layouts.admin')
@section('title', tr('collections'))
@section('content-header', tr('collections'))
@section('breadcrumb')
    <li class="breadcrumb-item active">
        <a href="{{route('admin.collections.index')}}">{{ tr('collections') }}</a>
    </li>
    <li class="breadcrumb-item">{{tr('view_collection')}}</li>
@endsection
@section('content')
<section class="content">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card user-profile-view-sec">
                    <div class="card-header border-bottom border-gray">
                        <h4 class="card-title">{{tr('view_collection')}}</h4>
                    </div>
                    <div class="card-content">
                        <div class="user-view-padding">
                            <div class="row"> 
                                <div class=" col-xl-6 col-lg-6 col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-xl mb-0">
                                            <tr >
                                                <th>{{tr('unique_id')}}</th>
                                                <td>{{$collection->unique_id ?: tr('n_a')}}</td>
                                            </tr>
                                             <tr>
                                                <th>{{tr('user')}}</th>
                                                <td><a href="{{$collection->user->name ? route('admin.users.view',['user_id' => $collection->user_id] ?: 0) : '#'}}" class="{{ $collection->user->name ? '' : 'link-disabled'}}"> {{$collection->user->name ?? tr('n_a')}}</a></td>
                                            </tr> 
                                            <tr>
                                                <th>{{ tr('name') }}</th>
                                                <td>{{ $collection->name ?: tr('na')}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ tr('amount') }}</th>
                                                <td>{{ $collection->amount ? formatted_amount($collection->amount) : 0.00}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('status')}}</th>
                                                <td>
                                                   <span class="btn {{ $collection->status ? 'btn-success' : 'btn-danger'}} btn-sm">{{$collection->status ? tr('approved') : tr('declined')}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('created_at')}} </th>
                                                <td>{{common_date($collection->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('updated_at')}} </th>
                                                <td>{{common_date($collection->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr> 
                                        </table>
                                    </div>
                                </div>
                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="card user-profile-view-sec">
                    <div class="card-header border-bottom border-gray">
                        <h4 class="card-title">{{tr('collection_files')}}</h4>
                    </div>
                    <div class="card-content">
                            <div class="col-md-12">
                                <div class="row">
                                  @foreach($collection->collectionFiles as $collection_file)
                                    <div class="col-3">
                                        <div class="card gallery">
                                            <div class="media profil-cover-details">
                                                @if($collection_file->file_type == FILE_TYPE_IMAGE)
                                                <img src="{{ $collection_file->file ?: asset('placeholder.jpeg')}}" alt="" class="img-thumbnail img-fluid height-100 gallery-size" alt="Card image">
                                                @else
                                                <video class="img-thumbnail img-fluid height-100 gallery-size" controls>
                                                    <source src="{{ $collection_file->file ?: asset('placeholder.jpeg') }}" type="video/mp4">
                                                </video>
                                                @endif
                                                <a 
                                                  onclick="return confirm('{{ tr('collection_file_delete_confirmation', $collection->user->name ?? tr('na')) }}');" 
                                                  href="{{ route('admin.collection_file.delete', ['collection_file_id' => $collection_file->id]) }}" 
                                                  class="link" 
                                                  data-toggle="tooltip" 
                                                  title="{{ tr('delete') }}" 
                                                  data-original-title="{{ tr('delete') }}"
                                                >
                                                  <i class="ion ion-trash-a icon-section-setup"></i>
                                                </a>
                                            </div> 
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        <hr>
                    </div>
                </div>
            </div>
     </section>
@endsection