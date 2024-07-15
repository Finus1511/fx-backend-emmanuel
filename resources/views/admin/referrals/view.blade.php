@extends('layouts.admin') 

@section('content-header', tr('user_referrals'))

@section('breadcrumb')

    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index')}}">{{tr('user_referrals')}}</a></li>

    <li class="breadcrumb-item active" aria-current="page">
        <span>{{ tr('view_user_referrals') }}</span>
    </li> 
           
@endsection 

@section('content')

<section class="content">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-header border-bottom border-gray">

                      <h2><a href="{{route('admin.users.view',['user_id' => $referrals_code_details->user_id])}}">{{$referrals_code_details->username ?: tr('n_a')}}
                    </a></h2>
                </div>

                <div class="card-content collapse show">

                    <div class="card-body card-dashboard">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered table-striped tab-content">
                                    <tbody>
                                        <tr>
                                            <td>{{ tr('referral_code') }}</td>
                                            <td>{{$referrals_code_details->referral_code ?: tr('n_a')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('no_joined_users') }}</td>
                                            <td>{{$referrals_code_details->total_referrals}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('referral_earnings') }}</td>
                                            <td>{{$referrals_code_details->referral_earnings_formatted}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{ tr('referee_earnings') }}</td>
                                            <td>{{$referrals_code_details->referee_earnings_formatted}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered table-striped tab-content">
                                    <tbody>
                                        <tr>
                                            <td>{{ tr('total') }}</td>
                                            <td>{{$referrals_code_details->total_formatted}}</td>
                                        </tr>

                                          <tr>
                                            <td>{{ tr('used') }}</td>
                                            <td>{{$referrals_code_details->used_formatted}}</td>
                                        </tr>

                                          <tr>
                                            <td>{{ tr('remaining') }}</td>
                                            <td>{{$referrals_code_details->remaining_formatted}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><br>
                            <div class="col-md-12"><br>
                               <div><h6><strong>{{tr('referral_users')}} :</strong></h6></div>
                               <div class="table-responsive">
                                    <table id="dataTable" class="table table-striped table-bordered sourced-data">
                                    <thead>
                                        <tr>
                                            <th>{{tr('s_no')}}</th>
                                            <th>{{tr('username')}}</th>
                                            <th>{{tr('referral_code')}}</th>
                                            <th>{{tr('referral_earnings')}}</th>
                                            <th>{{tr('device_type')}}</th>
                                            <th>{{tr('created_at')}}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                       @if($referrals_user_details->isNotEmpty())

                                         @foreach($referrals_user_details as $i => $referral)
                                              <tr>
                                                <td>{{ $i+$referrals_user_details->firstItem() }}</td>

                                                <td>
                                                    <a href="{{route('admin.users.view',['user_id' => $referral->user_id])}}">
                                                    {{$referral->username}}
                                                    </a>
                                                </td>

                                                <td>{{$referral->referral_code}}</td>

                                                <td>{{$referrals_code_details->referral_earnings_formatted}}</td>

                                                <td>{{$referral->device_type}}</td>

                                                <td>{{common_date($referral->created_at , Auth::guard('admin')->user()->timezone)}}</td>
                                            </tr>

                                        @endforeach

                                        @endif
                                        
                                    </tbody>
                                
                                </table>
                                <div class="pull-right" id="paglink">{{ $referrals_user_details->appends(request()->input())->links('pagination::bootstrap-4') }}
                               </div>

                               </div>

                            </div>
                            
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
@endsection

