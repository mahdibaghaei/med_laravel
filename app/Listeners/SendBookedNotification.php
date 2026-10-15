<?php

namespace App\Listeners;

use App\Events\AppointmentBooked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Notification;


class SendBookedNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */

    public function handle(AppointmentBooked $event)
    {
        $appointment = $event->appointment;
        $doctor = $appointment->doctor;
        $patient = $appointment->patient;

        // Create a notification for the doctor
        Notification::create([
            'user_id' => $doctor->user_id,
            'message' => "You have a new appointment booked with {$patient->name} on {$appointment->start_time}.",
        ]);

        // Create a notification for the patient
        Notification::create([
            'user_id' => $patient->id,
            'message' => "Your appointment with Dr. {$doctor->user->name} has been booked for {$appointment->start_time}.",
        ]);
    }
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
   
}
