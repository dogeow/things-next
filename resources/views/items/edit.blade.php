<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('编辑物品') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- 添加消息提示 -->
                    @if (session('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- 在主表单之前添加一个隐藏的删除图片表单 -->
                    <form id="deleteImageForm" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>

                    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- 基本信息 -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">物品名称 <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" 
                                    value="{{ old('name', $item->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                            </div>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">数量 <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" id="quantity" 
                                    value="{{ old('quantity', $item->quantity) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    min="1" required>
                            </div>

                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700">购买时间</label>
                                <input type="date" name="purchase_date" id="purchase_date" 
                                    value="{{ old('purchase_date', $item->purchase_date?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    max="{{ date('Y-m-d') }}">
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">过期时间</label>
                                <input type="date" name="expiry_date" id="expiry_date" 
                                    value="{{ old('expiry_date', $item->expiry_date?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700">购买价格</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">¥</span>
                                    </div>
                                    <input type="number" name="purchase_price" id="purchase_price" 
                                        value="{{ old('purchase_price', $item->purchase_price) }}"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        step="0.01" min="0">
                                </div>
                            </div>

                            <!-- 分类选择 -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">分类</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="category" 
                                        name="new_category" 
                                        list="category-list"
                                        value="{{ old('new_category', $item->category?->name) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="输入或选择分类（可选）"
                                        autocomplete="off">
                                    <datalist id="category-list">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}" data-id="{{ $category->id }}">
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id', $item->category_id) }}">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">留空将使用默认分类</p>
                            </div>

                            <!-- 地点选择 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">存放地点（可选）</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-1">
                                    <!-- 区域选择/输入 -->
                                    <div>
                                        <input type="text" 
                                            id="area_input" 
                                            list="area-list"
                                            value="{{ $item->spot?->room?->area?->name }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="选择或输入区域"
                                            autocomplete="off">
                                        <datalist id="area-list">
                                            @foreach($locations as $location)
                                                <option value="{{ $location['area'] }}">
                                            @endforeach
                                        </datalist>
                                    </div>

                                    <!-- 房间选择/输入 -->
                                    <div>
                                        <input type="text" 
                                            id="room_input" 
                                            list="room-list"
                                            value="{{ $item->spot?->room?->name }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="选择或输入房间"
                                            autocomplete="off">
                                        <datalist id="room-list">
                                        </datalist>
                                    </div>

                                    <!-- 具体位置输入 -->
                                    <div>
                                        <input type="text" 
                                            id="spot_input" 
                                            list="spot-list"
                                            value="{{ $item->spot?->name }}"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="选择或输入具体位置"
                                            autocomplete="off">
                                        <datalist id="spot-list">
                                        </datalist>
                                    </div>
                                </div>
                                <input type="hidden" name="location_input" id="location_input" 
                                    value="{{ $item->spot ? $item->spot->room->area->name . '/' . $item->spot->room->name . '/' . $item->spot->name : '' }}">
                                <input type="hidden" name="spot_id" id="spot_id" value="{{ $item->spot_id }}">
                            </div>

                            <!-- 描述 -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">描述</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $item->description) }}</textarea>
                            </div>

                            <!-- 图片管理部分 -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">物品图片</label>
                                
                                <!-- 主表单内的图片显示 -->
                                <div class="mt-2 grid grid-cols-6 gap-4" id="existingImages">
                                    @foreach($item->images as $image)
                                        <div class="relative border-2 {{ $image->is_primary ? 'border-blue-500' : 'border-gray-300' }} rounded-lg overflow-hidden">
                                            <img src="{{ asset('storage/' . $image->path) }}" 
                                                class="w-full h-24 object-cover" 
                                                alt="{{ $item->name }}">
                                            <div class="absolute top-1 right-1 bg-white rounded-full p-1 shadow flex gap-1">
                                                @if($image->is_primary)
                                                    <span class="text-blue-500 text-xs">主图</span>
                                                @endif
                                                <button type="button" 
                                                    onclick="deleteImage('{{ route('items.images.destroy', $image->id) }}')"
                                                    class="text-red-500 hover:text-red-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- 新图片上传 -->
                                <div class="mt-4 grid grid-cols-6 gap-4" id="imagePreviewGrid">
                                    <label class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors cursor-pointer w-[120px] h-[120px] flex flex-col items-center justify-center">
                                        <input type="file" 
                                            name="images[]" 
                                            multiple 
                                            accept="image/*" 
                                            class="hidden"
                                            onchange="handleImageSelect(this)">
                                        <div class="space-y-1">
                                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="text-sm text-gray-600">
                                                添加图片
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <input type="hidden" name="primary_image" id="primaryImage" value="0">
                                <div id="imageError" class="mt-2 text-sm text-red-600 hidden"></div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end space-x-3">
                            <a href="{{ route('dashboard') }}" 
                                class="px-4 py-2 border rounded-md hover:bg-gray-50">
                                取消
                            </a>
                            <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                保存修改
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- ... 原有的脚本代码 ... -->
    <script>
    // 创建一个数组来存储已上传的文件
    let uploadedFiles = [];
    let fileCount = 0;

    function handleImageSelect(input) {
        const files = input.files;
        const previewGrid = document.getElementById('imagePreviewGrid');
        const uploadLabel = previewGrid.querySelector('label');
        
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'relative border-2 border-gray-300 rounded-lg overflow-hidden cursor-pointer image-preview w-[120px] h-[120px]';
                
                const currentIndex = fileCount;
                preview.setAttribute('data-index', currentIndex);
                
                // 创建隐藏的文件输入
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'file';
                hiddenInput.name = `images[]`;
                hiddenInput.classList.add('hidden');
                hiddenInput.id = `image-${currentIndex}`;
                
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                hiddenInput.files = dataTransfer.files;
                
                uploadedFiles.push({
                    index: currentIndex,
                    input: hiddenInput
                });
                
                fileCount++;

                // 检查是否是第一张图片
                const isFirstImage = document.querySelectorAll('.image-preview').length === 0;
                
                preview.innerHTML = `
                    <div class="w-full h-full">
                        <img src="${e.target.result}" class="w-full h-full object-cover" alt="预览图片">
                    </div>
                    <div class="absolute top-1 right-1 bg-white rounded-full p-1 shadow flex gap-1 tag-container">
                        ${isFirstImage ? '<span class="text-blue-500 text-xs main-tag">主图</span>' : ''}
                        <button type="button" class="text-red-500 hover:text-red-700" onclick="removeImage(${currentIndex}, this.parentElement.parentElement)">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                preview.appendChild(hiddenInput);
                previewGrid.insertBefore(preview, uploadLabel);

                // 如果是第一张图片，添加选中状态
                if (isFirstImage) {
                    document.getElementById('primaryImage').value = 0;
                    preview.classList.add('ring-2', 'ring-blue-500');
                }
            };
            reader.readAsDataURL(file);
        });

        input.value = '';
    }

    // 删除图片的函数
    function removeImage(index, previewElement) {
        event.stopPropagation();
        
        uploadedFiles = uploadedFiles.filter(file => file.index !== index);
        fileCount--;
        
        // 移除当前预览元素
        previewElement.remove();
        
        // 更新所有预览元素的索引和主图状态
        const previews = document.querySelectorAll('.image-preview');
        previews.forEach((preview, newIndex) => {
            preview.setAttribute('data-index', newIndex);
            
            // 更新主图标识
            const tagContainer = preview.querySelector('.tag-container');
            const isFirstImage = newIndex === 0;
            
            if (tagContainer) {
                tagContainer.innerHTML = `
                    ${isFirstImage ? '<span class="text-blue-500 text-xs main-tag">主图</span>' : ''}
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removeImage(${newIndex}, this.parentElement.parentElement)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
            }
            
            // 更新选中状态
            if (isFirstImage) {
                preview.classList.add('ring-2', 'ring-blue-500');
                document.getElementById('primaryImage').value = newIndex;
            } else {
                preview.classList.remove('ring-2', 'ring-blue-500');
            }
        });
        
        // 如果没有图片了，重置主图索引
        if (previews.length === 0) {
            document.getElementById('primaryImage').value = 0;
        }
    }

    // 表单提交前的验证
    document.querySelector('form').addEventListener('submit', function(e) {
        const errorDiv = document.getElementById('imageError');
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
        
        if (fileCount === 0) {
            e.preventDefault();
            errorDiv.textContent = '请至少上传一张图片';
            errorDiv.classList.remove('hidden');
            return false;
        }
    });

    document.getElementById('category').addEventListener('input', function(e) {
        const input = e.target;
        const datalist = document.getElementById('category-list');
        const categoryIdInput = document.getElementById('category_id');
        
        // 获取所有选项
        const options = Array.from(datalist.options);
        
        // 查找是否匹配已有分类
        const matchingOption = options.find(option => option.value === input.value);
        
        if (matchingOption) {
            // 如果匹配到已有分类，设置 category_id
            categoryIdInput.value = matchingOption.dataset.id;
        } else {
            // 如果是新分类，清空 category_id
            categoryIdInput.value = '';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const areaInput = document.getElementById('area_input');
        const roomInput = document.getElementById('room_input');
        const spotInput = document.getElementById('spot_input');
        const locationInput = document.getElementById('location_input');
        const spotIdInput = document.getElementById('spot_id');

        // 存储所有地点数据
        const locations = @json($locations);
        
        // 更新房间列表的函数
        function updateRoomList(areaName) {
            const area = locations.find(a => a.area === areaName);
            const roomList = document.getElementById('room-list');
            roomList.innerHTML = '';
            
            if (area && area.rooms) {
                area.rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.name;
                    roomList.appendChild(option);
                });
            }
        }

        // 更新位置列表的函数
        function updateSpotList(areaName, roomName) {
            const area = locations.find(a => a.area === areaName);
            const room = area?.rooms?.find(r => r.name === roomName);
            const spotList = document.getElementById('spot-list');
            spotList.innerHTML = '';
            
            if (room && room.spots) {
                room.spots.forEach(spot => {
                    const option = document.createElement('option');
                    option.value = spot.name;
                    option.setAttribute('data-id', spot.id);
                    spotList.appendChild(option);
                });
            }
        }

        // 初始化地点选择
        if (areaInput.value) {
            updateRoomList(areaInput.value);
            if (roomInput.value) {
                updateSpotList(areaInput.value, roomInput.value);
            }
        }

        // 区域输入事件监听
        areaInput.addEventListener('change', function() {
            updateRoomList(this.value);
            roomInput.value = '';
            spotInput.value = '';
            spotIdInput.value = '';
        });

        areaInput.addEventListener('input', function() {
            this.dispatchEvent(new Event('change'));
        });

        // 房间输入事件监听
        roomInput.addEventListener('change', function() {
            updateSpotList(areaInput.value, this.value);
            spotInput.value = '';
            spotIdInput.value = '';
        });

        roomInput.addEventListener('input', function() {
            this.dispatchEvent(new Event('change'));
        });

        // 位置输入事件监听
        spotInput.addEventListener('input', function() {
            const area = locations.find(a => a.area === areaInput.value);
            const room = area?.rooms?.find(r => r.name === roomInput.value);
            const spot = room?.spots?.find(s => s.name === this.value);
            
            if (spot) {
                spotIdInput.value = spot.id;
            } else {
                spotIdInput.value = '';
            }
            locationInput.value = `${areaInput.value}/${roomInput.value}/${this.value}`;
        });

        // 调试日志
        console.log('Initial values:', {
            area: areaInput.value,
            room: roomInput.value,
            spot: spotInput.value,
            locations: locations
        });
    });

    function deleteImage(url) {
        if (confirm('确定要删除这张图片吗？')) {
            const form = document.getElementById('deleteImageForm');
            form.action = url;
            form.submit();
        }
    }
    </script>
</x-app-layout>
