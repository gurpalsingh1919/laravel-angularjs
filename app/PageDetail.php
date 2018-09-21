<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageDetail extends Model
{
    protected $table = 'tbl_page_detail';
    protected $fillable = ['user_id', 'name','myfunnel_id','slug', 'industry_id', 'template_id', 'description', 'is_default', '	is_deleted', 'created_at','updated_at'];
    public function myfunnel()
    {
    	return $this->belongsTo('App\MyFunnel');
    }
    public function Ebookuser()
    {
    	return $this->hasMany('App\Ebookuser');
    }
}
