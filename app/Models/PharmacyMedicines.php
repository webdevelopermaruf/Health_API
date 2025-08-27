<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PharmacyMedicines extends Model
{
    public $fillable = ['code'];
    public function supplier(){
        return $this->belongsTo(PharmacySupplier::class, 'pharmacy_supplier_id', 'id');
    }

    public function patient(){
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    protected static function updateMedicines()
    {
        Cache::forget('pharmacy_medicines');
        Cache::put('pharmacy_medicines', PharmacyMedicines::with('supplier')->whereNotIn('status', [-1])->get(), 3600);
    }

    public function purchases()
    {
        return $this->belongsToMany(PharmacyPurchases::class, 'pharmacy_purchase_medicine', 'pharmacy_medicine_id', 'pharmacy_purchase_id')
            ->withPivot('qty', 'price', 'prevStock')
            ->withTimestamps();
    }


}
