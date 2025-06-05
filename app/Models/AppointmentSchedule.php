<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentSchedule extends Model
{

    public function room()
    {
        return $this->belongsTo(Rooms::class,'rooms_id', 'id');
    }
}
