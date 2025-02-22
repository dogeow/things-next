<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    protected $fillable = [
        'item_id',
        'path',
        'is_primary',
        'sort_order'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class)->withDefault();
    }
} 