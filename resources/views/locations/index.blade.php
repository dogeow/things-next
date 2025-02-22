<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('存放地点管理') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 添加新地点 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">添加新地点</h3>
                    <form action="{{ route('locations.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="location_input" class="block text-sm font-medium text-gray-700">地点路径</label>
                            <div class="mt-1">
                                <input type="text" 
                                    id="location_input" 
                                    name="location_input" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="区域/房间/具体位置">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">格式：区域/房间/具体位置（如：家里/书房/第一个抽屉）</p>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                添加地点
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 地点列表 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">我的地点</h3>
                    <div class="space-y-6">
                        @forelse($areas as $area)
                            <div class="border rounded-lg p-4">
                                <h4 class="text-lg font-medium">{{ $area->name }}</h4>
                                <div class="mt-4 space-y-4">
                                    @foreach($area->rooms as $room)
                                        <div class="ml-4">
                                            <h5 class="text-md font-medium">{{ $room->name }}</h5>
                                            <div class="mt-2 ml-4 space-y-2">
                                                @foreach($room->spots as $spot)
                                                    <div class="flex items-center justify-between">
                                                        <span>{{ $spot->name }}</span>
                                                        <span class="text-sm text-gray-500">
                                                            {{ $spot->items_count ?? 0 }} 个物品
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                还没有添加任何地点
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 