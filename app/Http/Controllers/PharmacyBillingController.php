<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Billing;
use App\Models\GeneralSettings;
use App\Models\LabReport;
use App\Models\Patient;
use App\Models\PharmacyBilling;
use App\Models\BillingTransactions;
use App\Models\PharmacyMedicines;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class PharmacyBillingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function store(Request $request){
        try{
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;
            $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'medicines'=> 'required',
                'discount' => 'nullable',
                'VAT' => 'required|numeric',
                'payable' => 'required|numeric',
                'received' => 'required|numeric',
                'changes' => 'required|numeric',
            ]);

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
                    'status'   => 1,
                    'updated_at' => now(),
                ]);
            }

            // check stocks
            foreach ($request->input('medicines') as $medicine) {
                $med = PharmacyMedicines::find($medicine['id']);
                // Check stock first
                $med->decrement('qty', $medicine['qty']);
            }


            // billing
            $billing_id = PharmacyBilling::insertGetId([
                'patient_id' => $patientId, // outdoor patient
                'cases_id' => null, // indoor patient
                'medicines' => json_encode($request->input('medicines')),
                'discount_type' => $request->input('discount')['type'] ?? null,
                'discount' => $request->input('discount')['amount'] ?? 0,
                'VAT' => $request->input('VAT'),
                'payable' => $request->input('payable'),
                'received' => $request->input('received'),
                'changes' => $request->input('changes'),
                'user_id' => $user->id,
                'status' => $request->input('status') ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            BillingTransactions::insert([
                'patient_id' => $patientId,
                'cases_id' => null,
                'trx_type' => 1, // 0 = refund 1 = payment
                'amount' => $request->input('received'),
                'billing_id' => $billing_id,
                'billing_type' => 2, // 1 = hospital billing counter, 2 = pharmacy counter
                'payment_methods_id' => $request->input('payment_method') ?? 1,
                'user_id' => $user->id, // received by
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['data' => ['billing'=> $billing_id], 'msg' => 'success', 'status' => 200]);
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function showPharmacy($billId)
    {
        $settings = GeneralSettings::first();
        $data = PharmacyBilling::with(['patient'])->where('id', $billId)->first();
        if($data){
            return view('print.pharmacy_billing', ['data'=>$data, 'settings'=> $settings]);
        }
    }
}
