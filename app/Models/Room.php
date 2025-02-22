<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['area_id', 'name'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function spots()
    {
        return $this->hasMany(Spot::class);
    }
} 