<?php

namespace App\Http\Controllers;

use App\Models\LabReport;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LabReportController extends Controller
{
    /**
     * Display a listing of the resource.
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


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $files = $request->file('files');
            $reports = [];
            foreach ($files as $file) {
                $filename = now()->format('Ymd_Hi_v') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('lab_reports/'.$id, $filename, 'public');
                $reports[] = $path;
            }
            $get = LabReport::find($id);
            if ($get) {
                $existingReports = $get->report ? json_decode($get->report, true) : [];
                $reports = array_merge($existingReports, $reports);
                $get->report = json_encode($reports);
                $get->save();
            }else{
                $update = LabReport::where('id', $id)->update([
                    'report' =>  $reports,
                ]);
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
                'status' => 2,
            ]);
            return response()->json(['data' => $update, 'msg' => 'success', 'status' => 200]);
        }catch (ValidationException  $e) {
            return response()->json(['data' => $e->errors(), 'msg' => 'error', 'status' => 422]);
        }
    }
}
