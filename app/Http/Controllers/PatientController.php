<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::whereNotIn('status', [-1])
            ->get();

        return response()->json([
            'data'=> $patients,
            'msg' => 'success',
            'status'=> 200
        ]);
    }
    public function show(string $id)
    {
        return response()->json([
            'data'=> Patient::where('id', $id)->first(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function searchByPhone(string $phone)
    {
        return response()->json([
            'data'=> Patient::where('phone', $phone)->first(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }
}
