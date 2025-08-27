<?php

namespace App\Http\Controllers;

use App\Models\LabReport;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LabReportController extends Controller
{
    /**
     status == 0 pending
     * status == 1 ready
     * status == 2 published
     * status == 3 == delivered
     *
     */
    public function index()
    {
        return response()->json([
            'data'=> LabReport::whereNotIn('status', [-1])
                ->with(['patient','billing', 'service'])
                ->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }

    public function delivery()
    {
        return response()->json([
            'data'=> LabReport::where('status', 2) // published
                ->with(['patient','billing', 'service'])
                ->get(),
            'msg' => 'success',
            'status'=> 200
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $files = $request->file('files');
            $reports = [];
            foreach ($files as $file) {
                $filename = now()->format('Ymd_Hi_v') . '.' . strtolower($file->getClientOriginalExtension());
                $path = $file->storeAs('lab_reports/'.$id, $filename, 'public');
                $reports[] = $path;
            }
            $get = LabReport::find($id);
            if ($get) {
                $existingReports = $get->report ? json_decode($get->report, true) : [];
                $reports = array_merge($existingReports, $reports);
                $get->report = json_encode($reports);
                $get->status = 1; // ready
                $get->save();
            }else{
                return response()->json(['data' => "not found", 'msg' => 'error', 'status' => 400]);
            }
            return response()->json(['data' => $reports, 'msg' => 'success', 'status' => 200]);
        } catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function publish(string $id)
    {
        try{
            $update = LabReport::where('id', $id)->update([
                'status' => 2, // published
            ]);
            return response()->json(['data' => $update, 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }

    public function delivered(string $id)
    {
        try{
            $update = LabReport::where('id', $id)->update([
                'status' => 3, // delivered
            ]);
            return response()->json(['data' => $update, 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
}
