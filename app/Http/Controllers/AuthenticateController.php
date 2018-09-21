<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Auth;
use Config;
use Hash;
use Redirect;
use Session;
use App\http\Controllers\Authenticate;
use AWeberAPI;
use App\Helpers\Common;
use App\Helpers\AutoResponderData;
use App\Autoresponder;
use Validator;
use App\Userplans;
use App\Plans;
use Illuminate\Support\Facades\Mail;
use DB;
define("AWEBER_CONSUMER_KEY","AkofhFT2pOme1GGmk422ygna");
define("AWEBER_CONSUMER_SECRET","Nun4t92o36IcqMCifpckPhKsGSLwg5vODSdyB332");
define("ICONTACT_API_ID","4eduYYGDhsIE29ymOhtB8w2QUZDNvbxl");
//define("MERCHANT_EMAIL","gurpal.singh-facilitator@softobiz.com");
//define( 'SSL_URL', 'https://www.paypal.com/cgi-bin/webscr' );

class AuthenticateController extends Controller
{

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it
       // $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    /**
     * Return the user
     *
     * @return Response
     */
    public function index()
    {
		
       // Retrieve all the users in the database and return them        
        //$users = User::all();
    	$users = User::whereRaw('role = 1')->get();
    	$usersDetail=$users->toArray();
    	
    	for($i=0;$i<count($usersDetail);$i++)
    	{
    		$currentDate=date('Y-m-d H:i:s');
    		$user_id=$usersDetail[$i]['id'];
    		$users = Userplans::whereRaw('user_id ='.$user_id)->get();
    		$friends_votes =DB::table('tbl_user_plans')
    			->where('tbl_user_plans.user_id',$user_id)
    			->where('tbl_user_plans.status',1)
    			->where('tbl_user_plans.start_date','<=',$currentDate)
    			->where('tbl_user_plans.next_recurring_date','>=',$currentDate)
    			->join('tbl_plans', 'tbl_plans.id', '=', 'tbl_user_plans.plan_id')
    			->select('tbl_plans.name', 'tbl_user_plans.next_recurring_date','tbl_user_plans.id')
    		 	->orderBy('tbl_user_plans.id', 'desc')->first();
    		$dataPlan=json_decode(json_encode($friends_votes), True);
    		//echo "<pre>";print_r($friends_votes->next_recurring_date->format('y.m'));die;
    		if(isset($dataPlan) && count($dataPlan) >=1)
    		{
    			$usersDetail[$i]['payment_plan']=$dataPlan['name'];
    			$dateNext=$dataPlan['next_recurring_date'];
    			
    			$usersDetail[$i]['Next_date']=date ("Y-m-d", strtotime($dateNext));
    		}
    		if($usersDetail[$i]['user_status']=='1')
    		{
    			$usersDetail[$i]['statusUser']='Active';
	  		}
    		else if($usersDetail[$i]['user_status']=='2')
    		{
    		  	$usersDetail[$i]['statusUser']='Suspend';
	  		}
	  		else if($usersDetail[$i]['user_status']=='3')
	  		{
	  			$usersDetail[$i]['statusUser']='Cancel';
	  		}
	  		$usersDetail[$i]['fullname']=$usersDetail[$i]['first_name'].' '.$usersDetail[$i]['last_name'];
    	}
        $status="success";
        $finalArr=array("users"=>$usersDetail);
        $result = Common::sendRequest($status,$finalArr);
        return $result;
       // return $users;
    } 
    /**
     * Return a JWT
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {
    	$rules = [
    	'email' => 'required|email',
    	'password' => 'required'
    			];
   
    	$this->validate($request,$rules);
    
        $credentials = $request->only('email', 'password');
        $user = User::where('email', '=', $credentials['email'])->first();
    	$customClaims = ['user_status' => $user->user_status];
    	if($customClaims['user_status'] =='1')
    	{
	        try {
	            // verify the credentials and create a token for the user
	            if (! $token = JWTAuth::attempt($credentials)) {
	                return response()->json(['error' => 'invalid_credentials'], 401);
	            }
	        } catch (JWTException $e) {
	            // something went wrong
	            return response()->json(['error' => 'could_not_create_token'], 500);
	        }
	
	        // if no errors are encountered we can return a JWT
	        return response()->json(compact('token'));
    	}
    	else 
    	{
    		return response()->json(['error' => 'Your account has been suspend/cancel. Please contact with administrator !'], 401);
    	}
    }
	public function getUserDetail(Request $request)
	{
		$data=$request->input();
		if(isset($data['user_id']))
		{	
			$user_id = $data['user_id'];
		}
		else
		{  
			$token = $data['token'];
			$user_id = User::getId($token);
		}	
			if($user_id)
			{
				$userDetail=User::find($user_id);
				$userDetail->user_name=$userDetail->first_name .' '.$userDetail->last_name;
				$userDetail=$userDetail->toArray();
				
				$status="success";
				$finalArr=array("user"=>$userDetail);
			}
			else
			{
				$status="fail";
				$finalArr=array("users"=>"User not exist");
			}
			$result = Common::sendRequest($status,$finalArr);
			return $result;
		
	}

	public function getUserStatus(Request $request)
	{
		$data=$request->input();
		$token = $data['token'];
		$user_id =User::getId($token);
		if($user_id)
		{ 
			$userDetail=User::find($user_id);
			$status=$userDetail->status;
			if($status=='0')
			{
				$url="http://".$_SERVER['HTTP_HOST'].'/paymentstatus';
				//return Redirect::to($url);
				$status="success";
				$finalArr=array("url"=>$url);
			}
			else 
			{
				$status="fail";
				$finalArr=array("message"=>"");
			}
			//$userDetail=$userDetail->toArray();
			
			
		}
		else
		{
			$status="fail";
			$finalArr=array("users"=>"User not exist");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	

	//*******************Check Forgotpassword Email*************************//
		public function checkForgotEmail(Request $request)
		{
			$data=$request->input();
			$email = $data['email_id'];
			$user = User::where('email', '=', $email)->first();
			if ($user === null) 
			{
				$status="fail";
				$finalArr=array("message"=>"Email id does not Exists");
			}
			else
			{
				$email = $user->email;
				$firstname = $user->first_name;
				$lastname = $user->last_name;
				$name = $firstname.' '.$lastname;
				$ran = rand(100, 99999);
			    $length = 40;
				$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
				$randm_token = $randomString.$ran;
				
				$resetlink = "http://".$_SERVER['HTTP_HOST'].'/resetpassword/'.$randm_token;
				$emaildata = array('link' => $resetlink,'username'=>$email,'name'=>$name);
				$userData=User::where('email', $email)->update(['remember_token' =>$randm_token]);
				if($userData==1)
				{
				Mail::send('emails.email_template', $emaildata, function ($message) use ($user) {
				
					$message->from(Config::get('constants.ADMIN_EMAIL'), 'Administrator');
				
					$message->to($user->email)->subject('Reset your password to login');
				
				});
					$status="success";
					$finalArr=array('message'=>"An email is send to your email id.Please check your mail to reset the password");
				}
				else
				{
					$status = 'fail';
					$finalArr = array('message'=>"An error occur.Please try again !");
				}
			}
			$result = Common::sendRequest($status,$finalArr);
			return $result;
		}
		
