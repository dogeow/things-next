<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    protected $fillable = ['room_id', 'name'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    // 获取完整路径
    public function getFullPathAttribute()
    {
        return $this->room->area->name . '/' . $this->room->name . '/' . $this->name;
    }
} 