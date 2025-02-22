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
                                <label for="name" class="block text-sm font-medium text-gray-700">物品名称</label>
                                <input type="text" name="name" id="name" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                            </div>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">数量</label>
                                <input type="number" name="quantity" id="quantity" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    min="1" value="1" required>
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">过期时间</label>
                                <input type="date" name="expiry_date" id="expiry_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    min="{{ date('Y-m-d') }}">
                                <p class="mt-1 text-sm text-gray-500">可选，留空表示无过期时间</p>
                            </div>

                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700">购买时间</label>
                                <input type="date" name="purchase_date" id="purchase_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    max="{{ date('Y-m-d') }}">
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
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <select name="category_id" id="category_select" 
                                        class="rounded-l-md border-r-0 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">选择或输入新分类</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="new_category" name="new_category" 
                                        class="hidden rounded-r-md block w-full border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="输入新分类名称">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">选择已有分类或输入新分类名称</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">描述</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">物品图片</label>
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
                                        <div class="text-sm text-gray-500 mt-1">数量: {{ $item->quantity }}</div>
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

document.getElementById('category_select').addEventListener('change', function() {
    const newCategoryInput = document.getElementById('new_category');
    if (this.value === '') {
        newCategoryInput.classList.remove('hidden');
        this.classList.add('rounded-r-none');
    } else {
        newCategoryInput.classList.add('hidden');
        this.classList.remove('rounded-r-none');
        newCategoryInput.value = ''; // 清空输入
    }
});
</script>
