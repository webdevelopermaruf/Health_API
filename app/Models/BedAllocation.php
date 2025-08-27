<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedAllocation extends Model
{
    public function cases()
    {
        return $this->belongsTo(Cases::class, 'cases_id', 'id');
    }

    public function beds()
    {
        return $this->belongsTo(Beds::class, 'current_bed', 'id');
    }

}
