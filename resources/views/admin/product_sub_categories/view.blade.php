@extends('layouts.admin')

@section('title', tr('view_product_sub_categories'))

@section('content-header', tr('product_sub_categories'))

@section('breadcrumb')

    
      <li class="breadcrumb-item"><a href="{{route('admin.product_sub_categories.index')}}">{{tr('view_product_sub_categories')}}</a>
    <li class="breadcrumb-item active">{{tr('view_product_sub_categories')}}</a>
    </li>

@endsection

@section('content')

<section class="content">

    <div class="row">

            <div class="col-xl-12 col-lg-12">

                <div class="card user-profile-view-sec">

                     <div class="card-header border-bottom border-gray">

                      <h4 class="card-title">{{tr('view_product_sub_category')}} - {{$product_sub_category->name ?: tr('n_a')}}</h4>

                    </div>
                    
                    <div class="card-content">

                        <div class="col-md-12">

                        <div class="card profile-with-cover">

                            <div class="media profil-cover-details w-100">

                                <div class="media-left pl-2 pt-2">

                                    <a class="profile-image">
                                       <img src="{{ $product_sub_category->picture ?: asset('placeholder.jpg')}}" alt="{{ $product_sub_category->name}}" class="img-thumbnail img-fluid img-border height-100"
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
                                         <tr>
                                    <th>{{tr('product_sub_category_name')}}</th>
                                    <td>{{$product_sub_category->name ?: tr('n_a')}}</td>
                                </tr>

                                <tr>
                                    <th>{{tr('product_category_name')}}</th>
                                    <td><a href="{{route('admin.product_categories.view',['product_category_id' => $product_sub_category->product_category_id])}}">{{$product_sub_category->productCategory->name ?? tr('n_a')}}</a></td>
                                </tr>

                                <tr>
                                                
                                    <th>{{tr('user_products')}}</th>
                                    <td><a href="{{route('admin.user_products.index',['product_sub_category_id' => $product_sub_category->id])}}">{{$product_sub_category->UserProducts->count() ?? tr('n_a')}}</a></td>

                                </tr>

                                <tr>
                                    <th>{{tr('status')}}</th>
                                    <td>
                                        @if($product_sub_category->status == APPROVED) 

                                            <span class="badge badge-success">{{tr('approved')}}</span>

                                        @else
                                            <span class="badge badge-danger">{{tr('declined')}}</span>

                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                  <th>{{tr('created_at')}} </th>
                                  <td>{{common_date($product_sub_category->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr>

                                <tr>
                                  <th>{{tr('updated_at')}} </th>
                                  <td>{{common_date($product_sub_category->updated_at , Auth::guard('admin')->user()->timezone)}}</td>
                                </tr> 

                                <tr>
                                    <th>{{tr('description')}}</th>
                                    <td>{{$product_sub_category->description ?: tr('n_a')}}</td>
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

                                           <a class="btn btn-secondary btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.product_sub_categories.edit', ['product_sub_category_id'=>$product_sub_category->id] )}}"> &nbsp;{{tr('edit')}}</a>

                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12">

                                            <a class="btn btn-danger btn-block btn-min-width mr-1 mb-1" onclick="return confirm(&quot;{{tr('product_sub_category_delete_confirmation' , $product_sub_category->name)}}&quot;);" href="{{route('admin.product_sub_categories.delete', ['product_sub_category_id'=> $product_sub_category->id] )}}">&nbsp;{{tr('delete')}}</a>

                                        </div>

                                        @endif

                                        <div class="col-xl-6 col-lg-6 col-md-12">

                                            @if($product_sub_category->status == APPROVED)
                                                 <a class="btn btn-warning btn-block btn-min-width mr-1 mb-1" href="{{route('admin.product_sub_categories.status' ,['product_sub_category_id'=> $product_sub_category->id] )}}" onclick="return confirm(&quot;{{$product_sub_category->name}} - {{tr('product_sub_category_decline_confirmation')}}&quot;);">&nbsp;{{tr('decline')}} </a> 
                                            @else

                                                <a  class="btn btn-success btn-block btn-min-width mr-1 mb-1" href="{{route('admin.product_sub_categories.status' , ['product_sub_category_id'=> $product_sub_category->id] )}}">&nbsp;{{tr('approve')}}</a> 
                                            @endif

                                       </div>

                                       <div class="col-xl-6 col-lg-6 col-md-12">

                                           <a class="btn btn-info btn-block btn-min-width mr-1 mb-1 " href="{{route('admin.user_products.index',['product_sub_category_id' => $product_sub_category->id])}}"> &nbsp;{{tr('total_products')}}</a>

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

@section('scripts')

@endsection
