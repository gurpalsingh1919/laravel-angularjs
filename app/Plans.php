<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
	protected $table = 'tbl_plans';
	protected $fillable = ['name', 'price','validity','status','created_at','updated_at'];
}
	
