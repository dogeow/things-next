<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 统计数据 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">总物品数</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalItems }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">用户数</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">今日新增物品</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ $todayItems }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 最近添加的物品 -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">最新物品</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($recentItems as $item)
                                <div class="border rounded-lg p-4">
                                    <div class="flex gap-4">
                                        <!-- 物品图片 -->
                                        <div class="w-24 h-24 flex-shrink-0">
                                            @if($item->primaryImage)
                                                <img src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                                     alt="{{ $item->name }}"
                                                     class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <!-- 物品信息 -->
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold">{{ $item->name }}</h3>
                                            <p class="text-gray-600 line-clamp-2">{{ $item->description }}</p>
                                            <div class="mt-2 flex justify-between items-center">
                                                <span class="text-sm text-gray-500">
                                                    by {{ $item->user->name }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    {{ $item->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('items.index') }}"
                               class="text-blue-600 hover:text-blue-800">
                                查看所有物品 →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 活跃用户排行 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">活跃用户</h2>
                    <div class="space-y-4">
                        @foreach($topUsers as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="text-lg font-semibold text-gray-700">
                                        {{ $user->name }}
                                    </span>
                                </div>
                                <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                    {{ $user->items_count }} 件物品
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
