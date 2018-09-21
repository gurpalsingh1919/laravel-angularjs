<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	protected $table = 'tbl_transaction';
	protected $fillable = ['subscr_id', 'plan_id','user_plan_id', 'paypal_id', 'cost','user_id','created_at','updated_at'];
}
