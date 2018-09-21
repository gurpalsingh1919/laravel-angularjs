<?php namespace App\helpers;
class Common 
{
	public static function sendRequest($status,$message)
	{
		//echo "<pre>";print_r($message);die;
		return response()->json(['status' =>$status,'data'=>$message]);
	}
	//public static function authmen

}
