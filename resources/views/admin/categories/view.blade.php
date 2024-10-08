@extends('layouts.admin')

@section('title', tr('categories'))

@section('content-header', tr('categories'))

@section('breadcrumb')

    <li class="breadcrumb-item active">
        <a href="{{route('admin.categories.index')}}">{{ tr('categories') }}</a>
    </li>

    <li class="breadcrumb-item">{{$title ?? tr('view_category')}}</li>

@endsection

@section('content')

<section class="content">

        <div class="row">

            <div class="col-xl-12 col-lg-12">

                <div class="card user-profile-view-sec">

                    <div class="card-header border-bottom border-gray">

                        <h4 class="card-title">{{$title ?? tr('view_category')}} - {{$category->name ?? ''}}</h4>

                    </div>

                    <div class="card-content">

                        <div class="col-md-12">

                            <div class="card profile-with-cover">

                                <div class="media profil-cover-details w-100">

                                    <div class="media-left pl-2 pt-2">

                                        <a class="profile-image">
                                             <img src="{{ $category->picture ?: asset('placeholder.jpg')}}" alt="{{ $category->name}}" class="img-thumbnail img-fluid img-border height-100"
                                        alt="Card image">
                                        </a>

                                    </div>

                                   
                                </div>

                            
                            </div>
                        
                        </div>

                        <hr>
                        <div class="user-view-padding">
                            <div class="row"> 

                                <div class=" col-xl-6 col-lg-6 col-md-12">
                                    <div class="table-responsive">

                                        <table class="table table-xl mb-0">
                                            <tr >
                                                <th>{{tr('category_name')}}</th>
                                                <td>{{$category->name ?: tr('n_a')}}</td>
                                            </tr>
                                             <tr>
                                                <th>{{tr('description')}}</th>
                                                <td>{{$category->description ?: tr('n_a')}}</td>
                                            </tr> 
                                            <tr>
                                                <th>{{ tr('total_users') }}</th>
                                                <td>{{ $category->total_users ?: 0 }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{tr('status')}}</th>
                                                <td>
                                                    @if($category->status == APPROVED) 

                                                        <span class="badge badge-success">{{tr('approved')}}</span>

                                                    @else
                                                        <span class="badge badge-danger">{{tr('declined')}}</span>

                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('created_at')}} </th>
                                                <td>{{common_date($category->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <th>{{tr('updated_at')}} </th>
                                                <td>{{common_date($category->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr> 
                                        </table>

                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-12">

                                    <div class="px-2 resp-marg-top-xs">

                                        <div class="card-title">{{tr('action')}}</div>

                                        <div class="row">

                                            @if(Setting::get('is_demo_control_enabled') == YES)

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-secondary btn-block btn-min-width mr-1 mb-1 " href="javascript:void(0)"> &nbsp;{{tr('edit')}}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" href="javascript:void(0)">&nbsp;{{tr('delete')}}</a>

                                            </div>


                                            @else

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.categories.edit', ['category_id'=>$category->id] )}}"> &nbsp;{{tr('edit')}}</a>

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('category_delete_confirmation' , $category->name)}}&quot;);" href="{{route('admin.categories.delete', ['category_id'=> $category->id] )}}">&nbsp;{{tr('delete')}}</a>

                                            </div>

                                            @endif

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                 @if($category->status == APPROVED)
                                                     <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.categories.status' ,['category_id'=> $category->id] )}}" onclick="return confirm(&quot;{{$category->name}} - {{tr('category_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                                                @else

                                                    <a  class="btn btn-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.categories.status' , ['category_id'=> $category->id] )}}">&nbsp;{{tr('approve')}}</a> 
                                                @endif

                                            </div>

                                            <div class="col-xl-6 col-lg-6 col-md-12">

                                                <a class="btn btn-info btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.users.index', ['category_id' => $category->id] )}}"> &nbsp;{{tr('total_users')}}</a>

                                            </div>


                                        </div>

                                    </div>

                                </div>
                        
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

</section>

@endsection


