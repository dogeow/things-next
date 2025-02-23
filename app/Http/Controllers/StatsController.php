<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Area;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 基础统计
        $basicStats = [
            'total_value' => Item::where('user_id', $user->id)
                ->whereNotNull('purchase_price')
                ->sum('purchase_price'),
            'categories_count' => ItemCategory::where('user_id', $user->id)->count(),
            'locations_count' => Area::where('user_id', $user->id)
                ->join('rooms', 'areas.id', '=', 'rooms.area_id')
                ->join('spots', 'rooms.id', '=', 'spots.room_id')
                ->count(),
            'items_count' => Item::where('user_id', $user->id)->count(),
        ];

        // 分类统计
        $categoryStats = ItemCategory::where('user_id', $user->id)
            ->withCount('items')
            ->withSum('items', 'purchase_price')
            ->get();

        // 位置统计
        $locationStats = Area::where('user_id', $user->id)
            ->with(['rooms.spots' => function($query) {
                $query->withCount('items')
                    ->withSum('items', 'purchase_price');
            }])
            ->get();

        // 月度统计
        $monthlyStats = Item::where('user_id', $user->id)
            ->whereNotNull('purchase_date')
            ->select(
                DB::raw('DATE_FORMAT(purchase_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(purchase_price) as total_price')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('stats', compact(
            'basicStats',
            'categoryStats',
            'locationStats',
            'monthlyStats'
        ));
    }
} 