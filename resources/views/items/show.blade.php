<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $item->name }}
            </h2>
            @can('update', $item)
                <a href="{{ route('items.edit', $item) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    编辑物品
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- 图片展示区 -->
                    <div class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- 主图 -->
                            <div class="aspect-w-4 aspect-h-3 rounded-lg overflow-hidden">
                                @if($item->primaryImage)
                                    <img src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                         alt="{{ $item->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- 其他图片网格 -->
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($item->images->where('is_primary', false) as $image)
                                    <div class="aspect-w-1 aspect-h-1 rounded-lg overflow-hidden">
                                        <img src="{{ asset('storage/' . $image->path) }}"
                                             alt="{{ $item->name }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- 物品信息 -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h3>
                            <div class="mt-4 space-y-6">
                                <p class="text-gray-700">{{ $item->description }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">数量</h4>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $item->quantity }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">所有者</h4>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $item->user->name }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">创建时间</h4>
                                    <p class="mt-1 text-gray-900">{{ $item->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">最后更新</h4>
                                    <p class="mt-1 text-gray-900">{{ $item->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">过期时间</h4>
                                    @if($item->expiry_date)
                                        <p class="mt-1 text-gray-900">{{ date('Y-m-d', strtotime($item->expiry_date)) }}
                                            @if(strtotime($item->expiry_date) < time())
                                                (已过期)
                                            @endif
                                        </p>
                                    @else
                                        <p class="mt-1 text-gray-900">无过期时间</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 