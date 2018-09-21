<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EbookPopUpImages extends Model
{
    protected $table = 'tbl_ebook_popup_images';
    protected $fillable = ['user_id', 'file_name', 'original_name', 'file_size', 'file_type', 'is_default', 'created_at', 'updated_at'];
	
}
