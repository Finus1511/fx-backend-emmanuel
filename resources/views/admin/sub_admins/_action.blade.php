<div class="dropdown">
    <button class="btn dropdown-toggle btn-warning" type="button" data-toggle="dropdown">{{tr('action')}}</button>
    <div class="dropdown-menu dropdown-grid cols-4 action-dropdown-menu">

        {{-- sub_admins CRUD Actions start --}}

        <a class="dropdown-item" href="{{route('admin.sub_admin.view', ['sub_admin_id' => $sub_admin->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/view_details.svg')}}" alt="view"/>
            <span class="title">{{tr('view')}}</span>
        </a>

        @if(Setting::get('is_demo_control_enabled') == YES)

            <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('edit') }}</a>

            <a class="dropdown-item" href="javascript:void(0)">&nbsp;{{ tr('delete') }}</a>

        @else 

            <a class="dropdown-item" href="{{route('admin.sub_admin.edit', ['sub_admin_id' => $sub_admin->id])}}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/edit_image.svg')}}" alt="about.svg"/>
                <span class="title">{{tr('edit')}}</span>
            </a>
            
            <a class="dropdown-item" onclick="return confirm(&quot;{{ tr('sub_admin_delete_confirmation' , $sub_admin->name) }}&quot;);" href="{{ route('admin.sub_admin.delete', ['sub_admin_id' => $sub_admin->id,'page'=>request()->input('page')] ) }}">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/full_trash.svg')}}" alt="full_trash.svg"/>
                <span class="title">{{tr('delete')}}</span>
            </a>

        @endif

        {{-- sub_admins CRUD Actions end --}}

        {{-- sub_admin Approve/Decline actions start --}}

        @if($sub_admin->status == APPROVED)

            <a class="dropdown-item" href="{{route('admin.sub_admin.status', ['sub_admin_id' => $sub_admin->id])}}" onclick="return confirm(&quot;{{ $sub_admin->name }} - {{ tr('sub_admin_decline_confirmation') }}&quot;);">
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/disapprove.svg')}}" alt="disapprove.svg"/>
                <span class="title">{{tr('decline')}}</span>
            </a>

        @else

            <a class="dropdown-item" href="{{route('admin.sub_admin.status', ['sub_admin_id' => $sub_admin->id])}}" >
                <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/approve.svg')}}" alt="approve.svg"/>
                <span class="title">{{tr('approve')}}</span>
            </a>

        @endif

        <a class="dropdown-item" href="{{route('admin.sub_admin.role_access', ['sub_admin_id' => $sub_admin->id])}}">
            <img class="icon" src="{{asset('admin-assets/vendors/flat-color-icons/instagram-followers.svg')}}" alt="instagram-followers.svg"/>
            <span class="title">{{tr('role_access')}}</span>
        </a>

    </div>
</div>
