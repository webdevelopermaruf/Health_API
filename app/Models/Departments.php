<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departments extends Model
{

    public function parentDepartment(): BelongsTo
    {
        return $this->belongsTo(Departments::class, 'parent_dept');
    }

    public function childDepartments(): HasMany
    {
        return $this->hasMany(Departments::class, 'parent_dept');
    }
    public function doctors()
    {
        return $this->hasMany(Doctors::class, 'department_id');
    }
}
