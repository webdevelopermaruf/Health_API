<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyPurchases extends Model
{
    protected $fillable = [
        'pharmacy_supplier_id',
        'medicines',
        'total_qty',
        'payable',
        'paid',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Get all medicines in this purchase.
     */
    public function medicines()
    {
        return $this->belongsToMany(PharmacyMedicines::class, 'pharmacy_purchase_medicine',  'pharmacy_purchase_id', 'pharmacy_medicine_id')
            ->withPivot('qty', 'price', 'prevStock')
            ->withTimestamps();
    }

    public function supplier()
    {
        return $this->belongsTo(PharmacySupplier::class, 'pharmacy_supplier_id');
    }

    public function transactions()
    {
        return $this->hasMany(OutTransaction::class, 'trx_id')->where('type', 2);
    }
}
