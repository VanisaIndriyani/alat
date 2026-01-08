<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';
    protected $fillable = ['name','position','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}