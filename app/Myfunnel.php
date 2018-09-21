<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Myfunnel extends Model
{
 	protected $table = 'tbl_myfunnel';
    protected $fillable = ['user_id', 'funnel_name','cname', 'slug', 'group_tag','created_at','updated_at'];
	public function pagedetail()
    {
        return $this->hasMany('App\PageDetail')->where('is_deleted', '=', '0');
    }
	/*public function getAllFunnelOfUser($user_id)
	{
		
	}*/
}
