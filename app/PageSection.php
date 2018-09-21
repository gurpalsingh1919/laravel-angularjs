<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $table = 'tbl_page_section';
    
    protected $fillable = ['template_id', 'option_id', 'page_detail_id', 'value', '	status'];
	//
	public function getTemplateData()
	{
		
	}
}
