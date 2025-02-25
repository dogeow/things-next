<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-4 lg:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4">
                    <!-- 图片展示区 -->
                    <div class="mb-6">
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                            <!-- 主图 -->
                            <div class="lg:col-span-3">
                                <div class="relative pb-[75%]">
                                    @if($item->primaryImage)
                                        <img src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                             alt="{{ $item->name }}"
                                             class="absolute inset-0 w-full h-full object-contain">
                                    @else
                                        <div class="absolute inset-0 bg-gray-100 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- 其他图片网格 -->
                            <div class="lg:col-span-2">
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($item->images->where('is_primary', false) as $image)
                                        <div class="relative pb-[100%]">
                                            <img src="{{ asset('storage/' . $image->path) }}"
                                                 alt="{{ $item->name }}"
                                                 class="absolute inset-0 w-full h-full object-cover rounded-lg">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 物品信息 -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="lg:col-span-2">
                            <h3 class="text-xl font-bold text-gray-900">{{ $item->name }}</h3>
                            <div class="mt-3">
                                <p class="text-gray-700">{{ $item->description }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="space-y-3">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">数量</h4>
                                    <p class="mt-1 text-base font-semibold text-gray-900">{{ $item->quantity }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">分类</h4>
                                    <p class="mt-1 text-gray-900">{{ $item->category ? $item->category->name : '未分类' }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">购买时间</h4>
                                    <p class="mt-1 text-gray-900">
                                        {{ $item->purchase_date ? date('Y-m-d', strtotime($item->purchase_date)) : '未记录' }}
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">购买价格</h4>
                                    <p class="mt-1 text-gray-900">
                                        {{ $item->purchase_price ? '¥' . number_format($item->purchase_price, 2) : '未记录' }}
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">所有者</h4>
                                    <p class="mt-1 text-base text-gray-900">{{ $item->user->name }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">创建时间</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $item->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">最后更新</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $item->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">过期时间</h4>
                                    @if($item->expiry_date)
                                        <p class="mt-1 text-gray-900">{{ date('Y-m-d', strtotime($item->expiry_date)) }}
                                            @if(strtotime($item->expiry_date) < time())
                                                <span class="text-red-500">(已过期)</span>
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