<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Services::whereNotIn('status', [-1])->get();
        return response()->json([
            'data'=> $services,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'data'=> Services::where('id', $id)->first(),
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
            $insert = Services::insert([
                "name" => ucwords($request->input("name")),
                "description" => ucwords($request->input("description")),
                "parent_dept" => $request->input("parent_dept") ?? null,
                "status" => $request->input("status") ?? 1
            ]);
            return response()->json([
                'data' => $insert,
                'msg' => 'success',
                'status' => 201
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
        //
    }
    public function destroy(Request$request, string $id)
    {
        try{
            $delete = Services::where('id',$id)->update([
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
