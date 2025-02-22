<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Spot;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::where('user_id', auth()->id())->get();
        \Log::info('Categories query result:', [
            'user_id' => auth()->id(),
            'categories_count' => $categories->count(),
            'categories' => $categories->toArray()
        ]);

        $items = Item::where('user_id', auth()->id())
            ->with(['images', 'category'])
            ->latest()
            ->paginate(9);
            
        // 获取所有地点的完整路径，用于自动完成
        $locations = Spot::whereHas('room.area', function($query) {
            $query->where('user_id', auth()->id());
        })->get()->map(function($spot) {
            return [
                'id' => $spot->id,
                'fullPath' => $spot->fullPath
            ];
        });

        return view('dashboard', compact('items', 'categories', 'locations'));
    }
} 