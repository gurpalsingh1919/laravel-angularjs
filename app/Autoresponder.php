<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Autoresponder extends Model
{
     protected $table = 'tbl_autoresponder';
    
    protected $fillable = ['user_id',
     'name',
     'consumer_key',
     'consumer_secret',
     'is_active',
     'api_list',
     'access_token',
     'accessTokenSecret',
     'requestTokenSecret',
   	'created_at',
	'updated_at',
    ];
    public static function addNewKeys($name,$consumerKey,$consumerSecret,$access_token,$access_token)
    {
    	$isactive='1';
    	$apilist='';
    	DB::table('tbl_autoresponder')->insert(
    	['name' => $name, 
    	'consumer_key' =>$consumerKey,
		'consumer_secret'=>$consumerSecret,
		'is_active'=>$isactive,
		/*'api_list'=>$apilist,
		'access_token'=>$access_token,
		'accessTokenSecret'=>$accessTokenSecret,*/
		'requestTokenSecret'=>$requestTokenSecret]
    	);
    }
    
}
