<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use RRule\RRule;


class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::with('user')->get();
        return response()->json($doctors);
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
        if(auth()->user()->role !== 'admin'){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'expertise' => 'required|string|max:255',
            'medical_code' => 'required',
        ]);
        $doctor=DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'doctor',
            ]);
            return Doctor::create([
                'user_id' => $user->id,
                'expertise' => $request->expertise,
                'medical_code' => $request->medical_code,
            ]);
        });
        return response()->json(['message' => 'Doctor created successfully', 'doctor' => $doctor], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

    public function slots(Request $request,$id){
        $request->validate([
            'date'=>'required'
        ]);

        $doctor=Doctor::findOrFail($id);
        $date=$request->date;

        $booked=Appointment::where('doctor_id',$doctor->id)->where('date',$date)
        ->where('status','!=','cancelled')->pluck('time')->toArray();

        $slots=[];

        foreach($doctor->availabilities as $a){
            $rule = new RRule($a->rrule . ';DTSTART=20260101T000000');
            if(!$rule->occursAt($date)){
                continue;
            }

            $time=strtotime($a->start_time);
            $end=strtotime($a->end_time);
            while($time<$end){
                $t = date('H:i', $time);
                if(!in_array($t,$booked)){
                    $slots[]=$t;
                }
                $time+=30*60;
            }
        }
        return response()->json($slots);
    }
}
