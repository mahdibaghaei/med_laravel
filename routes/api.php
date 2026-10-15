<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\DoctorController;
use App\Models\Availability;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::post('/doctors', [DoctorController::class, 'store']);
    Route::get('/availabilities', [AvailabilityController::class, 'index']);
    Route::post('/availabilities', [AvailabilityController::class, 'store']);
    Route::get('/doctors/{id}/slots',[DoctorController::class,'slots']);
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
    Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
    Route::patch('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
    Route::get('/notifications', function(){
        return auth()->user()->notifications;
    });
    Route::get('/admin/appointments',[AdminController::class,'appointments']);
    Route::get('/admin/report',[AdminController::class,'report']);
    Route::get('/admin/users',[AdminController::class,'users']);
    Route::put('/admin/users/{id}',[AdminController::class,'updateUser']);
    Route::delete('/admin/users/{id}',[AdminController::class,'deleteUser']);
    Route::put('/admin/doctors/{id}',[AdminController::class,'updateDoctor']);


});