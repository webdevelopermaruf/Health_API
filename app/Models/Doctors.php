<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctors extends Model
{
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Departments::class,'department_id');
    }

    public function schedules(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AppointmentSchedule::class,'doctors_id');
    }
}
