<?php

namespace App\Http\Controllers;

use App\Models\Doctors;
use App\Models\User;
use Buglinjo\LaravelWebp\Facades\Webp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DoctorController extends Controller
{
    public function index(Request $request){
        if($request->query('dept')){
            $doctors = Doctors::whereNotIn('status', [-1])
                ->where('department_id', $request->query('dept'))
                ->with(['user','department'])
                ->get();
        }else{
            $doctors = Doctors::whereNotIn('status', [-1])
                ->with(['user','department'])
                ->get();
        }

        return response()->json([
            'data'=> $doctors,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function show(string $id){
        return response()->json([
            'data'=> Doctors::with(['user','department','schedules.room'])->findOrFail($id),
            'msg' => 'success',
            'status'=> 200
        ]);
    }
    // Store new doctor and user
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users',
                'phone' => 'required|string|unique:users',
                'password' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'designation' => 'nullable|string',
                'specialization' => 'nullable|string',
                'license_number' => 'nullable|string',
                'qualification' => 'nullable|string',
                'experience' => 'nullable|string',
                'about' => 'required|string',
                'availability' => 'required|string',
                'picture' => 'nullable|image|mimes:jpeg,png,jpg,webp',
                'address' => 'nullable|string',
                'dob' => 'nullable|date',
                'blood' => 'nullable|string',
                'gender'=> 'nullable',
                'status' => 'required',
            ]);

            $last_code = User::where('code', 'like', 'DR-%')->max('code');
            $next_number = $last_code ? (int) str_replace('DR-', '', $last_code) + 1 : date('y') . '0001';
            $directory = "users/DR-" . $next_number;
            $hasUploadedPicture = false;
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            if ($request->file('picture') && $request->file('picture')->getClientOriginalExtension() === 'webp') {
                $request->file('picture')->storeAs($directory, "picture.webp", 'public');
                $hasUploadedPicture = true;
            } else if ($request->file('picture')) {
                Webp::make($request->file('picture'))
                    ->save(storage_path("app/public/{$directory}/picture.webp"));
                $hasUploadedPicture = true;
            }

            $user = User::insertGetId([
                'code' => 'DR-' . $next_number,
                'name' => ucwords($request->name),
                'email' => strtolower($request->email),
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'designation' => ucwords($request->designation),
                'department_id' => $request->department_id,
                'address' => ucwords($request->address),
                'dob' => $request->dob,
                'blood' => $request->blood,
                'gender' => $request->gender,
                'picture' => $hasUploadedPicture? "/storage/users/DR-" . $next_number . "/picture.webp": null,
                'status' => $request->status ?? 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]);
            $doctor = Doctors::insert([
                'user_id' => $user,
                'department_id' => $request->department_id,
                'specialization' => ucwords($request->specialization),
                'license_number' => $request->license_number,
                'qualification' => ucwords($request->qualification),
                'experience' => ucwords($request->experience),
                'about' => ucfirst($request->about),
                'availability' => ucfirst($request->availability),
                'status' => $request->status ?? 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            if($doctor){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 201]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
    // Update doctor and user
    public function update(Request $request, $id)
    {
        try {
            $doctor = Doctors::findOrFail($id);
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|email|unique:users,id,'.$doctor->user_id,
                'phone' => 'required|string|unique:users,id,'.$doctor->user_id,
                'department_id' => 'required|exists:departments,id',
                'designation' => 'nullable|string',
                'specialization' => 'nullable|string',
                'license_number' => 'nullable|string',
                'qualification' => 'nullable|string',
                'experience' => 'nullable|string',
                'about' => 'required|string',
                'availability' => 'required|string',
                'picture' => 'nullable|image|mimes:jpeg,png,jpg,webp',
                'address' => 'nullable|string',
                'dob' => 'nullable|date',
                'blood' => 'nullable|string',
                'gender'=> 'nullable',
                'status' => 'required',
            ]);

            $user = User::where('id', $doctor->user_id)->update([
                'name' => ucwords($request->name),
                'email' => strtolower($request->email),
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
            $doctor = Doctors::where('id', $id)->update([
                'department_id' => $request->department_id,
                'specialization' => ucwords($request->specialization),
                'license_number' => $request->license_number,
                'qualification' => ucwords($request->qualification),
                'experience' => ucwords($request->experience),
                'about' => ucfirst($request->about),
                'availability' => ucfirst($request->availability),
                'status' => $request->status ?? 1,
                'updated_at'=>now()
            ]);

            if($user && $doctor){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        } catch (ValidationException $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
    public function destroy(Request $request, string $id)
    {
        try{
            $delete = Doctors::where('id',$id)->update([
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
