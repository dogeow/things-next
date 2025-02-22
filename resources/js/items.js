window.handleImageSelect = function(input) {
    const previewGrid = document.getElementById('imagePreviewGrid');
    const files = input.files;

    // 移除上传按钮外的所有预览
    Array.from(previewGrid.children).forEach(child => {
        if (!child.classList.contains('upload-button')) {
            child.remove();
        }
    });

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
            previewGrid.insertBefore(preview, previewGrid.firstChild);
        };
        reader.readAsDataURL(file);
    });
}

window.setPrimaryImage = function(index) {
    document.getElementById('primaryImage').value = index;
    
    // 更新UI显示
    const previewGrid = document.getElementById('imagePreviewGrid');
    Array.from(previewGrid.children).forEach(child => {
        if (child.classList.contains('upload-button')) return;
        const badge = child.querySelector('.bg-blue-500');
        if (badge) badge.remove();
    });

    const previews = Array.from(previewGrid.children).filter(child => !child.classList.contains('upload-button'));
    if (previews[index]) {
        const badge = document.createElement('div');
        badge.className = 'absolute top-2 right-2 bg-blue-500 text-white px-2 py-1 rounded-full text-xs';
        badge.textContent = '主图';
        previews[index].appendChild(badge);
    }
} 