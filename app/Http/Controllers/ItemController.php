<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Area;
use App\Models\Room;
use App\Models\Spot;
use App\Models\ItemImage;

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
        $categories = ItemCategory::where('user_id', auth()->id())->get();
        return view('items.create', compact('categories'));
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
                'expiry_date' => 'nullable|date|after_or_equal:today',
                'purchase_date' => 'nullable|date|before_or_equal:today',
                'purchase_price' => 'nullable|numeric|min:0',
                'category_id' => 'nullable|exists:item_categories,id',
                'new_category' => 'required_if:category_id,null|nullable|string|max:255',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'primary_image' => 'required|integer|min:0',
                'location_input' => 'required|string',
                'spot_id' => 'nullable|exists:spots,id'
            ]);

            DB::beginTransaction();

            // 处理新分类
            if (empty($validated['category_id']) && !empty($validated['new_category'])) {
                $category = ItemCategory::create([
                    'name' => $validated['new_category'],
                    'user_id' => auth()->id()
                ]);
                $validated['category_id'] = $category->id;
            }

            // 如果没有选择已有地点，创建新的地点层级
            if (empty($validated['spot_id']) && !empty($validated['location_input'])) {
                $parts = array_map('trim', explode('/', $validated['location_input']));
                if (count($parts) === 3) {
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

                    $validated['spot_id'] = $spot->id;
                }
            }

            // 创建物品
            $item = $request->user()->items()->create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'quantity' => $validated['quantity'],
                'expiry_date' => $validated['expiry_date'],
                'purchase_date' => $validated['purchase_date'],
                'purchase_price' => $validated['purchase_price'],
                'category_id' => $validated['category_id'],
                'spot_id' => $validated['spot_id']
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

            DB::commit();

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
            DB::rollBack();
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
        
        $categories = ItemCategory::where('user_id', auth()->id())->get();
        
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

        \Log::info('Final locations data:', ['locations' => $locations]);
        \Log::info('Item location data:', [
            'area' => $item->spot?->room?->area?->name,
            'room' => $item->spot?->room?->name,
            'spot' => $item->spot?->name,
        ]);

        return view('items.edit', compact('item', 'categories', 'locations'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'purchase_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:item_categories,id',
            'new_category' => 'required_if:category_id,null|nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // 处理新分类
            if (empty($validated['category_id']) && !empty($validated['new_category'])) {
                $category = ItemCategory::create([
                    'name' => $validated['new_category'],
                    'user_id' => auth()->id()
                ]);
                $validated['category_id'] = $category->id;
            }

            $item->update($validated);

            DB::commit();
            return redirect()->route('dashboard')->with('success', '物品更新成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()]);
        }
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

    public function deleteImage($imageId)
    {
        $image = ItemImage::findOrFail($imageId);
        $this->authorize('update', $image->item);

        try {
            // 删除存储的文件
            Storage::disk('public')->delete($image->path);
            // 删除数据库记录
            $image->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 