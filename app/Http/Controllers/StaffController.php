<?php

namespace App\Http\Controllers;

use App\Models\Staffs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staffs = Staffs::whereNotIn('status', [-1])
            ->with(['user','department', 'salary_structure'])
            ->get();

        return response()->json([
            'data'=> $staffs,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json([
            'data'=> Staffs::where('id', $id)->with(['user','department', 'salary_structure'])->first(),
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
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|unique:users,phone',
                'password' => 'required|string',
                'department_id' => 'nullable|exists:departments,id',
                'salary_structure_id' => 'nullable|exists:salary_structures,id',
                'designation' => 'nullable|string',
                'picture' => 'nullable|images',
                'address' => 'nullable|string',
                'dob' => 'nullable|date',
                'qualification' => 'nullable|string',
                'experience' => 'nullable|string',
                'about' => 'nullable|string',
                'blood' => 'nullable|string',
                'gender' => 'nullable',
                'status' => 'required',
            ]);

            $last_code = User::where('code', 'like', 'EMP-%')->max('code');
            $next_number = $last_code ? (int) str_replace('EMP-', '', $last_code) + 1 : date('y') . '0001';

            $user = User::insertGetId([
                'code' => 'EMP-' . $next_number,
                'name' => ucwords($request->name),
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password), // default or random password
                'designation' => ucwords($request->designation),
                'department_id' => $request->department_id,
                'address' => ucwords($request->address),
                'dob' => $request->dob,
                'blood' => $request->blood,
                'gender' => $request->gender,
                'picture' => $request->picture,
                'status' => $request->status ?? 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]);
            $staff = Staffs::insert([
                'user_id' => $user,
                'department_id' => $request->department_id,
                'salary_structure_id' => $request->salary_structure_id,
                'qualification' => $request->qualification,
                'experience' => $request->experience,
                'about' => $request->about,
                'status' => $request->status ?? 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            if($staff){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 201]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }
        }catch (ValidationException $e){
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $staff = Staffs::findOrFail($id);
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,id,'.$staff->user_id,
                'phone' => 'required|string|unique:users,id,'.$staff->user_id,
                'department_id' => 'required|exists:departments,id',
                'salary_structure_id' => 'nullable|exists:salary_structures,id',
                'designation' => 'nullable|string',
                'qualification' => 'nullable|string',
                'experience' => 'nullable|string',
                'about' => 'required|string',
                'picture' => 'nullable|string',
                'address' => 'nullable|string',
                'dob' => 'nullable|date',
                'blood' => 'nullable|string',
                'gender'=> 'nullable',
                'status' => 'required',
            ]);
            $user = User::where('id', $staff->user_id)->update([
                'name' => ucwords($request->name),
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'designation' => ucwords($request->designation),
                'department_id' => $request->department_id,
                'address' => ucwords($request->address),
                'dob' => $request->dob,
                'picture' => $request->picture,
                'blood' => $request->blood,
                'gender' => $request->gender,
                'status' => $request->status ?? 1,
                'permission' => null,
                'updated_at'=>now()
            ]);
            $staff = Staffs::where('id', $id)->update([
                'department_id' => $request->department_id,
                'salary_structure_id' => $request->salary_structure_id,
                'qualification' => ucwords($request->qualification),
                'experience' => ucwords($request->experience),
                'about' => ucfirst($request->about),
                'status' => $request->status ?? 1,
                'updated_at'=>now()
            ]);

            if($user && $staff){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        } catch (ValidationException $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try{
            $delete = Staffs::where('id',$id)->update([
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
