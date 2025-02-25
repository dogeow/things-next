<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Spot;
use App\Models\Area;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::where('user_id', auth()->id())->get();
        
        $items = Item::where('user_id', auth()->id())
            ->with(['images', 'category'])
            ->latest()
            ->paginate(9);

        // 计算购买时间差
        $items->each(function($item) {
            if ($item->created_at) {
                // 使用Carbon::now()的实例来计算时间差，确保时间差是正确的
                $item->created_at_diff = $item->created_at->diffForHumans();
            } else {
                $item->created_at_diff = '';
            }
        });

        return view('dashboard', compact('items', 'categories'));
    }
} 