@extends('layouts.admin')

@section('title', tr('edit_hashtag'))

@section('content-header', tr('edit_hashtag'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{route('admin.hashtags.index')}}">{{tr('hashtag')}}</a></li>

    <li class="breadcrumb-item active">{{tr('edit_hashtag')}}</a></li>

@endsection

@section('content')

    @include('admin.hashtags._form')

@endsection