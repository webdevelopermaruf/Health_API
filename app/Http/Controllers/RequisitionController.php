<?php

namespace App\Http\Controllers;

use App\Events\RequisitionEvent;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class RequisitionController extends Controller
{
    public function index()
    {
        $requisitions = Requisition::with(['case.patient', 'case.bed.room', 'case.department','billing.patient', 'requisite_by'])
            ->orderBy('created_at', 'desc')
            ->whereNotIn('status', [-1])->get();
        return response()->json([
            'data'   => $requisitions,
            'msg'    => 'success',
            'status' => 200,
        ]);
    }

    public function show(string $id)
    {
        $requisition = Requisition::with(['case','bill.patient', 'requisite_by'])->where('id', $id)->whereNotIn('status', [-1])->get();
        return response()->json([
            'data'   => $requisition,
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
                'medicines'          => 'required',
            ]);

            $insert = Requisition::insert([
                'cases_id'        => $request->cases_id,
                'medicines'        => json_encode($request->medicines),
                'pharmacy_billing_id' => null,
                'requisite_by'        => $user->id,
                'status'        => $request->status ?? 0, // 0 = sent request; 1 = ready for pickup; 2 = done
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
            event(new RequisitionEvent(['msg'=> "WS Connected"]));
            if ($insert) {
                return response()->json(['data' => $request->all(), 'msg' => 'success', 'status' => 201]);
            } else {
                return response()->json(['data' => $request->all(), 'msg' => 'failed', 'status' => 400]);
            }

        } catch (ValidationException $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function update(Request $request, string $id)
    {
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
                'status'        => $request->status ?? 0,
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

    public function updateStatus(Request $request){
        try {
            $validation = $request->validate([
                'requisitionId' => 'required|integer',
                'status' => 'required|integer',
            ]);
            $update = Requisition::where('id', $request->input('requisitionId'))->update([
                'status' => $request->input('status'),
            ]);
            return response()->json(['data' => $request->all(), 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
}
