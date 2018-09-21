<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Helpers\Common;
use App\Helpers\PayPal_IPN;
//use App\Helpers\Global_Paypal;
use Illuminate\Support\Facades\Redirect;
use Auth;
use Config;
use App\Userplans;
use Hash;
use DB;
use App\User;
use App\Plans;
use App\Subscription;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Session;
use App\http\Controllers\Authenticate;
use Validator;
use Illuminate\Support\Facades\Mail;
//use PayPal;

define("MERCHANT_EMAIL","softobiztest-facilitator@gmail.com");
define( 'SSL_URL', 'https://www.paypal.com/cgi-bin/webscr' );
define('ADMIN_EMAIL', 'gurpal.singh@softobiz.com');

define('PAYPAL_API_USERNAME', 'softobiztest-facilitator_api1.gmail.com');
define( 'PAYPAL_PWD', 'LQVQP36FTQHWRERX' );
define( 'PAYPAL_SIGNATURE', 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-AHm1OfNxHg.NqYR8D8-UlsMX6AwL' );
class PaymentController extends Controller
{
	
	//******************* User Registration ********************************//
	public function userRegistration(Request $request)
	{
		$data = $request->input();
		//echo "<pre>";print_r($data);die;
		$rules = array('email' => 'unique:users,email');
		$input=array();
		$input['email']=$request->email;
		$input['password']=$request->password;
		$validator = Validator::make($input, $rules);
		 
		if ($validator->fails())
		{
	
			$status="fail";
			$finalArr=array("message"=>"Email address is already registered.");
			//$result = Common::sendRequest($status,$finalArr);
			//return $result;
		}
		else
		{
	
	
			$password=Hash::make($request->password);
			$register=new  User;
			$register->email=$request->email;
			$register->first_name=$request->first_name;
			$register->last_name=$request->last_name;
			$register->contact_no=$request->contact_no;
			$name= $request->first_name.' '.$request->last_name;
			$email=$request->email;
			$mainpassword=$request->password;
			//$register->password=self::passwordEncyption($request->password);
			$register->password=$password;
			$register->is_registered='1';
			$register->status='0';
			$register->subdomain=strtolower($request->first_name.$request->last_name);
			$register->updated_at=date('Y-m-d H:i:s');
	
			if($register->save())
			{
				$plan_id=$request->payment_plan;
				$user_id=$register->id;
				$planData=Plans::find($plan_id);
				$Cost=$planData->price;
				$validity=$planData->validity;
				$Plan_name=$planData->name;
				$userDetail=User::find($user_id);
				$detail=$userDetail->toArray();
				//******* User Plan **********//
				$registerPlans=new Userplans;
				$registerPlans->user_id=$user_id;
				$registerPlans->plan_id=$plan_id;
				$registerPlans->cost=$Cost;
				$registerPlans->validity=$validity;
				//$registerPlans->start_date=date('Y-m-d H:i:s');
				//$registerPlans->next_recurring_date=date('Y-m-d H:i:s', strtotime('+'.$validity.' month'));
				$registerPlans->status='0';
				$registerPlans->payment_status='1';
				if($registerPlans->save())
				{
					$user_plan_id=$registerPlans->id;
					$login_url="https://".$_SERVER['HTTP_HOST'].'/login';
					$data = array('name' => $name,
							'email'=>$email,
							'password'=>$mainpassword,
							'login_url'=>$login_url);
					Mail::send('emails.register_email', $data, function ($message) use ($userDetail){
						 
						$message->from(ADMIN_EMAIL, 'Registration email');
						 
						$message->to($userDetail->email)->subject('Welcome To Launch A Funnel');
						 
					});
					
					
					$data=array(
							'merchant_email'=>MERCHANT_EMAIL,
							'product_name'=>$Plan_name,
							'f_amount'=>$Cost, 		// trail Period Amount
							'f_cycle'=>'M',			// trail Period M=montrh,Y=year ,D=Days, W='week'
							'f_period'=>$validity, 	// trail Cycle
							's_amount'=>$Cost,		// Second` Amount
							's_cycle'=>'M',			// Second Period M=montrh,Y=year ,D=Days, W='week'
							's_period'=>$validity,	// Second Cycle
							'currency_code'=>'USD',
							'thanks_page'=>"https://".$_SERVER['HTTP_HOST'].'/payment/Paymentsuccess',
							'notify_url'=>"https://".$_SERVER['HTTP_HOST'].'/payment/Paymentsuccess',
							'cancel_url'=>"https://".$_SERVER['HTTP_HOST'].'/paymentstatus',
							'paypal_mode'=>true,
							'user_id'=>$user_id,
							'plan_id'=>$plan_id,
							'user_plan_id'=>$user_plan_id,
							'currency_symbole'=>'$'
					);
	
					$paymentData=self::infotutsPaypal( $data);
					$token=self::authenticateUser($input['email'],$input['password']);
					$content=$token->getContent();
	    			$tokencontent=json_decode($content);
					$status="success";
					$finalArr=array("paypaldata"=>$paymentData,'token'=>$tokencontent->token);
				}
				else
				{
					$status="fail";
					$finalArr=array("message"=>"An error occur. Please try again !");
					//$result = Common::sendRequest($status,$finalArr);
					//return $result;
				}
		   
			}
			else
			{
				$status="fail";
				$finalArr=array("message"=>"An error occur. Please try again !");
				//$result = Common::sendRequest($status,$finalArr);
				//return $result;
			}
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
		//echo "<pre>";print_r($request->email);die;
	
	}
	public function getrepayment(Request $request)
	{
		
		$data = $request->input();
		//echo "<pre>";print_r($data);die;                         -
		$token = $data['token'];
		$user_id =User::getId($token);
		if($user_id)
		{
			$Plan_name=$data['plan_name'];
			$Cost=$data['cost'];
			$validity=$data['validity'];
			$plan_id=$data['plan_id'];
			
			$registerPlans=new Userplans;
			$registerPlans->user_id=$user_id;
			$registerPlans->plan_id=$plan_id;
			$registerPlans->cost=$Cost;
			$registerPlans->validity=$validity;
			//$registerPlans->start_date=date('Y-m-d H:i:s');
			//$registerPlans->next_recurring_date=date('Y-m-d H:i:s', strtotime('+'.$validity.' month'));
			$registerPlans->status='0';
			$registerPlans->payment_status='1';
			if($registerPlans->save())
			{
				$user_plan_id=$registerPlans->id;
				$data=array(
						'merchant_email'=>MERCHANT_EMAIL,
						'product_name'=>$Plan_name,
						'f_amount'=>$Cost, 		// trail Period Amount
						'f_cycle'=>'M',			// trail Period M=montrh,Y=year ,D=Days, W='week'
						'f_period'=>$validity, 	// trail Cycle
						's_amount'=>$Cost,		// Second` Amount
						's_cycle'=>'M',			// Second Period M=montrh,Y=year ,D=Days, W='week'
						's_period'=>$validity,	// Second Cycle
						'currency_code'=>'USD',
						'thanks_page'=>"https://".$_SERVER['HTTP_HOST'].'/payment/Paymentsuccess',
						'notify_url'=>"https://".$_SERVER['HTTP_HOST'].'/payment/Paymentsuccess',
						'cancel_url'=>"https://".$_SERVER['HTTP_HOST'].'/paymentstatus',
						'paypal_mode'=>true,
						'user_id'=>$user_id,
						'plan_id'=>$plan_id,
						'user_plan_id'=>$user_plan_id,
						'currency_symbole'=>'$'
				);
				
				$paymentData=self::infotutsPaypal( $data);
				//$token=self::authenticateUser($input['email'],$input['password']); 
		    	//$content=$token->getContent();
		    	//$tokencontent=json_decode($content);
		    	//print_r($tokencontent->token);die;	
		    	$status="success";
		    	$finalArr=array("paypaldata"=>$paymentData);
		}
		else 
		{
			$status="fail";
			$finalArr=array("message"=>"You are logged out. Please login again !");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	}
	public function infotutsPaypal( $data)
	{
		define( 'SSL_SAND_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr' );
		$action = '';
		//Is this a test transaction?                       
		$action = ($data['paypal_mode']) ? SSL_SAND_URL : SSL_URL;
		$form = '';
		$form .= '<form id="paybtn" name="frm_payment_method" action="' . $action . '" method="post">';
		$form .= '<input type="hidden" name="business" value="' . $data['merchant_email'] . '" />';
		// Instant Payment Notification & Return Page Details /
		//$form .= '<input type="hidden" name="notify_url" value="' . $data['notify_url'] . '" />';
		$form .= '<input type="hidden" name="cancel_return" value="' . $data['cancel_url'] . '" />';
		//$form .= '<input type="hidden" name="return" value="' . $data['thanks_page'] . '" />';
		$form .= '<input type="hidden" name="rm" value="2" />';
		// Configures Basic Checkout Fields -->
		$form .= '<input type="hidden" name="lc" value="" />';
		$form .= '<input type="hidden" name="no_shipping" value="1" />';
		$form .= '<input type="hidden" name="no_note" value="1" />';
		$form .= '<input type="hidden" name="custom" value="user_id=' . $data['user_id'] . '&plan_id='.$data['plan_id']. '&user_plan_id='.$data['user_plan_id'].'" />';
		$form .= '<input type="hidden" name="currency_code" value="' . $data['currency_code'] . '" />';
		$form .= '<input type="hidden" name="page_style" value="paypal" />';
		$form .= '<input type="hidden" name="charset" value="utf-8" />';
		$form .= '<input type="hidden" name="item_name" value="' . $data['product_name'] . '" />';
		//$form .= '<input type="hidden"  name="txn_id" value="_xclick"/>';
		//Recurring  Price
		$form .= '<input type="hidden" name="cmd" value="_xclick-subscriptions" />';
		/* Customizes Prices, Payments & Billing Cycle */
		$form .= '<input type="hidden" name="src" value="1" />';
		/* Value for each installments */
		$form .= '<input type="hidden" name="srt" value="0" /> ';
		$form .= '<input type="hidden" name="a1" value="'. $data['f_amount'] . '" />';
		/** First Period 	*/
		$form .=  '<input type="hidden" name="p1" value="'. $data['f_period'] . '" />';
		/** First Period Cycle e.g: Days,Months	*/
		$form .= '<input type="hidden" name="t1" value="'. $data['f_cycle'] . '"/>';
		/** Second Period Price	*/
		$form .= '<input type="hidden" name="a3" value="'. $data['s_amount'] . '" />';
		/** Second Period 	*/
		$form .= '<input type="hidden" name="p3" value="'. $data['s_period'] .'" />';
		/** Second Period Cycle 	*/
		$form .= '<input type="hidden" name="t3" value="'. $data['s_cycle'] . '" />';
		//$form .= '<input type="submit" value="submit" />';
		$form .= '</form>';
		$form .= '<script>';
		$form .= 'setTimeout("document.frm_payment_method.submit()", 0);';
		$form .= '</script>';
		return $form;
	}
	function authenticateUser($email,$password)
	{
		$credentials=array("email"=>$email,"password"=>$password);
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
		$token= response()->json(compact('token'));
		 
		return $token;
	}
	
  	Public function Paymentsuccess(Request $request)
  	{   		
  		//echo "i am in success";die;
  		$data = $request->input();
  		//echo "<pre>";print_r($data);die;
  		if(isset($_POST))
  		{
  			parse_str($data['custom'],$MYVAR);
  			$plan_id=$MYVAR['plan_id'];
  			$user_plan_id=$MYVAR['user_plan_id'];
  			$PlanDetail=Plans::find($plan_id);
  			$validity=$PlanDetail->validity;
  			$transaction=new Transaction;
  			$transaction->subscr_id=$data['subscr_id'];
  			$transaction->txn_id=$data['subscr_id'];
  			$transaction->txn_type="2";
  			$transaction->paypal_id=$data['payer_email'];
  			$transaction->cost=$data['mc_amount1'];
  			$transaction->plan_id=$MYVAR['plan_id'];
  			$transaction->user_id=$MYVAR['user_id'];
  			$transaction->user_plan_id=$user_plan_id;
  			if($transaction->save())
  			{
  				$user_id=$transaction->user_id;
  				$newUserData=user::find($user_id);
  				$newUserData->status='1';
  				if($newUserData->save())
  				{

  					$newUserplanSatatus=Userplans::find($user_plan_id);
  					$newUserplanSatatus->start_date=date('Y-m-d H:i:s');
  					$newUserplanSatatus->next_recurring_date=date('Y-m-d H:i:s', strtotime('+'.$validity.' month'));
  					$newUserplanSatatus->status='1';
					$newUserplanSatatus->save();
  					
  					$name=$newUserData->first_name.' '.$newUserData->last_name;
  					$data = array('name' => $name,
  							'plan'=>$data['item_name'],
  							'subscr_type'=>$data['item_name'],
  							'first_payment_amt'=>$data['mc_amount1'],
  							'recurring_amt'=>$data['mc_amount3']);
  					Mail::send('emails.payment_email', $data, function ($message) use ($newUserData){
  							
  						$message->from(ADMIN_EMAIL, 'Administrator');
  							
  						$message->to($newUserData->email)->subject('Payment success');
  							
  					});
  					
  					
  					$url="https://".$_SERVER['HTTP_HOST'].'/dashboard';
  					return Redirect::to($url);
  					$status="success";
  					$finalArr=array("message"=>"Your Payment success.");
  						
  				}
  				  				
  			}
  			else
  			{
  				$status="fail";
  				$finalArr=array("message"=>"An error occur.");
  				$url="https://".$_SERVER['HTTP_HOST'].'/paymentstatus';
  				return Redirect::to($url);
  			}
  			
  			$result = Common::sendRequest($status,$finalArr);
  			return $result;
  		}
  		//echo "<pre>";print_r($_POST);die;
  		
    	
  }
  	public function getpaymentplans()
  	{
	  	$plans = Plans::all();
	  	
	  	$plansDetail=$plans->toArray();
	  	//echo "<pre>";print_r($plansDetail);die;
	  	$status="success";
	  	//$allplans=array();
	  	for($i=0;$i<count($plansDetail);$i++)
	  	{
	  		//echo "<pre>";print_r($plansDetail);die;
	  		//$allplans['id']=$plansDetail['status'];
	  		if($plansDetail[$i]['status']=='1')
	  		{
	  			$plansDetail[$i]['plan_status']='Enabled';
	  		}
	  		else 
	  		{
	  			$plansDetail[$i]['plan_status']='Disabled';
	  		}
	  	}
	  	$finalArr=array("plans"=>$plansDetail);
	  	$result = Common::sendRequest($status,$finalArr);
	  	return $result;
  }
	  public function ipnResponse(Request $request)
	  {
		  	$data = $request->input();
		  	mail("gurpal.singh@softobiz.com","IPN Received",print_r($data,true));
		  	
		  	if(isset($data['payment_status']) && $data['payment_status']=='Refunded')
		  	{ //mail("gurpal.singh@softobiz.com",'$data["payment_status"]',print_r($data,true));
		  		//if($data['payment_status']=='Refunded')
		  		//{
		  			//mail("gurpal.singh@softobiz.com",'$data["payment_status"]',print_r($data,true));
		  			self::subscr_refunded($data);
		  		//}
		  	
		  	}
		  	else
		  	{
		  		$txn_type=$data['txn_type'];
		  		
		  		switch($txn_type)
		  		{
		  			case 'subscr_payment':
		  				self:: subscr_payment($data);
		  			case 'recurring_payment_profile_created':
		  				self:: subscr_signup($data);
	  				case 'recurring_payment':
	  					self:: subscr_signup($data);
		  			case 'subscr_cancel':
		  				self::subscr_cancel($data);
		  		}
		  		 		
		  	}
		 	  
	  }
	  function subscr_refunded($data)
	  {
	  	mail("gurpal.singh@softobiz.com","I m in payment",print_r($data,true));
	  	if(isset($data['custom']))
	  	{
	  		parse_str($data['custom'],$MYVAR);
	  		$user_plan_id=$MYVAR['user_plan_id'];
	  	}
	  	else if(isset($data['rp_invoice_id']))
	  	{
	  		$user_plan_id=$data['rp_invoice_id'];
	  	}
	  	
	  //	$ipn = New PayPal_IPN();
	  //	$verified=$ipn->ipn_response($data);
	  	//if ($verified)
	  	//{
	  		$transaction=new Transaction;
	  		$transaction->subscr_id=$data['subscr_id'];
	  		$transaction->txn_id=$data['txn_id'];
	  		//$transaction->plan_id=$MYVAR['plan_id'];
	  		$transaction->paypal_id=$data['payer_email'];
	  		//$transaction->cost=$data['mc_gross'];
	  		//$transaction->user_id=$MYVAR['user_id'];
	  		$transaction->user_plan_id=$user_plan_id;
	  		$transaction->txn_type='4';
	  		if($transaction->save())
	  		{
	  			$userPlanDetail= Userplans::find($user_plan_id);
	  			//********** Save User Plan **************//
	  			$NewUserPlan= new Userplans;
	  			$NewUserPlan->user_id=$userPlanDetail->user_id;
	  			$NewUserPlan->plan_id=$userPlanDetail->plan_id;
	  			$NewUserPlan->cost=$userPlanDetail->cost;
	  			$NewUserPlan->validity=$userPlanDetail->validity;
	  			$NewUserPlan->start_date=$userPlanDetail->start_date;
	  			//$NewUserPlan->next_recurring_date=$user_id;
	  			$NewUserPlan->next_recurring_date=$userPlanDetail->next_recurring_date;
	  			$NewUserPlan->status= '1';
	  			$NewUserPlan->payment_status= '4';
	  			$NewUserPlan->previous_userplan_id= $user_plan_id;
	  			$NewUserPlan->save();
	  			
	  			 
	  		}
	  	
	  //	}
	  	
	  	
	  }
	  function subscr_payment($data)
	  {
	  		mail("gurpal.singh@softobiz.com","I m in payment",print_r($data,true));
	  		if(isset($data['custom']))
	  		{
	  			parse_str($data['custom'],$MYVAR);
	  			$user_plan_id=$MYVAR['user_plan_id'];
	  		}
	  		else if(isset($data['rp_invoice_id']))
	  		{
	  			$user_plan_id=$data['rp_invoice_id'];
	  		}
	  		//parse_str($data['custom'],$MYVAR);
	  		$ipn = New PayPal_IPN();
	  		$verified=$ipn->ipn_response($data);
	  		if ($verified)
	  		{
  					$transaction=new Transaction;
  					$transaction->subscr_id=$data['subscr_id'];
  					$transaction->txn_id=$data['txn_id'];
  					//$transaction->plan_id=$MYVAR['plan_id'];
  					$transaction->paypal_id=$data['payer_email'];
  					//$transaction->cost=$data['mc_gross'];
  					//$transaction->user_id=$MYVAR['user_id'];
  					$transaction->user_plan_id=$user_plan_id;
  					$transaction->txn_type='1';
  					if($transaction->save())
  					{
  						//*************************** update user status **********************************//
  						$user_id=$transaction->user_id;
  						$newUserData=user::find($user_id);
  						$newUserData->status='1';
  						$newUserData->save();
  					  	//************************** Set user plan Status ************************//
  						$newUserplanSatatus=Userplans::find($user_plan_id);
  						$newUserplanSatatus->start_date=date('Y-m-d H:i:s');
  						$newUserplanSatatus->next_recurring_date=date('Y-m-d H:i:s', strtotime('+'.$validity.' month'));
  						$newUserplanSatatus->status='1';
  						$NewUserPlan->payment_status= '1';
  						$newUserplanSatatus->save();
  	
  					}
	  			
	  		}
	  		else
	  		{
	  			if(isset($data['custom']) && $data['custom'] !='')
	  			{
	  				//parse_str($data['custom'],$MYVAR);
	  				$user_id=$MYVAR['user_id'];
	  				$newUserData=user::find($user_id);
	  				$newUserData->status='0';
	  				$newUserData->save();
	  				$name=$newUserData->first_name.' '.$newUserData->last_name;
	  				$data = array('name' => $name);
	  				Mail::send('emails.payment_failure', $data, function ($message) use ($newUserData){
	  					$message->from(ADMIN_EMAIL, 'Administrator');
	  					 
	  					$message->to($newUserData->email)->subject('Payment failure');
	  					 
	  				});
	  			}
	  		}
	}
	function subscr_cancel($data)
	{
		mail("gurpal.singh@softobiz.com","I m in payment",print_r($data,true));
		if(isset($data['custom']))
	  		{
	  			parse_str($data['custom'],$MYVAR);
	  			$user_plan_id=$MYVAR['user_plan_id'];
	  		}
	  		else if(isset($data['rp_invoice_id']))
	  		{
	  			$user_plan_id=$data['rp_invoice_id'];
	  		}
		$ipn = New PayPal_IPN();
		$verified=$ipn->ipn_response($data);
		if ($verified)
		{
			$transaction=new Transaction;
			$transaction->subscr_id=$data['subscr_id'];
			$transaction->txn_id=$data['subscr_id'];
			//$transaction->plan_id=$MYVAR['plan_id'];
			$transaction->paypal_id=$data['payer_email'];
			//$transaction->cost=$data['mc_amount1'];
			//$transaction->user_id=$MYVAR['user_id'];
			$transaction->user_plan_id=$user_plan_id;
			$transaction->txn_type='3';
			if($transaction->save())
			{
				$userPlanDetail= Userplans::find($MYVAR['user_plan_id']);
				//********** Save User Plan **************//
				$NewUserPlan= new Userplans;
				$NewUserPlan->user_id=$userPlanDetail->user_id;
				$NewUserPlan->plan_id=$userPlanDetail->plan_id;
				$NewUserPlan->cost=$userPlanDetail->cost;
				$NewUserPlan->validity=$userPlanDetail->validity;
				$NewUserPlan->start_date=$userPlanDetail->start_date;
				//$NewUserPlan->next_recurring_date=$user_id;
				$NewUserPlan->next_recurring_date=$userPlanDetail->next_recurring_date;
				$NewUserPlan->status= '1';
				$NewUserPlan->payment_status= '3';
				$NewUserPlan->previous_userplan_id= $user_plan_id;
				$NewUserPlan->save();
				
				
				$type='1';
				$subscrData  = Subscription::whereRaw('user_plan_id = "'.$user_plan_id.'" and type="'.$type.'"')->get();
				$subdata =  $subscrData->toArray();
				if(isset($subdata[0]['id']) && $subdata[0]['id'] !='')
				{
					$subscr_Id=$subdata[0]['id'];
					$SubscriptionDataObj=Subscription::find($subscr_Id);
					$SubscriptionDataObj->status='1';
					$SubscriptionDataObj->save();
				}
			}
		}
		else
		{
			if(isset($data['custom']) && $data['custom'] !='')
			{
				$user_id=$MYVAR['user_id'];
				$newUserData=user::find($user_id);
				//$newUserData->status='0';
				//$newUserData->save();
				$name=$newUserData->first_name.' '.$newUserData->last_name;
				$data = array('name' => $name);
				Mail::send('emails.payment_failure', $data, function ($message) use ($newUserData){
					$message->from(ADMIN_EMAIL, 'Administrator');
						
					$message->to($newUserData->email)->subject('Payment failure');
						
				});
			}
		}
	}
	function subscr_signup($data)
	{
		mail("gurpal.singh@softobiz.com","I m in payment",print_r($data,true));
			if(isset($data['custom']))
	  		{
	  			parse_str($data['custom'],$MYVAR);
	  			$user_plan_id=$MYVAR['user_plan_id'];
	  		}
	  		else if(isset($data['rp_invoice_id']))
	  		{
	  			$user_plan_id=$data['rp_invoice_id'];
	  		}
		$ipn = New PayPal_IPN();
		$verified=$ipn->ipn_response($data);
		if ($verified)
		{
			$transaction=new Transaction;
			$transaction->subscr_id=$data['recurring_payment_id'];
			$transaction->txn_id=$data['recurring_payment_id'];
			//$transaction->plan_id=$MYVAR['plan_id'];
			$transaction->paypal_id=$data['payer_email'];
			//$transaction->cost=$data['mc_amount1'];
			//$transaction->user_id=$MYVAR['user_id'];
			$transaction->user_plan_id=$user_plan_id;
			$transaction->txn_type='2';
			if($transaction->save())
			{
					//************************** Set user plan Status ************************//
  						$newUserplanSatatus=Userplans::find($user_plan_id);
  						$newUserplanSatatus->status='1';
  						$newUserplanSatatus->save();
			}
			
		}
		else
		{ mail("gurpal.singh@softobiz.com","Payment not verified",print_r($data,true));
			/*if(isset($data['custom']) && $data['custom'] !='')
			{
				$user_id=$MYVAR['user_id'];
				$newUserData=user::find($user_id);
				//$newUserData->status='0';
				//$newUserData->save();
				$name=$newUserData->first_name.' '.$newUserData->last_name;
				$data = array('name' => $name);
				Mail::send('emails.payment_failure', $data, function ($message) use ($newUserData){
					$message->from(ADMIN_EMAIL, 'Administrator');
		
					$message->to($newUserData->email)->subject('Payment failure');
		
				});
			}*/
		}
	}
	/*public function ipnResponse(Request $request)
	{
	  		$data = $request->input();
		  	mail("gurpal.singh@softobiz.com","I m in payment",print_r($data,true));
		  	if($data['txn_type'] == 'subscr_payment')
		  	{
				  	$ipn = New PayPal_IPN();
				  	$verified=$ipn->ipn_response($data);
					if ($verified) 
				  	{
				  		if($data['txn_type'] == 'subscr_payment')
				  		{
				  			$subscr_id=$data['subscr_id'];
				  			parse_str($data['custom'],$MYVAR);
				  			$user_id=$MYVAR['user_id'];
				  			$user_plan_id=$MYVAR['user_plan_id'];
				  			$TransactionData  = Transaction::whereRaw('subscr_id = "'.$subscr_id.'" and user_id="'.$user_id.'"')->get();
				  			$txndata =  $TransactionData->toArray();
				  			if(isset($txndata[0]['id']) && $txndata[0]['id'] !='')
				  			{
				  				$id = $txndata[0]['id'];
				  				$transaction=Transaction::find($id);
				  				$transaction->txn_id= $data['txn_id'];
				  				$transaction->cost=$data['mc_gross'];
				  				$transaction->plan_id=$MYVAR['plan_id'];
				  				$transaction->user_plan_id=$user_plan_id;
				  				if($transaction->save())
				  				{
				  					$user_id=$transaction->user_id;
				  					$newUserData=user::find($user_id);
				  					$newUserData->status='1';
				  					$newUserData->save();
				  					
				  					
				  					$newUserplanSatatus=Userplans::find($user_plan_id);
				  					$newUserplanSatatus->start_date=date('Y-m-d H:i:s');
				  					$newUserplanSatatus->next_recurring_date=date('Y-m-d H:i:s', strtotime('+'.$validity.' month'));
				  					 
				  					$newUserplanSatatus->status='1';
				  					$newUserplanSatatus->save();
				  				}
				  			}
				  			else 
				  			{
				  				mail("gurpal.singh@softobiz.com","i m not in transaction",$user_id);
				  				$transaction=new Transaction;
				  				$transaction->subscr_id=$data['subscr_id'];
				  				$transaction->txn_id=$data['txn_id'];
				  				//$transaction->plan_id=($data['period1']=='1 M' ? '1'  : ($data['period1']=='3 M' ? '2' : '3' ));
				  				$transaction->plan_id=$MYVAR['plan_id'];
				  				$transaction->paypal_id=$data['payer_email'];
				  				$transaction->cost=$data['mc_gross'];
				  				$transaction->user_id=$user_id;
				  				$transaction->user_plan_id=$user_plan_id;
				  				if($transaction->save())
				  				{
				  					$user_id=$transaction->user_id;
				  					$newUserData=user::find($user_id);
				  					$newUserData->status='1';
				  					$newUserData->save();
				  					
				  					$newUserplanSatatus=Userplans::find($user_plan_id);
				  					$newUserplanSatatus->start_date=date('Y-m-d H:i:s');
				  					$newUserplanSatatus->next_recurring_date=date('Y-m-d H:i:s', strtotime('+'.$validity.' month'));
				  						
				  					$newUserplanSatatus->status='1';
				  					$newUserplanSatatus->save();
				  					 
				  				}
				  			}
				  		}
				  	} 
				  	else
				  	{
				  		if(isset($data['custom']) && $data['custom'] !='')
				  		{
				  			parse_str($data['custom'],$MYVAR);
				  			$user_id=$MYVAR['user_id'];
				  			$newUserData=user::find($user_id);
				  			$newUserData->status='0';
				  			$newUserData->save();
				  			$name=$newUserData->first_name.' '.$newUserData->last_name;
				  			$data = array('name' => $name);
				  			Mail::send('emails.payment_failure', $data, function ($message) use ($newUserData){
				  				$message->from(ADMIN_EMAIL, 'Administrator');
				  			
				  				$message->to($newUserData->email)->subject('Payment failure');
				  			
				  			});
				  		}
				  		
				  		
				  	}
		  	}
	  }*/
	 

	  public function addNewPaymentPlan(Request $request)
	  {
	  	$data = $request->input();
	  
	  	$plan_name=$data['plan_name'];
	  	$plan_price=$data['plan_price'];
	  	$plan_validity=$data['plan_validity'];
	  	$addnewplans = new Plans;
	  	$addnewplans->name=$plan_name;
	  	$addnewplans->price=$plan_price;
	  	$addnewplans->validity=$plan_validity;
	  	$addnewplans->status='1';
	  	if($addnewplans->save())
	  	{
	  		$status="success";
	  		$finalArr=array("message"=>"Your plan has been added successfully");
	  	}
	  	else 
	  	{
	  		$status="fail";
	  		$finalArr=array("message"=>"An error occur. Please try again !");
	  	}
	  	
	  	$result = Common::sendRequest($status,$finalArr);
	  	return $result;
	  }
	  public function getPlanDetail(Request $request)
	  {
	  	$data = $request->input();
	  	$paln_id=$data['plan_id'];
	  	if(isset($paln_id) && $paln_id !='')
	  	{
	  		$planDetail = Plans:: find($paln_id);
	  		$plan=$planDetail->toArray();
	  		$status="success";
	  		$finalArr=array("plan"=>$plan);
	  	}
	  	else
	  	{
	  		$status="fail";
	  		$finalArr=array("message"=>"An error occur. Please try again !");
	  	}
	  	$result = Common::sendRequest($status,$finalArr);
	  	return $result;
	  }
	  public function updatePaymentPlan(Request $request)
	  {
	  	$data = $request->input();
	  	 
	  	$plan_name=$data['plan_name'];
	  	$plan_price=$data['plan_price'];
	  	$plan_validity=$data['plan_validity'];
	  	$paln_id=$data['plan_id'];
	  	$paln_status=$data['plan_status'];
	  	$addnewplans = Plans:: find($paln_id);
	  	$addnewplans->name=$plan_name;
	  	$addnewplans->price=$plan_price;
	  	$addnewplans->validity=$plan_validity;
	  	$addnewplans->status=$paln_status;
	  	if($addnewplans->save())
	  	{
	  		$status="success";
	  		$finalArr=array("message"=>"Your plan has been added successfully");
	  	}
	  	else
	  	{
	  		$status="fail";
	  		$finalArr=array("message"=>"An error occur. Please try again !");
	  	}
	  
	  	$result = Common::sendRequest($status,$finalArr);
	  	return $result;
	  }
	  public function deletePaymentPlan(Request $request)
	  {
		  	$data = $request->input();
		  	$paln_id=$data['plan_id'];
		  	$plansOjb = Plans:: find($paln_id);
		  	if($plansOjb->delete())
		  	{
		  		$status="success";
		  		$finalArr=array("message"=>"Your plan has been deleted successfully");
		  	}
	  		else
	  		{
	  			$status="fail";
	  			$finalArr=array("message"=>"An error occur. Please try again !");
	  		}
	  		$result = Common::sendRequest($status,$finalArr);
	  		return $result;
	  }
	  public function getUserAllPlan(Request $request) 
	  {
		  	$data = $request->input();
		  	$token = $data['token'];
		  	$user_id =User::getId($token);
		  	if($user_id)
		  	{
		  		//********* All Plans *************//
		  		if(isset($data['user_id']) && $data['user_id'] !='')
		  		{
		  			$user_id=$data['user_id'];
		  		}
		  		$status="1";
		  		$currentDate=date('Y-m-d H:i:s');
		  		$plansDetails=Userplans::whereRaw('user_id = "'.$user_id.'" and status="'.$status.'"')->orderBy('id', 'desc')->get();
		  		
		  		
		  		//echo "<pre>";print_r(count($allPlansArr));die;
		  		
		  		
		  		
		  		if(isset($plansDetails) && count($plansDetails) >=1)
		  		{
		  			$allPlansArr=$plansDetails->toArray();
		  			for($i=0;$i< count($allPlansArr);$i++)
		  			{
		  				$plan_id=$allPlansArr[$i]['plan_id'];
		  				$planDetail=Plans::find($plan_id);
		  				$allPlansArr[$i]['plan_name']=$planDetail->name;
		  				$allPlansArr[$i]['plan_price']=$planDetail->price;
		  				$allPlansArr[$i]['plan_time']=$planDetail->validity;
		  				
		  				$dateNext=$allPlansArr[$i]['next_recurring_date'];
		  				$startdateNext=$allPlansArr[$i]['start_date'];
		  				
		  				$refundDate=date('Y-m-d H:i:s', strtotime($startdateNext. ' + 14 days'));
		  				//echo $currentDate.'<br>';
		  				//echo $startdateNext.'<br>';die;
		  				//if(strtotime($currentDate) <= strtotime($refundDate) || $allPlansArr[$i]['payment_status']=='4')
						//{$allPlansArr[$i]['refund']='1';}
						//else{$allPlansArr[$i]['refund']='0';}
    					$allPlansArr[$i]['Next_date']=date ("l jS \of F Y", strtotime($dateNext));
    					$allPlansArr[$i]['plan_start_date']=date ("l jS \of F Y", strtotime($startdateNext));
		  				if($allPlansArr[$i]['payment_status']=='1' || $allPlansArr[$i]['payment_status']=='2')
		  				{
		  					if(strtotime($currentDate) >= strtotime($startdateNext) && strtotime($currentDate) <= strtotime($dateNext))
		  					{
		  						$allPlansArr[$i]['payment_status']="Current Plan";
		  					}
							else if(strtotime($currentDate) < strtotime($dateNext))
							{
								$allPlansArr[$i]['payment_status']="Plan Changed";
							}
							else 
							{
								$allPlansArr[$i]['payment_status']="Invoice";
							}
		  				}
		  				else if($allPlansArr[$i]['payment_status']=='3')
		  				{
		  					$allPlansArr[$i]['payment_status']="Plan Cancelled";
		  				}
		  				else if($allPlansArr[$i]['payment_status']=='4')
		  				{
		  					$allPlansArr[$i]['payment_status']="Payment Refunded";
		  				}
		  				
		  				
		  			}
		  			//********* Current Plan *********//
		  			$CurrentPlansDetails=Userplans::whereRaw('user_id = "'.$user_id.'" and status="'.$status.'" and start_date <= now() and next_recurring_date >= now() and (payment_status="1" OR payment_status="2")')->orderBy('id', 'desc')->first();
		  			
		  			
		  			if(isset($CurrentPlansDetails) && count($CurrentPlansDetails) >=1)
		  			{
		  				$currentArr=$CurrentPlansDetails->toArray();
		  				$plan_id=$currentArr['plan_id'];
		  				$planDetail=Plans::find($plan_id);
		  				$currentArr['plan_name']=$planDetail->name;
		  				$currentArr['plan_price']=$planDetail->price;
		  				$currentArr['plan_time']=$planDetail->validity;
		  				
		  				
		  				$startdateNext=$currentArr['start_date'];
		  				$refundDate=date('Y-m-d H:i:s', strtotime($startdateNext. ' + 14 days'));
		  				
						//if(strtotime($currentDate) <= strtotime($refundDate)){$currentArr['refund']='1';}else{$allPlansArr['refund']='0';}
		  				if(strtotime($currentDate) <= strtotime($refundDate) || $currentArr['payment_status'] !='4')
						{$currentArr['refund']='1';}
						else{$currentArr['refund']='0';}

						$currentArr['start_date']=date ("l jS \of F Y", strtotime($currentArr['start_date']));
		  				$currentArr['next_recurring_date']=date ("l jS \of F Y", strtotime($currentArr['next_recurring_date']));
		  			}
		  			
		  			$status="success";
		  			$finalArr=array("AllPlans"=>$allPlansArr,"currentPlan"=>$currentArr);
		  		}
		  		else 
		  		{
		  			$status="fail";
		  			$finalArr=array("message"=>"you have not purchage any plan");
		  		}
		  		
		  		
		  		//echo "<pre>";print_r($currentArr);die;
		  		
		  	}
		  	else
		  	{
		  		$status="fail";
		  		$finalArr=array("message"=>"You are logged out. Please login again !");
		  	}
		  	$result = Common::sendRequest($status,$finalArr);
		  	return $result;
	  }
	public function paymentDeclineRequest(Request $request)
	{
		$data = $request->input();
		//echo "<pre>";print_r($data);die;                         -
		$token = $data['token'];
		$user_id =User::getId($token);
		if($user_id)
		{	
			$userPlanId=$data['user_plan_id'];
			$type=$data['payment_type'];
			$reason=$data['reason'];
			$subsciptionDetail= new Subscription;
			$subsciptionDetail->user_plan_id=$userPlanId;
			$subsciptionDetail->user_name=$data['user_name'];
			$subsciptionDetail->email=$data['user_email'];
			$subsciptionDetail->description=$reason;
			$subsciptionDetail->type=$type;
			$subsciptionDetail->status='0';
			
			
			if($subsciptionDetail->save())
			{
						
				 	//print_r($userPlanId);die;
					$paymentData=self::getPaymentDetailByUserPlanId($userPlanId);
				 	if(isset($paymentData) && !empty($paymentData))
				 	{
				 		self::sendEmails($paymentData,$type);
				 		$status="success";
				 		$finalArr=array("message"=>"Your request has been accepted. We will update you as soon as possible !");
				 	}
				 	else 
				 	{
				 		$status="fail";
				 		$finalArr=array("message"=>"Your request is already in process please wait.");
				 	}
				 	
			}
		}
	  	else
	  	{
	  		$status="fail";
	  		$finalArr=array("message"=>"You are logged out. Please login again !");
	  	}
	  	$result = Common::sendRequest($status,$finalArr);
	  	return $result;
	}
	function getPaymentDetailByUserPlanId($userPlanId)
	{
		//$userDetail=User::find($user_id);
		$friends_votes =DB::table('tbl_user_plans')
				->where('tbl_user_plans.id',$userPlanId)
				->join('tbl_plans', 'tbl_plans.id', '=', 'tbl_user_plans.plan_id')
				->join('tbl_transaction', 'tbl_transaction.user_plan_id', '=', 'tbl_user_plans.id')
				->join('users', 'users.id', '=', 'tbl_user_plans.user_id')
				->select('users.id as user_id','users.first_name','users.last_name','users.email','tbl_plans.name','tbl_user_plans.cost', 'tbl_user_plans.start_date', 'tbl_user_plans.next_recurring_date','tbl_user_plans.id','tbl_transaction.txn_id')
				->first();
				$dataPlanDetail=json_decode(json_encode($friends_votes), True);
		
				if(isset($dataPlanDetail) && !empty($dataPlanDetail))
				{
					$name=$dataPlanDetail['first_name'].' '.$dataPlanDetail['last_name'];
					$email=$dataPlanDetail['email'];
					$plan_name=$dataPlanDetail['name'];
					$cost=$dataPlanDetail['cost'];
					$start_date=date ("l jS \of F Y", strtotime($dataPlanDetail['start_date']));
					$next_recurring_date=date ("l jS \of F Y", strtotime($dataPlanDetail['next_recurring_date']));
					$User_plan_id=$dataPlanDetail['id'];
					$txn_id=$dataPlanDetail['txn_id'];
					$user_id=$dataPlanDetail['user_id'];
					$userDetail=User::find($user_id);
					$data = array('user_name' => $name,
							'user_email'=>$email,
							'plan_name'=>$plan_name,
							'Plan_Price'=>$cost,
							'subcription_date'=>$start_date,
							'transaction_id'=>$txn_id,
							'recurring_date'=>$next_recurring_date,
							'user_plan_id'=>$User_plan_id,
							'user_id'=>$user_id
					);
					return $data;
					
				}
				
				
				
		
	}
	function sendEmails($dataPlanDetail,$payment_type)
	{
		$user_id=$dataPlanDetail['user_id'];
		$userDetail=User::find($user_id);
		if($payment_type=='1')
		{
			$dataPlanDetail['Subscription_type']='cancel';
				//************ Send Admin ***************//
				Mail::send('emails.payment_cancel_admin', $dataPlanDetail, function ($message){
						
					$message->from(ADMIN_EMAIL, 'Launch A Funnel');
						
					$message->to(ADMIN_EMAIL)->subject('Subscription cancel request');
						
				});
				
				//*********** Send User *****************//
					Mail::send('emails.payment_cancel_user', $dataPlanDetail, function ($message) use ($userDetail){
							
						$message->from(ADMIN_EMAIL, 'Launch A Funnel');
							
						$message->to($userDetail->email)->subject('Subscription cancel request');
							
					});
		}
		else if($payment_type=='2')
		{
			//************ Send Admin ***************//
			$dataPlanDetail['Subscription_type']='refund';
			Mail::send('emails.payment_cancel_admin', $dataPlanDetail, function ($message){
			
				$message->from(ADMIN_EMAIL, 'Launch A Funnel');
			
				$message->to(ADMIN_EMAIL)->subject('Subscription Refund request');
			
			});
			
				//*********** Send User *****************//
				Mail::send('emails.payment_cancel_user', $dataPlanDetail, function ($message) use ($userDetail){
						
					$message->from(ADMIN_EMAIL, 'Launch A Funnel');
						
					$message->to($userDetail->email)->subject('Subscription Refund Request');
						
				});
		}
			
		
		
	}
	
	public function cancelThePaymentProgrammatically(Request $request)
	{
		$data = $request->input();
		$token = $data['token'];
		$user_id =User::getId($token);
		if($user_id)
		{
			$userPlanId=$data['user_plan_id'];
			$action="cancel";
			$txn_type="1";
			$txnDetail=Transaction::whereRaw('user_plan_id="'.$userPlanId.'" and txn_type="'.$txn_type.'"')->get();
			$txnDetailArr=$txnDetail->toArray();
			if(!empty($txnDetailArr[0]['id']) && $txnDetailArr[0]['id'] !='')
			{
				$profile_id=$txnDetail[0]['subscr_id'];
				$result=self::change_subscription_status( $profile_id, $action );
				//echo "<pre>";print_r($result);die;
				if($result["ACK"]=='Success')
				{
					//$userPlanId=$data['user_plan_id'];
					$type=$data['payment_type'];
					$reason=$data['reason'];
					$subsciptionDetail= new Subscription;
					$subsciptionDetail->user_name=$data['user_name'];
					$subsciptionDetail->email=$data['user_email'];
					$subsciptionDetail->user_plan_id=$userPlanId;
					$subsciptionDetail->description=$reason;
					$subsciptionDetail->type=$type;
					$subsciptionDetail->status='0';
					if($subsciptionDetail->save())
					{
							
						$paymentData=self::getPaymentDetailByUserPlanId($userPlanId);
						if(isset($paymentData) && !empty($paymentData))
						{
							self::sendEmails($paymentData,$type);
							$status="success";
							$finalArr=array("message"=>"Your request has been accepted. We will update you as soon as possible !");
						}
						else
						{
							$status="fail";
							$finalArr=array("message"=>"An error occur!");
						}
					
					}
					
				}
				else 
				{
					$status="fail";
					$message="Your recurring payment already has been canceled";
					$finalArr=array("message"=>$message);
				}
				
			}
			else 
			{
				$status="fail";
				$message="You can not cancel ";
				$finalArr=array("message"=>$message);
				
			}
			
			
		}
		else
		{
			$status="fail";
			$finalArr=array("message"=>"You are logged out. Please login again !");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	
	/**
	 * Performs an Express Checkout NVP API operation as passed in $action.
	 *
	 * Although the PayPal Standard API provides no facility for cancelling a subscription, the PayPal
	 * Express Checkout  NVP API can be used.
	 */
	function change_subscription_status( $profile_id, $action ) 
	{
		//echo  env('PAYPAL_SANDBOX_API_USERNAME');die;
		//$response = PayPal::getProvider()->getRecurringPaymentsProfileDetails($profile_id);
		//echo "<pre>";print_r($response);die;
		$api_username=PAYPAL_API_USERNAME;
		$pwd=PAYPAL_PWD;
		$signature=PAYPAL_SIGNATURE;
		$api_request = 'USER=' . urlencode( $api_username )
		.  '&PWD=' . urlencode( $pwd )
		.  '&SIGNATURE=' . urlencode( $signature )
		.  '&VERSION=76.0'
				.  '&METHOD=ManageRecurringPaymentsProfileStatus'
						.  '&PROFILEID=' . urlencode( $profile_id )
						.  '&ACTION=' . urlencode( $action )
						.  '&NOTE=' . urlencode( 'Profile cancelled at store' );
	
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp' ); // For live transactions, change to 'https://api-3t.paypal.com/nvp'
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
	
		// Uncomment these to turn off server and peer verification
		 curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		 curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		 curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
	
		
		
		// Set the API parameters for this transaction
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $api_request );
	
		// Request response from PayPal
		$response = curl_exec( $ch );
	
		// If no response was received from PayPal there is no point parsing the response
		if( ! $response )
			die( 'Calling PayPal to change_subscription_status failed: ' . curl_error( $ch ) . '(' . curl_errno( $ch ) . ')' );
	
		curl_close( $ch );
	
		// An associative array is more usable than a parameter string
		parse_str( $response, $parsed_response );
	
		return $parsed_response;
	}
	
	public function getAllRefundList(Request $request)
	{
		$data = $request->input();
		$token = $data['token'];
		$user_id =User::getId($token);
		if($user_id)
		{
			$txn_type='2';
			$status='1';
			//$subscrRefund=Subscription::where('type="'.$txn_type.'" and status="'.$status.'"')
			//->where()->get();
			$subscrRefund=DB::table('tbl_change_subscr_status as subscr')
			->where('subscr.type',$txn_type)
			->where('tbl_transaction.txn_type',1)
			->join('tbl_user_plans', 'tbl_user_plans.id', '=', 'subscr.user_plan_id')
			->join('tbl_plans', 'tbl_plans.id', '=', 'tbl_user_plans.plan_id')
			->join('tbl_transaction', 'tbl_transaction.user_plan_id', '=', 'subscr.user_plan_id')
			->select('subscr.id','subscr.user_name','tbl_user_plans.user_id','subscr.status', 'subscr.email','subscr.description','tbl_transaction.txn_id','tbl_plans.name','tbl_plans.price')
			->orderBy('subscr.id', 'desc')->get();
			$subscrRefundData=json_decode(json_encode($subscrRefund), True);
			$status="success";
			$finalArr=array("refunds"=>$subscrRefundData);
			//echo "<pre>";print_r($subscrRefundData);die;
		}
		else
		{
			$status="fail";
			$finalArr=array("message"=>"You are logged out. Please login again !");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function getAllCancellationList(Request $request)
	{
		$data = $request->input();
		$token = $data['token'];
		$user_id =User::getId($token);
		if($user_id)
		{
			$txn_type='1';
			$status='1';
			//$subscrRefund=Subscription::where('type="'.$txn_type.'" and status="'.$status.'"')
			//->where()->get();
			$subscrRefund=DB::table('tbl_change_subscr_status as subscr')
			->where('subscr.type',$txn_type)
			->where('tbl_transaction.txn_type',3)
			->join('tbl_user_plans', 'tbl_user_plans.id', '=', 'subscr.user_plan_id')
			->join('users', 'users.id', '=', 'tbl_user_plans.user_id')
			->join('tbl_plans', 'tbl_plans.id', '=', 'tbl_user_plans.plan_id')
			->join('tbl_transaction', 'tbl_transaction.user_plan_id', '=', 'subscr.user_plan_id')
			->select('users.id as user_id','subscr.id','subscr.user_name','tbl_user_plans.user_id','tbl_user_plans.status','subscr.email','subscr.description','tbl_transaction.txn_id','tbl_plans.name','tbl_plans.price')
			->orderBy('subscr.id', 'desc')->get();
			$subscrRefundData=json_decode(json_encode($subscrRefund), True);
			$status="success";
			$finalArr=array("cancelUser"=>$subscrRefundData);
			//echo "<pre>";print_r($subscrRefundData);die;
		}
		else
		{
			$status="fail";
			$finalArr=array("message"=>"You are logged out. Please login again !");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
			
	public function changeUserPaymentPlan(Request $request)
	{
		$data = $request->input();
		$token = $data['token'];
		$user_id =User::getId($token);
		//echo "<pre>";print_r($data);die;
		if($user_id)
		{
			
			$subsciptionDetail= new Subscription;
			$subsciptionDetail->plan_id=$data['plan_id'];
			$subsciptionDetail->user_name=$data['user_name'];
			$subsciptionDetail->email=$data['user_email'];
			$subsciptionDetail->user_plan_id=$data['user_plan_id'];
			$subsciptionDetail->type=$data['payment_type'];
			$subsciptionDetail->status='0';
			$planDetail=Plans::find($data['plan_id']);
			$plan_name=$planDetail->name;
			
			//if($subsciptionDetail->save())
			//{	
				$changePlan_id=$subsciptionDetail->id;
				$api_request = 'USER=' . urlencode( PAYPAL_API_USERNAME )
				.  '&PWD=' . urlencode( PAYPAL_PWD )
				.  '&SIGNATURE=' . urlencode( PAYPAL_SIGNATURE )
				.  '&VERSION=86.0'
				.  '&METHOD=SetExpressCheckout'
				.  '&L_BILLINGTYPE0=RecurringPayments'
				//.  '&PAYMENTREQUEST_n_AMT='. urlencode( $AMT )
				.  '&L_BILLINGAGREEMENTDESCRIPTION0='. urlencode( $plan_name )
				//.  '&DESC='. $plan_name   #The description of the billing agreement
				//.  '&L_PAYMENTREQUEST_0_QTY1=1'
				//.  '&PAYMENTREQUEST_0_ITEMAMT='. urlencode( $AMT )
				.  '&cancelUrl=https://'.$_SERVER['HTTP_HOST'].'/mysettings/subscription'    #For use if the consumer decides not to proceed with payment
				.  '&returnUrl=https://'.$_SERVER['HTTP_HOST'].'/mysettings/subscription'; 	  #For use if the consumer proceeds with payment
				
				$result=self::curl_request($api_request);
				//echo "<pre>";print_r($result);die;
				if($result['ACK']=='Success')
				{
					$token=$result['TOKEN'];
					$subsciptionDetail->token=$token;
					$subsciptionDetail->save();
					
					$status="success";
  					$finalArr=array("message"=>"Your Payment success.","token"=>$token);
				}
				else 
				{
					$status="fail";
  					$finalArr=array("message"=>"An error occur. Please Try again !");
				}
			//}
			}
			else
			{
				$status="fail";
				$finalArr=array("message"=>"You are logged out. Please login again !");
			}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
		
	}	
	public function getDetailAndCreateRecurring(Request $request)
	{
		$data = $request->input();
		$token = $data['token'];
		$user_id =User::getId($token);
		//echo "<pre>";print_r($user_id);die;
		if($user_id)
		{
				$user_token=$data['Plan_token'];
				$subscrDetail  = Subscription::whereRaw('token = "'.$user_token.'"')->orderBy('id', 'desc')->get();
				//$subscrDetail=Subscription::where("token"=>$user_token)->get();
				$subscrArr=$subscrDetail->toArray();
				if(isset($subscrArr[0]['id']) && $subscrArr[0]['id'] !='')
				{
					$subscr_id=$subscrArr[0]['id'];
					$token=$subscrArr[0]['token'];
					$user_plan_id=$subscrArr[0]['user_plan_id'];
					$plan_id=$subscrArr[0]['plan_id'];
					//********* New Plan Detail ************//
					$planDetail=Plans::find($plan_id);
					$name=$planDetail->name;
					$amount=$planDetail->price;
					$validity=$planDetail->validity;
					// ******** Previous Plan detail ***********//
					$userPlanDetail=Userplans::find($user_plan_id);
					$plan_startDate=$userPlanDetail->next_recurring_date;
					
					
					
					//******** Get Express Chckout Details ********//
					$api_request = 'USER=' . urlencode( PAYPAL_API_USERNAME )
					.  '&PWD=' . urlencode( PAYPAL_PWD )
					.  '&SIGNATURE=' . urlencode( PAYPAL_SIGNATURE )
					.  '&VERSION=86.0'
					.  '&METHOD=GetExpressCheckoutDetails'
					.  '&TOKEN=' . urlencode( $token );
						
					$result=self::curl_request($api_request);
					if($result['ACK']=='Success')
					{
						//********** Save User Plan **************//
						$NewUserPlan= new Userplans;
						$NewUserPlan->user_id=$user_id;
						$NewUserPlan->plan_id=$plan_id;
						$NewUserPlan->cost=$amount;
						$NewUserPlan->validity=$validity;
						$NewUserPlan->start_date=$plan_startDate;
						//$NewUserPlan->next_recurring_date=$user_id;
						$NewUserPlan->next_recurring_date=date('Y-m-d H:i:s', strtotime($plan_startDate.'+'.$validity.' month'));
						$NewUserPlan->status= '0';
						$NewUserPlan->payment_status= '2';
						$NewUserPlan->previous_userplan_id= $user_plan_id;
						$NewUserPlan->save();
						$user_plan_id=$NewUserPlan->id;
						
						
						
						$apyer_id=$result['PAYERID'];
						$token=$result['TOKEN'];
						if(isset($apyer_id) && $apyer_id !='')
						{
							
							
							$createREcurring=self::create_recurring($name,$amount,$plan_startDate,$token,$apyer_id,$validity,$user_plan_id);
							//print_r($createREcurring);die;
							
							if($createREcurring['ACK']=='Success')
							{
								//********* Save Subscription *************//
								$detail_subscr=Subscription::find($subscr_id);
								$detail_subscr->payer_id=$apyer_id;
								$detail_subscr->status='1';
								$detail_subscr->save();
								
								$userPlanDetail=Userplans::find($user_plan_id);
								$userPlanDetail->status='1';
								$userPlanDetail->save();
								//*********** Save transaction ************//
								/*$subscr_id=	$createREcurring['PROFILEID'];
								$transaction=new Transaction;
			  					$transaction->subscr_id=$subscr_id;
			  					$transaction->txn_id=$subscr_id;
			  					$transaction->plan_id=$plan_id;
			  					//$transaction->paypal_id=$apyer_id;
			  					$transaction->cost=$amount;
			  					$transaction->user_id=$user_id;
			  					$transaction->user_plan_id=$user_plan_id;
			  					$transaction->txn_type='2';
			  					$transaction->save();*/
								//echo "else";print_r($createREcurring);die;
								$status="success";
								$finalArr=array("message"=>"Your payment plan has been updated.You need to cancel the previous one.");
							}
							else
							{
								//echo "else";print_r($createREcurring);die;
								$msg=$createREcurring["L_SHORTMESSAGE0"];
								$status="fail";
								$finalArr=array("message"=>$msg);
							}
						}
					
					
						
					}
					else
					{
						$status="fail";
						$finalArr=array("message"=>"An error occur. Please Try again !");
					}
				}
				/*echo "<pre>";print_r($subscrArr);die;
				if(isset($token) && $token !='')
				{
					
				}
				else 
				{
					$url='http://'.$_SERVER['HTTP_HOST'].'/mysettings/subscription';
					return Redirect::to($url);
				}*/
		}
		else
		{
			$status="fail";
			$finalArr=array("message"=>"You are logged out. Please login again !");
		}
		$result = Common::sendRequest($status,$finalArr);
		return $result;
	}
	public function curl_request($api_request)
	{

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp' ); // For live transactions, change to 'https://api-3t.paypal.com/nvp'
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
			
		// Uncomment these to turn off server and peer verification
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt($ch, CURLOPT_SSLVERSION, 6);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		// Set the API parameters for this transaction
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $api_request );
		// Request response from PayPal
		$response = curl_exec( $ch );
		// If no response was received from PayPal there is no point parsing the response
		if( ! $response )
			die( 'Calling PayPal to change_subscription_status failed: ' . curl_error( $ch ) . '(' . curl_errno( $ch ) . ')' );
		curl_close( $ch );
		// An associative array is more usable than a parameter string
		parse_str( $response, $parsed_response );
		$result=$parsed_response;
		return $result;
	}	
	public function create_recurring($name,$amount,$next_recurringDate,$token,$apyer_id,$validity,$user_plan_id)
	{
		
			    $api_username=PAYPAL_API_USERNAME;
			    $pwd=PAYPAL_PWD;
			    $signature=PAYPAL_SIGNATURE;
			   // $next_recurringDate='2016-07-08 08:31:30';
				$api_request = 'USER=' . urlencode( $api_username )
		    	.  '&PWD=' . urlencode( $pwd )
			    .  '&SIGNATURE=' . urlencode( $signature )
			    .  '&VERSION=76.0'
			    .  '&METHOD=CreateRecurringPaymentsProfile'
		    	.  '&AMT=' . urlencode( $amount )
			    .  '&VERSION=86'
		    	.   '&TOKEN='. urlencode( $token )
			    .   '&PAYERID=' . urlencode( $apyer_id )  #Identifies the customer's account
			    .   '&PROFILESTARTDATE='.$next_recurringDate    #Billing date start, in UTC/GMT format
			    .    '&DESC='. urlencode( $name )     #Profile description - same as billing agreement description
			    .   '&BILLINGPERIOD=Month'    #Period of time between billings
			    .   '&BILLINGFREQUENCY='.$validity
			    //.   '&PAYMENTREQUEST_0_CUSTOM=255'
			    .   '&PROFILEREFERENCE='.$user_plan_id
			   
			    . 	'&CURRENCYCODE=USD'    #The currency, e.g. US dollars
			    .   '&COUNTRYCODE=US'    #The country code, e.g. US
			    .  '&NOTE=' . urlencode( 'REcurring Profile Authorisation at store' );
		
		$result=self::curl_request($api_request);
		return	$result;	
	}
	
}
