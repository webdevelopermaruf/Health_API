<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    public function room()
    {
        return $this->belongsTo(Rooms::class,'rooms_id','id');
    }
}
