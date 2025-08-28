<?php

namespace App\Http\Controllers;

use App\Models\BedAllocation;
use App\Models\Beds;
use App\Models\BillingTransactions;
use App\Models\Cases;
use App\Models\IndoorBillings;
use App\Models\Patient;
use App\Models\PharmacyBilling;
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

    /**
     * Display the specified resource.
     */
    public function indoorBillingData(Request $request)
    {
        try {
            $data = $request->validate([
                'cases_id' =>  'nullable',
                'bed' =>  'nullable',
                'phone' =>  'nullable',
            ]);
//            $output = ;
            $case_id = [];
            if($data['cases_id']){
                $case_id = $request->input('cases_id');
                $get_data = Cases::with('patient.cases.patient')->where('id', $data['cases_id'])->first();
                $output['patient'] = $get_data['patient']['cases']['patient'];
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
            $output['transaction']= BillingTransactions::where('cases_id', $case_id)->get();
            $output['bed_bills'] = BedAllocation::with('beds', 'allocator')->where('cases_id', $case_id)->get();
            $output['services'] = IndoorBillings::where('cases_id', $case_id)->first();

            return response()->json(['data' => $output, 'msg' => 'success', 'status'=> 200]);
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }catch (\Exception $e){
            return response()->json(['data' => "Not Found", 'msg' => 'error', 'status' => 404]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
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
//                $storage['name'] = gettype($service['service']);
                if(gettype($service['service']) == 'string') {
                    $storage['service'] = $service['service'];
                    $storage['price'] = $service['price'];
                }else{
                    $storage['id'] = $service['service']['id'];
                    $storage['service'] = $service['service']['name'];
                    $storage['price'] = $service['service']['amount'];
                }
                $storage['user_id'] = $service['user_id'];
                $storage['qty'] = $service['qty'];
                $storage['total'] = $service['total'];
                $storage['date'] = $service['date'];
                $storage['status'] = true; // this mean next time non-editable.
                $sanitizeServices[] = $storage;
            }
            $sanitizeServices = json_encode($sanitizeServices);
            IndoorBillings::updateOrInsert(
            ['cases_id' => $cases_id, 'patient_id' => $request->input('patient_id')], fn ($exists) => $exists ? [
                'services'=> $sanitizeServices,
                'payable' => 0,
                'received_by' => $user->id,
                'created_at'=> now(),
                'updated_at'=> now(),
            ]:
            [
                'services'=> $sanitizeServices,
                'payable' => 0,
                'received_by' => $user->id,
                'created_at'=> now(),
                'updated_at'=> now(),
                'received' => 0
            ]);
            return response()->json(['data'=> "saved", 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function generate_bill(Request $request, string $cases_id)
    {
        try{
//            $user =
        }catch (ValidationException $e){

        }
    }
}
