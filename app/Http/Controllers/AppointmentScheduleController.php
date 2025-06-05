<?php

namespace App\Http\Controllers;

use App\Models\AppointmentSchedule;
use App\Models\Doctors;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Doctors::with(['user','department','schedules.room'])->get();
        return response()->json([
            'data'=> $data,
            'msg' => 'success',
            'status'=> 200
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try{
            if($request->input('schedule_id')){
                AppointmentSchedule::where('id',$request->input('schedule_id') )->update([
                    "doctors_id" => $request->input('doctors_id'),
                    "rooms_id" => $request->input('room'),
                    "fee" => $request->input('fee'),
                    "schedule" => json_encode($request->input('schedule')),
                    "appointment_date" => $request->input('appointment_date') ?? null,
                    "updated_at" => now(),
                ]);
            }else{
                AppointmentSchedule::insert([
                    "doctors_id" => $request->input('doctors_id'),
                    "rooms_id" => $request->input('room'),
                    "fee" => $request->input('fee'),
                    "schedule" => json_encode($request->input('schedule')),
                    "appointment_date" => $request->input('appointment_date') ?? null,
                    "created_at" => now(),
                    "updated_at" => now(),
                ]);
            }
            return response()->json([
                'data'=> $request->all(),
                'msg' => 'success',
                'status'=> 200
            ]);
        }catch (ValidationException $e){
            return response()->json(['data'=> $e->errors(), 'msg' => 'error' , 'status'=> 422]);
        }
    }

}
