<?php

namespace App\Http\Controllers;

use App\Models\Features;
use App\Models\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function index(Request $request){
        return response()->json(['data'=> 'Unauthorized', 'msg' => 'failed' , 'status'=> 401]);
    }
    public function login(Request $request){

        if($request->input('email')) {
            $user = \App\Models\Auth::where('email', $request->input('email'))->first();
            Auth::loginUsingId($user->id);
        }else{
            $request->validate([
                'identifier' => 'required',
                'password' => 'required',
            ]);

            $identifier = $request->input('identifier');
            $password = $request->input('password');
            $credentials = [];
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $credentials = ['email' => $identifier, 'password' => $password];
            } elseif (is_numeric($identifier)) {
                $credentials = ['phone' => $identifier, 'password' => $password];
            } else {
                $credentials = ['code' => $identifier, 'password' => $password];
            }
        }

        try{
            if(!$request->input('email')){
                if(Auth::attempt($credentials)){
                    $user = Auth::user();
                }
            }else{
                $user = Auth::user();
            }

            if(!$user){
                return response()->json(['data'=> 'Unauthorized', 'msg' => 'failed' , 'status'=> 203]);
            }
            $user->tokens()->delete(); // delete previous keys
            $token = $user->createToken('auth')->plainTextToken;
            $user->token = $token;
            $settings = GeneralSettings::findOrFail(1);
            $response = Http::post(json_decode($settings->attendance)->server . '/jwt-api-token-auth/', [
                "username" => json_decode($settings->attendance)->username,
                "password" => json_decode($settings->attendance)->password,
            ]);
            if ($response->successful()) {
                $user->attendance = $response->json();
                $newAttendance = json_decode($settings->attendance);
                $newAttendance->remember = $response->json()['token'];
                GeneralSettings::where('id', 1)->update(['attendance'=> json_encode($newAttendance)]);
            }else {
                $user->attendance = "Something went wrong";
            }
            return response()->json(['data'=> $user, 'msg' => 'success' , 'status'=> 200])->cookie('access_token', $token, 60, null, null, true, true);
        }
        catch (\Exception $e){
            return response()->json(['data'=> $e->getMessage(), 'msg' => 'failed' , 'status'=> 401]);
        }
    }
}
