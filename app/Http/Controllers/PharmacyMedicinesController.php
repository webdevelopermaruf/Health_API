<?php

namespace App\Http\Controllers;

use App\Models\PharmacyMedicines;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PharmacyMedicinesController extends Controller
{
    /**
     * Display a listing of medicines.
     */
    public function index()
    {
        $medicines = PharmacyMedicines::with('supplier')->whereNotIn('status', [-1])->get();

        return response()->json([
            'data'   => $medicines,
            'msg'    => 'success',
            'status' => 200,
        ]);
    }

    /**
     * Display the specified medicine.
     */
    public function show(string $id)
    {
        return response()->json([
            'data'   => PharmacyMedicines::with('supplier')->findOrFail($id),
            'msg'    => 'success',
            'status' => 200,
        ]);
    }

    /**
     * Store a newly created medicine in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code'          => 'required|string|unique:pharmacy_medicines,code',
                'name'          => 'required|string',
                'generic_name'  => 'required|string',
                'pharmacy_supplier_id'  => 'required|integer',
                'shelf'         => 'nullable|string',
                'company'       => 'nullable|string',
                'factory_price' => 'required|numeric|min:0',
                'sales_price'   => 'required|numeric|min:0',
                'qty'           => 'required|integer|min:0',
                'expiry_date'   => 'nullable|date',
                'status'        => 'required|integer', // add a status column in your table
            ]);

            $insert = PharmacyMedicines::insert([
                'code'          => strtolower($request->code),
                'name'          => ucwords($request->name),
                'generic_name'  => ucwords($request->generic_name),
                'shelf'         => $request->shelf,
                'pharmacy_supplier_id'=> $request->pharmacy_supplier_id,
                'company'       => $request->company,
                'factory_price' => $request->factory_price,
                'sales_price'   => $request->sales_price,
                'qty'           => $request->qty,
                'expiry_date'   => $request->expiry_date,
                'status'        => $request->status ?? 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            if ($insert) {
                return response()->json(['data' => $request->all(), 'msg' => 'success', 'status' => 201]);
            } else {
                return response()->json(['data' => $request->all(), 'msg' => 'failed', 'status' => 400]);
            }

        } catch (ValidationException $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    /**
     * Update the specified medicine.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'code'          => "required|string|unique:pharmacy_medicines,code,$id",
                'name'          => 'required|string',
                'generic_name'  => 'required|string',
                'shelf'         => 'nullable|string',
                'pharmacy_supplier_id'  => 'required|integer',
                'company'       => 'nullable|string',
                'factory_price' => 'required|numeric|min:0',
                'sales_price'   => 'required|numeric|min:0',
                'qty'           => 'required|integer|min:0',
                'expiry_date'   => 'nullable|date',
                'status'        => 'required|integer',
            ]);

            $update = PharmacyMedicines::where('id', $id)->update([
                'code'          => strtolower($request->code),
                'name'          => ucwords($request->name),
                'generic_name'  => ucwords($request->generic_name),
                'shelf'         => $request->shelf,
                'pharmacy_supplier_id'=> $request->pharmacy_supplier_id,
                'company'       => $request->company,
                'factory_price' => $request->factory_price,
                'sales_price'   => $request->sales_price,
                'qty'           => $request->qty,
                'expiry_date'   => $request->expiry_date,
                'status'        => $request->status ?? 1,
                'updated_at'    => now(),
            ]);

            if ($update) {
                return response()->json(['data' => $request->all(), 'msg' => 'success', 'status' => 200]);
            } else {
                return response()->json(['data' => $request->all(), 'msg' => 'failed', 'status' => 400]);
            }

        } catch (ValidationException $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    /**
     * Soft delete medicine (status = -1).
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $delete = PharmacyMedicines::where('id', $id)->update([
                'status'     => -1,
                'updated_at' => now(),
            ]);

            if ($delete) {
                return response()->json(['data' => $request->all(), 'msg' => 'success', 'status' => 200]);
            } else {
                return response()->json(['data' => $request->all(), 'msg' => 'failed', 'status' => 400]);
            }

        } catch (ValidationException $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
}
