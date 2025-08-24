<?php

namespace App\Http\Controllers;

use App\Models\BedAllocation;
use App\Models\Beds;
use App\Models\Cases;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class IndoorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function cases()
    {
        $cases = Cases::with(['patient', 'department', 'prepared', 'doctor.user', 'referred', 'bed.room'])->where('status', 1)->get();
        return response()->json([
            'data'=> $cases,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'local_guardian' => 'required',
                'doctorDept' => 'required',
                'doctor' => 'required',
                'bed' => 'required',
                'admissionTimeStamp' => 'required',
            ]);
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            // patient registration
            $patient = Patient::where('phone', $request->input('phone'))->orWhere('nid', $request->input('nid'))->first();
            if(!$patient){
                $patientId = Patient::insertGetId([
                    'name'     => $request->input('name'),
                    'nid'      => $request->input('nid'),
                    'phone'    => $request->input('phone'),
                    'dob'      => $request->input('dob'),
                    'blood'    => $request->input('blood'),
                    'gender'   => $request->input('gender'),
                    'address'  => $request->input('address'),
                    'local_guardian'  => json_encode($request->input('local_guardian')),
                    'status'   => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }else{
                $patientId = $patient->id;
                Patient::where('id', $patient->id)->update([
                    'name'     => $request->input('name'),
                    'nid'      => $request->input('nid'),
                    'phone'    => $request->input('phone'),
                    'dob'      => $request->input('dob'),
                    'blood'    => $request->input('blood'),
                    'gender'   => $request->input('gender'),
                    'address'  => $request->input('address'),
                    'local_guardian'  => json_encode($request->input('local_guardian')),
                    'status'   => 1,
                    'updated_at' => now(),
                ]);
            }
            // generate a case id with it
            $case_id = Cases::insertGetId([
                'patient_id' => $patientId,
                'doctors_id'  => $request->input('doctor'),
                'departments_id'  => $request->input('doctorDept'),
                'referred_by'  => null,
                'prepared_by' => $user->id,
                'status' => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            // add a bed allocation table
            BedAllocation::insert([
                'cases_id'    => $case_id,
                'current_bed' => $request->input('bed'),
                'discharged_from_bed'=> null,
                'entered_at'=> $request->input('admissionTimeStamp'),
                'exited_at'=> null,
                'allocated_by'=> $user->id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            Beds::where('id', $request->input('bed'))->update([
                'is_booked'=> 1
            ]);
            return response()->json(['data'=> ['case'=> $case_id], 'msg' => 'success' , 'status'=> 201]);
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
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
}
