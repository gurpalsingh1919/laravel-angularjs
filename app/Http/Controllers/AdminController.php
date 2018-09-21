<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Helpers\Common;
use App\Helpers\PayPal_IPN;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Config;
use App\Userplans;
use Hash;
use App\User;
use App\Plans;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Session;
use App\http\Controllers\Authenticate;
use Validator;
use Illuminate\Support\Facades\Mail;
define('ADMIN_EMAIL', 'administrator@launchafunnel.com');
class AdminController extends Controller
{
	
	public function authenticateAdmin(Request $request)
	{
		$data = $request->input();
		$token = User::authenticateUser($data['email'], $data['pass']);
		$content=$token->getContent();
		$tokencontent=json_decode($content); 
		if(isset($tokencontent->token))
		{ 	
			$token = $tokencontent->token;
			$status="success";
			$finalArr=array('message'=>"admin",'token'=>$token);
		}
		else if(isset($tokencontent->error))
		{   
			$token = $tokencontent->error;
			$status="fail";
			$finalArr=array("message"=>$token);
		}
		
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function userRegister(Request $request)
	{
		$data = $request->input();
		$rules = array('email' => 'unique:users,email');
		$input=array();
		$input['email']=$request->email;
		$input['password']=$request->password;
		$validator = Validator::make($input, $rules);
			
		if ($validator->fails())
		{		
			$status="fail";
			$finalArr=array("message"=>"Email address is already registered.");
		}
		else
		{
			$password=Hash::make($request->password);
			$register=new User;
			$register->email=$request->email;
			$register->first_name=$request->first_name;
			$register->last_name=$request->last_name;
			$register->contact_no=$request->contact_no;
			$name= $request->first_name.' '.$request->last_name;
			$email=$request->email;
			$mainpassword=$request->password;
			$register->password=$password;
			$register->is_registered='1';
			$register->status='0';
			$register->user_status='1';
			$register->subdomain=strtolower($request->first_name.$request->last_name);
			$register->updated_at=date('Y-m-d H:i:s');
		
			if($register->save())
			{						
				$login_url="http://".$_SERVER['HTTP_HOST'].'/login';
				$data = array('name' => $name,
						'email'=>$email,
						'password'=>$mainpassword,
						'login_url'=>$login_url);
				Mail::send('emails.register_email', $data, function ($message) use ($register){
						
					$message->from(ADMIN_EMAIL, 'Registration email');
						
					$message->to($register->email)->subject('Welcome To Launch A Funnel');
						
				});
				$status="success";
				$finalArr=array("message"=>"User registered successfully");
			}
			else
			{
				$status="fail";
				$finalArr=array("message"=>"An error occur. Please try again!");
			}	
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function sendResetEmail(Request $request)
	{
		$data = $request->input();
		$userId=$data['user_id'];
		$userDetail = User::find($userId);
		$email=$userDetail->email;
		
		if(isset($email))
		{
			//$email = $userDetail->email;
			$firstname = $userDetail->first_name;
			$lastname = $userDetail->last_name;
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
				Mail::send('emails.email_template', $emaildata, function ($message) use ($userDetail) {
			
					$message->from(Config::get('constants.ADMIN_EMAIL'), 'Administrator');
			
					$message->to($userDetail->email)->subject('Reset your password to login');
			
				});
				$status="success";
				$finalArr=array('message'=>"Reset password email hasn been sent");
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
			$finalArr = array('message'=>"Email id not exist.Please try again !");
		}
		
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	/*public function getUserPaymentHistory(Request $request)
	{
		$data = $request->input();
		$IdOfUser = $data['user_id'];
		$token = $data['token'];
		$user_id =User::getId($token);
		if($user_id)
		{
			$status='1';
			$Userplans = Transaction::WhereRaw('user_id = "'.$IdOfUser.'"')->get();
			$userPlanDetail=$Userplans->toArray();
			for($i=0;$i<count($userPlanDetail);$i++)
			{
				$plan_id=$userPlanDetail[$i]['plan_id'];
				$plan=Plans::find($plan_id);
				$userPlanDetail[$i]['plan_name']=$plan->name;
			}
			//echo "<pre>";print_r($Userplans);die;
			$status="success";
			$finalArr=array("PaymentHistory"=>$userPlanDetail);
		}
		else 
    	{
    		$status="fail";
    		$finalArr=array("message"=>"Token has been expired.Please Login again");
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
		
	}*/

}
