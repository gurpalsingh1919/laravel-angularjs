<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Helpers\Common;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\PageSection;
use App\TemplateImages;
use DB;
use App\ThirdParty;
use App\EbookPopUpImages;
use App\http\Controllers\Authenticate;
use App\PageDetail;
use App\Myfunnel;
use App\Template;
use App\Autoresponder;
use App\Ebookuser;
use App\Uniquehit;
use App\Helpers\AutoResponderData;
define('APP_ID','4eduYYGDhsIE29ymOhtB8w2QUZDNvbxl');
class MyfunnelController extends Controller
{

    public function getTemplateDetail(Request $request)
    { 
    	$inputs = $request->input();
    	$new_temp_slug_url = $inputs['new_temp_slug_url'];
    	$tempId  = PageDetail::whereRaw('slug = "'.$new_temp_slug_url.'"')->get();
    	$templatedetail =  $tempId->toArray();
    	$template_id = $templatedetail[0]['template_id'];
    	$page_detail_id = $templatedetail[0]['id'];
    	
    	$uniqueHit = $inputs['uniqueHit'];
    	if($uniqueHit =='new')
    	{
    		$browser = $this->getBrowser();
    		$ipaddress =$this->get_client_ip();
    		$this->addUniqueHits($ipaddress,$browser['name'],$page_detail_id);
    	}
   
    	//*********************** main page detail Data **************************//
    	$pageSectionData = PageSection::whereRaw('template_id = "'.$template_id.'" and page_detail_id= "'.$page_detail_id.'" and status = 1')->get();
    	$pageSection =  $pageSectionData->toArray();
    	$jsondata=array();
    	$finalcatData=array();
    	foreach($pageSection as $detail)
    	{
    		$detailArr['option_id'] = $detail['option_id'];
    		if($detail['option_id'] =='3')
    		{
    			//$detailArr['value'] = json_decode($detail['value']);
    			$jsondata=json_decode($detail['value']);
    		}
    		else
    		{
    			$detailArr['value'] = json_decode($detail['value']);
    			$finalcatData[] = $detailArr;
    		}
    	
    	}
    	//print_r($pageSection); die;
    	//******************** Template Images *************************//
    	$finalImageData='';
    	$tempImages=TemplateImages::WhereRaw('template_id = "'.$template_id.'" and status=1 and is_deleted = 0')->whereIn('is_default', [1, 0])->get();
    	$imagestemplate =  $tempImages->toArray();
    	foreach($imagestemplate as $imagess)
    	{
    		$ImageArr['id'] = $imagess['id'];
    		$ImageArr['file_name'] = $imagess['file_name'];
    		$ImageArr['image_type'] = $imagess['image_type'];
    		$finalImageData[] = $ImageArr;
    	}
    	//******************** Get ebook Images  *************************//
    	$ebooksAllImages = EbookPopUpImages::WhereRaw('template_id = "'.$template_id.'" and status= 1')->select('id', 'file_name', 'user_id','is_default')->get();
    	$ebookformimg=$ebooksAllImages->toArray();
    	//************** Left Data*******************************//
   
    	$status = 'success';
    	$finalArr = array(
    			'rightArr'=>$finalcatData,
    			'styleArr'=>$jsondata,
    			'templateImages'=>$finalImageData,
    			'ebookImages'=>$ebookformimg,
    			'template_id'=>$template_id,
    			'page_detail_id'=>$page_detail_id,
    			
    	);
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    	
    }
    public function SubscribeNewUser(Request $request)
    {
    	$inputs = $request->input();
    	$username = $inputs['username'];
    	$useremail = $inputs['useremail'];
    	$usernumber = $inputs['usernumber'];
    	$pageDetailId = $inputs['pageDetailId'];
    	$api_name = $inputs['api_name'];
    	$list_id = $inputs['list_id'];
    	$SubscriberDetail= new Ebookuser;
    	$SubscriberDetail->name=$username;
    	$SubscriberDetail->email=$useremail;
    	$SubscriberDetail->contact_no=$usernumber;
    	$SubscriberDetail->page_detail_id=$pageDetailId;
    	$SubscriberDetail->created_at= date('Y-m-d H:i:s');
    	if($SubscriberDetail->save())
    	{
    		$status = 'success';
    		$finalArr = array('message'=>'You are register successfully');
    		if($api_name !='' && $list_id !='')
    		{
    			$params=array();
    			$params['first_name']=$username;
    			$params['email_id']=$useremail;
    			$params['list_id']=$list_id;
    			$params['phone_no']=$usernumber;
    			$appData=$this->getApiDetails($api_name);
    			$data = AutoResponderData::addContact($api_name, $params,$appData);
    		}
    		
    		//print_r($data);die;
    		
    	}
    	else 
    	{
    		$status = 'fail';
    		$finalArr = array('message'=>'An error occur Please try agai n !');
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;
    }
    function getApiDetails($apiname)
    {
    	$result=Autoresponder::whereRaw('name = "'.$apiname.'"')->get();
    	$result_apiData=$result->toArray();
    	$param=array();
    	if($result_apiData)
    	{
    		if($apiname=='icontact')
    		{
    			$param['consumer_key']=$result_apiData[0]['consumer_key'];
    			$param['consumer_secret']=$result_apiData[0]['consumer_secret'];
    			$param['app_id']=APP_ID;
    		}
    		else if($apiname=='aweber')
    		{
    			$param['access_token']=$result_apiData[0]['access_token'];
    			$param['access_token_secret']=$result_apiData[0]['accessTokenSecret'];
    			$param['consumer_key']=$result_apiData[0]['consumer_key'];
    			$param['consumer_secret']=$result_apiData[0]['consumer_secret'];
    		}
    		else if($apiname=='getresponse')
    		{
    			$param['apiKey']=$result_apiData[0]['consumer_key'];
    			
    		}
    		
    		
    	}
    	return $param;
    	//echo "<pre>";print_r($param);die;
    }
    public function addUniqueHits($ip,$browser,$page_detail_id)
    {
    	$addnewuniquehits=new Uniquehit;
    	$addnewuniquehits->ip=$ip;
    	$addnewuniquehits->browser=$browser;
    	$addnewuniquehits->page_detail_id=$page_detail_id;
    	$addnewuniquehits->created_at= date('Y-m-d H:i:s');
    	$addnewuniquehits->save();
    	/*if($SubscriberDetail->save())
    	{
    		$status = 'success';
    		$finalArr = array('message'=>'You are register successfully');
    	}
    	else
    	{
    		$status = 'fail';
    		$finalArr = array('message'=>'An error occur Please try again !');
    	}
    	$result = Common::sendRequest($status,$finalArr);
    	return $result;*/
    	
    }
	function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
    function getBrowser($agent = null){
    	$u_agent = ($agent!=null)? $agent : $_SERVER['HTTP_USER_AGENT'];
    	$bname = 'Unknown';
    	$platform = 'Unknown';
    	$version= "";
    
    	//First get the platform?
    	if (preg_match('/linux/i', $u_agent)) {
    		$platform = 'linux';
    	}
    	elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
    		$platform = 'mac';
    	}
    	elseif (preg_match('/windows|win32/i', $u_agent)) {
    		$platform = 'windows';
    	}
    
    	// Next get the name of the useragent yes seperately and for good reason
    	if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
    	{
    		$bname = 'Internet Explorer';
    		$ub = "MSIE";
    	}
    	elseif(preg_match('/Firefox/i',$u_agent))
    	{
    		$bname = 'Mozilla Firefox';
    		$ub = "Firefox";
    	}
    	elseif(preg_match('/Chrome/i',$u_agent))
    	{
    		$bname = 'Google Chrome';
    		$ub = "Chrome";
    	}
    	elseif(preg_match('/Safari/i',$u_agent))
    	{
    		$bname = 'Apple Safari';
    		$ub = "Safari";
    	}
    	elseif(preg_match('/Opera/i',$u_agent))
    	{
    		$bname = 'Opera';
    		$ub = "Opera";
    	}
    	elseif(preg_match('/Netscape/i',$u_agent))
    	{
    		$bname = 'Netscape';
    		$ub = "Netscape";
    	}
    
    	// finally get the correct version number
    	$known = array('Version', $ub, 'other');
    	$pattern = '#(?<browser>' . join('|', $known) .
    	')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    	if (!preg_match_all($pattern, $u_agent, $matches)) {
    		// we have no matching number just continue
    	}
    
    	// see how many we have
    	$i = count($matches['browser']);
    	if ($i != 1) {
    		//we will have two since we are not using 'other' argument yet
    		//see if version is before or after the name
    		if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
    			$version= $matches['version'][0];
    		}
    		else {
    			$version= $matches['version'][1];
    		}
    	}
    	else {
    		$version= $matches['version'][0];
    	}
    
    	// check if we have a number
    	if ($version==null || $version=="") {$version="?";}
    
    	return array(
    			'userAgent' => $u_agent,
    			'name'      => $bname,
    			'version'   => $version,
    			'platform'  => $platform,
    			'pattern'    => $pattern
    	);
    }
    public function getNumberOfhits($page_detail_id)
    {
    	$users = Uniquehit::whereRaw('page_detail_id = "'.$page_detail_id.'"')->get();
    	$uniqueHits=$uniqueHits->toArray();
    	return $uniqueHits;
    }
   

	
}
