@extends('layouts.admin')

@section('title', tr('featured_users'))

@section('content-header', tr('featured_users'))

@section('breadcrumb')

<li class="breadcrumb-item active">
    <a href="{{route('admin.users.index')}}">{{ tr('featured_users') }}</a>
</li>

<li class="breadcrumb-item">{{tr('featured_users')}}</li>

@endsection

@section('content')

<section class="content">
    
    <div class="row">

        <div class="col-12">

            <div class="card blocked-user-sec">

                <div class="card-header border-bottom border-gray">

                    <h4 class="card-title">{{tr('featured_users')}}</h4>


                </div>

                <div class="box box-outline-purple">

                <div class="box-body">

                    <div class="table-responsive">
                     
                       @include('admin.users.featured_users._search')

                        <table id="dataTable" class="table table-bordered table-striped display nowrap margin-top-10">

                            <thead>
                                <tr>
                                    <th>{{ tr('s_no') }}</th>
                                    <th>{{ tr('featured_user') }}</th>
                                    <th>{{ tr('email') }}</th>
                                    <th>{{ tr('status') }}</th>
                                    <th>{{ tr('action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($featured_users as $i => $user)

                                <tr>

                                    <td>{{ $i+$featured_users->firstItem() }}</td>

                                    <td><a href="{{ route('admin.users.view', ['user_id' => $user->id] ) }}">{{$user->name ?: tr('n_a')}}</a>
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.users.view', ['user_id' => $user->id] ) }}">{{$user->email ?: tr('n_a')}}
                                        </a>
                                    </td>

                                    <td>
                                       <span class="badge badge-{{ $user->status == USER_APPROVED ? 'success' : 'warning' }}">{{ $user->status == USER_APPROVED ? tr('approved') : tr('declined') }}</span>
                                    </td>

                                    <td>
                                       <a href="{{ route('admin.users.remove_featured_user', ['user_id' => $user->id] ) }}" class="btn btn-sm btn-danger">&nbsp;{{ tr('remove') }}</a>
                                    </td>

                                </tr>


                                @endforeach

                            </tbody>

                        </table>

                        <div class="pull-right resp-float-unset" id="paglink">{{ $featured_users->appends(request()->input())->links('pagination::bootstrap-4') }}</div>

                    </div>

                </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection