<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
     protected $table = 'tbl_templates';
    
    protected $fillable = ['id', 'category_id', 'name', 'image', 'slug', 'temp_code'];
}
