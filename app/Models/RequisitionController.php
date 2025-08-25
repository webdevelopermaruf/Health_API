<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class RequisitionController extends Model
{
    public function index()
    {
        $requisitions = Requisition::with(['case','bill.patient', 'requisite_by'])->whereNotIn('status', [-1])->get();

        return response()->json([
            'data'   => $requisitions,
            'msg'    => 'success',
            'status' => 200,
        ]);
    }

    public function store(Request $request){
        try {
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            $request->validate([
                'cases_id'          => 'required|integer',
                'medicines'          => 'required|string',
            ]);

            $insert = Requisition::insert([
                'cases_id'        => $request->cases_id,
                'medicines'        => $request->medicines,
                'pharmacy_billing_id' => null,
                'requisite_by'        => $user->id,
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

    public function update(Request $request, string $id){
        try {
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            $request->validate([
                'cases_id'          => 'required|integer',
                'medicines'          => 'required',
            ]);

            $insert = Requisition::where('id', $id)->update([
                'cases_id'        => $request->cases_id,
                'medicines'        => $request->medicines,
                'pharmacy_billing_id' => null,
                'requisite_by'        => $user->id,
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
}
