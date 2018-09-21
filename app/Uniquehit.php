<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Uniquehit extends Model
{
	protected $table = 'tbl_page_unique_hits';
    protected $fillable = ['ip', 'browser', 'page_detail_id','created_at','updated_at'];
	
    public static function getAllVisitorOfTemplate($pageDetailId)
    {
    	$users = Uniquehit::whereRaw('page_detail_id = "'.$pageDetailId.'"')->get();
    	$users=$users->toArray();
    	return $users;
    	 
    }
}
