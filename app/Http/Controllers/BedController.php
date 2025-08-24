<?php

namespace App\Http\Controllers;

use App\Models\Beds;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        $beds = Beds::leftJoin('rooms', 'beds.rooms_id', '=', 'rooms.id')
            ->whereNotIn('beds.status', [-1])
            ->select(['beds.*', 'rooms.*', 'beds.id as id' ])
            ->orderBy('room_no','asc')
            ->orderBy('bed_number','asc')
            ->get();

        return response()->json([
            'data'=> $beds,
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function show(string $id){
        return response()->json([
            'data'=> Beds::with(['room'])->findOrFail($id),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function types(){
        return response()->json([
            'data'=> Beds::pluck('bed_type')->unique()->values()->all(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function available(){
        $beds = Beds::with('room')->where('is_booked',0)->get();
        return response()->json([
            'data'=> $beds,
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
                'room_id' => 'required',
                'bed_number' => 'required',
                'price' => 'required|numeric',
                'timeline' => 'required|integer',
                'type' => 'required|string',
                'status' => 'required',
            ]);


            $insert = Beds::insert([
                'rooms_id' => $request->room_id,
                'bed_number' => $request->bed_number,
                'bed_type' => ucwords($request->type),
                'price' => $request->price,
                'timeline' => $request->timeline,
                'status' => $request->status ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
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
                'room_id' => 'required',
                'bed_number' => 'required',
                'price' => 'required|numeric',
                'timeline' => 'required|integer',
                'type' => 'required|string',
                'status' => 'required',
            ]);


            $update = Beds::where('id', $id)->update([
                'rooms_id' => $request->room_id,
                'bed_number' => $request->bed_number,
                'price' => $request->price,
                'timeline' => $request->timeline,
                'bed_type' => ucwords($request->type),
                'status' => $request->status ?? 1,
                'updated_at' => now()
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
    public function destroy(Request $request, string $id)
    {
        try{
            $delete = Beds::where('id',$id)->update([
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
