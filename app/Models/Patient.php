<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    public function getAgeAttribute()
    {
        return Carbon::parse($this->dob)->age;
    }
}
