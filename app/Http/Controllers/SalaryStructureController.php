<?php

namespace App\Http\Controllers;

use App\Models\SalaryStructure;
use Illuminate\Http\Request;
use Nette\Schema\ValidationException;

class SalaryStructureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data'=> SalaryStructure::whereNotIn('status', [-1])->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }



    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'designation' => 'required',
                'basic_salary' => 'required|numeric',
                'salary_type' => 'required|numeric',
                'overtime_rate' => 'required|numeric',
                'overtime_type' => 'required|numeric',
                'status' => 'required|numeric',
            ]);

            $insert = SalaryStructure::insert([
                'designation' => $request->input('designation'),
                'basic_salary' => $request->input('basic_salary'),
                'salary_type' => $request->input('salary_type'),
                'overtime_rate' => $request->input('overtime_rate'),
                'overtime_type' => $request->input('overtime_type'),
                'paid_leave' => $request->input('paid_leave') ?? 0,
                'bonus' => $request->input('bonus') ?? 0,
                'emp_count' => 0,
                'allowances' => json_encode($request->input('allowances')),
                'deductions' => json_encode($request->input('deductions')),
                'status' => $request->input('status') ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if($insert){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 201]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        }catch(ValidationException $e){
            return response()->json(['data'=> $e->errors(), 'msg' => 'error' , 'status'=> 422]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $validatedData = $request->validate([
                'designation' => 'required',
                'basic_salary' => 'required|numeric',
                'salary_type' => 'required|numeric',
                'overtime_rate' => 'required|numeric',
                'overtime_type' => 'required|numeric',
                'status' => 'required|numeric',
            ]);

            $update = SalaryStructure::where('id', $id)->update([
                'designation' => $request->input('designation'),
                'basic_salary' => $request->input('basic_salary'),
                'salary_type' => $request->input('salary_type'),
                'overtime_rate' => $request->input('overtime_rate'),
                'overtime_type' => $request->input('overtime_type'),
                'paid_leave' => $request->input('paid_leave')??0,
                'bonus' => $request->input('bonus')??0,
                'emp_count' => 0,
                'allowances' => json_encode($request->input('allowances')),
                'deductions' => json_encode($request->input('deductions')),
                'status' => $request->input('status') ?? 1,
                'updated_at' => now(),
            ]);

            if($update){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        }catch(ValidationException $e){
            return response()->json(['data'=> $e->errors(), 'msg' => 'error' , 'status'=> 422]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try{
            $delete = SalaryStructure::where('id',$id)->update([
                'status' => -1,
                'updated_at' => now(),
            ]);

            if($delete){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }
        }
        catch(ValidationException  $e){
            return response()->json(['data'=> $e->errors(), 'msg' => 'error' , 'status'=> 422]);
        }
    }
}
