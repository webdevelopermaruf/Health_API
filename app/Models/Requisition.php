<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    public $table = 'pharmacy_requisitions';

    public function requisite_by(){
        return $this->belongsTo(User::class,'requisite_by');
    }
    public function billing(){
        return $this->belongsTo(PharmacyBilling::class,'pharmacy_billing_id');
    }

    public function case(){
        return $this->belongsTo(Cases::class,'cases_id');
    }
}
