@extends('layouts.admin')

@section('title', tr('admin_chat_messages'))

@section('content-header', tr('admin_chat_messages'))

@section('breadcrumb')
    <li class="breadcrumb-item active">
        <a href="{{ route('admin.admin_chat_messages.index') }}">{{ tr('admin_chat_messages') }}</a>
    </li>
    <li class="breadcrumb-item">{{ tr('chat_messages') }}</li>
@endsection

@section('content')
   <section class="content">
    <div class="chat-ui-sec">
        <div class="chat-ui-sidebar">
            <h3>Chat</h3>&nbsp;
            <div class="chat-ui-sidebar-collapse">
                <div class="chat-ui-collapse-body">
                    <a href="{{ route('admin.admin_chat_messages.index', ['sort_by' => ALL_USERS]) }}" class="user-list-card" id="all-users">
                        <div class="user-list-img-sec">
                            <img src="{{ asset('images/chat/dummy-user-img.jpeg') }}" class="user-list-img">
                        </div>
                        <div class="user-list-info">
                            <h6>{{tr('all_users')}}</h6>
                            <p>Online</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.admin_chat_messages.index', ['sort_by' => CONTENT_CREATORS]) }}" class="user-list-card" id="content-creators">
                        <div class="user-list-img-sec">
                            <img src="{{ asset('images/chat/dummy-user-img.jpeg') }}" class="user-list-img">
                        </div>
                        <div class="user-list-info">
                            <h6>{{tr('content_creators')}}</h6>
                        </div>
                    </a>
                    <a href="{{ route('admin.admin_chat_messages.index', ['sort_by' => DEFAULT_USERS]) }}" class="user-list-card" id="default-users">
                        <div class="user-list-img-sec">
                            <img src="{{ asset('images/chat/dummy-user-img.jpeg') }}" class="user-list-img">
                        </div>
                        <div class="user-list-info">
                            <h6>{{tr('default_users')}}</h6>
                        </div>
                    </a> 
                </div>
            </div>
        </div>
        <div class="chat-ui-main-wrapper order-bottom">
            <div class="chat-ui-main-wrapper-header">
                <a href="#" class="user-click">
                    <div class="chat-ui-main-wrapper-user">
                        <div class="user-message-img-sec">
                            <img src="{{ $admin->picture ?: asset('placeholder.jpeg') }}" class="user-message-header-img">
                        </div>
                        <div class="user-message-header-info">
                            <h5>{{ $admin->name ?: tr('na') }}</h5>
                        </div>
                    </div>
                </a>
                <div class="chat-ui-main-wrapper-icon-sec">
                    <button class="btn btn-danger" data-toggle="modal" data-target="#bulkMessge{{ $admin->id }}">{{ tr('bulk_message') }}</button>
                </div>
            </div>
            <div class="chat-ui-main-wrapper-body">
                <div class="message-content-sec">
                    @forelse($admin_chat_messages as $key => $chat_message)
                        @if($chat_message->message)
                        <div class="message-right-align">
                            <div class="message-user-img-sec">
                                <img src="{{ $admin->picture ?: asset('placeholder.jpeg') }}" class="message-user-img">
                                <div class="status-offline"></div>
                            </div>
                                <div class="message-user-info">
                                    <h6>{{ $chat_message->message }}</h6>
                                    <p>{{common_date($chat_message->created_at , Auth::guard('admin')->user()->timezone)}}</p>
                                </div>
                        </div>
                        @endif
                        @if($chat_message->file)
                        <div class="message-right-align">
                        <div class="message-user-img-sec">
                            <img src="{{ $admin->picture ?: asset('placeholder.jpeg') }}" class="message-user-img">
                            <div class="status-offline"></div>
                        </div>
                        <div class="message-user-info message-user-info-1">   
                            @if(strpos($chat_message->file, '.mp4') !== false || strpos($chat_message->file, '.mkv') !== false)
                            <div class="chat-post-video">
                                <video controls class="chat-post-img" width="100%" height="100%">
                                    <source src="{{ $chat_message->file }}" type="video/mp4">
                                </video>
                            </div>
                            @else
                            <div class="chat-post-img">
                                <img src="{{ $chat_message->file }}" class="chat-img">
                            </div>
                            @endif
                            <p>{{common_date($chat_message->created_at , Auth::guard('admin')->user()->timezone)}}</p>
                        </div>
                    </div>
                    @endif
                        @empty
                        <div class="no-data-text">
                            {{ tr('message_not_found') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div id="bulkMessge{{ $admin->id }}" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <form method="post" action="{{ route('admin.admin_chat_messages.send_bulk_message') }}" method="POST" enctype="multipart/form-data" role="form">
                <input type="hidden" name="admin_id" value="{{ $admin->id }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ tr('admin_chat_messages') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" title="{{ tr('close') }}">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">{{ tr('select_user_type') }}*</label><br>
                                    <select required class="form-control select3" name="user_type">
                                        <option class="select-color" value="{{ ALL_USERS_NUMBER }}">{{ tr('all_users') }}</option>
                                        <option class="select-color" value="{{ CONTENT_CREATOR_NUMBER }}">{{ tr('content_creators') }}</option>
                                        <option class="select-color" value="{{ NORMAL_USER_NUMBER }}">{{ tr('normal_users') }}</option>
                                    </select>
                                </div>
                            </div>
                        <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ tr('select_file') }}</label>
                                    <input type="file" class="form-control" id="file" name="file" accept="image/png,image/jpeg,image/jpg,image/gif,image/svg+xml,video/mp4,video/mkv">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="last_name">{{ tr('message') }}</label><br>
                                    <textarea id="message" type="text" name="message" class="form-control" placeholder="{{ tr('message') }}"></textarea>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <div class="pull-right">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ tr('cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ tr('submit') }}</button>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $("#" + "{{request('sort_by')}}").addClass("active");
    });
</script>

@if(Session::has('receivers'))
<script src="https://cdn.socket.io/socket.io-1.0.0.js"></script>
<script>
    let socket = io('{{ Setting::get('chat_socket_url') }}');
    var receivers = {!! json_encode(Session::get('receivers')) !!};
    data = {
        'receivers' : receivers,
        'admin_id' : '{{ auth()->guard('admin')->user()->id }}',
     }
    socket.emit('admin message', data);

    socket.close();
</script>
@endif
@endsection