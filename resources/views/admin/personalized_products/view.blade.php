@extends('layouts.admin') 

@section('title', tr('view_personalized_products'))

@section('content-header',tr('personalized_products'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.personalized_requests.products')}}">{{tr('personalized_products')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{tr('view_personalized_products')}}</span>
    </li>
           
@endsection  

@section('content')

<section class="content">
    
    <div class="row match-height">
    
        <div class="col-lg-12 col-md-12">

            <div class="card">
                
                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title" id="basic-layout-form">{{tr('view_personalized_products')}} - {{$personalized_product->unique_id ?: tr('na')}}
                    </h4>
                    
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                         <div class="card-group">

                            <div class="card card-margin-btm-zero">
                                @if($personalized_product->personalizedProductFiles)
                                    <div class="row">
                                    @foreach($personalized_product->personalizedProductFiles as $key => $product_file)
                                        @if(strpos($product_file->file, '.mp4') !== false || strpos($product_file->file, '.mkv') !== false)
                                            <div class="col-3">
                                                <video controls class="chat-post-img" width="100%" height="100%">
                                                    <source src="{{ $product_file->file ?: asset('placeholder.jpg') }}" type="video/mp4">
                                                </video>
                                            </div>
                                        @else   
                                            <div class="col-3"><img src="{{ $product_file->file ?: asset('placeholder.jpg') }}" class="img-fluid" alt="Image"></div>
                                        @endif    
                                    @endforeach
                                    </div>
                                @endif
                                <div class="card-body">

                                    <h4 class="card-title">{{ tr('description') }}</h4>
                            
                                    <p class="card-text ml-4"><?= $personalized_product->description ?: tr('n_a') ?></p>
                                    
                                </div>

                            </div>
                          
                            <div class="card card-margin-btm-zero">

                                <div class="card-body">

                                    <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                                        <tbody>

                                            <tr>
                                                <td>{{ tr('unique_id') }}</td>
                                                <td><a href="{{route('admin.personalized_products.view', ['personalized_product_id' => $personalized_product->id])}}">{{$personalized_product->unique_id ?: tr('n_a')}}</a></td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('name') }}</td>
                                                <td><a href="{{route('admin.personalized_products.view', ['personalized_product_id' => $personalized_product->id])}}">{{$personalized_product->name ?: tr('n_a')}}</a></td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('status') }}</td>
                                                   <td>
                                                    <span class="badge {{$personalized_product->status ? 'badge-success' : 'badge-danger' }}">{{$personalized_product->status ? tr('approved') : ('declined')}}</span> 
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('created_at') }}</td>
                                                <td>{{common_date($personalized_product->created_at,Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                            <tr>
                                                <td>{{ tr('updated_at') }}</td>
                                                <td>{{common_date($personalized_product->updated_at,Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                        </tbody>

                                      </table>

                                    <hr> 

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
