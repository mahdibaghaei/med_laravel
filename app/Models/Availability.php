<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = [
        'doctor_id',
        'rrule',
        'start_time',
        'end_time',
    ];

    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
}
