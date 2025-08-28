<?php

namespace App\Http\Controllers;


use App\Models\GeneralSettings;
use App\Models\Role;
use Buglinjo\LaravelWebp\Facades\Webp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $settings = GeneralSettings::first();
        if($request->header('token')){
            $accessToken = PersonalAccessToken::findToken($request->header('token'));
            $user = $accessToken->tokenable;
            $settings->permissions =  json_decode($user->permission);
            $settings->username = $user['name'];
            $settings->code = $user['code'];
            $settings->role = Role::where('id', $user['roles_id'])->value('name');
        }

        return response()->json([
            'data'=> $settings,
            'msg' => 'success',
            'status'=> 200
        ]);
    }


    public function update(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'maintenance' => 'required',
                'sms' => 'required',
            ]);
            $settings = GeneralSettings::where('id',1)->first();
            if($request->file('icon') && $request->file('icon')->getClientOriginalExtension() === 'webp'){
                $request->file('icon')->storePubliclyAs("icon.webp");
            }else if($request->file('icon')){
                Webp::make($request->file('icon'))->save(storage_path("app/public/icon.webp"));
            }
            $icon_path =$request->file('icon') ? json_encode(['icon'=> "icon.webp"]): $settings->icon;
            $update = GeneralSettings::where('id',1)->update([
                'name' => json_encode(["en"=> ucwords($request->input("name")), "bn"=> trim($request->input("name_bn"))]),
                'icon' => $icon_path,
                'maintenance' => $request->input('maintenance'),
                'sms' => $request->input('sms'),
                'updated_at' => now(),
            ]);

            if($update){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        } catch(ValidationException  $e){
            return response()->json(['data'=> $e->errors(), 'msg' => 'error' , 'status'=> 422]);
        }
    }

}
