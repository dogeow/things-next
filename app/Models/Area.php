<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 