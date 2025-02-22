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
                                <p class="mt-2 text-sm text-gray-500">点击图片设置为主图</p>
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

            <!-- 我的物品列表 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">我的物品</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse(Auth::user()->items as $item)
                            <div class="flex border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                <!-- 左侧图片 -->
                                <div class="w-1/3 relative">
                                    @if($item->primaryImage)
                                        <img src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                             alt="{{ $item->name }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- 右侧信息 -->
                                <div class="w-2/3 p-4">
                                    <a href="{{ route('items.show', $item) }}" class="block">
                                        <h4 class="text-lg font-semibold">{{ $item->name }}</h4>
                                        <div class="text-sm text-gray-500 mt-1">数量: {{ $item->quantity }}</div>
                                    </a>
                                    <div class="mt-2 flex justify-end">
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
</script>
