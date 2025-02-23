<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('我的物品管理') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 添加物品表单 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">添加新物品</h3>
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">物品名称 <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                            </div>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">数量 <span class="text-red-500">*</span></label>
                                <input type="number" name="quantity" id="quantity" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    min="1" value="1" required>
                            </div>

                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700">购买时间</label>
                                <input type="date" name="purchase_date" id="purchase_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    max="{{ date('Y-m-d') }}">
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">过期时间</label>
                                <input type="date" name="expiry_date" id="expiry_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
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
                                        step="0.01" min="0">
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
                                        autocomplete="off">
                                    <datalist id="category-list">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->name }}" data-id="{{ $category->id }}">
                                        @endforeach
                                    </datalist>
                                    <input type="hidden" name="category_id" id="category_id">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">输入新分类名称或从已有分类中选择</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">存放地点</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-1">
                                    <!-- 区域选择/输入 -->
                                    <div>
                                        <input type="text" 
                                            id="area_input" 
                                            list="area-list"
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
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="选择或输入具体位置"
                                            autocomplete="off">
                                        <datalist id="spot-list">
                                        </datalist>
                                    </div>
                                </div>
                                <input type="hidden" name="location_input" id="location_input">
                                <input type="hidden" name="spot_id" id="spot_id">
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">描述</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                        name="is_public" 
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        value="1">
                                    <span class="ml-2 text-sm text-gray-600">公开展示在广场</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">其他用户将可以在广场看到这个物品</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">物品图片 <span class="text-red-500">*</span></label>
                                <div class="mt-2 grid grid-cols-6 gap-4" id="imagePreviewGrid">
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
                                                点击上传
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <input type="hidden" name="primary_image" id="primaryImage" value="0">
                                <div id="imageError" class="mt-2 text-sm text-red-600 hidden"></div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                添加物品
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 物品列表部分 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">我的物品</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($items as $item)
                            <div class="border rounded-lg overflow-hidden flex">
                                <!-- 左侧图片 -->
                                <div class="w-1/3">
                                    @if($item->primaryImage)
                                        <img src="{{ asset('storage/' . $item->primaryImage->path) }}" 
                                            alt="{{ $item->name }}" 
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- 右侧信息 -->
                                <div class="w-2/3 p-4">
                                    <a href="{{ route('items.show', $item) }}" class="block">
                                        <h4 class="text-lg font-semibold">{{ $item->name }}</h4>
                                        <!-- 分类信息 -->
                                        @if($item->category)
                                            <div class="text-sm text-gray-500 mt-1">
                                                分类: {{ $item->category->name }}
                                            </div>
                                        @endif
                                        <div class="text-sm text-gray-500 mt-1">添加：{{ $item->created_at_diff }}</div>
                                        @if($item->expiry_date)
                                            <div class="text-sm {{ strtotime($item->expiry_date) < time() ? 'text-red-500' : 'text-gray-500' }} mt-1">
                                                过期时间: {{ date('Y-m-d', strtotime($item->expiry_date)) }}
                                                @if(strtotime($item->expiry_date) < time())
                                                    (已过期)
                                                @endif
                                            </div>
                                        @endif
                                    </a>
                                    <div class="mt-2 flex justify-end space-x-2">
                                        <a href="{{ route('items.edit', $item) }}" 
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                            编辑
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="md:col-span-2 lg:col-span-3 text-center py-8 text-gray-500">
                                还没有添加任何物品
                            </div>
                        @endforelse
                    </div>

                    <!-- 分页链接 -->
                    <div class="mt-6">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
// 创建一个数组来存储已上传的文件
let uploadedFiles = [];
let fileCount = 0;

function handleImageSelect(input) {
    const files = input.files;
    const previewGrid = document.getElementById('imagePreviewGrid');
    const uploadLabel = previewGrid.querySelector('label');
    
    // 处理每个选择的文件
    Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('div');
            preview.className = 'relative border-2 border-gray-300 rounded-lg overflow-hidden cursor-pointer image-preview w-[120px] h-[120px]';
            
            // 使用累积的文件计数作为索引
            const currentIndex = fileCount;
            preview.setAttribute('data-index', currentIndex);
            
            // 创建隐藏的文件输入
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'file';
            hiddenInput.name = `images[]`;
            hiddenInput.classList.add('hidden');
            hiddenInput.id = `image-${currentIndex}`;
            
            // 创建新的 DataTransfer 对象并添加文件
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            hiddenInput.files = dataTransfer.files;
            
            // 存储文件信息
            uploadedFiles.push({
                index: currentIndex,
                input: hiddenInput
            });
            
            fileCount++;
            
            preview.onclick = function() {
                document.getElementById('primaryImage').value = currentIndex;
                document.querySelectorAll('.image-preview').forEach(p => {
                    p.classList.remove('ring-2', 'ring-blue-500');
                });
                preview.classList.add('ring-2', 'ring-blue-500');
            };

            preview.innerHTML = `
                <div class="w-full h-full">
                    <img src="${e.target.result}" class="w-full h-full object-cover" alt="预览图片">
                </div>
                <div class="absolute top-1 right-1 bg-white rounded-full p-1 shadow flex gap-1">
                    ${currentIndex === parseInt(document.getElementById('primaryImage').value) ? 
                        '<span class="text-blue-500 text-xs">主图</span>' : ''}
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removeImage(${currentIndex}, this.parentElement.parentElement)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;
            
            // 添加隐藏的文件输入
            preview.appendChild(hiddenInput);
            
            // 将新的预览插入到上传按钮之前
            previewGrid.insertBefore(preview, uploadLabel);

            // 如果是第一张图片，自动设置为主图
            if (fileCount === 1) {
                document.getElementById('primaryImage').value = 0;
                preview.classList.add('ring-2', 'ring-blue-500');
            }
        };
        reader.readAsDataURL(file);
    });

    // 清空 input，这样可以重复选择相同的文件
    input.value = '';
}

// 删除图片的函数
function removeImage(index, previewElement) {
    // 从数组中移除文件
    uploadedFiles = uploadedFiles.filter(file => file.index !== index);
    fileCount--;
    
    // 移除预览元素
    previewElement.remove();
    
    // 更新其他预览元素的索引
    document.querySelectorAll('.image-preview').forEach((preview, newIndex) => {
        preview.setAttribute('data-index', newIndex);
    });
    
    // 如果删除的是主图，将第一张图片设为主图
    const primaryImageIndex = parseInt(document.getElementById('primaryImage').value);
    if (primaryImageIndex === index) {
        const firstPreview = document.querySelector('.image-preview');
        if (firstPreview) {
            document.getElementById('primaryImage').value = firstPreview.getAttribute('data-index');
            firstPreview.classList.add('ring-2', 'ring-blue-500');
        } else {
            document.getElementById('primaryImage').value = 0;
        }
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
</script>
