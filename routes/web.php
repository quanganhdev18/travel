<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/debug-schema', function() {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('tours');
    return implode(', ', $columns);
});
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tour-tron-goi', [HomeController::class, 'tours'])->name('frontend.tours.index');
Route::get('/tours/search', [HomeController::class, 'searchTours'])->name('frontend.tours.search');

use App\Http\Controllers\AppSettingsController;

Route::get('/locale/{locale}', [AppSettingsController::class, 'switchLocale'])->name('locale.switch');
Route::get('/currency/{currency}', [AppSettingsController::class, 'switchCurrency'])->name('currency.switch');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\TourActivityController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\TourItineraryController;
use App\Http\Controllers\Admin\TourGuideController;
use App\Http\Controllers\Admin\OngoingTourController;

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/bookings', [BookingController::class, 'index'])->name('admin.bookings.index');
    Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('admin.bookings.update_status');
    Route::post('/bookings/{id}/pnr', [BookingController::class, 'updatePnr'])->name('admin.bookings.update_pnr');

    // Quản lý Lịch trình (Itineraries)
    Route::get('tours/{tour}/itineraries', [TourItineraryController::class, 'index'])->name('admin.tours.itineraries.index');
    Route::post('tours/{tour}/itineraries', [TourItineraryController::class, 'store'])->name('admin.tours.itineraries.store');
    Route::delete('itineraries/{itinerary}', [TourItineraryController::class, 'destroy'])->name('admin.itineraries.destroy');

    // Quản lý Hoạt động (Activities)
    Route::post('itineraries/{itinerary}/activities', [TourActivityController::class, 'store'])->name('admin.itineraries.activities.store');
    Route::delete('activities/{activity}', [TourActivityController::class, 'destroy'])->name('admin.activities.destroy');
    // Route quản lý Điểm đến
    Route::resource('destinations', DestinationController::class)->except(['show'])->names('admin.destinations');

    // Route quản lý Danh mục
    Route::resource('categories', CategoryController::class)->except(['show'])->names('admin.categories');

    // Route quản lý Banners
    Route::resource('banners', BannerController::class)->except(['show'])->names('admin.banners');

    Route::get('/tours/create', [TourController::class, 'create'])->name('admin.tours.create');
    Route::post('/tours', [TourController::class, 'store'])->name('admin.tours.store');
    // Thùng rác
    Route::get('/tours/trash', [TourController::class, 'trash'])->name('admin.tours.trash');
    Route::post('/tours/{id}/restore', [TourController::class, 'restore'])->name('admin.tours.restore');
    Route::delete('/tours/{id}/force-delete', [TourController::class, 'forceDelete'])->name('admin.tours.force-delete');

    // Sửa và Xóa mềm
    Route::get('/tours/{id}/edit', [TourController::class, 'edit'])->name('admin.tours.edit');
    Route::put('/tours/{id}', [TourController::class, 'update'])->name('admin.tours.update');
    Route::delete('/tours/{id}', [TourController::class, 'destroy'])->name('admin.tours.destroy');
    Route::get('/tours/{id}/schedules', [TourController::class, 'schedules'])->name('admin.tours.schedules');
    Route::post('/tours/{id}/schedules', [TourController::class, 'storeSchedule'])->name('admin.tours.schedules.store');
    Route::get('/tours', [TourController::class, 'index'])->name('admin.tours.index');
    // Trong nhóm Route::prefix('admin')->middleware(['auth'])->group(function () { ... })

    // Route hiển thị trang danh sách ảnh của Tour
    Route::get('/tours/{id}/images', [TourController::class, 'images'])->name('admin.tours.images');

    // Các route phụ liên quan (nếu chưa có)
    Route::post('/tours/{id}/images', [TourController::class, 'storeImages'])->name('admin.tours.images.store');
    Route::post('/tours/{tourId}/images/{imageId}/set-primary', [TourController::class, 'setPrimaryImage'])->name('admin.tours.images.set-primary');
    // Nằm dưới các route images khác trong nhóm admin
    Route::delete('/tours/{tourId}/images/{imageId}', [TourController::class, 'destroyImage'])->name('admin.tours.images.destroy');

    // Quản lý Hướng dẫn viên
    Route::resource('tour-guides', TourGuideController::class)->except(['show'])->names('admin.tour_guides');

    // Quản lý Điều hành Tour (Ongoing Tours)
    Route::get('/ongoing-tours', [OngoingTourController::class, 'index'])->name('admin.ongoing_tours.index');
    Route::post('/ongoing-tours/{schedule}/assign-guides', [OngoingTourController::class, 'assignGuides'])->name('admin.ongoing_tours.assign_guides');

});
Route::get('/tours/{slug}', [TourController::class, 'show'])->name('frontend.tours.show');

use App\Http\Controllers\Frontend\FlightController;

Route::get('/flights', [FlightController::class, 'search'])->name('frontend.flights.search');

use App\Http\Controllers\Frontend\TourBookingController;
use App\Http\Controllers\Frontend\UserController;

use App\Http\Controllers\Frontend\OcrController;

Route::middleware(['auth'])->group(function () {
    // Route API OCR (được bảo vệ bằng auth và CSRF)
    Route::post('/api/scan-cccd', [OcrController::class, 'scanCccd'])->name('ocr.scan-cccd');

    // Route đặt Tour
    Route::post('/tours/checkout', [TourBookingController::class, 'checkout'])->name('frontend.tours.checkout');

    // Route này để lưu vào Database và chuyển sang Duffel API
    Route::post('/tours/book', [TourBookingController::class, 'store'])->name('frontend.tours.store');

    // Route đặt vé máy bay (Duffel)
    Route::get('/flights/checkout', [FlightController::class, 'checkout'])->name('frontend.flights.checkout');
    Route::post('/flights/book', [FlightController::class, 'book'])->name('frontend.flights.book');
    Route::get('/my-bookings', [UserController::class, 'myBookings'])->name('user.bookings');
});
