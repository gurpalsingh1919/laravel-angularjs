<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
   protected $table = 'tbl_change_subscr_status';
	protected $fillable = ['user_plan_id', 'description','token','plan_id','payer_id', 'type', 'created_at','updated_at'];
}
