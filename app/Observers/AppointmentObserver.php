<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\Notification;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        if($appointment->wasChanged('status')) {
            Notification::create([
                'user_id' => $appointment->patient_id,
                'message' => "Your appointment with Dr. {$appointment->doctor->user->name} has been {$appointment->status}.",
            ]);
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        //
    }
}
