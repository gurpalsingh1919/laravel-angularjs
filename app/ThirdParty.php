<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThirdParty extends Model
{
     protected $table = 'tbl_third_party_api_details';
    
    protected $fillable = ['user_id',
     'name',
     'token',
     'value',
     'is_active',
   	'created_at',
	'updated_at',
    ];
}
