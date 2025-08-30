<?php

namespace App\Http\Controllers;

use App\Models\BedAllocation;
use App\Models\Beds;
use App\Models\BillingTransactions;
use App\Models\CaseRecords;
use App\Models\Cases;
use App\Models\IndoorBillings;
use App\Models\LabReport;
use App\Models\Patient;
use App\Models\PharmacyBilling;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class IndoorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function cases()
    {
        $cases = Cases::with(['patient', 'department', 'prepared', 'doctor.user', 'requisitions', 'referred', 'bed.room', 'allocations'])->where('status', 1)->get();
        foreach ($cases as $case) {
            $case->current_allocation = collect($case->allocations)
                ->whereNull('exited_at')
                ->first()?->id;
        }
        return response()->json([
            'data'=> $cases,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function show(string $cases_id)
    {
        $case = Cases::with(['patient', 'department', 'prepared', 'doctor.user', 'requisitions', 'referred', 'bed.room',
            'allocations.beds.room', 'allocations.allocator', 'billing', 'lab_reports.prepared_by', 'lab_reports.service', 'case_record'])->where('id', $cases_id)->first();
        if ($case) {
            $case->current_allocation = collect($case->allocations)
                ->whereNull('exited_at')
                ->first()?->id;
            // Handle billing->services JSON
            if ($case->billing && !empty($case->billing->services)) {
                $services = json_decode($case->billing->services, true);
                if (is_array($services)) {
                    // Collect all user_ids to reduce queries
                    $userIds = collect($services)->pluck('user_id')->filter()->unique()->toArray();
                    $users = \App\Models\User::whereIn('id', $userIds)
                        ->get(['id', 'name', 'code'])
                        ->keyBy('id');
                    foreach ($services as &$service) {
                        if (isset($service['user_id']) && isset($users[$service['user_id']])) {
                            $service['user'] = $users[$service['user_id']]->name. ' - '. $users[$service['user_id']]->code;
                        }
                    }
                    // overwrite services with enriched array
                    $case->billing->services = $services;
                }
            }
        }
        return response()->json([
            'data'   => $case,
            'msg'    => 'success',
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'phone' => 'required',
                'dob' => 'required',
                'gender' => 'required',
                'local_guardian' => 'required',
                'doctorDept' => 'required',
                'doctor' => 'required',
                'bed' => 'required',
                'admissionTimeStamp' => 'required',
            ]);
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

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
                    'local_guardian'  => json_encode($request->input('local_guardian')),
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
                    'local_guardian'  => json_encode($request->input('local_guardian')),
                    'status'   => 1,
                    'updated_at' => now(),
                ]);
            }
            // generate a case id with it
            $case_id = Cases::insertGetId([
                'patient_id' => $patientId,
                'doctors_id'  => $request->input('doctor'),
                'departments_id'  => $request->input('doctorDept'),
                'referred_by'  => null,
                'prepared_by' => $user->id,
                'status' => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            // add a bed allocation table
            BedAllocation::insert([
                'cases_id'    => $case_id,
                'current_bed' => $request->input('bed'),
                'discharged_from_bed'=> null,
                'entered_at'=> $request->input('admissionTimeStamp'),
                'exited_at'=> null,
                'allocated_by'=> $user->id,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            Beds::where('id', $request->input('bed'))->update([
                'is_booked'=> 1
            ]);
            return response()->json(['data'=> ['case'=> $case_id], 'msg' => 'success' , 'status'=> 201]);
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function indoorBillingData(Request $request)
    {
        try {
            $data = $request->validate([
                'cases_id' =>  'nullable',
                'bed' =>  'nullable',
                'phone' =>  'nullable',
            ]);
            $case_id = [];
            if($data['cases_id']){
                $case_id = $request->input('cases_id');
                $get_data = Cases::with('patient')->where('id', $data['cases_id'])->first();
                $output['patient'] = $get_data['patient'];
                $output['cases'] = $get_data;
            }else if($data['bed']){
                 $get_data = Beds::with('bed.cases.patient')->where('id', $data['bed'])->first();
                 $case_id= $get_data['bed']['cases']['id'];
                 $output['patient'] = $get_data['bed']['cases']['patient'];
                $output['cases'] = $get_data['bed']['cases'];

            }else if($data['phone']){
                 $get_data = Patient::with('cases')->where('phone', $data['phone'])->first();
                 $case_id= $get_data['cases']['id'];
                 $output['patient'] = $get_data;
                 $output['cases'] = $get_data['cases'];
            }
            // fetch pharmacy bills
            $output['pharmacy'] = PharmacyBilling::where('cases_id', $case_id)->get();
            $output['transactions']= BillingTransactions::with('user')->where('cases_id', $case_id)->get();
            $output['bed_bills'] = BedAllocation::with('beds', 'allocator')->where('cases_id', $case_id)->get();
            $output['services'] = IndoorBillings::where('cases_id', $case_id)->first();

            return response()->json(['data' => $output, 'msg' => 'success', 'status'=> 200]);
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }catch (\Exception $e){
            return response()->json(['data' => "Not Found", 'msg' => 'error', 'status' => 404]);
        }
    }

    public function bed_transfer(Request $request, string $cases_id)
    {
        try {
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            $data = $request->validate([
                'from_bed' => 'required|numeric', // old bed id
                'bed' => 'required|numeric', // new bed id
                'allocation_id' => 'required|numeric', // from bed_allocation id
                'exited_at' => 'nullable', // time of changing.
            ]);
            $checkBed = Beds::where('id', $data['bed'])->first();
            if($checkBed->is_booked == 1){
                return response()->json(['data'=> "already booked by other", 'msg' => 'error', 'status' => 400]);
            }
            // from bed
            BedAllocation::where('id', $request->allocation_id)->update([
                'exited_at'=> $request->input('exited_at') ?? now(),
                'discharged_from_bed'=> 0, // 0 because bed is changing
                'updated_at' => now(),
            ]);
            BedAllocation::insert([
                'cases_id' => $cases_id,
                'current_bed'=> $request->input('bed'),
                'from_bed'=> $request->input('bed'),
                'discharged_from_bed'=> null,
                'entered_at'=> $request->input('exited_at') ?? now(),
                'exited_at'=> null,
                'allocated_by'=> $user->id,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);

            Beds::where('id',  $data['bed'])->update([
                'is_booked'=> 1
            ]);

            Beds::where('id',  $data['from_bed'])->update([
                'is_booked'=> 0
            ]);
            return response()->json(['data'=> "changed", 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
    public function mysqlDate($dateValue)
    {
        return Carbon::parse($dateValue)->format('Y-m-d H:i:s');
    }

    public function saveServiceBillingData(Request $request, string $cases_id)
    {
        try {
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            $services = $request->input('services');
            $sanitizeServices = [];
            foreach ($services as $service) {

                $storage = [];
                if(gettype($service['service']) == 'string') {
                    $storage['service'] = $service['service'];
                    $storage['price'] = $service['price'];
                }else{

                    $storage['id'] = $service['service']['id'];
                    $storage['service'] = $service['service']['name'];
                    $storage['price'] = $service['service']['amount'];
                }
                if($service['type']===3){
                    $storage['lab'] = true;
                }
                $storage['type'] = $service['type'];
                $storage['user'] = $service['user'];
                $storage['qty'] = $service['qty'];
                $storage['total'] = $service['total'];
                $storage['date'] = $service['date'];
                $storage['status'] = true; // this mean next time non-editable.
                $sanitizeServices[] = $storage;
            }
            $sanitizeServices = json_encode($sanitizeServices);
            $billing = IndoorBillings::updateOrInsert(
            ['cases_id' => $cases_id, 'patient_id' => $request->input('patient_id')], fn ($exists) => $exists ? [
                'services'=> $sanitizeServices,
                'payable' => 0,
                'received_by' => $user->id,
                'updated_at'=> now(),
            ]:
            [
                'services'=> $sanitizeServices,
                'payable' => 0,
                'received' => 0,
                'received_by' => $user->id,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);

            $billingId = IndoorBillings::where('cases_id', $cases_id)->where('patient_id', $request->input('patient_id'))->first()->id;
            foreach ($services as $service) {
               if ($service['type'] === 3 && !isset($service['lab'])) {
                   LabReport::insert([
                       'services_id' => $service['service']['id'],
                       'cases_id'    => $cases_id,
                       'patient_id'  => $request->input('patient_id'),
                       'billing_id'  => $billingId,
                       'billing_type'  => 1,
                       'user_id'     => null,
                       'status'      => 0,
                       'created_at'  => now(),
                       'updated_at'  => now(),
                   ]);
               }
            }


            return response()->json(['data'=> "saved", 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function generate_bill(Request $request, string $cases_id)
    {
        try{
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            $pharmacy_bill = PharmacyBilling::where('cases_id', $cases_id)->sum('payable');
            $services_list = IndoorBillings::where('cases_id', $cases_id)->get();
            $services_bill = 0;
            foreach ($services_list as $billing) {
                $services = json_decode($billing->services, true);
                foreach ($services as $service) {
                    $services_bill += floatval($service['total']); // sum the total
                }
            }
            $beds_list = BedAllocation::with('beds')->where('cases_id', $cases_id)->get();
            $bed_bill = 0;
            foreach ($beds_list as $allocation) {
                $bed = $allocation->beds;
                if (!$bed) continue; // skip if no bed assigned
                $entered_at = Carbon::parse($allocation->entered_at);
                $exited_at = $allocation->exited_at ? Carbon::parse($allocation->exited_at) : Carbon::now();
                $hours_spent = intval($entered_at->diffInHours($exited_at));
                $days_spent = intval($entered_at->diffInDays($exited_at));
                $price = floatval($bed->price);

                if ($bed->timeline == 1) {
                    // Hourly price
                    $bed_bill += $hours_spent * $price;
                } elseif ($bed->timeline == 2) {
                    // Daily price
                    $bed_bill += ($days_spent == 0 ? 1 : $days_spent) * $price;
                    $remaining_hours = $hours_spent - ($days_spent * 24);
                    if ($remaining_hours > 0) {
                        $bed_bill += $price;
                    }
                }
            }

            IndoorBillings::where('cases_id', $cases_id)->update([
                'total' => $services_bill,
                'pharmacy_bill' => $pharmacy_bill,
                'bed_bill' => $bed_bill,
                'payable' => ($bed_bill+$services_bill+$pharmacy_bill),
                'received_by'=>  $user->id,
                'updated_at'=> now(),
            ]);

            return response()->json(['data'=> "generated", 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
    public function receive_bill(Request $request, string $cases_id)
    {
        try{
            $validatedData = $request->validate([
                'amount' => 'required|numeric',
            ]);
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            $billing = IndoorBillings::where('cases_id', $cases_id)->first();
            $billing->increment('received', $request->input('amount'));
            $billing->updated_at = now();
            $billing->received_by = $user->id;
            $billing->save();

            BillingTransactions::insert([
                'patient_id'=> $billing->patient_id,
                'cases_id'=> $cases_id,
                'trx_type'=> 1, // payment
                'amount'=> $request->input('amount'),
                'billing_type'=> 1, // hospital counter
                'billing_id'=> $billing->id,
                'user_id'=> $user->id,
                'status'=> 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);

            return response()->json(['data'=> "received", 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
    public function getIndoorBilling(Request $request) {
        $billings=  IndoorBillings::with(['cases','patient', 'user'])->where('status', 0)->get(); // pending bills
        foreach ($billings as $billing) {
            $services_bill = 0;
            $services = json_decode($billing->services, true);
            foreach ($services as $service) {
                $services_bill += floatval($service['total']); // sum the total
            }
            $billing->total_service_bill = number_format($services_bill, 2,'.','');
        }
        return response()->json(['data' => $billings, 'msg' => 'success', 'status' => 200]);
    }
    public function approvedDiscount(Request $request, string $cases_id) {
        try {
            $validatedData = $request->validate([
                'amount' => 'required|numeric',
                'type' => 'required|numeric',
            ]);
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            if($user->roles_id === 1 || $user->roles_id === 2){
                $check = IndoorBillings::where('status', 0)->where('cases_id', $cases_id)->first();
                if($check){
                    $update = IndoorBillings::where('cases_id', $cases_id)->update([
                        'discount' =>  $request->input('amount'),
                        'discounted_by' => $user->id,
                        'discount_type' => $request->input('type'),
                    ]);
                    return response()->json(['data'=> "approved", 'msg' => 'success', 'status' => 200]);
                }else{
                    return response()->json(['data'=> "wrong", 'msg' => 'error', 'status' => 400]);
                }

            }else{
                return response()->json(['data'=> "wrong", 'msg' => 'error', 'status' => 400]);
            }
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function saveCaseRecords(Request $request, string $cases_id) {
        try{
            $validatedData = $request->validate([
                'dischargeDate' => 'required|date',
                'dischargeType' => 'required|string',
                'diagnosis' => 'required|string',
                'allergyHistory' => 'required|string',
                'complaints' => 'required|string',
                'pastHistory' => 'required|string',
                'findings' => 'required|string',
                'investigation' => 'required|string',
                'hospitalCourse' => 'required|string',
                'medicationsDuringStay' => 'required|string',
                'diet' => 'required|string',
                'dischargeMedications' => 'required|string',
                'advice' => 'required|string',
                'followUp' => 'required|string',
                'urgentCareInstructions' => 'required|string',
                'particulars' => 'nullable|string',
                'seniorHouseOfficer' => 'required|string',
                'specialist' => 'required|string',
                'consultant' => 'required|string',
            ]);
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            $case_record = [
                ...$validatedData,
                "user_id"=> $user->id,
                "cases_id"=> $cases_id,
                "updated_at"=> now(),
                "created_at"=> now(),
            ];
            CaseRecords::updateOrInsert(['cases_id' => $cases_id], $case_record);
            return response()->json(['data' => $case_record, 'msg' => 'success', 'status' => 200]);

        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
    public function updateAdmissionReason(Request $request, string $cases_id) {
        try{
            $validatedData = $request->validate([
                'dischargeDate' => 'required|date',
                'dischargeType' => 'required|string',
                'diagnosis' => 'required|string',
                'allergyHistory' => 'required|string',
                'complaints' => 'required|string',
                'pastHistory' => 'required|string',
                'findings' => 'required|string',
                'investigation' => 'required|string',
                'hospitalCourse' => 'required|string',
                'medicationsDuringStay' => 'required|string',
                'diet' => 'required|string',
                'dischargeMedications' => 'required|string',
                'advice' => 'required|string',
                'followUp' => 'required|string',
                'urgentCareInstructions' => 'required|string',
                'particulars' => 'nullable|string',
                'name' => 'required|string',
                'empId' => 'required|string',
                'dateTime' => 'required|date',
                'seniorHouseOfficer' => 'required|string',
                'specialist' => 'required|string',
                'consultant' => 'required|string',
            ]);
            $token = explode(' ', $request->header('Authorization'))[1];
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;

            $case_record = [
                ...$validatedData,
                "user_id"=> $user->id,
                "cases_id"=> $cases_id,
                "updated_at"=> now(),
                "created_at"=> now(),
            ];
            CaseRecords::updateOrInsert(['cases_id' => $cases_id], $case_record);
            return response()->json(['data' => $case_record, 'msg' => 'success', 'status' => 200]);

        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
}
