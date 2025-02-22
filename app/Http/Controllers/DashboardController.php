<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Spot;
use App\Models\Area;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::where('user_id', auth()->id())->get();
        
        $items = Item::where('user_id', auth()->id())
            ->with(['images', 'category'])
            ->latest()
            ->paginate(9);
            
        // 获取所有地点数据，包括完整的层级结构
        $locations = Area::where('user_id', auth()->id())
            ->with(['rooms.spots']) // 预加载关联数据
            ->get()
            ->map(function($area) {
                return [
                    'area' => $area->name,
                    'rooms' => $area->rooms->map(function($room) {
                        return [
                            'name' => $room->name,
                            'spots' => $room->spots->map(function($spot) {
                                return [
                                    'id' => $spot->id,
                                    'name' => $spot->name
                                ];
                            })->values()->all()
                        ];
                    })->values()->all()
                ];
            })->values()->all();

        // 添加调试日志
        \Log::info('Locations data:', [
            'locations' => $locations,
            'count' => count($locations)
        ]);

        return view('dashboard', compact('items', 'categories', 'locations'));
    }
} 