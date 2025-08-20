<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabReport extends Model
{
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d',
            'updated_at' => 'datetime:Y-m-d',
        ];
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billing_id');
    }
    public function service()
    {
        return $this->belongsTo(Services::class, 'services_id');
    }
}
