<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

       <!-- Styles -->
       <script src="https://cdn.tailwindcss.com"></script>
       <!-- Alpine.js -->
       <script defer src="https://unpkg.com/browse/alpinejs@3.14.8/dist/cdn.min.js"></script>
       <script>
           function handleImageSelect(input) {
               const previewGrid = document.getElementById('imagePreviewGrid');
               const files = input.files;
           
               // 保存上传按钮
               const uploadButton = previewGrid.firstElementChild;
               // 清空预览网格
               previewGrid.innerHTML = '';
               // 重新添加上传按钮
               previewGrid.appendChild(uploadButton);
           
               // 添加新的预览
               Array.from(files).forEach((file, index) => {
                   const reader = new FileReader();
                   reader.onload = function(e) {
                       const preview = document.createElement('div');
                       preview.className = 'relative border rounded-lg overflow-hidden group';
                       preview.innerHTML = `
                           <img src="${e.target.result}" class="w-full h-32 object-cover">
                           <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                               <button type="button" onclick="setPrimaryImage(${index})" class="px-3 py-1 bg-white rounded-full text-sm">
                                   设为主图
                               </button>
                           </div>
                           ${index === parseInt(document.getElementById('primaryImage').value) ? 
                               '<div class="absolute top-2 right-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs">主图</div>' : 
                               ''}
                       `;
                       previewGrid.insertBefore(preview, uploadButton);
                   };
                   reader.readAsDataURL(file);
               });
           }
           
           function setPrimaryImage(index) {
               document.getElementById('primaryImage').value = index;
               
               // 更新UI显示
               const previewGrid = document.getElementById('imagePreviewGrid');
               // 移除所有主图标记
               previewGrid.querySelectorAll('.bg-blue-500').forEach(badge => badge.remove());
               
               // 获取所有预览（除了上传按钮）
               const previews = Array.from(previewGrid.children).slice(0, -1);
               if (previews[index]) {
                   const badge = document.createElement('div');
                   badge.className = 'absolute top-2 right-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs';
                   badge.textContent = '主图';
                   previews[index].appendChild(badge);
               }
           }
       </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
