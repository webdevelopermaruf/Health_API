<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndoorBillings extends Model
{
    public $table = 'indoor_billings';
    public function cases(){
        return $this->belongsTo(Cases::class, 'cases_id', 'id');
    }
    public function patient(){
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'received_by', 'id');
    }
}
