<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 添加新分类 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">添加新分类</h3>
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">分类名称</label>
                            <div class="mt-1">
                                <input type="text" 
                                    id="name" 
                                    name="name" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                添加分类
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 分类列表 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">我的分类</h3>
                    <div class="space-y-4">
                        @forelse($categories as $category)
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <h4 class="text-lg font-medium">{{ $category->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $category->items_count }} 个物品</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($category->items_count == 0)
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="text-red-600 hover:text-red-800"
                                                onclick="return confirm('确定要删除这个分类吗？')">
                                                删除
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                还没有添加任何分类
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 