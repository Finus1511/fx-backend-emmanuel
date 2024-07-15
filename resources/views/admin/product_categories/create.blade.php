@extends('layouts.admin')

@section('title', tr('add_product_category'))

@section('content-header', tr('product_categories'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.product_categories.index')}}">{{tr('product_categories')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_category')}}</a></li>

@endsection

@section('content')

    @include('admin.product_categories._form')

@endsection
