<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\Billing;
use App\Models\BillingTransactions;
use App\Models\GeneralSettings;
use App\Models\LabReport;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class BillingController extends Controller
{
    public function index()
    {
        return response()->json([
            'data'=> Billing::with(['patient' ,'appointment.doctor.user.department'])
                ->orderBy('created_at', 'desc')
                ->whereNotIn('status', [-1])->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }
    public function show($billId)
    {
        $settings = GeneralSettings::first();
        $data = Billing::with(['patient' ,'appointment.doctor.user.department'])->where('id', $billId)->first();
        if($data){
            return view('print.billing', ['data'=>$data, 'settings'=> $settings]);
        }
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
                'services' => 'required_without:isAppointment',
                'isAppointment' => 'required_without:services',
                'doctor' => 'nullable',
                'appointment_date' => 'nullable|date',
                'slot' => 'nullable',
                'fee' => 'required',
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

           $services = collect($request->input('services'))->map(function ($service) {
               $service['service'] = $service['service']['name'] ?? null;
               return $service;
           });

            // billing
            $billing_id = Billing::insertGetId([
                'patient_id' => $patientId,
                'doctors_id' => $request->input('doctor'),
                'services' => json_encode($services),
                'appointment_fee' => $request->input('fee'),
                'services_fee' => $request->input('payable') - $request->input('fee'),
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
               'billing_type' => 1, // 1 = hospital billing counter, 2 = pharmacy counter
               'payment_methods_id' => $request->input('payment_method') ?? 1,
               'user_id' => $user->id, // received by
               'created_at' => now(),
               'updated_at' => now(),
           ]);
           // lab reports
           $lab = [];
           foreach ($services as $service) {
               if($service['type'] === 3){
                   $lab[] = [
                       'billing_id' => $billing_id,
                       'patient_id' => $patientId,
                       'services_id' => $service['id'],
                       'status' => 0,
                       'user_id' => $user->id,
                       'created_at' => now(),
                       'updated_at' => now(),
                   ];
               }
           }
           if(count($lab) > 0){
            LabReport::insert($lab);
           }
           $appointment = null;
           if($request->input('isAppointment')){
               if(!$request->input('appointment_id')){
                   $next_serial = Appointments::whereDate('appointment_date', $request->input('appointment_date'))
                       ->where('doctor_id', $request->input('doctor'))->latest()->value('serial_number') + 1 ?? 1000;
                   $appointment = Appointments::insertGetId([
                       'patient_id' => $patientId,
                       'doctor_id' => $request->input('doctor'),
                       'billing_id' => $billing_id,
                       'appointment_date' => $request->input('appointment_date'),
                       'slot' => $request->input('slot'),
                       'serial_number' => $next_serial,
                       'room' => $request->input('appointment_room'),
                       'reason' => $request->input('reason'),
                       'status' => $request->input('status') ?? 1,
                       'updated_at' => now(),
                       'created_at' => now()
                   ]);
               }else{
                   $appointment = $request->input('appointment_id');
                   Appointments::where('id', $appointment)->update(['status' => 1]);
               }
               $updateBilling = Billing::where('id', $billing_id)->update([
                   'appointments_id' => $appointment,
               ]);
           }
           return response()->json(['data' => ['billing'=> $billing_id, 'appointment' => $appointment], 'msg' => 'success', 'status' => 200]);
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
    public function pending_appointments(){
        return response()->json([
            'data'=> Appointments::with(['patient', 'doctor.user.department', 'doctor.schedules'])->where('status', 0)->latest()->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }
    public function report($invoice, $mobile_number)
    {
        $checkBilling = Billing::with('patient')
            ->where('id', $invoice)->first();
        return $checkBilling;
    }

}
