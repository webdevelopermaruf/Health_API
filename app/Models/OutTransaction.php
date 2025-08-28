<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutTransaction extends Model
{
    public function paid_by()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function paid_to()
    {
        if($this->type === 2){
            return $this->belongsTo(PharmacySupplier::class, 'paid_to');
        }
    }
}
