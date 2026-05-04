<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

use App\Http\Controllers\Admin\TourController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/tours/create', [TourController::class, 'create'])->name('admin.tours.create');
    Route::post('/tours', [TourController::class, 'store'])->name('admin.tours.store');
    Route::get('/tours/{id}/schedules', [TourController::class, 'schedules'])->name('admin.tours.schedules');
    Route::post('/tours/{id}/schedules', [TourController::class, 'storeSchedule'])->name('admin.tours.schedules.store');
    Route::get('/tours', [TourController::class, 'index'])->name('admin.tours.index');
    // Trong nhóm Route::prefix('admin')->middleware(['auth'])->group(function () { ... })

    // Route hiển thị trang danh sách ảnh của Tour
    Route::get('/tours/{id}/images', [TourController::class, 'images'])->name('admin.tours.images');

    // Các route phụ liên quan (nếu chưa có)
    Route::post('/tours/{id}/images', [TourController::class, 'storeImages'])->name('admin.tours.images.store');
    Route::post('/tours/{tourId}/images/{imageId}/set-primary', [TourController::class, 'setPrimaryImage'])->name('admin.tours.images.set-primary');
});
