@extends('layouts.admin') 

@section('content-header', tr('personalized_products'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.personalized_requests.products' )}}">{{tr('personalized_products')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('personalized_products') }}</span>
    </li>
           
@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{ tr('view_personalized_products') }}</h4>
                    
                </div>

                <div class="box-body">
                    <div class="callout bg-pale-secondary">
                        <h4>{{tr('notes')}}</h4>
                        <p>
                            </p><ul>
                                <li>{{tr('personalized_products_notes')}}</li>
                            </ul>
                        <p></p>
                    </div>
                </div>

                <div class="box box-outline-purple">

                <div class="box-body">
                    
                    <div class="table-responsive">

                        @include('admin.personalized_products._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">
                            <thead>
                                <tr>
                                    <th>{{tr('s_no')}}</th>
                                    <th>{{tr('unique_id')}}</th>
                                    <th>{{tr('name')}}</th>
                                    <th>{{tr('status')}}</th>
                                    <th>&nbsp;&nbsp;{{tr('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>


                                @foreach($personalized_products as $i => $personalized_product)

                                    <tr>
                                        <td>{{$i+$personalized_products->firstItem()}}</td>

                                        <td class="text-capitalize"><a href="{{route('admin.personalized_products.view', ['personalized_product_id' => $personalized_product->id])}}">{{$personalized_product->unique_id ?:  tr('na')}}</a></td>

                                        <td><a href="{{route('admin.personalized_products.view', ['personalized_product_id' => $personalized_product->id])}}">{{$personalized_product->name ?: tr('n_a')}}</a>
                                        </td>

                                        <td>
                                            <span class="badge {{$personalized_product->status ? 'badge-success' : 'badge-danger' }}">{{$personalized_product->status ? tr('approved') : ('declined')}}</span> 
                                        </td>

                                        <td>  

                                            <div class="btn-group" role="group">

                                                <button class="btn btn-outline-primary dropdown-toggle dropdown-menu-right" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ft-settings icon-left"></i> {{ tr('action') }}</button>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOutlineButton1">

                                                    <a class="dropdown-item" href="{{route('admin.personalized_products.view',['personalized_product_id' => $personalized_product->id])}}">
                                                        {{tr('view')}}
                                                    </a>
                                                    
                                                </div>
                                                 
                                            </div>
                                        

                                        </td>
                                    
                                    </tr>

                                @endforeach

                            </tbody>
                        
                        </table>

                        <div class="pull-right" id="paglink">{{ $personalized_products->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection