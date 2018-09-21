<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateImages extends Model
{
    protected $table = 'tbl_template_images';
    
    protected $fillable = ['user_id', 
    'template_id', 
    'parent', 
    'file_name', 
    'original_name',
	'file_size',
	'file_type',
	'table_id',
	'table_reference',
	'is_default',
	'created_at',
	'updated_at',
    ];
}
