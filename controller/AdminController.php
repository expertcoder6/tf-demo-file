<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\User;
use App\Models\Admin;
use App\Models\Offer;
use App\Models\FollowViewOffer;
use App\Models\Transaction;
use App\Models\Refer;
use App\Models\WithdrawalRequest;
use App\Models\UserIps;
use App\Models\UserSubscription;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    /*
    * Login Form
    */
    public function login( Request $request )
    {
        return view('admin.login');
    }//end of the function

    /*
    * Login Action
    */
    public function login_action( Request $request ){
        $input = $request->all();
        $username = $input['username'];
        $password = $input['password'];
        if($username != "" and $password != "" ){
            
            $admin = Admin::where('username',$username)
                          ->where('password',$password)
                          ->get();
            if(count($admin) > 0) { 
                Session::put('login_admin',$admin[0]);
                Session::save();
                return redirect()->route('admin_dashboard');
            }else{
                $_SESSION['errorAdmin'] = "Please Enter Correct Username And Password" ;
                return redirect()->route('admin_login');
            }    
             
        }//end of the condition
        else{
            $_SESSION['errorAdmin'] = "Please Fill Username And Password" ;
            return redirect()->route('admin_login');
        }//end of the else condition
            
    }//end of the function

    /*
    * Dashboard
    */
    public function dashboard( Request $request )
    {   
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        //get singup user info
        $todays_signup = User::whereDate('created_at', '=', date('Y-m-d'))->count(); 
        $total_signup  = User::where('_id','>',0)->count();

        //get affiliate  info
        $todays_affiliate =    Transaction::where('mc_gross',99.99)
                               ->whereDate('created_at', '=', date('Y-m-d'))
                               ->where('payment_status','Completed')
                               ->count();                              
        $total_affiliate  =    Transaction::where('mc_gross',99.99)
                               ->where('payment_status','Completed')
                               ->count();  

        //get affiliate pro info
        $todays_affiliate_pro =    Transaction::where('mc_gross',199.99)
                               ->whereDate('created_at', '=', date('Y-m-d'))
                               ->where('payment_status','Completed')
                               ->count();                              
        $total_affiliate_pro  =    Transaction::where('mc_gross',199.99)
                               ->where('payment_status','Completed')
                               ->count();                        

        //get total earning info
        $todays_earning =    Transaction::whereDate('created_at', '=', date('Y-m-d'))
                               ->where('payment_status','Completed')
                               ->sum('mc_gross')  ;              
        $total_earning  =    Transaction::where('payment_status','Completed')
                              ->sum('mc_gross')  ; 

        //get transaction count
        $todays_deposite =    Transaction::whereDate('created_at', '=', date('Y-m-d'))
                               ->where('payment_status','Completed')
                               ->count()  ;              
        $total_deposite  =    Transaction::where('payment_status','Completed')
                              ->count()  ;                       

        //total outstanding credits
        $total_outstanding_credits = User::sum('total_earn_credits');                          

        //get current total visitor
        $current_time = date('Y-m-d H:i:s');                      
        $get_real_visitor = User::whereRaw("DATE_ADD(last_activity, INTERVAL 5 MINUTE) >= '".$current_time."'")->count();    

        //get 10 latest follow offers
        $follow_offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id')  
                  ->where('offer_type','follow')
                  ->orderBy('id', 'desc')
                  ->limit(10)
                  ->select('streamer_offers.*','users.display_name')
                  ->get();     

        //get 10 latest channel view offers
        $view_channel_offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id')  
                  ->where('offer_type','view channel')
                  ->orderBy('id', 'desc')
                  ->limit(10)
                  ->select('streamer_offers.*','users.display_name')
                  ->get();       

        //get 10 latest channel view offers
        $view_offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id')  
                  ->where('offer_type','view')
                  ->orderBy('id', 'desc')
                  ->limit(10)
                  ->select('streamer_offers.*','users.display_name')
                  ->get();     

        
        $todays_refer_pending =  Refer::whereDate('created_at', '=', date('Y-m-d'))     
                         ->where('refer_id','<>',0)->where('status','Pending')->count();                 
        $todays_refer_completed =  Refer::whereDate('created_at', '=', date('Y-m-d'))     
                         ->where('refer_id','<>',0)->where('status','Completed')->count();                 

        //get total refers
        $total_refers = Refer::where('refer_id','<>',0)->count();  

        //total pending requests
        $total_withdrawal_requests =  DB::table('withdrawal_request')
                                      ->where('status', 'Under Review' ) 
                                      ->count();         

        //get total earning info
        $total_withdrawal_request_amount  = DB::table('withdrawal_request')
                                            ->where('status', 'Paid' ) 
                                            ->sum('request_amount')  ; 
        //total running offers                                                
        $total_running_offers             = Offer::where('status','Processing')
                                            ->count()  ;    

        //total running offers                                                
        $total_completed_offers           = Offer::where('status','Completed')
                                            ->count()  ;   



        //Get subscription signup
        $todays_unlimited_subscription_signup = 
            UserSubscription::whereDate('created_at', '=', date('Y-m-d'))
                               ->where('txn_type','subscr_signup')
                               ->count()  ;              
        $total_unlimited_subscription_signup  = 
            UserSubscription::where('txn_type','subscr_signup')
                              ->count()  ; 


        //Get subscription signup
        $todays_unlimited_subscription_cancel = 
            UserSubscription::whereDate('created_at', '=', date('Y-m-d'))
                               ->where('txn_type','subscr_cancel')
                               ->count()  ;              
        $total_unlimited_subscription_cancel  = 
            UserSubscription::where('txn_type','subscr_cancel')
                              ->count()  ;                         

        $total_unlimited_subscription_activate = $total_unlimited_subscription_signup - $total_unlimited_subscription_cancel ;


        return view('admin.dashboard',[ 
                                       'page_title'=> 'Admin Dashboard' , 
                                       'login_admin' => $login_admin ,
                                       'todays_signup' => $todays_signup ,
                                       'total_signup' => $total_signup ,
                                       'todays_affiliate' => $todays_affiliate ,
                                       'total_affiliate' => $total_affiliate ,
                                       'todays_earning' => $todays_earning ,
                                       'total_earning' => $total_earning ,
                                       'get_real_visitor' => $get_real_visitor ,
                                       'follow_offers' => $follow_offers ,
                                       'view_channel_offers' => $view_channel_offers ,
                                       'view_offers' => $view_offers ,
                                       'todays_deposite' => $todays_deposite ,
                                       'total_deposite' => $total_deposite ,
                                       'total_outstanding_credits' => $total_outstanding_credits,
                                       
                                       /*'todays_refer' => $todays_refer , */
                                       'todays_refer_pending' => $todays_refer_pending ,
                                       'todays_refer_completed' => $todays_refer_completed ,

                                       'total_refers'=> $total_refers ,
                                       'total_withdrawal_requests' => $total_withdrawal_requests,
                                       'total_withdrawal_request_amount' => $total_withdrawal_request_amount,
                                       'total_running_offers' => $total_running_offers ,
                                       'total_completed_offers' => $total_completed_offers ,

                                       'todays_affiliate_pro' => $todays_affiliate_pro ,
                                       'total_affiliate_pro' => $total_affiliate_pro,

                                       'todays_unlimited_subscription_signup'=>
                                       $todays_unlimited_subscription_signup ,
                                       'total_unlimited_subscription_signup'=>
                                       $total_unlimited_subscription_signup,
                                       'todays_unlimited_subscription_cancel'=>
                                       $todays_unlimited_subscription_cancel,
                                       'total_unlimited_subscription_cancel'=>
                                       $total_unlimited_subscription_cancel,
                                       'total_unlimited_subscription_activate'=>
                                       $total_unlimited_subscription_activate

                                       ]);  
    }//end of the function

    
    /*
    * All Users
    */
    public function all_users( Request $request )
    {   
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        //$users = User::all();           

        $limit = 50 ;
        $input = $request->all();
        if(isset($input['search'])){
            //get users
            $users = User::where('display_name','LIKE', '%' . $input['search'] . '%')
                    ->orWhere('email','LIKE', '%' . $input['search'] . '%') 
                    ->orderBy('id','desc')
                    ->paginate($limit);
     
            $users->appends(['search' => $input['search']]);

            //get total users
            $total_users =  User::where('display_name','LIKE', '%' . $input['search'] . '%')
                            ->orWhere('email','LIKE', '%' . $input['search'] . '%')
                            ->count();

            //search keyword                
            $search_keyword = $input['search'] ;   
            $sort = "" ;             
        }else if(isset($input['sort'])){
            //get users
            if($input['sort'] == "id_asc"){
              $users = User::orderBy('id','asc')->paginate($limit);
            }else if($input['sort'] == "id_desc"){
              $users = User::orderBy('id','desc')->paginate($limit);
            }else if($input['sort'] == "credit_asc"){
              $users = User::orderBy('total_credit_points','asc')->paginate($limit);
            }else if($input['sort'] == "credit_desc"){
              $users = User::orderBy('total_credit_points','desc')->paginate($limit);
            }else if($input['sort'] == "name"){
              $users = User::orderBy('name','asc')->paginate($limit);
            }else{
              $users = User::orderBy('id','asc')->paginate($limit);
            }  

            $users->appends(['sort' => $input['sort']]);

            //get total users
            $total_users = User::count();

            //search keyword                
            $search_keyword = '';
            $sort = $input['sort'] ;
        }else{
            //get users
            $users = User::orderBy('id','desc')->paginate($limit);

            //get total users
            $total_users = User::count();

            //search keyword                
            $search_keyword = '';
            $sort = "" ;
        }
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }  

        return view('admin.all_users',[ 
                                       'page_title'=> 'All Users' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'users' => $users ,
                                       'total_users' => $total_users ,
                                       'search_keyword' => $search_keyword,
                                       'sort' => $sort
                                       ]);  
    }//end of the function 

    /*
    * Add Credits
    */
    public function  add_credit( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }
        
        
        $input = $request->all();
        $credit = $input['credit'];
        $credit_user_id = $input['credit_user_id'];

        //update credit of login user
        $uo = User::where('id',$credit_user_id)->get() ;
        if(count($uo) > 0 ){
            $userObj = User::find($credit_user_id); 
            $userObj->total_credit_points = $userObj->total_credit_points + $credit;
            $userObj->save();

            $_SESSION['msgAdmin'] = $credit  ." Credit Added To ".$userObj->display_name ."'s Account Successfully";
        }else{
            $_SESSION['errorAdmin'] = "Credits Not Added Successfully , Something Went Wrong " ;
        }
        
        return redirect()->route('all_users');

        
    }//end of the function


    /*
    * User Detail
    */
    function user_detail( $id , Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $client_id = env('TWITCH_CLIENT_ID') ;
        $input = $request->all();
        $login_admin = Session::get('login_admin');
        $userObj = User::where('id',$id)->get();

        if(count($userObj) > 0 ){

            $uo = User::find($id); 

            /*
            Get user channel info of user
            */
            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, "https://api.twitch.tv/kraken/channels/".$uo->_id);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
            $headers = array();
            $headers[] = "Accept: application/vnd.twitchtv.v5+json";
            $headers[] = "Client-Id: ".$client_id;
            curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
            $result1 = curl_exec($ch1);
            if (curl_errno($ch1)) {
                echo 'Error:' . curl_error($ch1);
                return redirect('/');
            }
            curl_close ($ch1);
            $response = json_decode($result1);

            //get offers of that user
            $offers = Offer::where('user_id',$id)->get();

            //get all transation of that user
            $transactions = Transaction::where('user_id',$id)->get();


            //get all subscription detail of that user
            $subscriptions =  UserSubscription::where('user_id',$id)
                              ->orderBy('id','desc')
                              ->get();

              
            $refers =  DB::table('refer_logs as r')
                          ->join('users as u', 'u.id', '=', 'r.user_id')
                          ->where( 'r.refer_id' , $id )
                          ->orderBy('r.created_at', 'desc')
                          ->select('r.*','u.user_ip')
                          ->get(); 

$admincreditsrevertlists =	DB::select('call sp_credits_revert_list(?)',array($id));						  

            return view('admin.user_detail',[ 
                                       'page_title'=> 'User Detail' , 
                                       'login_admin' => $login_admin ,
                                       'user' => $uo ,
                                       'channel_info' => $response ,
                                       'offers' => $offers ,
                                       'transactions' => $transactions ,
                                       'refers' => $refers ,
                                       'subscriptions' => $subscriptions,
									   'admincreditsrevertlists' =>$admincreditsrevertlists
                                       ]);   
        }else{

            $_SESSION['errorAdmin'] = "Something Went Wrong!" ;
            return redirect()->route('all_users');

        }
    }//end of the function

    /*
    * All follow offer page
    */
    public function all_follow_offer( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        $limit = 20 ;
        $input = $request->all();
        if(isset($input['search'])){
            //get offers
            $offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id') 
                  ->where('display_name','LIKE', '%' . $input['search'] . '%') 
                  ->where('offer_type','follow')
                  ->orderBy('id', 'desc')
                  ->select('streamer_offers.*','users.display_name','users.email')
                  ->paginate($limit);     
            $offers->appends(['search' => $input['search']]);

            //get total offers
            $total_offers =   DB::table('streamer_offers')
                              ->join('users', 'users.id', '=', 'streamer_offers.user_id') 
                              ->where('display_name','LIKE', '%' . $input['search'] . '%') 
                              ->where('offer_type','follow')
                              ->count();
            //search keyword                  
            $search_keyword =  $input['search'] ;                 
        }else{
            //get offers
            $offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id')  
                  ->where('offer_type','follow')
                  ->orderBy('id', 'desc')
                  ->select('streamer_offers.*','users.display_name','users.email')
                  ->paginate($limit);  

            //get total offers      
            $total_offers = DB::table('streamer_offers')
                            ->join('users', 'users.id', '=', 'streamer_offers.user_id')  
                            ->where('offer_type','follow')
                            ->count(); 

            //search keyword                  
            $search_keyword =  '' ;
        }
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }  

        return view('admin.all_follow_offer',[ 
                                       'page_title'=> 'All Follow Offers' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'offers' => $offers ,
                                       'total_offers' => $total_offers ,
                                       'search_keyword' => $search_keyword
                                       ]);  
    }//end of the function


    /*
    * All channel view offer
    */
    public function all_channel_view_offer( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        $limit = 20 ;
        $input = $request->all();
        if(isset($input['search'])){
            //get offers
            $offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id') 
                  ->where('display_name','LIKE', '%' . $input['search'] . '%') 
                  ->where('offer_type','view channel')
                  ->orderBy('id', 'desc')
                  ->select('streamer_offers.*','users.display_name','users.email')
                  ->paginate($limit);     
            $offers->appends(['search' => $input['search']]);

            //get total offers      
            $total_offers = DB::table('streamer_offers')
                            ->join('users', 'users.id', '=', 'streamer_offers.user_id')  
                            ->where('display_name','LIKE', '%' . $input['search'] . '%') 
                            ->where('offer_type','view channel')
                            ->count(); 

            //search keyword                  
            $search_keyword =  $input['search'] ;
        }else{
            //get offers
            $offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id')  
                  ->where('offer_type','view channel')
                  ->orderBy('id', 'desc')
                  ->select('streamer_offers.*','users.display_name','users.email')
                  ->paginate($limit);    

            //get total offers      
            $total_offers = DB::table('streamer_offers')
                            ->join('users', 'users.id', '=', 'streamer_offers.user_id')   
                            ->where('offer_type','view channel')
                            ->count(); 

            //search keyword                  
            $search_keyword =  '' ;       
        }
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }  

        return view('admin.all_channel_view_offer',[ 
                                       'page_title'=> 'All Channel View Offers' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'offers' => $offers ,
                                       'total_offers' => $total_offers ,
                                       'search_keyword' => $search_keyword
                                       ]);  
    }//end of the function

    /*
    * All stream view offer
    */
    public function all_view_offer( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        $limit = 20 ;
        $input = $request->all();
        if(isset($input['search'])){
            //get offers
            $offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id') 
                  ->where('display_name','LIKE', '%' . $input['search'] . '%') 
                  ->where('offer_type','view')
                  ->orderBy('id', 'desc')
                  ->select('streamer_offers.*','users.display_name','users.email')
                  ->paginate($limit);     
            $offers->appends(['search' => $input['search']]);

            //get total offers      
            $total_offers = DB::table('streamer_offers')
                            ->join('users', 'users.id', '=', 'streamer_offers.user_id') 
                            ->where('display_name','LIKE', '%' . $input['search'] . '%')   
                            ->where('offer_type','view')
                            ->count(); 

            //search keyword                  
            $search_keyword =  $input['search'] ;  
        }else{
            //get offers
            $offers = DB::table('streamer_offers')
                  ->join('users', 'users.id', '=', 'streamer_offers.user_id')  
                  ->where('offer_type','view')
                  ->orderBy('id', 'desc')
                  ->select('streamer_offers.*','users.display_name','users.email')
                  ->paginate($limit);     

            //get total offers      
            $total_offers = DB::table('streamer_offers')
                            ->join('users', 'users.id', '=', 'streamer_offers.user_id')   
                            ->where('offer_type','view')
                            ->count(); 

            //search keyword                  
            $search_keyword =  '' ;      
        }
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }  

        return view('admin.all_view_offer',[ 
                                       'page_title'=> 'All Stream View Offers' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'offers' => $offers ,
                                       'total_offers' => $total_offers ,
                                       'search_keyword' => $search_keyword
                                       ]);  
    }//end of the function


    /*
    * All Deposites
    */
    public function all_deposite( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        $limit = 20 ;
        $input = $request->all();
        if(isset($input['search'])){
            
            $search = $input['search'] ;      
            $transactions = DB::table('transaction')
                  ->join('users', 'users.id', '=', 'transaction.user_id') 
                  ->where(function($q) use ($search) {
                      $q->where('display_name','LIKE', '%' . $search . '%')
                        ->orWhere('txn_id', 'LIKE' , '%'.$search.'%');
                  })
                  ->orderBy('id', 'desc')
                  ->select('transaction.*','users.display_name','users.email')
                  ->paginate($limit);         

            $transactions->appends(['search' => $input['search']]);
        }else{
            $search =  '';  
            $transactions = DB::table('transaction')
                  ->join('users', 'users.id', '=', 'transaction.user_id')  
                  ->orderBy('id', 'desc')
                  ->select('transaction.*','users.display_name','users.email')
                  ->paginate($limit);     
        }
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }  

        return view('admin.all_deposite',[ 
                                       'page_title'=> 'All Deposites' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'transactions' => $transactions ,
                                       'search' => $search
                                       ]);  
    }//end of the function


    /*
    * All Refers
    */
    public function all_refer( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        $limit = 20 ;
        $input = $request->all();
        if(isset($input['search'])){
            $refers = DB::table('refer_logs')
                  ->where('refer_name','LIKE', '%' . $input['search'] . '%') 
                  ->orWhere('user_name','LIKE', '%' . $input['search'] . '%') 
                  ->orderBy('id', 'desc')
                  ->select('refer_logs.*')
                  ->paginate($limit);     
            $refers->appends(['search' => $input['search']]);
        }else{
            $refers = DB::table('refer_logs')
                  ->orderBy('id', 'desc')
                  ->select('refer_logs.*')
                  ->paginate($limit);     
        }
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }  

        return view('admin.all_refer',[ 
                                       'page_title'=> 'All Refers' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'refers' => $refers
                                       ]);  
    }//end of the function


    /*
    * User Detail
    */
    function user_log( $id , Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $input = $request->all();
        $login_admin = Session::get('login_admin');
        $userObj = User::where('id',$id)->get();

        if(count($userObj) > 0 ){
            //$uo = User::where('id',$credit_user_id)->get() ;

            $limit = 20 ;
            $input = $request->all();
            if(isset($input['search'])){
                $user_logs =  DB::table('viewed_followed_offers as vfo')
                              ->join('streamer_offers as so', 'so.id', '=', 'vfo.streamer_offer_id')
                              ->join('users as u', 'u.id', '=', 'vfo.viewer_id')
                              ->join('users as us', 'us.id', '=', 'so.user_id')
                              ->where('u.display_name','LIKE', '%' . $input['search'] . '%') 
                              //->orWhere('us.display_name','LIKE', '%' . $input['search'] . '%') 
                              ->where('so.user_id',$id)
                              ->orWhere('vfo.viewer_id',$id)
                              ->orderBy('vfo.created_at', 'desc')
                              ->select('vfo.*','so.user_id','so.status','u.name as viewer_name','us.name as streamer_name')
                              ->paginate($limit);
                $user_logs->appends(['search' => $input['search']]);
            }else{
                $user_logs =  DB::table('viewed_followed_offers as vfo')
                              ->join('streamer_offers as so', 'so.id', '=', 'vfo.streamer_offer_id')
                              ->join('users as u', 'u.id', '=', 'vfo.viewer_id')
                              ->join('users as us', 'us.id', '=', 'so.user_id') 
                              ->where('so.user_id',$id)
                              ->orWhere('vfo.viewer_id',$id)
                              ->orderBy('vfo.created_at', 'desc')
                              ->select('vfo.*','so.user_id','so.status','u.name as viewer_name','us.name as streamer_name')
                              ->paginate($limit);    
            }


            return view('admin.user_log',[ 
                                       'page_title'=> 'User Logs' , 
                                       'login_admin' => $login_admin ,
                                       'user' => $userObj ,
                                       'user_logs' => $user_logs ,
                                       'user_id' => $id
                                       ]);   
        }else{

            $_SESSION['errorAdmin'] = "Something Went Wrong!" ;
            return redirect()->route('all_users');

        }
    }//end of the function

    /*
    * All withdrawal request list
    */
    function all_pending_withdrawal_request( Request $request ){
          if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
          }

          $login_admin = Session::get('login_admin');

          $limit = 20 ;
          $input = $request->all();
          if(isset($input['search'])){
              $withdrawal_requests =  DB::table('withdrawal_request as wr')
                              ->join('users as us', 'us.id', '=', 'wr.user_id')
                              ->where('wr.status', 'Under Review' ) 
                              ->where('us.display_name','LIKE', '%' . $input['search'] . '%') 
                              ->orderBy('wr.created_at', 'desc')
                              ->select('wr.*','us.display_name','us.paypal_email')
                              ->paginate($limit);
              $withdrawal_requests->appends(['search' => $input['search']]);
          }else{
              $withdrawal_requests =  DB::table('withdrawal_request as wr')
                              ->join('users as us', 'us.id', '=', 'wr.user_id') 
                              ->where('wr.status', 'Under Review' ) 
                              ->orderBy('wr.created_at', 'desc')
                              ->select('wr.*','us.display_name','us.paypal_email')
                              ->paginate($limit);
          }
          
          if(isset($input['page'])) {
              $number_start = $limit * ($input['page'] - 1) ;
          }else{
              $number_start = 0 ;
          }  

          return view('admin.all_pending_withdrawal_request',[ 
                                         'page_title'=> 'All Pending Withdrawal Request' , 
                                         'login_admin' => $login_admin ,
                                         'number_start' => $number_start ,
                                         'withdrawal_requests' => $withdrawal_requests
                                         ]);  
    } //end of the function

    /*
    * Request Paid
    */
    function request_paid( Request $request ){
        if(!is_admin_session()) {
            return "0";
        }

        $input = $request->all();
        $user_id = $input['user_id'];
        $withdrawal_id = $input['withdrawal_id'];
        $paid_transaction_id = $input['paid_transaction_id'];
        $paid_date = $input['paid_date'];

        $withdrawalObj = WithdrawalRequest::where('id',$withdrawal_id)->where('user_id',$user_id)->get();
        if(count($withdrawalObj) > 0){
          //update withdrawal request
          $wu = WithdrawalRequest::where('id',$withdrawal_id)
                ->where('user_id',$user_id)
                ->update([ 'status' => 'Paid' , 
                           'paid_transaction_id' => $paid_transaction_id ,
                           'paid_date' => $paid_date
                        ]);

          //send email notification to user 
          $userObj = User::find($user_id);      
          $data1['admin_email'] = env('ADMIN_EMAIL') ;
          $data1['to_email'] = $userObj->email ;
          $data = array('name'=>$userObj->display_name , 
              'amount' => $withdrawalObj[0]->request_amount);
          Mail::send('email.request_paid', $data, function($message) use ($data1) {
              $message->to( $data1['to_email'] )->subject
                  ('[TwitchFollowers] : Withdrawal Request Paid');
              $message->from( $data1['admin_email'] ,'Twitch Followers Team');
          });     

          return "1";      
        }//end of the if condition

        return "0"; 
    }//end of the function

    
    /*
    * Request Cancel with credits revert
    */
    function request_cancel( Request $request ){
        if(!is_admin_session()) {
            return "0";
        }

        $input = $request->all();
        $user_id = $input['user_id'];
        $withdrawal_id = $input['withdrawal_id'];
        $cancel_reason = $input['cancel_reason'];

        $withdrawalObj = WithdrawalRequest::where('id',$withdrawal_id)->where('user_id',$user_id)->get();
        if(count($withdrawalObj) > 0){
          $wu = WithdrawalRequest::where('id',$withdrawal_id)
                ->where('user_id',$user_id)
                ->update([ 'status' => 'Cancel' , 'cancel_reason' => $cancel_reason ]);

          $userObj = User::find($user_id);      
          $userObj->total_credit_points = $userObj->total_credit_points + $withdrawalObj[0]->request_credits ;
          $userObj->total_earn_credits = $userObj->total_earn_credits + $withdrawalObj[0]->request_credits ;
          $userObj->save();

          return "1";       
        }//end of the if condition

        return "0"; 
    }//end of the function



    /*
    * Request Cancel without credits revert
    */
    function request_cancel_without_credit_revert( Request $request ){
        if(!is_admin_session()) {
            return "0";
        }

        $input = $request->all();
        $user_id = $input['user_id'];
        $withdrawal_id = $input['withdrawal_id'];
        $cancel_reason = $input['cancel_reason'];

        $withdrawalObj = WithdrawalRequest::where('id',$withdrawal_id)->where('user_id',$user_id)->get();
        if(count($withdrawalObj) > 0){
          $wu = WithdrawalRequest::where('id',$withdrawal_id)
                ->where('user_id',$user_id)
                ->update([ 'status' => 'CancelWithoutRevert' , 'cancel_reason' => $cancel_reason ]);

          return "1";       
        }//end of the if condition

        return "0"; 
    }//end of the function


    
    /*
    * All Paid withdrawal request list
    */
    function all_paid_withdrawal_request( Request $request ){
          if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
          }

          $login_admin = Session::get('login_admin');

          $limit = 20 ;
          $input = $request->all();
          if(isset($input['search'])){
              $withdrawal_requests =  DB::table('withdrawal_request as wr')
                              ->join('users as us', 'us.id', '=', 'wr.user_id')
                              ->where('wr.status', 'Paid' ) 
                              ->where('us.display_name','LIKE', '%' . $input['search'] . '%') 
                              ->orderBy('wr.created_at', 'desc')
                              ->select('wr.*','us.display_name','us.paypal_email')
                              ->paginate($limit);
              $withdrawal_requests->appends(['search' => $input['search']]);
          }else{
              $withdrawal_requests =  DB::table('withdrawal_request as wr')
                              ->join('users as us', 'us.id', '=', 'wr.user_id') 
                              ->where('wr.status', 'Paid' ) 
                              ->orderBy('wr.created_at', 'desc')
                              ->select('wr.*','us.display_name','us.paypal_email')
                              ->paginate($limit);
          }
          
          if(isset($input['page'])) {
              $number_start = $limit * ($input['page'] - 1) ;
          }else{
              $number_start = 0 ;
          }  

          return view('admin.all_paid_withdrawal_request',[ 
                                         'page_title'=> 'All Paid Withdrawal Request' , 
                                         'login_admin' => $login_admin ,
                                         'number_start' => $number_start ,
                                         'withdrawal_requests' => $withdrawal_requests
                                         ]);  
    } //end of the function

    /*
    * All Cancel withdrawal request list
    */
    function all_cancel_withdrawal_request( Request $request ){
          if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
          }

          $login_admin = Session::get('login_admin');

          $limit = 20 ;
          $input = $request->all();
          if(isset($input['search'])){
              $withdrawal_requests =  DB::table('withdrawal_request as wr')
                              ->join('users as us', 'us.id', '=', 'wr.user_id')
                              //->where('wr.status', 'Cancel' ) 
                              ->where(function($q) {
                                  $q->where('wr.status', 'Cancel' ) 
                                    ->orWhere('wr.status', 'CancelWithoutRevert');
                                })
                              ->where('us.display_name','LIKE', '%' . $input['search'] . '%') 
                              ->orderBy('wr.created_at', 'desc')
                              ->select('wr.*','us.display_name','us.paypal_email')
                              ->paginate($limit);
              $withdrawal_requests->appends(['search' => $input['search']]);
          }else{
              $withdrawal_requests =  DB::table('withdrawal_request as wr')
                              ->join('users as us', 'us.id', '=', 'wr.user_id') 
                              //->where('wr.status', 'Cancel' ) 
                              ->where(function($q) {
                                  $q->where('wr.status', 'Cancel' ) 
                                    ->orWhere('wr.status', 'CancelWithoutRevert');
                                })
                              ->orderBy('wr.created_at', 'desc')
                              ->select('wr.*','us.display_name','us.paypal_email')
                              ->paginate($limit);
          }
          
          if(isset($input['page'])) {
              $number_start = $limit * ($input['page'] - 1) ;
          }else{
              $number_start = 0 ;
          }  

          return view('admin.all_cancel_withdrawal_request',[ 
                                         'page_title'=> 'All Cancel Withdrawal Request' , 
                                         'login_admin' => $login_admin ,
                                         'number_start' => $number_start ,
                                         'withdrawal_requests' => $withdrawal_requests
                                         ]);  
    } //end of the function


    public function block_unblock_user( Request $request ){
          if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
          }

          $input = $request->all();
          $user_id = $input['user_id'];
          $is_active = $input['is_active'];

          //User::update('is_active',$is_active)->where('id',$user_id)
          $userObj = User::find($user_id);
          $userObj->is_active = $is_active ;
          $userObj->email_viewer_notification = NULL ; 
          $userObj->save();

          if($is_active){
            $_SESSION['msgAdmin'] = $userObj->display_name." Unblock Successfully";
          }else{
            $_SESSION['msgAdmin'] = $userObj->display_name." Block Successfully";
          }

          return "1" ;
    }//end of the function




    public function real_time_path_affiliate( $user_id , Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        $client_id = env('TWITCH_CLIENT_ID') ;

        $user = User::find($user_id);

        ## Get follower count  ====================================================================
        $ch1 = curl_init();
        curl_setopt($ch1, CURLOPT_URL, "https://api.twitch.tv/kraken/channels/".$user->_id);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
        $headers = array();
        $headers[] = "Accept: application/vnd.twitchtv.v5+json";
        $headers[] = "Client-Id: ".$client_id;
        curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
        $result1 = curl_exec($ch1);
        if (curl_errno($ch1)) {
            echo 'Error:' . curl_error($ch1);
            return redirect('/');
        }
        curl_close ($ch1);
        $response1 = json_decode($result1);
        /*
        *Followers Reached - This is just a count of how many followers they have.
        */
        $total_followers = $response1->followers ;
        $total_views = $response1->views ;



        ## Get Stream Time , Stream Different Days , Average of 3 Viewers  =========================
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, "https://api.twitch.tv/kraken/channels/".$user->_id."/videos" );
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch2, CURLOPT_CUSTOMREQUEST, "GET");
        $headers = array();
        $headers[] = "Accept: application/vnd.twitchtv.v5+json";
        $headers[] = "Client-Id: ".$client_id;
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        $result2 = curl_exec($ch2);
        if (curl_errno($ch2)) {
            echo 'Error:' . curl_error($ch2);
            return redirect('/');
        }
        curl_close ($ch2);
        $response2 = json_decode($result2);


        /*
        *Stream Time - We need to find an API so we can show how many times they streamed for 2 hours in one sitting.
        */
        $video_streamed_for_two_hours = 0 ; 

        /*
        * Stream Different Days - Unique Active Channels Last 30 Days
        */
        $stream_different_days = 0 ; 
        $last_date = 0 ; 

        /*
        *Average of 3 Viewers - Unique Viewers Last 30 Days
        */
        $average_of_three_viewers = 0 ;

        foreach($response2->videos as $key => $value){

            if($value->broadcast_type == 'archive'){ // archive = streaming , upload = upload video

                $now = time(); 
                $your_date = strtotime($value->published_at);
                $datediff = $now - $your_date;
                $daydiff = round($datediff / (60 * 60 * 24)) ;

                if( $daydiff <= 30 ) {

                    #Stream Time
                    if($value->length >= 7200){ // 2 hours in seconds 
                        $video_streamed_for_two_hours++;
                    }//end of the inner if condition

                    #Stream Different Days
                    if($last_date == 0){
                        $last_date = $value->published_at ;
                        $stream_different_days++;
                    }else{
                        if($last_date != $value->published_at){
                            $last_date = $value->published_at ;
                            $stream_different_days++;
                        }
                    }//end of else

                    #Average of 3 Viewers 
                    if($value->views >= 3){
                        $average_of_three_viewers++ ;
                    }
                    
                }//end of the if condition

            }//end of the broadcast type

        }//end of the foreach

        #############################
        $completion_count = 0 ;
        if($total_followers >= 50){
          $completion_count++ ;
        }
        if($video_streamed_for_two_hours >= 8){
          $completion_count++ ;
        }
        if($stream_different_days >= 7){
          $completion_count++ ;
        }
        if($average_of_three_viewers >= 3){
          $completion_count++ ;
        }

        return view('admin.real_time_path_affiliate',[ 
                  'page_title' => 'Real Time Path to Twitch Affiliate' , 
                  'login_admin' => $login_admin ,
                  'total_followers' => $total_followers ,
                  'video_streamed_for_two_hours' => $video_streamed_for_two_hours ,
                  'stream_different_days' => $stream_different_days,
                  'average_of_three_viewers' => $average_of_three_viewers ,
                  'completion_count' => $completion_count ,
                  'user' => $user
                  ]);
    }//end of the function


    function admin_create_offer( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        $input = $request->all();
        $user_id = $input['user_id'] ;
        $userObj = User::find($user_id);
        $total_credit_points = $userObj->total_credit_points ;

        if($input['credit_per'] < 1 or $input['how_many'] < 1 or $input['total_credit'] < 1 ) {
            $_SESSION['errorAdmin'] = "You are entering wrong values" ;
            return redirect()->route('all_users');
            die;
        }
        

        if( $total_credit_points >= $input['total_credit'] ){

            $data['streamer_twitch_id'] = $userObj->_id;
            $data['user_id'] = $user_id ;
            $data['credit_per_rate'] = $input['credit_per'];
            $data['quantity_require'] = $input['how_many'];

            //check if user have created follow offer
            if($input['offer_type'] == 'follow'){
                $check_offer = Offer::where('user_id',$user_id)
                               ->where('offer_type','follow')
                               ->where('status','Processing')
                               ->get();
                if( count($check_offer) > 0 ){
                    $_SESSION['errorAdmin'] = "This user currently have a live Follow Sponsorship active.  Please wait until this sponsorship ends before adding a new one." ;
                    return redirect()->route('all_users');
                }                
            }
            //check if user have created view offer
            if($input['offer_type'] == 'view'){
                $check_offer = Offer::where('user_id',$user_id)
                               ->where('offer_type','view')
                               ->where('status','Processing')
                               ->get();
                if( count($check_offer) > 0 ){
                    $_SESSION['errorAdmin'] = "This user currently have a live Views Sponsorship active.  Please wait until this sponsorship ends before adding a new one." ;
                    return redirect()->route('all_users');
                }                
            }

            if($input['offer_type'] == 'view' and isset($input['how_many_consecutive_minutes']) ){
                $data['how_many_consecutive_minutes'] = $input['how_many_consecutive_minutes']; //### 
            } 

            if($input['how_many']=='unlimited'){
                $data['total'] = 0 ;
            }else{
                $data['total'] =  ( $data['credit_per_rate'] * $data['quantity_require'] ) ;//$input['total_credit'];
            }
            
            $data['offer_type'] = $input['offer_type'];

            //set priority if user is premium
            if( $userObj->is_affiliate || $userObj->premium_for_streamers || $userObj->premium_for_viewers || $userObj->premium_vcombo ){
                $data['priority'] = 10 ; //high priority
            }//end of the if condition
            else{
                $data['priority'] = 20 ; //low priority
            }

            if($data['offer_type'] == 'follow'){
                $message = "You have successfully created a follows sponsorship. "; 
            }else{
                $message = "You have successfully created a views sponsorship. ";
            } 

            Offer::create($data);

            $total_credit_points_new = $userObj->total_credit_points - $input['total_credit'] ;
            User::where( 'id', $user_id )
                ->update([ 'total_credit_points' => $total_credit_points_new ]);

            $_SESSION['msgAdmin'] = $message ;
            return redirect()->route('all_users');

        }else{

            
            $_SESSION['error'] = "This user don't have enough credit points to create this follows sponsorship" ;
            return redirect()->route('all_users');

        }
        
    }//end of the function



    function admin_create_offer_view_count( Request $request ){

        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $input = $request->all();
        $login_admin = Session::get('login_admin');
        $user_id = $input['user_id'] ;
        $userObj = User::find($user_id);
        $total_credit_points = $userObj->total_credit_points ;

        if($input['credit_per'] < 1 or $input['how_many_view'] < 1 or $input['total_credit'] < 1 ) {
            $_SESSION['errorAdmin'] = "You are entering wrong values" ;
            return redirect()->route('all_users');
            die;
        }

        if( $total_credit_points >= $input['total_credit'] ){

            $data['streamer_twitch_id'] = $userObj->_id;
            $data['user_id'] = $user_id ;
            $data['credit_per_rate'] = $input['credit_per'];
            $data['quantity_require'] = $input['how_many_view'];

            
            //check if user have created view offer
            $check_offer = Offer::where('user_id',$user_id)
                           ->where('offer_type','view channel')
                           ->where('status','Processing')
                           ->get();
            if( count($check_offer) > 0 ){
                $_SESSION['errorAdmin'] = "This user currently have a live Views Sponsorship active.  Please wait until this sponsorship ends before adding a new one." ;
                return redirect()->route('all_users');
            }

            $data['total'] = ($data['credit_per_rate'] * $data['quantity_require']);//$input['total_credit'];

            $data['offer_type'] = $input['offer_type'];
            $message = "Channel views sponsorship offer created successfully";

             //set priority if user is premium
            if( $userObj->is_affiliate || $userObj->premium_for_streamers || $userObj->premium_for_viewers || $userObj->premium_vcombo ){
                $data['priority'] = 10 ; //high priority
            }//end of the if condition
            else{
                $data['priority'] = 20 ; //low priority
            }

            Offer::create($data);

            $total_credit_points_new = $userObj->total_credit_points - $input['total_credit'] ;
            User::where( 'id', $user_id )
                ->update([ 'total_credit_points' => $total_credit_points_new ]);

            $_SESSION['msgAdmin'] = $message ;
            return redirect()->route('all_users');

        }else{

            $_SESSION['errorAdmin'] = "This user don't have enough credit points to create this follows sponsorship" ;
            return redirect()->route('all_users');

        }
        
    }//end of the function

    function user_log_csv($id){

          if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
          }

          $userObj = User::find($id);
          $user_logs1 =  DB::table('viewed_followed_offers as vfo')
                            ->join('streamer_offers as so', 'so.id', '=', 'vfo.streamer_offer_id')
                            ->join('users as u', 'u.id', '=', 'vfo.viewer_id')
                            ->join('users as us', 'us.id', '=', 'so.user_id') 
                            ->where('so.user_id',$id)
                            ->orWhere('vfo.viewer_id',$id)
                            ->orderBy('vfo.created_at', 'desc')
                            ->get(['vfo.*','so.user_id','so.status','u.name as viewer_name','us.name as streamer_name']);
                         
          $csvExporter = new \Laracsv\Export();
          $csvExporter->build($user_logs1, ['type','viewer_name', 'streamer_name','credit_earn','minute_viewed','request_ip', 'is_credits_revert' , 'created_at'])->download($userObj->display_name.'.csv');
          //return ;
    }//end of the function


    function open_close_offer( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $input = $request->all();
        $offer_id = $input['offer_id'] ;
        $offer_status = $input['offer_status'] ;

        if($offer_status == 'open'){ 
          $status = 'Processing' ;
        }
        elseif($offer_status == 'close'){
          $status = 'Closed' ;
        }  

        if($offer_status == 'open' or $offer_status == 'close'){
              $offerObj = Offer::find($offer_id) ;
              $offerObj->status = $status ;
              $offerObj->save();
        }

        return 1;
    }//end of the function


    /*
    * Email Blast
    */
    function email_blast( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        return view('admin.email_blast',[ 
                  'page_title' => 'Email Blast' , 
                  'login_admin' => $login_admin 
                  ]);
    }//end of the function 

    /*
    * Email blast action
    */
    function email_blast_action ( Request $request  ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $input = $request->all();
        $user_type = $input['user_type'];
        $subject = $input['subject'];
        $msg = $input['message'];

        if( $user_type != "" && $subject != "" && $msg != "" ){
            if($user_type == 'premium_viewers'){

              $users = User::where('premium_for_viewers','1')->get(['email','display_name']);
              
            }else if($user_type == 'premium_streamers'){

              $users = User::where('premium_for_streamers','1')->get(['email','display_name']);

            }else if($user_type == 'premium_combo'){

              $users = User::where('premium_vcombo','1')->get(['email','display_name']);
            
            }else if($user_type == 'all_premium_users'){

              $users = User::where('premium_vcombo','1')
                      ->orWhere('premium_for_streamers','1')
                      ->orWhere('premium_for_viewers','1')
                      ->get(['email','display_name']);
            
            }else if($user_type == 'affiliates'){

              $users = User::where('is_affiliate','1')
                      ->get(['email','display_name']);
            
            }else if($user_type == 'non_premium_user'){

              $users = User::where('premium_vcombo','<>','1')
                      ->where('premium_for_streamers','<>','1')
                      ->where('premium_for_viewers','<>','1')
                      ->get(['email','display_name']);
            }else if($user_type == 'users_who_purchase_credits'){

              $users = DB::table('transaction')
                  ->join('users', 'users.id', '=', 'transaction.user_id') 
                  ->where('transaction.mc_gross','10.00')
                  ->orWhere('transaction.mc_gross','25.00')
                  ->orWhere('transaction.mc_gross','50.00')
                  ->orWhere('transaction.mc_gross','75.00')
                  ->groupBy('transaction.user_id')
                  ->get(['users.email','users.display_name']);  
            }


            //loop of users to send emails 
            foreach($users as $key => $user){

              ########
              $data1['admin_email'] = env('ADMIN_EMAIL') ;
              $data1['to_email'] = $user->email ;
              $data1['subject'] = $subject ;
              $data = array( 'name'=>$user->display_name  , 'message1' => $msg );

              Mail::send('email.email_blast', $data, function($message) use ($data1) {
                $message->to( $data1['to_email'] )->subject( $data1['subject'] );
                $message->from( $data1['admin_email'] ,'Twitch Followers Team');
              }); 
              #########

            }//end of the foreach

            $_SESSION['msgAdmin'] = 'Emails Sent Successfully!' ;
            return redirect()->route('email_blast');
        }else{
            $_SESSION['errorAdmin'] = 'All fields are required!' ;
            return redirect()->route('email_blast');
        }//end of the else

    }//end of the function 


    /*
    * This function should be run manually in case of if user's all credits want to revert in streamers account
    */
    function revert_credits_by_userid( $viewer_id , Request $request ){

        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $followViewOffers =  DB::table('viewed_followed_offers as vfo')
                  ->join('streamer_offers as so', 'so.id', '=', 'vfo.streamer_offer_id')
                  ->where( 'vfo.viewer_id',$viewer_id )
                  ->get(['vfo.*','so.user_id']);

        foreach($followViewOffers as $key => $value){

          $credit_earn       = $value->credit_earn ;
          $streamer_offer_id = $value->streamer_offer_id ;
          $streamer_user_id  = $value->user_id ;

          User::where('id',$streamer_user_id)->increment('total_credit_points', (2*$credit_earn));
          User::where('id',$viewer_id)->decrement('total_credit_points', $credit_earn);
          User::where('id',$viewer_id)->decrement('total_earn_credits', $credit_earn);

        }//end of the function

        //update revert flag
        FollowViewOffer::where( 'viewer_id', $viewer_id )->update(['is_credits_revert' => '1']) ;

    }//end of the function 


    function withdrawal_request_csv(){

          if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
          }

          $withdrawal_requests =  DB::table('withdrawal_request as wr')
                ->join('users as us', 'us.id', '=', 'wr.user_id') 
                ->where('wr.status', 'Under Review' ) 
                ->orderBy('wr.created_at', 'desc')
                ->select(  'wr.*',
                           'us.display_name',
                           'us.paypal_email',
                           DB::raw('"USD" AS currency'),
                           DB::raw('"" AS item_name'),
                           DB::raw('"Thank You!" AS message')
                         )
                ->get(); 
          
          $csvExporter = new \Laracsv\Export();
          $csvExporter->build( $withdrawal_requests, 
                              ['paypal_email' ,
                                'request_amount',
                                'currency' ,
                                'item_name' ,
                                'message'
                              ] , 
                              [
                                  'header' => false,
                              ])
                      ->download('pending_withdrawal_requests.csv');
          //return ;
    }//end of the function




    /*
    * All Login Users ( Logged In Users )
    */
    public function all_login_users( Request $request )
    {   

        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');           

        $limit = 50 ;
        $input = $request->all();

        $current_time = date('Y-m-d H:i:s');      

        if(isset($input['search'])){

            //get users
            $search_keyword = $input['search'] ;

            $users =  User::where('is_login','1')   
                      ->whereRaw("DATE_ADD(last_activity, INTERVAL 5 MINUTE) >= '".$current_time."'") 
                      ->where( function( $query  ) use ( $search_keyword ) {
                        $query->orWhere('display_name','LIKE', '%' . $search_keyword . '%')
                          ->orWhere('email','LIKE', '%' . $search_keyword . '%') ;
                      }) 
                      ->orderBy('last_activity_counter','desc')
                      ->paginate($limit);     

            $users->appends(['search' => $input['search']]);

            //get total users                 
            $total_users =  User::where('is_login','1') 
                            ->whereRaw("DATE_ADD(last_activity, INTERVAL 5 MINUTE) >= '".$current_time."'") 
                            ->where( function( $query  ) use ( $search_keyword ) {
                                  $query->orWhere('display_name','LIKE', '%' . $search_keyword . '%')
                                  ->orWhere('email','LIKE', '%' . $search_keyword . '%') ;
                              }) 
                            ->count();                      
            //search keyword                
            //$search_keyword = $input['search'] ;                
        }else{
            //get users
            $users =  User::where('is_login','1')
                      ->whereRaw("DATE_ADD(last_activity, INTERVAL 5 MINUTE) >= '".$current_time."'")   
                      ->orderBy('last_activity_counter','desc')->paginate($limit); 
            $total_users =  User::where('is_login','1')
                            ->whereRaw("DATE_ADD(last_activity, INTERVAL 5 MINUTE) >= '".$current_time."'") 
                            ->count();
                   

            //search keyword                
            $search_keyword = '';
        }
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }  

        return view('admin.all_login_users',[ 
                                       'page_title'=> 'All Online Users' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'users' => $users ,
                                       'total_users' => $total_users ,
                                       'search_keyword' => $search_keyword
                                       ]);  
    }//end of the function  


    /*
    *  function to download online users csv file
    */
    function online_users_csv(){

          if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
          }

          $current_time = date('Y-m-d H:i:s');
          $online_users = User::whereRaw("DATE_ADD(last_activity, INTERVAL 5 MINUTE) >= '".$current_time."'")->orderBy('id','desc')->get();       
          
          $csvExporter = new \Laracsv\Export();
          $csvExporter->build( $online_users , 
                              [ 'name' ,
                                'email',
                                'user_ip'
                              ] , 
                              [
                                  'header' => true,
                              ])
                      ->download('online_users.csv');
          //return ;
    }//end of the function


    /*
    * Report Page
    */
    public function reports( Request $request )
    {
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin'); 


        $yearlySignUps   =  User::selectRaw('year(created_at) as year, count(*) as count')->orderBy('year','asc')->groupBy('year')->get();

        $yearlyReferrals   =  Refer::selectRaw('year(created_at) as year, count(*) as count')->orderBy('year','asc')->groupBy('year')->get();

        $yearlyAffiliateUpgrades   =  Transaction::selectRaw('year(created_at) as year, count(*) as count')->where('mc_gross','99.99')->where('payment_status','Completed')->orderBy('year','asc')->groupBy('year')->get();

        $yearlyPremiumViewers   =  Transaction::selectRaw('year(created_at) as year, count(*) as count')->where('mc_gross','19.99')->where('payment_status','Completed')->orderBy('year','asc')->groupBy('year')->get();

        $yearlyPremiumStreamers   =  Transaction::selectRaw('year(created_at) as year, count(*) as count')->where('mc_gross','24.99')->where('payment_status','Completed')->orderBy('year','asc')->groupBy('year')->get();

        $yearlyPremiumCombos   =  Transaction::selectRaw('year(created_at) as year, count(*) as count')->where('mc_gross','29.99')->where('payment_status','Completed')->orderBy('year','asc')->groupBy('year')->get();

        $yearlyTotalEarnings   =  Transaction::selectRaw('year(created_at) as year, sum(`mc_gross`) as total_earning')->where('payment_status','Completed')->orderBy('year','asc')->groupBy('year')->get();

        $monthlyEarningReports = DB::select("SELECT MONTH(`created_at`) AS month, YEAR(`created_at`) as year, SUM(`mc_gross`) as total FROM transaction WHERE YEAR(`created_at`) = '".date('Y')."' AND payment_status = 'Completed' GROUP BY YEAR(`created_at`), MONTH(`created_at`)");

        $monthlySignUpReports = DB::select("SELECT MONTH(`created_at`) AS month, YEAR(`created_at`) as year, COUNT(`id`) as total FROM users WHERE YEAR(`created_at`) = '".date('Y')."'  GROUP BY YEAR(`created_at`), MONTH(`created_at`)");

        $lastSevenDaysEarningReports = DB::select("SELECT DAY(`created_at`) AS d , SUM(`mc_gross`) as total FROM transaction WHERE created_at >= DATE(NOW()) - INTERVAL 7 DAY GROUP BY DAY(`created_at`)");

        $dayByDayEarningReports = DB::select("SELECT DAY(`created_at`) AS day , SUM(`mc_gross`) as total FROM transaction WHERE YEAR(`created_at`) = '".date('Y')."' AND MONTH(`created_at`) = '".date('m')."'  AND payment_status = 'Completed' GROUP BY  DAY(`created_at`)"); //".date('m')."

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];



        $totalEarningResult = DB::select("SELECT mc_gross , SUM(`mc_gross`) as total 
                              FROM transaction 
                              WHERE ( payment_status = 'Completed' ) 
                                AND ( mc_gross = '10.00' OR mc_gross = '25.00' OR mc_gross = '50.00' OR mc_gross = '75.00' OR mc_gross = '99.99' OR mc_gross = '9.99' OR mc_gross = '14.99' OR mc_gross = '19.99' OR mc_gross = '29.99' OR mc_gross = '24.99' OR mc_gross = '199.99' ) 
                              GROUP BY mc_gross"); 

        $totalEarningAllTime = DB::select("SELECT SUM(`mc_gross`) as total FROM transaction 
                               WHERE ( payment_status = 'Completed' ) 
                                 AND ( mc_gross = '10.00' OR mc_gross = '25.00' OR mc_gross = '50.00' OR mc_gross = '75.00' OR mc_gross = '99.99' OR mc_gross = '9.99' OR mc_gross = '14.99' OR mc_gross = '19.99' OR mc_gross = '29.99' OR mc_gross = '24.99' OR mc_gross = '199.99' )"); 

        $totalEarningTemp = NULL ;

        foreach($totalEarningResult as $key=>$row){
          $percentage = ( $row->total / $totalEarningAllTime[0]->total ) * 100 ;
          $totalEarningTemp[$row->mc_gross] =  $row->total.'@'.$percentage * 3.6; //degree
        }//end of the foreach



        return view('admin.reports',[ 
                                   'page_title'=> 'Reports' , 
                                   'login_admin' => $login_admin ,
                                   'monthNames' => $monthNames ,
                                   'yearlySignUps' => $yearlySignUps ,
                                   'yearlyReferrals' => $yearlyReferrals ,
                                   'yearlyAffiliateUpgrades' => $yearlyAffiliateUpgrades ,
                                   'yearlyTotalEarnings' => $yearlyTotalEarnings,
                                   'yearlyPremiumViewers' => $yearlyPremiumViewers ,
                                   'yearlyPremiumStreamers' => $yearlyPremiumStreamers ,
                                   'yearlyPremiumCombos' => $yearlyPremiumCombos ,
                                   'monthlyEarningReports' => $monthlyEarningReports ,
                                   'monthlySignUpReports' => $monthlySignUpReports ,
                                   'lastSevenDaysEarningReports' => $lastSevenDaysEarningReports ,
                                   'dayByDayEarningReports' => $dayByDayEarningReports ,
                                   'totalEarningTemp' => $totalEarningTemp ,
                                   'totalEarningAllTime' => $totalEarningAllTime[0]->total
                                    ]);
    }//end of the function


    public function make_logout_user ( $user_id , Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin'); 

        User::where('id',$user_id)->update([ 
                                    'last_activity_counter' => 0 ,
                                    'is_login' => 0  
                                  ]);
        return redirect()->route('all_login_users');
    }//end of the function




    /* 
    * All Users based on ips uses
    */
    public function users_based_on_ips_uses( Request $request )
    {   
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');
        
        $limit = 50 ;
        $input = $request->all();


        if(isset($input['search'])){
            $search = $input['search'] ;

            $userObjsCount =  User::where('email','LIKE','%'.$search.'%')
                              ->orWhere('name','LIKE','%'.$search.'%')
                              ->count() ;

            if($userObjsCount > 0) {

                $userObjs =  User::where('email','LIKE','%'.$search.'%')
                            ->orWhere('name','LIKE','%'.$search.'%')
                            ->get(['id']) ;
                $user_ids = NULL ;             
                foreach($userObjs as $item){
                  $user_ids[] = $item->id ;
                }//end of the foreach   

                $user_ids_string = implode(',',$user_ids);   
            }else{
                $user_ids_string = '0' ;
            }     
                
            $users =  UserIps::
                      selectRaw(" count(distinct id) as count , user_id ")
                      ->whereRaw(" user_id IN ( ".$user_ids_string." ) ")
                      ->orderBy('count','DESC')
                      ->groupBy('user_id')
                      ->paginate($limit); 

            $users->appends(['search' => $input['search']]);          
  
        }else{
            $search = '' ;
            $users =  UserIps::
                  selectRaw(" count(distinct id) as count , user_id ")
                  ->orderBy('count','DESC')
                  ->groupBy('user_id')
                  ->paginate($limit);        
        }          
        //print_r($users);die ;
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }


        return view('admin.users_based_on_ips_uses',[ 
                                       'page_title'=> 'All Users based on IPs uses' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'users' => $users ,
                                       'search' => $search 
                                       ]);  
    }//end of the function 


    /*
    * User IP Detail
    */
    function user_ip_detail( $id , Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $client_id = env('TWITCH_CLIENT_ID') ;
        $input = $request->all();
        $login_admin = Session::get('login_admin');
        $userObj = User::where('id',$id)->first();

        if( $userObj ){

            /*
            Get user channel info of user
            */
            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, "https://api.twitch.tv/kraken/channels/".$userObj->_id);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
            $headers = array();
            $headers[] = "Accept: application/vnd.twitchtv.v5+json";
            $headers[] = "Client-Id: ".$client_id;
            curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
            $result1 = curl_exec($ch1);
            if (curl_errno($ch1)) {
                echo 'Error:' . curl_error($ch1);
                return redirect('/');
            }
            curl_close ($ch1);
            $response = json_decode($result1);
  
            $ips =  UserIps::where( 'user_id' , $id )
                             ->orderBy('id','DESC')
                             ->get() ;

            return view('admin.user_ip_detail',[ 
                                       'page_title'=> 'User IPs Detail' , 
                                       'login_admin' => $login_admin ,
                                       'user' => $userObj ,
                                       'channel_info' => $response , 
                                       'ips' => $ips
                                       ]);   
        }else{

            $_SESSION['errorAdmin'] = "Something Went Wrong!" ;
            return redirect()->route('users_based_on_ips_uses');

        }
    }//end of the function


    /*
    * All Subscription
    */
    public function all_subscriptions( Request $request ){
        if(!is_admin_session()) {
            $_SESSION['errorAdmin'] = "Please Enter Username And Password" ;
            return redirect()->route('admin_login');
        }

        $login_admin = Session::get('login_admin');

        $limit = 20 ;
        $input = $request->all();
        if(isset($input['search'])){
            
            $search = $input['search'] ;      
            $transactions = DB::table('user_subscriptions')
                  ->join('users', 'users.id', '=', 'user_subscriptions.user_id') 
                  ->where(function($q) use ($search) {
                      $q->where('display_name','LIKE', '%' . $search . '%')
                        ->orWhere('txn_id', 'LIKE' , '%'.$search.'%')
                        ->orWhere('subscr_id', 'LIKE' , '%'.$search.'%');
                  })
                  ->orderBy('id', 'desc')
                  ->select('user_subscriptions.*','users.display_name','users.email')
                  ->paginate($limit);         

            $transactions->appends(['search' => $input['search']]);
        }else{
            $search =  '';  
            $transactions = DB::table('user_subscriptions')
                  ->join('users', 'users.id', '=', 'user_subscriptions.user_id')  
                  ->orderBy('id', 'desc')
                  ->select('user_subscriptions.*','users.display_name','users.email')
                  ->paginate($limit);     
        }
        
        if(isset($input['page'])) {
            $number_start = $limit * ($input['page'] - 1) ;
        }else{
            $number_start = 0 ;
        }  

        return view('admin.all_subscriptions',[ 
                                       'page_title'=> 'All Subscriptions' , 
                                       'login_admin' => $login_admin ,
                                       'number_start' => $number_start ,
                                       'transactions' => $transactions ,
                                       'search' => $search
                                       ]);  
    }//end of the function
}//end of the class
