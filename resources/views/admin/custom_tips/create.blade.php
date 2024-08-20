@extends('layouts.admin')

@section('title', tr('add_custom_tip'))

@section('content-header', tr('custom_tips'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.custom_tips.index')}}">{{tr('custom_tips')}}</a></li>

    <li class="breadcrumb-item active">{{tr('add_custom_tip')}}</a></li>

@endsection

@section('content')

    @include('admin.custom_tips._form')

@endsection
