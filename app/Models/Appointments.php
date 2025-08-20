<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    public function doctor(){
        return $this->belongsTo(Doctors::class,'doctor_id');
    }
    public function patient(){
        return $this->belongsTo(Patient::class,'patient_id');
    }
}
