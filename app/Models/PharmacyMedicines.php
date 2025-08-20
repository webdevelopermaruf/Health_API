<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyMedicines extends Model
{
    public function supplier(){
        return $this->belongsTo(PharmacySupplier::class, 'pharmacy_supplier_id', 'id');
    }
}
