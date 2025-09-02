<?php

namespace App\Http\Controllers;

use App\Models\Appointments;
use App\Models\BillingTransactions;
use App\Models\Cases;
use App\Models\Doctors;
use App\Models\IndoorBillings;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function todayDoctorsAppointment()
    {
        $today = strtolower(str_split(date('l'), 3)[0]);
        $doctors = Doctors::with(['user', 'schedules', 'department'])->where('status', 1)->get();
        foreach ($doctors as $doctor) {
            $s = json_decode($doctor->schedules->schedule);
            $doctor['today'] = $s->$today ?? null;
        }
        return response()->json([
            'data' => $doctors,
            'message' => 'success',
            'status' => 200
        ]);

    }

    public function receptionStatistics()
    {
        $stats = [];
        $stats['indoor'] = Cases::whereDate('created_at', today())->where('status', 1)->count();
        $stats['discharged'] = Cases::with('case_record')->where('status', 0)->where('updated_at', today())->count();

        $outdoor = BillingTransactions::where('status', 1)
            ->where('trx_type', 1)->where('billing_type', 1)->whereDate('created_at', today())->get();
        $stats['outdoor'] = $outdoor->count();
        $stats['outdoor_charge'] = $outdoor->sum('amount');
        $stats['appointments'] = Appointments::where('status', 1)->whereDate('created_at', today())->count();
        return response()->json([
            'data' => $stats,
            'message' => 'success',
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
