<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function department(){
        return $this->belongsTo(Departments::class, 'departments_id');
    }
    public function doctor()
    {
        return $this->belongsTo(Doctors::class, 'doctors_id');
    }
    public function prepared(){
        return $this->belongsTo(User::class, 'prepared_by');
    }
    public function referred(){
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function bed(){
        return $this->hasOneThrough(Beds::class, BedAllocation::class, 'cases_id', 'id', 'id', 'current_bed');
    }
}
