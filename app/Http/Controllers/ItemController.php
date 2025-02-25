<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use App\Models\Area;
use App\Models\Room;
use App\Models\Spot;
use App\Models\ItemImage;

class ItemController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $categories = ItemCategory::where('user_id', auth()->id())->get();
        
        $query = Item::with(['user','images', 'category', 'spot.room.area']);
        
        // 如果用户已登录，显示公开物品和自己的物品
        if (auth()->check()) {
            $query->where(function($q) {
                $q->where('is_public', true)
                  ->orWhere('user_id', auth()->id());
            });
        } else {
            // 未登录用户只能看到公开物品
            $query->where('is_public', true);
        }
        
        // 如果有搜索关键词
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // 分类筛选
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 购买时间范围筛选
        if ($request->filled('purchase_date_from')) {
            $query->whereDate('purchase_date', '>=', $request->purchase_date_from);
        }
        if ($request->filled('purchase_date_to')) {
            $query->whereDate('purchase_date', '<=', $request->purchase_date_to);
        }

        // 过期时间范围筛选
        if ($request->filled('expiry_date_from')) {
            $query->whereDate('expiry_date', '>=', $request->expiry_date_from);
        }
        if ($request->filled('expiry_date_to')) {
            $query->whereDate('expiry_date', '<=', $request->expiry_date_to);
        }

        // 购买价格范围筛选
        if ($request->filled('price_from')) {
            $query->where('purchase_price', '>=', $request->price_from);
        }
        if ($request->filled('price_to')) {
            $query->where('purchase_price', '<=', $request->price_to);
        }

        // 存放地点筛选
        if ($request->filled('area') || $request->filled('room') || $request->filled('spot')) {
            $query->whereHas('spot.room.area', function($q) use ($request) {
                // 区域筛选
                if ($request->filled('area')) {
                    $q->where('areas.name', $request->area);
                }
                
                // 房间筛选
                if ($request->filled('room')) {
                    $q->whereHas('rooms', function($q) use ($request) {
                        $q->where('rooms.name', $request->room);
                    });
                }
                
                // 具体位置筛选
                if ($request->filled('spot')) {
                    $q->whereHas('rooms.spots', function($q) use ($request) {
                        $q->where('spots.name', $request->spot);
                    });
                }
            });
        }

        $items = $query->latest()->paginate(10);

         // 计算购买时间差
         $items->each(function($item) {
            if ($item->created_at) {
                // 使用Carbon::now()的实例来计算时间差，确保时间差是正确的
                $item->created_at_diff = $item->created_at->diffForHumans();
            } else {
                $item->created_at_diff = '';
            }
        });
        
        // 保持搜索参数在分页链接中
        $items->appends($request->all());
        
        // 获取所有地点数据，用于筛选
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
        
        return view('items.index', compact('items', 'categories', 'locations'));
    }

    public function create()
    {
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

        return view('items.create', compact('categories', 'locations'));
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
                'new_category' => 'nullable|string|max:255',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'primary_image' => 'required|integer|min:0',
                'location_input' => 'nullable|string',
                'spot_id' => 'nullable|exists:spots,id',
                'is_public' => 'boolean'
            ]);

            DB::beginTransaction();

            // 处理新分类或使用默认分类
            if (empty($validated['category_id'])) {
                if (!empty($validated['new_category'])) {
                    // 如果输入了新分类名称，创建新分类
                    $category = ItemCategory::create([
                        'name' => $validated['new_category'],
                        'user_id' => auth()->id()
                    ]);
                    $validated['category_id'] = $category->id;
                } else {
                    $category = ItemCategory::firstOrCreate(
                        ['name' => '未分类', 'user_id' => auth()->id()]
                    );
                    $validated['category_id'] = $category->id;
                }
            }

            // 处理地点信息
            if (!empty($validated['location_input'])) {
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
                'spot_id' => $validated['spot_id'],
                'is_public' => $request->boolean('is_public', false)
            ]);

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $primaryIndex = (int) $request->input('primary_image', 0);
                
                // 创建图片管理器实例
                $manager = new ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );

                foreach ($images as $index => $image) {
                    // 生成唯一文件名(不带扩展名)
                    $baseFilename = uniqid('item_');
                    $extension = $image->getClientOriginalExtension();
                    
                    // 原始图片文件名
                    $originalFilename = $baseFilename . '_original.' . $extension;
                    // 缩略图文件名
                    $thumbnailFilename = $baseFilename . '_thumb.' . $extension;
                    
                    // 保存原始图片
                    $originalPath = $image->storeAs('items', $originalFilename, 'public');
                    
                    // 使用 Intervention Image 创建缩略图
                    $interventionImage = $manager->read($image);
                    // 按照原始比例缩放，宽度设为300
                    $interventionImage->scale(width: 300);
                    
                    // 保存缩略图
                    $thumbnailPath = 'items/' . $thumbnailFilename;
                    Storage::disk('public')->put(
                        $thumbnailPath, 
                        $interventionImage->toJpg()->toString()
                    );

                    // 验证文件是否成功保存
                    if (!Storage::disk('public')->exists($originalPath) || 
                        !Storage::disk('public')->exists($thumbnailPath)) {
                        throw new \Exception("图片保存失败");
                    }

                    // 创建图片记录
                    \DB::table('item_images')->insert([
                        'item_id' => $item->id,
                        'path' => $originalPath,
                        'thumbnail_path' => $thumbnailPath,
                        'is_primary' => $index == $primaryIndex,
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('items.index'),
                    'message' => '物品创建成功！'
                ]);
            }

            return redirect()->route('items.index')
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
            'new_category' => 'nullable|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image' => 'required|integer|min:0',
            'location_input' => 'nullable|string',
            'spot_id' => 'nullable|exists:spots,id',
            'is_public' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            // 处理新分类或使用默认分类
            if (empty($validated['category_id'])) {
                if (!empty($validated['new_category'])) {
                    // 如果输入了新分类名称，创建新分类
                    $category = ItemCategory::create([
                        'name' => $validated['new_category'],
                        'user_id' => auth()->id()
                    ]);
                    $validated['category_id'] = $category->id;
                } else {
                    // 获取或创建默认分类
                    $category = ItemCategory::firstOrCreate(
                        ['name' => '分类', 'user_id' => auth()->id()],
                        ['name' => '分类', 'user_id' => auth()->id()]
                    );
                    $validated['category_id'] = $category->id;
                }
            }

            $item->update($validated);

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $primaryIndex = (int) $request->input('primary_image', 0);
                
                // 创建图片管理器实例
                $manager = new ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );

                foreach ($images as $index => $image) {
                    // 生成唯一文件名(不带扩展名)
                    $baseFilename = uniqid('item_');
                    $extension = $image->getClientOriginalExtension();
                    
                    // 原始图片文件名
                    $originalFilename = $baseFilename . '_original.' . $extension;
                    // 缩略图文件名
                    $thumbnailFilename = $baseFilename . '_thumb.' . $extension;
                    
                    // 保存原始图片
                    $originalPath = $image->storeAs('items', $originalFilename, 'public');
                    
                    // 使用 Intervention Image 创建缩略图
                    $interventionImage = $manager->read($image);
                    // 按照原始比例缩放，宽度设为300
                    $interventionImage->scale(width: 300);
                    
                    // 保存缩略图
                    $thumbnailPath = 'items/' . $thumbnailFilename;
                    Storage::disk('public')->put(
                        $thumbnailPath, 
                        $interventionImage->toJpg()->toString()
                    );

                    // 验证文件是否成功保存
                    if (!Storage::disk('public')->exists($originalPath) || 
                        !Storage::disk('public')->exists($thumbnailPath)) {
                        throw new \Exception("图片保存失败");
                    }

                    // 创建图片记录
                    \DB::table('item_images')->insert([
                        'item_id' => $item->id,
                        'path' => $originalPath,
                        'thumbnail_path' => $thumbnailPath,
                        'is_primary' => $index == $primaryIndex,
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('items.index')->with('success', '物品更新成功！');
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
            ->where('is_public', true)
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
        return redirect()->route('items.index')->with('success', '物品已删除！');
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function destroyImage(ItemImage $image)
    {
        // 确保当前用户有权限删除这张图片
        if ($image->item->user_id !== auth()->id()) {
            abort(403);
        }

        // 如果是主图，直接阻止删除
        if ($image->is_primary) {
            return back()->with('error', '不能删除主图，请先设置其他图片为主图');
        }

        // 删除原始图片和缩略图
        Storage::disk('public')->delete($image->path);
        Storage::disk('public')->delete($image->thumbnail_path);

        // 删除数据库记录
        $image->delete();

        return back()->with('success', '图片已删除');
    }

    public function setPrimary(ItemImage $image)
    {
        // 确保当前用户有权限修改这张图片
        if ($image->item->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // 将该物品的所有图片设为非主图
            ItemImage::where('item_id', $image->item_id)
                ->update(['is_primary' => false]);

            // 将当前图片设为主图
            $image->update(['is_primary' => true]);

            DB::commit();
            return back()->with('success', '主图设置成功');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '设置主图失败：' . $e->getMessage());
        }
    }
} 