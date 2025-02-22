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
                    <form action="{{ route('items.update', $item) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">物品名称</label>
                                <input type="text" name="name" id="name" 
                                    value="{{ old('name', $item->name) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                            </div>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">数量</label>
                                <input type="number" name="quantity" id="quantity" 
                                    value="{{ old('quantity', $item->quantity) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    min="1" required>
                            </div>

                            <div>
                                <label for="purchase_date" class="block text-sm font-medium text-gray-700">购买时间</label>
                                <input type="date" name="purchase_date" id="purchase_date" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    max="{{ date('Y-m-d') }}"
                                    value="{{ old('purchase_date', $item->purchase_date ? date('Y-m-d', strtotime($item->purchase_date)) : '') }}">
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
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <select name="category_id" id="category_select" 
                                        class="rounded-l-md border-r-0 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">选择或输入新分类</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="text" id="new_category" name="new_category" 
                                        class="hidden rounded-r-md block w-full border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="输入新分类">
                                </div>
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-gray-700 text-sm font-bold mb-2">过期时间</label>
                                <input type="date" name="expiry_date" id="expiry_date" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    min="{{ date('Y-m-d') }}"
                                    value="{{ old('expiry_date', $item->expiry_date ? date('Y-m-d', strtotime($item->expiry_date)) : '') }}">
                                <p class="mt-1 text-sm text-gray-500">可选，留空表示无过期时间</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">描述</label>
                                <textarea name="description" id="description" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $item->description) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end space-x-3">
                            <a href="{{ route('dashboard') }}" 
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                取消
                            </a>
                            <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                更新物品
                            </button>
                        </div>

                        @if ($errors->any())
                            <div class="mt-4">
                                <div class="font-medium text-red-600">
                                    {{ __('提交的表单有错误。') }}
                                </div>

                                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>

                    <!-- 删除物品表单 -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-red-600 font-medium mb-4">危险操作区域</h3>
                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                onclick="return confirm('确定要删除这个物品吗？此操作不可恢复。')">
                                删除物品
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('category_select').addEventListener('change', function() {
        const newCategoryInput = document.getElementById('new_category');
        if (this.value === '') {
            newCategoryInput.classList.remove('hidden');
            this.classList.add('rounded-r-none');
        } else {
            newCategoryInput.classList.add('hidden');
            this.classList.remove('rounded-r-none');
        }
    });
    </script>
</x-app-layout> 