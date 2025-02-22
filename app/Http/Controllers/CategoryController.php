<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::where('user_id', auth()->id())
            ->withCount('items')
            ->orderBy('items_count', 'desc')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        try {
            ItemCategory::create([
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            return back()->with('success', '分类添加成功！');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => '添加分类失败：' . $e->getMessage()]);
        }
    }

    public function destroy(ItemCategory $category)
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $category->delete();
            return back()->with('success', '分类删除成功！');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => '删除分类失败：' . $e->getMessage()]);
        }
    }
} 