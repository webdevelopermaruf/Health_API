<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Departments::whereNotIn('status', [-1])
            ->with('parentDepartment')
            ->get();

        return response()->json([
            'data'=> $departments,
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
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:departments',
                'description' => 'nullable|string',
                'parent_dept' => 'nullable|exists:departments,id',
            ]);
            $insert = Departments::insert([
                "name" => $validator->input("name"),
                "description" => $validator->input("description"),
                "parent_dept" => $validator->input("parent_dept") ?? null,
                "status" => $validator->input("status") ?? 1
            ]);
            return response()->json([
                'data' => $insert,
                'msg' => 'success',
                'status' => 200
            ]);
        }catch(ValidationException  $e){
            return response()->json(['data'=> $e->errors(), 'msg' => 'error' , 'status'=> 422]);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:departments,name,' . $id,
                'description' => 'nullable|string',
                'parent_dept' => 'nullable|exists:departments,id',
            ]);
            $update = Departments::where('id', $id)->update([
                "name" => $validator->input("name"),
                "description" => $validator->input("description"),
                "parent_dept" => $validator->input("parent_dept"),
                "status" => $validator->input("status") ?? 1
            ]);
            return response()->json([
                'data' => $update,
                'msg' => 'success',
                'status' => 200
            ]);
        }catch(ValidationException  $e){
            return response()->json(['data'=> $e->errors(), 'msg' => 'error' , 'status'=> 422]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request$request, string $id)
    {
        try{
            $delete = Departments::where('id',$id)->update([
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
