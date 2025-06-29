@extends('layouts.admin')

@section('title', tr('sub_admins'))

@section('content-header', tr('sub_admins'))

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.sub_admin.index')}}">{{ tr('sub_admins') }}</a>
</li>

<li class="breadcrumb-item">{{$title ?? tr('view_sub_admins')}}</li>

@endsection

@section('content')

<section class="content">

    <div class="row">
        <div class="col-12">
        <div class="card">

            <div class="card-header border-bottom border-gray">

                <h4 class="card-title">{{$title ?? tr('view_sub_admins')}}</h4>

                <div class="heading-elements">
                    
                    <a href="{{ route('admin.sub_admin.create') }}" class="btn btn-primary"><i class="ft-plus icon-left"></i>{{ tr('add_sub_admin') }}</a>

                </div>

            </div>
            <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">

                        @include('admin.sub_admins._search')

                        <table id="checkBoxData" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('name') }}</th>
                                    <th>{{ tr('email') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{tr('created_at')}}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($sub_admins as $i => $sub_admin)

                                <tr>

                                    <td>{{ $i+$sub_admins->firstItem() }}</td>

                                    <td class="white-space-nowrap">
                                        <a href="{{route('admin.sub_admin.view' , ['sub_admin_id' => $sub_admin->id])}}" class="custom-a">
                                            {{Str::limit($sub_admin->name ,15) ?: tr('n_a')}}
                                        </a>
                                    </td>
                                    <td>{{ $sub_admin->email }}</td>
                                    <td>
                                        @if($sub_admin->status == APPROVED)

                                        <span class="badge badge-success">{{ tr('approved') }}</span>

                                        @else

                                        <span class="badge badge-warning">{{ tr('declined') }}</span>

                                        @endif
                                    </td>
                                    <td>{{ common_date($sub_admin->created_at , Auth::guard('admin')->user()->timezone) }}</td>

                                    <td>

                                        @include('admin.sub_admins._action')

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right" id="paglink">{{ $sub_admins->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /card -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->

@endsection