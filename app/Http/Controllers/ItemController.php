<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $items = Item::with('user')->latest()->paginate(10);
        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image' => 'required|integer|min:0'
        ]);

        $item = $request->user()->items()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'quantity' => $validated['quantity'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                try {
                    // 直接使用 public disk 存储图片
                    $path = $image->store('items', 'public');
                    
                    // 验证文件是否成功保存
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception('文件保存失败');
                    }

                    $item->images()->create([
                        'path' => $path,
                        'is_primary' => $index == $request->primary_image,
                        'sort_order' => $index
                    ]);
                } catch (\Exception $e) {
                    // 记录错误
                    \Log::error('图片上传失败: ' . $e->getMessage());
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['images' => '图片上传失败，请重试']);
                }
            }
        }

        return redirect()->route('dashboard')
            ->with('success', '物品创建成功！');
    }

    public function edit(Item $item)
    {
        $this->authorize('update', $item);
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', '物品更新成功！');
    }

    public function plaza()
    {
        $recentItems = Item::with('user')
            ->latest()
            ->take(6)
            ->get();

        $topUsers = \App\Models\User::withCount('items')
            ->orderBy('items_count', 'desc')
            ->take(5)
            ->get();

        $totalItems = Item::count();
        $totalUsers = \App\Models\User::count();
        $todayItems = Item::whereDate('created_at', today())->count();

        return view('plaza', compact(
            'recentItems',
            'topUsers',
            'totalItems',
            'totalUsers',
            'todayItems'
        ));
    }

    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);
        $item->delete();
        return redirect()->route('dashboard')->with('success', '物品已删除！');
    }
} 