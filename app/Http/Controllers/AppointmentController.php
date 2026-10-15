<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\AppointmentBooked;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user=auth()->user();
        if($user->role === 'doctor'){
            return $user->appointments()->with('doctor.user')->get();
        }
        if($user->role === 'patient'){
            return $user->appointments()->with('patient')->get();
        }
        return Appointment::with('doctor.user','patient')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(auth()->user()->role !== 'patient') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);
        $appointment=DB::transaction(function () use ($request) {
            $exists=Appointment::where('doctor_id',$request->doctor_id)
                ->where('date',$request->date)
                ->where('time',$request->time)
                ->lockForUpdate()
                ->exists();
            
            if($exists){
                return response()->json(['message' => 'Slot already booked'], 409);}
            return Appointment::create([
                'patient_id' => auth()->user()->id,
                'doctor_id' => $request->doctor_id,
                'date' => $request->date,
                'time' => $request->time,
                'status'=>'pending',
            ]);
        });
        AppointmentBooked::dispatch($appointment);
        return response()->json(['message' => 'Appointment booked successfully', 'appointment' => $appointment], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user=auth()->user();
        $appointment=Appointment::with('doctor.user','patient')->findOrFail($id);

        $isPatient=$appointment->patient_id->id === $user->id;
        $isDoctor=$user->role=='doctor'&&$appointment->doctor_id === $user->doctor->id;

        if(!$isPatient&&!$isDoctor && $user->role!=='admin'){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $appointment
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function cancel(string $id)
    {
        $user=auth()->user();
        $appointment=Appointment::findOrFail($id);

        $isPatient=$appointment->patient_id === $user->id;
        $isDoctor=$user->role=='doctor'&&$appointment->doctor_id === $user->doctor->id;

        if(!$isPatient&&!$isDoctor && $user->role!=='admin'){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $appointment->status='cancelled';
        $appointment->save();
        return response()->json(['message' => 'Appointment cancelled successfully', 'appointment' => $appointment], 200);
    }

    public function updateStatus(Request $request, string $id)
    {
        $user=auth()->user();
        if($user->role!=='doctor'){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $appointment=Appointment::findOrFail($id);
        if($appointment->doctor_id !== $user->doctor->id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'status'=>'required|in:pending,confirmed,completed,cancelled'
        ]);
        $appointment->status=$request->status;
        $appointment->save();
        return response()->json(['message' => 'Appointment status updated successfully', 'appointment' => $appointment], 200);
    }
}
