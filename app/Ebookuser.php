<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ebookuser extends Model
{
	protected $table = 'tbl_optin_users';
    protected $fillable = ['name', 'contact_no', 'email', 'page_detail_id','created_at','updated_at'];
	
    public static function getAllRegisterUserOfTemplate($pageDetailId)
    {
    	$users = Ebookuser::whereRaw('page_detail_id = "'.$pageDetailId.'"')->get();
    	$users=$users->toArray();
    	return $users;
    	
    }
    public function pagedetail()
    {
    	return $this->belongsTo('App\PageDetail');
    }
    public static function getAllcontactsOfFunnel($step_id,$startdate,$enddate)
    {
    	$projects='';
    	if($startdate !='' && $enddate !='')
    	{
    		$projects = self::whereBetween('updated_at', array($startdate, $enddate))
    		->where('page_detail_id', '=', $step_id)
    		->get();
    	}
    	else
    	{
    		$projects = Ebookuser::whereRaw('page_detail_id = "'.$step_id.'"')->get();
    	}

    	return $projects;
    }
}
