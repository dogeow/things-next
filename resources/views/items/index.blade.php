<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('我的物品管理') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 添加物品按钮 -->
            <div class="mb-6">
                <a href="{{ route('items.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    添加新物品
                </a>
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
                                没有找到任何物品
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
