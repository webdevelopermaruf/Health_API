<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staffs extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Departments::class);
    }

    public function salary_structure()
    {
        return $this->belongsTo(SalaryStructure::class);
    }
}
