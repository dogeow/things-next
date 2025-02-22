<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @auth
                        <div class="mb-4">
                            <a href="{{ route('dashboard') }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                添加新物品
                            </a>
                        </div>
                    @endauth

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($items as $item)
                            <div class="border rounded-lg p-4">
                                <h3 class="text-lg font-semibold">{{ $item->name }}</h3>
                                <p class="text-gray-600">{{ $item->description }}</p>
                                <p class="text-sm text-gray-500">数量: {{ $item->quantity }}</p>
                                <p class="text-sm text-gray-500">所有者: {{ $item->user->name }}</p>

                                @can('update', $item)
                                    <div class="mt-4">
                                        <a href="{{ route('items.edit', $item) }}"
                                           class="text-blue-600 hover:text-blue-800">
                                            编辑
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
