<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\AppointmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AppointmentObserver::class)]

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'time',
        'status',
        'reminded'
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
