<?php

use App\Models\Appointment;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
     $appointments=Appointment::where('reminded',false)->where('status','confirmed')->get();
     foreach($appointments as $appointment){
        $start=Carbon::parse($appointment->date.' '.$appointment->time);

        if($start->between(now(),now()->addHour(2))){
            Notification::create([
                'user_id'=>$appointment->patient_id,
                'message'=>'You have an appointment with Dr. '.$appointment->doctor->user->name.' at '.$start->format('H:i').' on '.$start->format('Y-m-d'),
            ]);
            $appointment->reminded=true;
            $appointment->save();
        }
})->everyFiveMinutes();