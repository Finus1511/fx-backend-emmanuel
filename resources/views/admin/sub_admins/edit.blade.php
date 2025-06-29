@extends('layouts.admin')

@section('title', tr('edit_sub_admin'))

@section('content-header', tr('sub_admins'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.sub_admin.index')}}">{{tr('sub_admins')}}</a></li>
    
    <li class="breadcrumb-item active">{{tr('edit_sub_admin')}}</a></li>

@endsection

@section('content')

    @include('admin.sub_admins._form')

@endsection