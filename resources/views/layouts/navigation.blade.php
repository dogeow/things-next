<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('items.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('plaza')" :active="request()->routeIs('plaza')">
                        {{ __('广场') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('items.index')" :active="request()->routeIs('items.index')">
                            {{ __('我的物品') }}
                        </x-nav-link>
                        <x-nav-link :href="route('locations.index')" :active="request()->routeIs('locations.*')">
                            {{ __('存放地点') }}
                        </x-nav-link>
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                            {{ __('分类管理') }}
                        </x-nav-link>
                        <x-nav-link :href="route('stats')" :active="request()->routeIs('stats')">
                            {{ __('物品统计') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- 添加搜索框 -->
                <div class="relative mx-4">
                    <form action="{{ route('items.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" 
                                   id="searchInput"
                                   name="search" 
                                   placeholder="搜索物品..." 
                                   value="{{ request('search') }}"
                                   class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pr-16"
                                   onkeypress="if(event.keyCode == 13) { this.form.submit(); return false; }">
                            
                            <!-- X 按钮 (仅在有输入时显示) -->
                            <button type="button" 
                                    id="clearSearchBtn"
                                    onclick="clearSearch()"
                                    class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                    style="{{ request('search') ? '' : 'display: none;' }}">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            
                            <!-- 搜索按钮 -->
                            <button type="submit" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('个人资料') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                    this.closest('form').submit();">
                                    {{ __('退出登录') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">登录</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-gray-900">注册</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('plaza')" :active="request()->routeIs('plaza')">
                {{ __('广场') }}
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('items.index')" :active="request()->routeIs('items.index')">
                    {{ __('我的物品') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('locations.index')" :active="request()->routeIs('locations.*')">
                    {{ __('存放地点') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    {{ __('分类管理') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('stats')" :active="request()->routeIs('stats')">
                    {{ __('物品统计') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- 响应式搜索框 -->
        <div class="px-4 py-2 border-t border-gray-200">
            <form action="{{ route('items.index') }}" method="GET">
                <div class="relative">
                    <input type="text" 
                           id="mobileSearchInput"
                           name="search" 
                           placeholder="搜索物品..." 
                           value="{{ request('search') }}"
                           class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 pr-16"
                           onkeypress="if(event.keyCode == 13) { this.form.submit(); return false; }">
                    
                    <!-- X 按钮 (仅在有输入时显示) -->
                    <button type="button" 
                            id="mobileClearSearchBtn"
                            onclick="clearMobileSearch()"
                            class="absolute right-8 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            style="{{ request('search') ? '' : 'display: none;' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    
                    <!-- 搜索按钮 -->
                    <button type="submit" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('个人资料') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                            this.closest('form').submit();">
                            {{ __('退出登录') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ __('登录') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('注册') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const mobileSearchInput = document.getElementById('mobileSearchInput');
        const mobileClearSearchBtn = document.getElementById('mobileClearSearchBtn');
        
        // 监听输入变化 - 桌面版
        searchInput.addEventListener('input', function() {
            clearSearchBtn.style.display = this.value.length > 0 ? 'block' : 'none';
        });
        
        // 监听输入变化 - 移动版
        mobileSearchInput.addEventListener('input', function() {
            mobileClearSearchBtn.style.display = this.value.length > 0 ? 'block' : 'none';
        });
    });
    
    function clearSearch() {
        const searchInput = document.getElementById('searchInput');
        searchInput.value = '';
        document.getElementById('clearSearchBtn').style.display = 'none';
        searchInput.focus();
    }
    
    function clearMobileSearch() {
        const mobileSearchInput = document.getElementById('mobileSearchInput');
        mobileSearchInput.value = '';
        document.getElementById('mobileClearSearchBtn').style.display = 'none';
        mobileSearchInput.focus();
    }
</script>
