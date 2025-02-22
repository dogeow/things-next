<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
        try {
            // 添加更详细的调试日志
            \Log::info('开始处理图片上传', [
                'has_files' => $request->hasFile('images'),
                'files_data' => $request->file('images'),
                'files_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
                'request_all' => $request->all(),
                'content_type' => $request->header('Content-Type'),
                'request_files' => $_FILES, // 原始文件数据
                'request_headers' => $request->headers->all(), // 所有请求头
            ]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:1',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'primary_image' => 'required|integer|min:0'
            ]);

            \DB::beginTransaction(); // 开始事务

            try {
                $item = $request->user()->items()->create([
                    'name' => $validated['name'],
                    'description' => $validated['description'],
                    'quantity' => $validated['quantity'],
                ]);

                \Log::info('物品创建成功', ['item_id' => $item->id]);

                if ($request->hasFile('images')) {
                    $images = $request->file('images');
                    $primaryIndex = (int) $request->input('primary_image', 0);

                    \Log::info('处理图片数组', [
                        'images_count' => count($images),
                        'primary_index' => $primaryIndex
                    ]);

                    foreach ($images as $index => $image) {
                        // 生成唯一的文件名
                        $filename = uniqid('item_') . '.' . $image->getClientOriginalExtension();
                        
                        // 存储图片
                        $path = $image->storeAs('items', $filename, 'public');
                        
                        \Log::info('准备保存图片记录', [
                            'index' => $index,
                            'path' => $path,
                            'is_primary' => $index == $primaryIndex,
                            'item_id' => $item->id
                        ]);

                        // 验证文件是否成功保存
                        if (!Storage::disk('public')->exists($path)) {
                            throw new \Exception("文件 {$path} 保存失败");
                        }

                        // 直接使用 DB 查询构建器来创建记录，以便捕获任何SQL错误
                        $imageRecord = \DB::table('item_images')->insert([
                            'item_id' => $item->id,
                            'path' => $path,
                            'is_primary' => $index == $primaryIndex,
                            'sort_order' => $index,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        \Log::info('图片记录创建结果', [
                            'success' => $imageRecord,
                            'path' => $path
                        ]);
                    }
                }

                \DB::commit(); // 提交事务

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('dashboard'),
                        'message' => '物品创建成功！'
                    ]);
                }

                return redirect()->route('dashboard')
                    ->with('success', '物品创建成功！');

            } catch (\Exception $e) {
                \DB::rollBack(); // 回滚事务
                \Log::error('数据库操作失败', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // 删除已上传的图片
                if (isset($path)) {
                    Storage::disk('public')->delete($path);
                }

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['general' => $e->getMessage()]);
            }

        } catch (\Exception $e) {
            \Log::error('整体处理失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()]);
        }
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

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }
} 