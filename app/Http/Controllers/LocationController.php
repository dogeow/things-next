<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Room;
use App\Models\Spot;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $areas = Area::where('user_id', auth()->id())
            ->with(['rooms.spots'])
            ->get();

        // 获取所有地点的完整路径，用于自动完成
        $locations = Spot::whereHas('room.area', function($query) {
            $query->where('user_id', auth()->id());
        })->get()->map(function($spot) {
            return [
                'id' => $spot->id,
                'fullPath' => $spot->fullPath
            ];
        });

        return view('locations.index', compact('areas', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_input' => 'required|string'
        ]);

        $parts = array_map('trim', explode('/', $validated['location_input']));
        
        if (count($parts) !== 3) {
            return back()->withErrors(['location_input' => '请按照格式输入：区域/房间/具体位置']);
        }

        try {
            // 创建或获取区域
            $area = Area::firstOrCreate([
                'user_id' => auth()->id(),
                'name' => $parts[0]
            ]);

            // 创建或获取房间
            $room = Room::firstOrCreate([
                'area_id' => $area->id,
                'name' => $parts[1]
            ]);

            // 创建具体位置
            $spot = Spot::create([
                'room_id' => $room->id,
                'name' => $parts[2]
            ]);

            return back()->with('success', '地点添加成功！');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => '添加地点失败：' . $e->getMessage()]);
        }
    }
} 