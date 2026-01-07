<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'school_name','department_name','address','head_name','head_nip','theme_primary','footer_text','logo_path'
    ];
}