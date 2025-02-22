<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
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
            
        return view('dashboard', compact('items', 'categories'));
    }
} 