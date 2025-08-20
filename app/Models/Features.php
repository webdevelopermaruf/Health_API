<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Features extends Model
{
    public function children()
    {
        return $this->hasMany(Features::class, 'parent', 'id')
            ->with('children'); // recursion
    }
}
