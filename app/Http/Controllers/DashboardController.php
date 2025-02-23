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

        // 计算购买时间差
        $items->each(function($item) {
            if ($item->created_at) {
                // 使用Carbon::now()的实例来计算时间差，确保时间差是正确的
                $item->created_at_diff = $item->created_at->diffForHumans();
            } else {
                $item->created_at_diff = '';
            }
        });

        return view('dashboard', compact('items', 'categories', 'locations'));
    }
} 