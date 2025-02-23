<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ItemController::class, 'plaza'])->name('plaza');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 图片删除路由必须放在 resource 路由之前
    Route::delete('/items/images/{image}', [ItemController::class, 'destroyImage'])
        ->name('items.images.destroy')
        ->where('image', '[0-9]+'); // 添加约束，确保 image 是数字

    // 需要登录的物品管理路由
    Route::resource('items', ItemController::class)->except(['index', 'show']);
    
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::put('/items/images/{image}/set-primary', [ItemController::class, 'setPrimary'])
        ->name('items.images.set-primary');

    Route::get('/stats', [StatsController::class, 'index'])->name('stats');
});

// 公开的物品路由
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

require __DIR__.'/auth.php';
