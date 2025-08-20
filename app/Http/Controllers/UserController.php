<?php

namespace App\Http\Controllers;

use App\Models\Features;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('department')->get();
        return response()->json([
            'data'=> $users,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function features(){
        return response()->json([
            'data'=> Features::whereNull('parent')->with('children')
                ->whereNotIn('status', [-1])->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function updatePermission(Request $request, string $id)
    {
        $token = ltrim($request->header('Authorization'),  'Bearer ');
        if($request->header('Authorization')){
            $accessToken = PersonalAccessToken::findToken($token);
            $user = $accessToken->tokenable;
        }
        try{
            User::where('id', $id)->update([
                'permission' => json_encode($request->permission, JSON_FORCE_OBJECT, true)
            ]);
            return response()->json([
                'data'=> ["id"=> $id, "permission"=> $request->permission],
                'msg' => 'success',
                'status'=> 200,
                'updateRequired'=> $user->id == $id ? true : false,
            ]);
        }catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
}
