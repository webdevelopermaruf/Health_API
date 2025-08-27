<?php

namespace App\Http\Controllers;

use App\Models\Beds;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        if($request->query('floor')){
            $rooms = Rooms::whereNotIn('status', [-1])
                ->where('floor_no', $request->query('floor'))
                ->orderBy('room_no', 'asc')
                ->get();
        }else{
            $rooms = Rooms::whereNotIn('status', [-1])
            ->orderBy('floor_no', 'asc')
            ->orderBy('room_no', 'asc')->get();
        }
        return response()->json([
            'data'=> $rooms,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function booked(Request $request){

            $rooms = Beds::with('room')->whereNotIn('status', [-1])
                ->where('is_booked', 1)
                ->orderBy('rooms_id', 'asc')->get();

        return response()->json([
            'data'=> $rooms,
            'msg' => 'success',
            'status'=> 200
        ]);
    }


    public function show(string $id){
        return response()->json([
            'data'=> Rooms::findOrFail($id),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function types(){
        return response()->json([
            'data'=> Rooms::pluck('type')->unique()->values()->all(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function floors(){
        return response()->json([
            'data'=> Rooms::orderby('floor_no', 'asc')->pluck('floor_no')->unique()->values()->all(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $request->validate([
                'type' => 'required|string',
                'room_no' => 'required|string',
                'floor_no' => 'required|integer',
                'bed_capacity' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'required',
            ]);

            $insert = Rooms::insert([
                'type' => ucwords($request->type),
                'room_no' => $request->room_no,
                'floor_no' => $request->floor_no,
                'bed_capacity' => $request->bed_capacity,
                'description' => $request->description,
                'status' => $request->status ?? 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]);

            if($insert){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 201]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $request->validate([
                'type' => 'required|string',
                'room_no' => 'required|string',
                'floor_no' => 'required|integer',
                'bed_capacity' => 'required|integer',
                'description' => 'nullable|string',
                'status' => 'required',
            ]);


            $update = Rooms::where('id', $id)->update([
                'type' => ucwords($request->type),
                'room_no' => $request->room_no,
                'floor_no' => $request->floor_no,
                'bed_capacity' => $request->bed_capacity,
                'description' => $request->description,
                'status' => $request->status ?? 1,
                'updated_at' => now(),
            ]);

            if($update){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }

        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,string $id)
    {
        try{
            $delete = Rooms::where('id',$id)->update([
                'status' => -1,
                'updated_at' => now(),
            ]);

            if($delete){
                return response()->json(['data'=> $request->all(), 'msg' => 'success' , 'status'=> 200]);
            }else{
                return response()->json(['data'=> $request->all(), 'msg' => 'failed' , 'status'=> 400]);
            }
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
}
