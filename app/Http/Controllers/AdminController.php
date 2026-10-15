<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function appointments(){
        if(auth()->user()->role!='admin'){
            return response()->json(['message'=>'not allowed']);
        }
        return Appointment::with('patient','doctor.user')->get();
    }

    public function report(){
        if(auth()->user()->role!='admin'){
            return response()->json(['message'=>'not allowed']);
        }
        $total=Appointment::count();
        $byStatus=Appointment::select('status',DB::raw('count(*) as count'))->groupby('status')->get();

        $busiest=Appointment::select('doctor_id',DB::raw('count(*) as count'))->groupby('doctor_id')->get();

        return response()->json([
            'total'=>$total,
            'by_status'=>$byStatus,
            'busiest_doctor'=>$busiest,
        ]);
    }

    public function user(){
        if(auth()->user()->role!='admin'){
            return response()->json(['message'=>'not allowed']);
        }

        return User::all();
    }

    public function updateUser(Request $request ,$id){
        if(auth()->user()->role!='admin'){
            return response()->json(['message'=>'not allowed']);
        }
        $request->validate([
            'name'=>'required',
            'email'=>'required|email'.$id,
            'role'=>'required|in:admin,doctor,patient',
        ]);
        $user=User::findOrFail($id);
        $user->update($request->only('name','email' ,'role'));
        return response()->json($user);
    }

    public function deleteUser($id){
        if(auth()->user()->role!='admin'){
            return response()->json(['message'=>'not allowed']);
        }
        User::findOrFail($id)->delete();
        return response()->json(['message'=>'user deleted']);
    }

    public function updateDoctor(Request $request,$id){
        if(auth()->user()->role!='admin'){
            return response()->json(['message'=>'not allowed']);
        }
        $request->validate([
            'expertise'=>'required',
            'medical_code'=>'required',
        ]);

        $doctor=Doctor::findOrFail($id);
        $doctor->update($request->only('expertise','medical_code'));

        return response()->json($doctor->load('user'));
    }
}
