<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyBilling extends Model
{
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
