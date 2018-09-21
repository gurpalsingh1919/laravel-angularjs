<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Userplans extends Model
{
	protected $table = 'tbl_user_plans';
	protected $fillable = ['user_id', 'plan_id', 'cost', 'validity','start_date','next_recurring_date','status','created_at','updated_at'];
}
