<?php

namespace App\Http\Controllers;

use App\Models\GeneralSettings;
use App\Models\Patient;
use App\Models\PharmacyBilling;
use App\Models\BillingTransactions;
use App\Models\PharmacyMedicines;
use App\Models\PharmacyPurchases;
use App\Models\Requisition;
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
        return response()->json([
            'data'=> PharmacyBilling::with(['patient'])
                ->orderBy('created_at', 'desc')
                ->whereNotIn('status', [-1])->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }
    public function getPurchases()
    {
        return response()->json([
            'data'=> PharmacyPurchases::with(['supplier'])
                ->orderBy('created_at', 'desc')
                ->whereNotIn('status', [-1])->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function getMedicinePurchases(){
        return response()->json([
            'data'=> PharmacyMedicines::with('purchases')->first(),
            'msg' => 'success',
            'status'=> 200
        ]);
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

            // decrement stocks
            foreach ($request->input('medicines') as $medicine) {
                $med = PharmacyMedicines::find($medicine['id']);
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

    public function requisite(Request $request)
    {
        $token = explode(' ', $request->header('Authorization'))[1];
        $accessToken = PersonalAccessToken::findToken($token);
        $user = $accessToken->tokenable;

        // decrement stocks
        foreach ($request->input('medicines') as $medicine) {
            $med = PharmacyMedicines::find($medicine['id']);
            $med->decrement('qty', $medicine['qty']);
        }
        PharmacyMedicines::updateMedicines();

        // billing
        $billing_id = PharmacyBilling::insertGetId([
            'patient_id' => $request->input('patientId'),
            'cases_id' => $request->input('cases_id'),
            'medicines' => json_encode($request->input('medicines')),
            'discount_type' => $request->input('discount')['type'] ?? null,
            'discount' => $request->input('discount')['amount'] ?? 0,
            'VAT' => $request->input('VAT'),
            'payable' => $request->input('payable'),
            'received' => $request->input('received'),
            'changes' => $request->input('changes'),
            'user_id' => $user->id,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $updateRequisite = Requisition::where('id', $request->input('requisitionId'))->update([
            'pharmacy_billing_id' => $billing_id,
            'status' => $request->input('status'),
        ]);
        return response()->json(['data' => ['billing'=> $billing_id], 'msg' => 'success', 'status' => 200]);
    }

    public function showPharmacy($billId)
    {
        $settings = GeneralSettings::first();
        $data = PharmacyBilling::with(['patient'])->where('id', $billId)->first();
        if($data){
            return view('print.pharmacy_billing', ['data'=>$data, 'settings'=> $settings]);
        }
    }

    public function purchase(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'pharmacy_supplier_id'=> 'required',
                'medicines'=> 'required',
                'payable'=> 'required|numeric',
                'paid'=> 'required|numeric',
            ]);
            $total_qty  = 0;
            foreach ($request->input('medicines') as $medicine) {
                $total_qty += $medicine['qty'];
            }

            $purchase = PharmacyPurchases::create([
                'pharmacy_supplier_id' => $request->input('pharmacy_supplier_id'),
                'medicines' => json_encode($request->input('medicines')),
                'total_qty' => $total_qty,
                'payable' => $request->input('payable'),
                'paid' => $request->input('paid'),
                'status' => $request->input('payable') > $request->input('paid') ? 0 : 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Prepare medicines for attachment to the pivot table
            $medicineData = [];
            foreach ($request->input('medicines') as $medicine) {
                $medicineData[$medicine['id']] = [
                    'qty' => $medicine['qty'],
                    'prevStock' => $medicine['prevStock'],
                    'price' => $medicine['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Update PharmacyMedicines stock and pricing
                $med = PharmacyMedicines::find($medicine['id']);
                $newQty = $medicine['qty'];
                $newPrice = $medicine['price']; // New factory price
                $newSales = $medicine['sales']; // New sales price
                $prevQty = $med->qty; // Current quantity (before increment)
                $prevFactoryPrice = $med->factory_price;
                $prevSalesPrice = $med->sales_price;

                // Calculate average factory price
                if ($prevQty + $newQty > 0) {
                    $avgFactoryPrice = (($prevQty * $prevFactoryPrice) + ($newQty * $newPrice)) / ($prevQty + $newQty);
                } else {
                    $avgFactoryPrice = $newPrice; // Fallback if total quantity is 0
                }

                // Calculate average sales price
                if ($prevQty + $newQty > 0) {
                    $avgSalesPrice = (($prevQty * $prevSalesPrice) + ($newQty * $newSales)) / ($prevQty + $newQty);
                } else {
                    $avgSalesPrice = $newSales; // Fallback if total quantity is 0
                }

                // Update medicine details
                $med->factory_price = $avgFactoryPrice;
                $med->sales_price = $avgSalesPrice;
                $med->pharmacy_supplier_id = $request->input('pharmacy_supplier_id');
                $med->increment('qty', $newQty);
                $med->save();
            }
            $purchase->medicines()->attach($medicineData);

            PharmacyMedicines::updateMedicines();
            return response()->json(['data' => $request->all(), 'msg' => 'success', 'status' => 201]);
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function duePayPurchase(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate([
                'paying'=> 'required|numeric',
                'status'=> 'required|integer',
            ]);
            $data = PharmacyPurchases::findOrFail($id);
            $data->increment('paid', $validatedData['paying']);
            $data->status =  $validatedData['status'];
            $data->updated_at = now();
            $data->save();

            return response()->json(['data' => $request->all(), 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException  $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }

    }
}
