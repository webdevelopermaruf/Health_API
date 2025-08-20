<?php

namespace App\Http\Controllers;

use App\Models\PharmacySupplier;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PharmacySuppliersController extends Controller
{

    public function index()
    {
        $suppliers = PharmacySupplier::whereNotIn('status', [-1])->get();

        return response()->json([
            'data'=> $suppliers,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function show(string $id){

        return response()->json([
            'data'=> PharmacySupplier::findOrFail($id),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string',
                'company_name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'nullable|string',
                'status' => 'required',
            ]);

            $insert = PharmacySupplier::insert([
                'name' => ucwords($request->name),
                'company_name' =>ucwords($request->company_name),
                'email' => strtolower($request->email),
                'phone' => $request->phone,
                'address' => ucfirst($request->address),
                'status' => $request->status ?? 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            if($insert){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 201]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $request->validate([
                'name' => 'required|string',
                'company_name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'address' => 'nullable|string',
                'status' => 'required',
            ]);


            $update = PharmacySupplier::where('id', $id)->update([
                'name' => ucwords($request->name),
                'company_name' =>ucwords($request->company_name),
                'email' => strtolower($request->email),
                'phone' => $request->phone,
                'address' => ucfirst($request->address),
                'status' => $request->status ?? 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            if($update){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        try{
            $delete = PharmacySupplier::where('id',$id)->update([
                'status' => -1,
                'updated_at' => now(),
            ]);

            if($delete){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
}
