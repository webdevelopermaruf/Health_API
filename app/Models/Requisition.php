<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    public function requisite_by(){
        return $this->belongsTo(User::class,'requested_by');
    }
    public function bill(){
        return $this->belongsTo(PharmacyBilling::class,'requested_by');
    }

    public function case(){
        return $this->belongsTo(Cases::class,'requested_by');
    }
}
