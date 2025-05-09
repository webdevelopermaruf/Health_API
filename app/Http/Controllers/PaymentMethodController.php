<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data'=> PaymentMethods::whereNotIn('status', [-1])->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'name' => 'required',
                'type' => 'required',
                'status' => 'numeric',
            ]);

            $insert = PaymentMethods::insert([
                'name' => ucwords(trim($request->input('name'))),
                'type' => $request->input('type'),
                'provider' => $request->input('provider') ?: null,
                'details' => $request->input('details') ?: null,
                'status' => $request->input('status') ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if($insert){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 201]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }
        }
        catch(ValidationException  $e){
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
                'name' => 'required',
                'type' => 'required',
                'status' => 'numeric',
            ]);

            $update = PaymentMethods::where('id',$id)->update([
                'name' => ucwords(trim($request->input('name'))),
                'type' => $request->input('type'),
                'provider' => $request->input('provider') ?: null,
                'details' => $request->input('details') ?: null,
                'status' => $request->input('status') ?? 1,
                'updated_at' => now(),
            ]);

            if($update){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }
        }
        catch(ValidationException  $e){
            return response()->json(['data'=> $e->errors(), 'msg' => 'error' , 'status'=> 422]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try{
            $delete = PaymentMethods::where('id',$id)->update([
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
