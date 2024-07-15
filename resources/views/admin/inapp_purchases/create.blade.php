@extends('layouts.admin')

@section('title', tr('inapp_purchases'))

@section('content-header', tr('inapp_purchases'))

@section('breadcrumb')

    
    <li class="breadcrumb-item"><a href="{{route('admin.inapp_purchases.index')}}">{{tr('inapp_purchases')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_inapp_purchase')}}</a></li>

@endsection

@section('content')

    @include('admin.inapp_purchases._form')

@endsection