	//*******************Password Reset Function*************************//
		public function resetPassword(Request $request)
		{

			$data = $request->input();
			$pass = $data['pass']; 
			$token = $data['token'];
			
			$user = User::where('remember_token', '=', $token)->first();
			$email = $user->email;
			$name = $user->first_name;
			$password=Hash::make($pass);
			if($user != null || $user !='')
			{
				$user->remember_token="";
				$user->password=$password;		
				$emaildata = array('username'=>$email ,'name'=>$name,'password'=>$pass);
			if($user->save())
			{
				Mail::send('emails.reset_template', $emaildata, function ($message) use ($user) {
				
					$message->from(Config::get('constants.ADMIN_EMAIL'), 'Administrator');
				
					$message->to($user->email)->subject('Your password is reset');
					});
				$status="success";
				$finalArr=array('message'=>"Your Password is reset successfully!");
			}
			else
			{
				$status = 'fail';
				$finalArr = array('message'=>"An error occur.Please try again !");
			}
			}
			else 
			{
				$status = 'fail';
				$finalArr = array('message'=>"your token is used please send mail again");
			}
			

			$result = Common::sendRequest($status,$finalArr);
			return $result;

		}

    function checkYourDomain(Request $request)
    {
    	$data = $request->input();
    	$sub_domain = $data['sub_domain'];
    	$token = $data['token'];
    	$user_id =User::getId($token);
    	//$res = User::find($user_id);
    	$userData=User::WhereRaw('id != "'.$user_id.'" and subdomain="'.$sub_domain.'"')->get();
    	$allUser =  $userData->toArray();
    	if(count($allUser) >0)
    	{
    		$status="success";
    		$finalArr=array("message"=>"Sub domain is already exist.");
    	}
    	else 
    	{
    		$status="successwithnoerro";
    		$finalArr=array("message"=>"");
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    }
    function checkYourEmail(Request $request)
    {
    	$data = $request->input();
    	$email = $data['email_id'];
    	$token = $data['token'];
    	if(isset($data['user_id']) && $data['user_id'] !='')
    	{
    		$user_id =$data['user_id'];
    	}
    	else 
    	{
    		$user_id =User::getId($token);
    	}
    	
    	//$res = User::find($user_id);
    	$userData=User::WhereRaw('id != "'.$user_id.'" and email="'.$email.'"')->get();
    	$allUser =  $userData->toArray();
    	if(count($allUser) >0)
    	{
    		$status="success";
    		$finalArr=array("message"=>"Email is already exist.");
    	}
    	else
    	{
    		$status="successwithnoerro";
    		$finalArr=array("message"=>"");
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    }
    
    public function updateProfileInfo(Request $request)
    { 
    	$data = $request->input();
    	$sub_domain = $data['subdomain'];
    	$email = $data['email_id'];
    	$first_name = $data['first_name'];
    	$last_name = $data['last_name'];
    	$contact_number = $data['contact_number'];
    	$token = $data['token'];
    	$user_id =User::getId($token); 
    	if($user_id)
    	{
    		/*if(isset($data['user_id']) && $data['user_id'] !='' )
    		{
    			$userId=$data['user_id'];
    			$userDetail=User::find($userId);
    		}
    		else 
    		{
    			$userDetail=User::find($user_id);
    		}*/
    		$userDetail=User::find($user_id);
    		$userDetail->email=$email;
    		$userDetail->subdomain=strtolower($sub_domain);
    		$userDetail->first_name=$first_name;
    		$userDetail->last_name=$last_name;
    		$userDetail->contact_no=$contact_number;
    		if($userDetail->save())
    		{
    			$status="success";
    			$finalArr=array("message"=>"Your detail has been updated.");
    		}
    		else 
    		{
    			$status="fail";
    			$finalArr=array("message"=>"An error occurs. Please try again !");
    		}
    		$result = Common::sendRequest($status,$finalArr);
    		return $result;
    	}
    	
    }
    public function updateUserProfileInfo(Request $request)
    {
    	$data = $request->input();
    	$sub_domain = $data['subdomain'];
    	$email = $data['email_id'];
    	$first_name = $data['first_name'];
    	$last_name = $data['last_name'];
    	$contact_number = $data['contact_number'];
    	$token = $data['token'];
    	$user_id =User::getId($token);
    	if($user_id)
    	{
    		if(isset($data['user_id']) && $data['user_id'] !='' )
    		{
    			$userId=$data['user_id'];
    			$userDetail=User::find($userId);
    			$userDetail->email=$email;
    			$userDetail->subdomain=strtolower($sub_domain);
    			$userDetail->first_name=$first_name;
    			$userDetail->last_name=$last_name;
    			$userDetail->contact_no=$contact_number;
    			if(isset($data['user_status']) && $data['user_status'] !='') 
    			{
    				$userDetail->user_status=$data['user_status'];
    			}
    			if($userDetail->save())
    			{
    				$status="success";
    				$finalArr=array("message"=>"Your detail has been updated.");
    			}
    			else
    			{
    				$status="fail";
    				$finalArr=array("message"=>"An error occurs. Please try again !");
    			}
    		}
    		else 
    		{
    			$status="fail";
    			$finalArr=array("message"=>"User not exist !");
    		}
    		
    		$result = Common::sendRequest($status,$finalArr);
    		return $result;
    	}
    	else 
    	{
    		$status="fail";
    		$finalArr=array("message"=>"Token Expired. Please Login !");
    	}
    	 
    }
  
    //******************* get Response Api ********************************//
    public function getResponseRequest(Request $request)
    {
    	$rules = [
    	'apiKey' => 'required'
    			];
    	
    	$this->validate($request, $rules);
    	$data=$request->input();
    	if($data)
    	{
    		$apiname=$data['apiName'];
    		$result=AutoResponderData::getAutoresponderData($apiname, $data);
    		if($result)
    		{
    			$token = $data['token'];
				$user_id =User::getId($token);
	    		$autoresponderData=Autoresponder::WhereRaw('user_id = "'.$user_id.'" and name="'.$apiname.'"')->get();
	    		//$imagestemplate =  $autoresponderData->toArray();
	    		
	    		if(count($autoresponderData->toArray()) >0)
	    		{
	    			$idOfuser=$autoresponderData[0]['id'];
	    			$Autoresponder = Autoresponder::find($idOfuser);
	    			$Autoresponder->user_id=$user_id;
	    			$Autoresponder->consumer_key=$data['apiKey'];
	    			$Autoresponder->is_active='1';
	    			$Autoresponder->api_list = $result;
	    			$Autoresponder->save();
	    			$message="Your List has been updated";
	    			$status="Success";
	    			$finalArr = array('message'=>$message);    		
	    		}
	    		else 
	    		{
	    			$Autoresponder=new	Autoresponder;
	    			$Autoresponder->name=$apiname;
	    			$Autoresponder->user_id=$user_id;
	    			$Autoresponder->consumer_key=$data['apiKey'];
	    			$Autoresponder->api_list=$result;
	    			$Autoresponder->is_active='1';
	    			$Autoresponder->save();
	    			$status="Success";
	    			$message="Your List has been updated";
	    			$finalArr = array('message'=>$message);
	    		}
    		}
    		else 
    		{
    			$status="Fail";
    			$result="Invalid API key";
    			$finalArr = array('message'=>$result);
    		}
    		
    	}
    	else 
    	{
    		$status="Fail";
    		$result="Please Enter API Key";
    		$finalArr = array('message'=>$result);
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    }
    //******************* iContact Api Request********************************//
    public function getIcontactRequest(Request $request)
    {
    	$rules = [
    	'api_user_name' => 'required',
    	'api_pwd' => 'required'
    			];
    	
    	$this->validate($request, $rules);
    	$data=$request->input();
    	if($data)
    	{
    		$data['api_id']=ICONTACT_API_ID;
    		$apiname=$data['apiName'];
    		$result=AutoResponderData::getAutoresponderData($apiname, $data);
    		
    		if(!empty($result))
    		{
    			$token = $data['token'];
				$user_id =User::getId($token);
    			$autoresponderData=Autoresponder::WhereRaw('user_id = "'.$user_id.'" and name="'.$apiname.'"')->get();
    			
    			if(count( $autoresponderData->toArray()) >0)
    			{
    				$imagestemplate =  $autoresponderData->toArray();
    				$idOfuser=$autoresponderData[0]['id'];
    				$Autoresponder = Autoresponder::find($idOfuser);
    				$Autoresponder->user_id=$user_id;
    				$Autoresponder->consumer_key=$data['api_user_name'];
    				$Autoresponder->consumer_secret=$data['api_pwd'];
    				$Autoresponder->is_active='1';
    				$Autoresponder->api_list = $result;
    				$Autoresponder->save();
    				$status="Success";
    				$message="Your List has been updated";
	    			$finalArr = array('message'=>$message);
    				 
    			}
    			else
    			{
    				$Autoresponder=new	Autoresponder;
    				$Autoresponder->name=$apiname;
    				$Autoresponder->user_id=$user_id;
    				$Autoresponder->consumer_key=$data['api_user_name'];
    				$Autoresponder->consumer_secret=$data['api_pwd'];
    				$Autoresponder->is_active='1';
    				$Autoresponder->api_list=$result;
    				$Autoresponder->save();
    				$status="Success";
    				$message="Your List has been updated";
	    			$finalArr = array('message'=>$message);
    			}
    		}
    		else
    		{
    			$status="Fail";
    			$result="User name and password not exist";
    			$finalArr = array('message'=>$result);
    		}
    		
    	}
    	else
    	{
    		$status="Fail";
    		$result="Please Fill All Fields";
    		$finalArr = array('message'=>$result);
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    }
    //************************** aweber Api Request **************************//
    public function aweberRequest(Request $request)
    { 
    	/*$rules = [
    	'consumerKey' => 'required',
    	'consumerSecret' => 'required'
    			];*/
    	    	
    	//$this->validate($request, $rules);
    	$consumerKey=AWEBER_CONSUMER_KEY;
    	$consumerSecret=AWEBER_CONSUMER_SECRET;
    	$api='';
       	
    	$data=$request->input(); 
    	//print_r($data); die;
    	if($data)
    	{
    		//$consumerKey=$data['consumerKey'];
    		//$consumerSecret=$data['consumerSecret'];
    		$data['consumerKey']=AWEBER_CONSUMER_KEY;
			$data['consumerSecret']=AWEBER_CONSUMER_SECRET;
    		$apiname=$data['apiName'];
    		$token = $data['token'];
			$user_id =User::getId($token);
    		$autoresponderData=Autoresponder::WhereRaw('user_id = "'.$user_id.'" and name="'.$apiname.'"')->get();
    		
	    	if(count($autoresponderData->toArray()) >0)
    		{
    			$imagestemplate =  $autoresponderData->toArray();
    			$idOfuser=$autoresponderData[0]['id'];
    		}
    		else 
    		{
    			$Autoresponder=new	Autoresponder;
    			$Autoresponder->name=$apiname;
    			$Autoresponder->user_id=$user_id;
    			$Autoresponder->consumer_key=$consumerKey;
    			$Autoresponder->consumer_secret=$consumerSecret;
    			$Autoresponder->is_active='1';
    			if($Autoresponder->save())
    			{
    				$idOfuser=$Autoresponder->id;
    			}
    		}
    		
    		//*************** Receive Request **********************//
    		
    		$result=AutoResponderData::getAutoresponderData($apiname, $data);
    		if($result)
    		{
    			
    			$requestTokenSecret=$result['request_token_secret'];
    			$authorizeUrl=$result['authorize_url'];
    			$Autoresponder = Autoresponder::find($idOfuser);
    			$Autoresponder->consumer_key=$consumerKey;
    			$Autoresponder->consumer_secret=$consumerSecret;
    			$Autoresponder->requestTokenSecret=$requestTokenSecret;
    			$Autoresponder->is_active='1';
    			$Autoresponder->save();
    			$status="Success";
    			$finalArr = array('redirectUrl'=>$authorizeUrl);
    		
    		}
    		else
    		{
    			$status="fail";
    			$finalArr = array('message'=>"Consumer key or consumer secret not exist");
    		}
       	}
    	else 
    	{
    		$status="fail";
    		$finalArr = array('message'=>"Please Fill All Fields");
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    
    }
    //********************* Get access token for aweber ************************//
    public function getAccessToken(Request $request)
    {
    	$rules = [
    	'oauth_token' => 'required',
    	'oauth_verifier' => 'required',
    	'api_name' =>'required'
    			];
    	$oauthToken='';
    	$oauthVerifier='';
    	
    	$data=$request->input();
    	if($data)
    	{
    		$oauthToken=$data['oauth_token'];
    		$oauthVerifier=$data['oauth_verifier'];
    		$api=$data['api_name'];
    	}
    	$token = $data['token'];
		$user_id =User::getId($token);
    	//$api_name='aweber';
    	$autoresponderData=Autoresponder::WhereRaw('user_id = "'.$user_id.'" and name="'.$api.'"')->get();
    	$idOfuser=$autoresponderData[0]['id'];
    	$autoresponderDataarr =  $autoresponderData->toArray();
    	$data['consumerKey']=$autoresponderDataarr[0]['consumer_key'];
    	$data['consumerSecret']=$autoresponderDataarr[0]['consumer_secret'];
    	$data['request_token_secret']= $autoresponderDataarr[0]['requestTokenSecret'];
    	
    	$result=AutoResponderData::getAutoresponderData($api, $data);
    	if($result)
    	{
    		$account=$result['account'];
    		$responseData=array();
    		foreach($account->lists as $offset => $list)
    		{
    			$responseData[]= array('id' => $list->id,'name'=> $list->name);
    		}
    			$apiList= json_encode($responseData);
    			$Autoresponder = Autoresponder::find($idOfuser);
    			$Autoresponder->access_token=$result['access_token'];
    			$Autoresponder->accessTokenSecret=$result['access_token_secret'];
    			$Autoresponder->is_active='1';
    			$Autoresponder->api_list = $apiList;
    			$Autoresponder->save();
    		$status="Success";
    		$message="Your List has been updated";
	    			$finalArr = array('message'=>$message);
    		
    	}
    	else 
    	{
    		$status="fail";
    		$authorizeUrl='';
    		$finalArr = array('message'=>"An error occur");
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    	
    	    	
    }

    public function getApiUserList (Request $request) 
    {
    	$rules = ['api_name' =>'required'];
    	$data=$request->input();
    	$this->validate($request, $rules);
    	//echo "<pre>";print_r($data);die;
    	if(key_exists('api_name',$data))
    	{   //echo $data['api_name'];
    		$apiName=$data['api_name'];
    		$token = $data['token'];
			$user_id =User::getId($token);
    		$pageSectionData = Autoresponder::whereRaw('name = "'.$apiName.'" and user_id="'.$user_id.'" and is_active = "1"')->orderBy('id', 'desc')->first();
    		//echo "<pre>";print_r($pageSectionData);die; 
			//echo "counter".count($pageSectionData);die;
    		if(isset($pageSectionData) && count($pageSectionData) >=1 )
    		{
    			$pageSection =  $pageSectionData->toArray();
    			$status='success';
    			$list=$pageSection['api_list'];
    			//echo "<pre>";print_r($list);die;
    			$finalArr=array('api_list'=>$list);
    			
    		}
    		else 
    		{
    			$status='fail';
    			$list='';
    			$finalArr=array('api_list'=>$list);
    		}
    		
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    		
    	}
    	
    }
    
    public function changeAutoresponderStatus(Request $request) 
    {
    	$rules = ['api_name' =>'required',
    			'status'=> 'required'];
    	$this->validate($request, $rules);
    	$data=$request->input();
    	
    	if(key_exists('api_name',$data))
    	{
    		$apiName=$data['api_name'];
    		$statusapi=$data['status'];
    		$token = $data['token'];
			$user_id =User::getId($token);
    		$pageSectionData = Autoresponder::whereRaw('name = "'.$apiName.'" and user_id="'.$user_id.'"')->orderBy('id', 'desc')->first();
    	
    		if(isset($pageSectionData) && count($pageSectionData)>=1 )
    		{
    			$pageSection =  $pageSectionData->toArray();
    			//echo count($pageSectionData);
    			//echo "<pre>";print_r($pageSection);die;
    			$idOfuser=$pageSection['id'];
    			
    			$Autoresponder = Autoresponder::find($idOfuser);
    		
    			$Autoresponder->is_active=$statusapi;
    			if($Autoresponder->save())
    			{
    				$status='success';
    				$finalArr=array('Message'=>"Your status has been changed successfully");
    			}
    			else 
    			{
    				$status='fail';
    				$finalArr=array('Message'=>"An error occur");
    			}
    			
    			 
    		}
    		else
    		{
    			$status='fail';
    			$finalArr=array('Message'=>"An error occur");
    		}
    	}
    	else 
    	{
    		$status='fail';
    		$finalArr=array('Message'=>"An error occur");
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    	
    	
    }
    
   //Reset Password
    public function updatePassword(Request $request) 
    {    	
    	$data = $request->input();
    	$old_pass= $data['old_pass'];
    	$password = $data['pass'];
    	$token = $data['token'];
    	$user_id =User::getId($token);
    	$res = User::find($user_id);
    	$oldpasswordData = User::find($user_id)->password;
    	$res->password=Hash::make($password);
    	$user = User::where('id', '=', $user_id)->first();
    	
    	if (Hash::check($old_pass, $oldpasswordData)) 
    	{
    		if($res->save())
    		{
    			
    			$emaildata = array('username'=>$user->email ,'name'=>$user->first_name,'password'=>$password);
    			Mail::send('emails.reset_template', $emaildata, function ($message) use ($user) {
    			
    				$message->from(Config::get('constants.ADMIN_EMAIL'), 'Administrator');
    			
    				$message->to($user->email)->subject('Your password is reset');
    			
    			});
    			$status='success';
    			$finalArr=array('Message'=>"Your Password is successfully changed");
    		}
    		else
    		{
    			$status='fail';
    			$finalArr=array('Message'=>"An error occur");
    		}
    	}
    	else
    	{
    		  $status='fail';
    		  $finalArr=array('Message'=>"Your old password does not exist");
    	}
    	
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    }
    
}
