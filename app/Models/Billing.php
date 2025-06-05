<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    public function patient(){
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function doctor(){
        return $this->belongsTo(Doctors::class, 'doctors_id', 'id');
    }

    public function appointment(){
        return $this->belongsTo(Appointments::class, 'appointments_id', 'id');
    }
}
