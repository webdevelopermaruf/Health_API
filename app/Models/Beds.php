<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beds extends Model
{
    public function room(){
        return $this->belongsTo(Rooms::class, 'rooms_id','id');
    }

    public function bed(){
        return $this->hasOne(BedAllocation::class, 'current_bed','id');
    }
}
