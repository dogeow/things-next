<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('编辑物品') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">编辑物品信息</h3>
                        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">返回列表</a>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">物品名称 <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('name', $item->name) }}"
                                    required>
                            </div>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">数量 <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" id="quantity" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    min="1" value="{{ old('quantity', $item->quantity) }}" required>
                            </div>

                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700">购买时间</label>
                                <input type="date" name="purchase_date" id="purchase_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('purchase_date', $item->purchase_date) }}"
                                    max="{{ date('Y-m-d') }}">
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">过期时间</label>
                                <input type="date" name="expiry_date" id="expiry_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    value="{{ old('expiry_date', $item->expiry_date) }}"
                                    min="{{ date('Y-m-d') }}">
                            </div>

                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700">购买价格</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">¥</span>
                                    </div>
                                    <input type="number" name="purchase_price" id="purchase_price" 
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        step="0.01" min="0"
                                        value="{{ old('purchase_price', $item->purchase_price) }}">
                                </div>
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">分类</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="category" 
                                        name="new_category" 
                                        list="category-list"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="输入或选择分类"
                                        value="{{ old('new_category', $item->category ? $item->category->name : '') }}"
                                        autocomplete="off">
                                    <datalist id="category-list">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}" data-id="{{ $category->id }}">
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" name="category_id" id="category_id" value="{{ $item->category_id }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">输入新分类名称或从已有分类中选择</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">存放地点</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-1">
                                    <div>
                                        <input type="text" 
                                            id="area_input" 
                                            list="area-list"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="选择或输入区域"
                                            value="{{ $item->spot ? explode('/', $item->spot->full_name)[0] : '' }}"
                                            autocomplete="off">
                                        <datalist id="area-list">
                                            @foreach($locations as $location)
                                                <option value="{{ $location['area'] }}">
                                            @endforeach
                                        </datalist>
                                    </div>

                                    <div>
                                        <input type="text" 
                                            id="room_input" 
                                            list="room-list"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="选择或输入房间"
                                            value="{{ $item->spot ? explode('/', $item->spot->full_name)[1] : '' }}"
                                            autocomplete="off">
                                        <datalist id="room-list"></datalist>
                                    </div>

                                    <div>
                                        <input type="text" 
                                            id="spot_input" 
                                            list="spot-list"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="选择或输入具体位置"
                                            value="{{ $item->spot ? explode('/', $item->spot->full_name)[2] : '' }}"
                                            autocomplete="off">
                                        <datalist id="spot-list"></datalist>
                                    </div>
                                </div>
                                <input type="hidden" name="location_input" id="location_input" value="{{ $item->spot ? $item->spot->full_name : '' }}">
                                <input type="hidden" name="spot_id" id="spot_id" value="{{ $item->spot_id }}">
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">描述</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $item->description) }}</textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">物品图片</label>
                                <div class="mt-2 grid grid-cols-6 gap-4" id="imagePreviewGrid">
                                    @foreach($item->images as $index => $image)
                                        <div class="relative border-2 border-gray-300 rounded-lg overflow-hidden cursor-pointer image-preview w-[120px] h-[120px]" data-index="{{ $index }}">
                                            <div class="w-full h-full">
                                                <img src="{{ asset('storage/' . $image->path) }}" class="w-full h-full object-cover" alt="物品图片">
                                            </div>
                                            <div class="absolute top-1 right-1 bg-white rounded-full p-1 shadow flex gap-1 tag-container">
                                                @if($image->is_primary)
                                                    <span class="text-blue-500 text-xs main-tag">主图</span>
                                                @endif
                                                <button type="button" class="text-red-500 hover:text-red-700" onclick="removeImage({{ $index }}, this.parentElement.parentElement)">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                                        </div>
                                    @endforeach
                                    
                                    <label class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors cursor-pointer w-[120px] h-[120px] flex flex-col items-center justify-center">
                                        <input type="file" 
                                            name="images[]" 
                                            id="images"
                                            multiple 
                                            accept="image/*" 
                                            class="hidden"
                                            onchange="handleImageSelect(this)">
                                        <div class="space-y-1">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="text-sm text-gray-600">
                                                添加更多图片
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <input type="hidden" name="primary_image" id="primaryImage" value="{{ $item->primaryImage ? $item->primaryImage->id : 0 }}">
                                <div id="imageError" class="mt-2 text-sm text-red-600 hidden"></div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-between">
                            <button type="button" 
                                onclick="if(confirm('确定要删除这个物品吗？')) { document.getElementById('delete-form').submit(); }"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                删除物品
                            </button>
                            <div class="flex gap-4">
                                <a href="{{ route('dashboard') }}" 
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    取消
                                </a>
                                <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    保存修改
                                </button>
                            </div>
                        </div>
                    </form>

                    <form id="delete-form" action="{{ route('items.destroy', $item) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
// 复用 dashboard.blade.php 中的 JavaScript 代码
// ... 这里需要复制 dashboard.blade.php 中的所有 JavaScript 代码 ...
</script>
