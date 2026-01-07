<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_name',
        'student_nis',
        'equipment_id',
        'borrowed_at',
        'planned_return_at',
        'returned_at',
        'purpose',
        'status',
        'fine_amount',
    ];

    protected $dates = ['borrowed_at','planned_return_at','returned_at'];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}