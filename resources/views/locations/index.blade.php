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
                    <form action="{{ route('locations.store') }}" method="POST" id="locationForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- 区域选择/输入 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">区域</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="area_input" 
                                        list="area-list"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="选择或输入新区域"
                                        autocomplete="off">
                                    <datalist id="area-list">
                                        @foreach($areas as $area)
                                            <option value="{{ $area->name }}">
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <!-- 房间选择/输入 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">房间</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="room_input" 
                                        list="room-list"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="选择或输入新房间"
                                        autocomplete="off">
                                    <datalist id="room-list">
                                    </datalist>
                                </div>
                            </div>

                            <!-- 具体位置输入 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">具体位置</label>
                                <div class="mt-1">
                                    <input type="text" 
                                        id="spot_input" 
                                        list="spot-list"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="选择或输入新位置"
                                        autocomplete="off">
                                    <datalist id="spot-list">
                                    </datalist>
                                </div>
                            </div>
                        </div>

                        <!-- 隐藏的完整路径输入 -->
                        <input type="hidden" name="location_input" id="location_input">

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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const areaInput = document.getElementById('area_input');
        const roomInput = document.getElementById('room_input');
        const spotInput = document.getElementById('spot_input');
        const locationInput = document.getElementById('location_input');
        const form = document.getElementById('locationForm');

        // 存储所有地点数据
        const locations = @json($areas->map(function($area) {
            return [
                'area' => $area->name,
                'rooms' => $area->rooms->map(function($room) {
                    return [
                        'name' => $room->name,
                        'spots' => $room->spots->pluck('name')
                    ];
                })
            ];
        }));

        // 当区域输入变化时更新房间列表
        areaInput.addEventListener('input', function() {
            const area = locations.find(a => a.area === this.value);
            const roomList = document.getElementById('room-list');
            roomList.innerHTML = '';
            
            if (area) {
                area.rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.name;
                    roomList.appendChild(option);
                });
            }

            // 清空房间和位置输入
            if (this.value !== roomInput.getAttribute('data-last-valid')) {
                roomInput.value = '';
                spotInput.value = '';
            }
            roomInput.setAttribute('data-last-valid', this.value);
        });

        // 当房间输入变化时更新位置列表
        roomInput.addEventListener('input', function() {
            const area = locations.find(a => a.area === areaInput.value);
            const room = area?.rooms.find(r => r.name === this.value);
            const spotList = document.getElementById('spot-list');
            spotList.innerHTML = '';
            
            if (room) {
                room.spots.forEach(spot => {
                    const option = document.createElement('option');
                    option.value = spot;
                    spotList.appendChild(option);
                });
            }

            // 清空位置输入
            if (this.value !== spotInput.getAttribute('data-last-valid')) {
                spotInput.value = '';
            }
            spotInput.setAttribute('data-last-valid', this.value);
        });

        // 表单提交前组合完整路径
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!areaInput.value || !roomInput.value || !spotInput.value) {
                alert('请填写完整的地点信息');
                return;
            }

            locationInput.value = `${areaInput.value}/${roomInput.value}/${spotInput.value}`;
            this.submit();
        });
    });
    </script>
</x-app-layout> 