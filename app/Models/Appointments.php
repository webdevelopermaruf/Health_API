<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments extends Model
{
    public function doctor(){
        return $this->belongsTo(Doctors::class,'doctor_id');
    }
}
