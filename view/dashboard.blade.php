@extends('layouts.admin')

@section('user_content')
        <div class="main-content-area">


            <div class="row">
              <hr/>
            </div>

            <div class="row">


                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Today's Transaction Pending Refers</span>
                            <h4>{{ $todays_refer_pending }}</h4>
                            <i class="fa fa-user red-bg"></i>
                            <h5>Total Refers : {{$total_refers}}</h5>
                        </div>
                    </div>
                </div> 

                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Today's Transaction Completed Refers</span>
                            <h4>{{ $todays_refer_completed }}</h4>
                            <i class="fa fa-user red-bg"></i>
                            <h5>-</h5>
                        </div>
                    </div>
                </div> 

                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Today's Signup</span>
                            <h4>{{$todays_signup}}</h4>
                            <i class="fa fa-user skyblue-bg"></i>
                            <h5>Total Users : {{$total_signup}}</h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Today's Earning</span>
                            <h4>${{$todays_earning}}</h4>
                            <i class="fa fa-usd green-bg"></i>
                            <h5>Total Earning : ${{$total_earning}}</h5>
                        </div>
                    </div><!-- Widget -->
                </div>


            </div>
            <div class="row">

                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Real Visitors</span>
                            <h4>{{$get_real_visitor}}</h4>
                            <i class="fa fa-area-chart blue-bg"></i>
                            <h5>-</h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Outstanding Credits</span>
                            <h4>{{$total_outstanding_credits}}</h4>
                            <i class="fa fa-area-chart skyblue-bg"></i>
                            <h5> ${{ round($total_outstanding_credits/1000) }} </h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Affiliate Upgrades</span>
                            <h4>${{$todays_affiliate * 99.99}} ( {{$todays_affiliate}} )</h4>
                            <i class="fa fa-usd green-bg"></i>
                            <h5>Total Upgrades : ${{$total_affiliate * 99.99}}</h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Deposits Made Today</span>
                            <h4>{{$todays_deposite}}</h4>
                            <i class="fa fa-area-chart blue-bg"></i>
                            <h5>Total Deposits : {{$total_deposite}}</h5>
                        </div>
                    </div><!-- Widget -->
                </div>

            </div>

            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Pending Withdrawal Requests</span>
                            <h4>{{$total_withdrawal_requests}}</h4>
                            <i class="fa fa-clock-o red-bg"></i>
                            <h5>-</h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Total Paid Withdrawal Request </span>
                            <h4>${{round($total_withdrawal_request_amount)}}</h4>
                            <i class="fa fa-usd green-bg"></i>
                            <h5> - </h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Total Running Offers</span>
                            <h4>{{$total_running_offers}}</h4>
                            <i class="fa fa-area-chart skyblue-bg"></i>
                            <h5>Total Compl. Offers : {{$total_completed_offers}}</h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Affiliate Pro Upgrades</span>
                            <h4>${{$todays_affiliate_pro * 199.99}} ( {{$todays_affiliate_pro}} )</h4>
                            <i class="fa fa-usd green-bg"></i>
                            <h5>Total Upgrades : ${{$total_affiliate_pro * 99.99}}</h5>
                        </div>
                    </div><!-- Widget -->
                </div>
            </div>


            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Today Unlimited View Subscription Signup</span>
                            <h4>{{$todays_unlimited_subscription_signup}}</h4>
                            <i class="fa fa-area-chart skyblue-bg"></i>
                            <h5>Total Signups : {{$total_unlimited_subscription_signup}}</h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Today Unlimited View Subscription Cancel</span>
                            <h4>{{$todays_unlimited_subscription_cancel}}</h4>
                            <i class="fa fa-area-chart skyblue-bg"></i>
                            <h5>Total Cancels : {{$total_unlimited_subscription_cancel}}</h5>
                        </div>
                    </div><!-- Widget -->
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="widget">
                        <div class="quick-report-widget">
                            <span>Total Unlimited View Subscription Activate</span>
                            <h4>{{$total_unlimited_subscription_activate}}</h4>
                            <i class="fa fa-area-chart skyblue-bg"></i>
                            <h5>-</h5>
                        </div>
                    </div>
                </div>
                
            </div>



            <div class="row">
                <div class="col-md-12">
                    <div class="widget blank no-padding">
                        <div class="panel panel-default work-progress-table">
                              <!-- Default panel contents -->
                            <div class="panel-heading">
                                10 Latest Follow offers</i>
                                <span style="float:right">
                                    <a href="{{ route('all_follow_offer') }}" class="btn btn-block blue-bg add_credit_button">
                                       See All Follow Offers
                                    </a>
                                </span>
                            </div>
                              <!-- Table -->
                            <table class="table">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Repeat </th>
                                  </tr>
                                </thead>
                                <tbody>

                                        @if(count($follow_offers) > 0)
                                            @foreach($follow_offers as $key => $offer)
                                                <?php 
                                                $completion_percentage =  (( $offer->recieved_view_follow / $offer->quantity_require )  * 100 ) ;
                                                $completion_percentage =   number_format($completion_percentage,0);
                                                if($completion_percentage < 15){
                                                    $completion_percentage_ = 15 ;
                                                }else{
                                                    $completion_percentage_ = $completion_percentage ;
                                                }
                                                ?> 
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>
                                                        <a href="{{ route('admin_user_detail', [ 'id' => $offer->user_id ]) }}">{{$offer->display_name}}</a>
                                                    </td>
                                                    <td>
                                                        <div class="progress">
                                                            <div style="width: {{ $completion_percentage_ }}%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{$completion_percentage}}" role="progressbar" class="red progress-bar">
                                                            <span>{{$completion_percentage}}%</span>
                                                           </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($offer->status == 'Completed')
                                                            <span class="label label-success">
                                                               {{$offer->status}}
                                                            </span>
                                                        @else
                                                            <span class="label label-warning">
                                                               {{$offer->status}}
                                                            </span>
                                                        @endif 
                                                    </td>
                                                    <td>
                                                        
                                                        @php
                                                          $plan_count = get_repeat_plan_count( $offer->user_id )
                                                        @endphp

                                                        @if($plan_count > 1)
                                                            <span class="label label-success">
                                                               Yes ( {{$plan_count}} )
                                                            </span>
                                                        @else
                                                            <span class="label label-danger">
                                                               No
                                                            </span>
                                                        @endif
                                                        
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" align="center"> No record Found </td>
                                            </tr>        
                                        @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="widget blank no-padding">
                        <div class="panel panel-default work-progress-table">
                              <!-- Default panel contents -->
                            <div class="panel-heading">
                                10 Latest Channel View Offers</i>
                                <span style="float:right">
                                    <a href="{{ route('all_channel_view_offer') }}" class="btn btn-block blue-bg add_credit_button">
                                       See All Channel View Offers
                                    </a>
                                </span>
                            </div>
                              <!-- Table -->
                            <table class="table">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Repeat </th>
                                  </tr>
                                </thead>
                                <tbody>
                                 
                                        @if(count($view_channel_offers) > 0)
                                            @foreach($view_channel_offers as $key => $offer)
                                                <?php 
                                                $completion_percentage =  (( $offer->recieved_view_follow / $offer->quantity_require )  * 100 ) ;
                                                $completion_percentage =   number_format($completion_percentage,0);
                                                if($completion_percentage < 15){
                                                    $completion_percentage_ = 15 ;
                                                }else{
                                                    $completion_percentage_ = $completion_percentage ;
                                                }
                                                ?> 
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>
                                                        <a href="{{ route('admin_user_detail', [ 'id' => $offer->user_id ]) }}">{{$offer->display_name}}</a>
                                                    </td>
                                                    <td>
                                                        <div class="progress">
                                                            <div style="width: {{ $completion_percentage_ }}%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{$completion_percentage}}" role="progressbar" class="red progress-bar">
                                                            <span>{{$completion_percentage}}%</span>
                                                           </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($offer->status == 'Completed')
                                                            <span class="label label-success">
                                                               {{$offer->status}}
                                                            </span>
                                                        @else
                                                            <span class="label label-warning">
                                                               {{$offer->status}}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                          $plan_count = get_repeat_plan_count( $offer->user_id )
                                                        @endphp

                                                        @if($plan_count > 1)
                                                            <span class="label label-success">
                                                               Yes ( {{$plan_count}} )
                                                            </span>
                                                        @else
                                                            <span class="label label-danger">
                                                               No
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" align="center"> No record Found </td>
                                            </tr>        
                                        @endif
                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="widget blank no-padding">
                        <div class="panel panel-default work-progress-table">
                              <!-- Default panel contents -->
                            <div class="panel-heading">
                                10 Latest Stream View Offers</i>
                                <span style="float:right">
                                    <a href="{{ route('all_view_offer') }}" class="btn btn-block blue-bg add_credit_button">
                                       See All Stream View Offers
                                    </a>
                                </span>
                            </div>
                              <!-- Table -->
                            <table class="table">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Repeat </th>
                                  </tr>
                                </thead>
                                <tbody>
                                  
                                        @if(count($view_offers) > 0)
                                            @foreach($view_offers as $key => $offer)
                                                <?php 
                                                

                                                if($offer->quantity_require != 'unlimited'){
                                                    $completion_percentage =  (( $offer->recieved_view_follow / $offer->quantity_require )  * 100 ) ;
                                                    $completion_percentage =   number_format($completion_percentage,0);
                                                    $is_unlimited =  false ; 
                                                }else{
                                                    $completion_percentage = 50 ;
                                                    $is_unlimited =  true ; 
                                                }
                                                

                                                if($completion_percentage < 15){
                                                    $completion_percentage_ = 15 ;
                                                }else{
                                                    $completion_percentage_ = $completion_percentage ;
                                                }
                                                ?> 
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td>
                                                        <a href="{{ route('admin_user_detail', [ 'id' => $offer->user_id ]) }}">{{$offer->display_name}}</a>
                                                    </td>
                                                    <td>
                                                        @if(!$is_unlimited)
                                                            <div class="progress">
                                                                <div style="width: {{ $completion_percentage_ }}%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{$completion_percentage}}" role="progressbar" class="red progress-bar">
                                                                <span>{{$completion_percentage}}%</span>
                                                               </div>
                                                            </div>
                                                        @else
                                                            <span class="label label-primary">Unlimited</span> 
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($offer->status == 'Completed')
                                                            <span class="label label-success">
                                                               {{$offer->status}}
                                                            </span>
                                                        @else
                                                            <span class="label label-warning">
                                                               {{$offer->status}}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                          $plan_count = get_repeat_plan_count( $offer->user_id )
                                                        @endphp

                                                        @if($plan_count > 1)
                                                            <span class="label label-success">
                                                               Yes ( {{$plan_count}} )
                                                            </span>
                                                        @else
                                                            <span class="label label-danger">
                                                               No
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" align="center"> No record Found </td>
                                            </tr>        
                                        @endif

                                  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


@endsection