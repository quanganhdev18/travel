<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/debug-schema', function () {
    $columns = Schema::getColumnListing('tours');

    return implode(', ', $columns);
});
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tour-tron-goi', [HomeController::class, 'tours'])->name('frontend.tours.index');
Route::get('/tours/search', function (Request $request) {
    return redirect()->route('frontend.tours.index', $request->query());
})->name('frontend.tours.search');
Route::get('/api/destinations/search', [HomeController::class, 'searchDestinations'])->name('api.destinations.search');

use App\Http\Controllers\Admin\AddonController;
// Frontend Controllers
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
// Admin Controllers
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\OngoingTourController;
use App\Http\Controllers\Admin\TourActivityController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\TourGuideController;
use App\Http\Controllers\Admin\TourItineraryController;
use App\Http\Controllers\AppSettingsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\Frontend\FavoriteController;
use App\Http\Controllers\Frontend\FlightController;
use App\Http\Controllers\Frontend\OcrController;
use App\Http\Controllers\Frontend\TicketController;
use App\Http\Controllers\Frontend\TourBookingController;
use App\Http\Controllers\Frontend\TourController as FrontendTourController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Guide\ScheduleController;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Frontend
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');

Route::get('/locale/{locale}', [AppSettingsController::class, 'switchLocale'])
    ->name('locale.switch');

Route::get('/currency/{currency}', [AppSettingsController::class, 'switchCurrency'])
    ->name('currency.switch');

Route::post('/cookie-consent/accept', [CookieConsentController::class, 'accept'])
    ->name('cookie.consent.accept');

Route::post('/cookie-consent/decline', [CookieConsentController::class, 'decline'])
    ->name('cookie.consent.decline');

// VNPay Callbacks
Route::get('/tours/vnpay-return', [TourBookingController::class, 'vnpayReturn'])
    ->name('frontend.tours.vnpay_return');
Route::get('/tours/vnpay-ipn', [TourBookingController::class, 'vnpayIpn'])
    ->name('frontend.tours.vnpay_ipn');

// Tìm chuyến bay
Route::get('/flights', [FlightController::class, 'search'])
    ->name('frontend.flights.search');
Route::get('/api/flights/search', [FlightController::class, 'searchApi'])
    ->name('api.flights.search');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::get('/tickets/search', [TicketController::class, 'search'])->name('frontend.tickets.search');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth Frontend
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    // OCR CCCD
    Route::post('/api/scan-cccd', [OcrController::class, 'scanCccd'])
        ->name('ocr.scan-cccd');

    Route::post('/api/coupons/apply', [TourBookingController::class, 'applyCoupon'])
        ->name('coupons.apply');

    // Đặt Tour
    Route::get('/tours/checkout', [TourBookingController::class, 'checkout'])
        ->name('frontend.tours.checkout');

    Route::post('/tours/book', [TourBookingController::class, 'store'])
        ->name('frontend.tours.store');

    // Đặt vé máy bay
    Route::get('/flights/checkout', [FlightController::class, 'checkout'])
        ->name('frontend.flights.checkout');

    Route::post('/flights/book', [FlightController::class, 'book'])
        ->name('frontend.flights.book');

    // Tài khoản user
    Route::get('/user/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/user/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/user/avatar', [UserController::class, 'updateAvatar'])->name('user.avatar.update');
    Route::post('/user/password', [UserController::class, 'changePassword'])->name('user.password.change');

    Route::get('/my-bookings', fn () => redirect()->route('user.profile', ['tab' => 'bookings']))->name('user.bookings');
    Route::get('/my-bookings/{id}', [UserController::class, 'bookingDetail'])->name('user.bookings.detail');
    Route::post('/my-bookings/{id}/cancel', [UserController::class, 'cancelBooking'])->name('user.bookings.cancel');

    Route::post('/reviews', [UserController::class, 'storeReview'])->name('user.reviews.store');

    Route::get('/my-wishlists', fn () => redirect()->route('user.profile', ['tab' => 'wishlists']))->name('user.wishlists');
    Route::post('/wishlists/toggle', [UserController::class, 'toggleWishlist'])->name('user.wishlists.toggle');
    Route::post('/wishlists/remove', [UserController::class, 'removeWishlist'])->name('user.wishlists.remove');

    // Thanh toán lại bằng VNPay
    Route::get('/bookings/{id}/pay-vnpay', [TourBookingController::class, 'payWithVNPay'])
        ->name('frontend.bookings.pay_vnpay');
});

// Điểm đến
Route::get('/destinations', [App\Http\Controllers\Frontend\DestinationController::class, 'index'])
    ->name('frontend.destinations.index');

// Chi tiết Tour
Route::get('/tours/{slug}', [FrontendTourController::class, 'show'])
    ->name('frontend.tours.show');
Route::middleware('auth')->group(function () {

    Route::post('/tours/{tour}/favorite', [FavoriteController::class, 'toggle'])
        ->name('frontend.favorites.toggle');

    Route::delete('/tours/{tour}/favorite', [FavoriteController::class, 'destroy'])
        ->name('frontend.favorites.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // User Management
    Route::get('/chat', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('admin.chat.index');

    Route::resource('users', App\Http\Controllers\Admin\UserController::class)
        ->names('admin.users');
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])
        ->name('admin.users.toggle-status');

    // Booking
    Route::get('/bookings', [BookingController::class, 'index'])
        ->name('admin.bookings.index');

    // Quản lý tài khoản (Users)
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)
        ->except(['show'])
        ->names('admin.users');

    Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus'])
        ->name('admin.bookings.update_status');

    Route::post('/bookings/{id}/pnr', [BookingController::class, 'updatePnr'])
        ->name('admin.bookings.update_pnr');

    // Lịch trình Tour
    Route::get('tours/{tour}/itineraries', [TourItineraryController::class, 'index'])
        ->name('admin.tours.itineraries.index');

    Route::post('tours/{tour}/itineraries', [TourItineraryController::class, 'store'])
        ->name('admin.tours.itineraries.store');

    Route::delete('itineraries/{itinerary}', [TourItineraryController::class, 'destroy'])
        ->name('admin.itineraries.destroy');

    // Hoạt động Tour
    Route::post('itineraries/{itinerary}/activities', [TourActivityController::class, 'store'])
        ->name('admin.itineraries.activities.store');

    Route::delete('activities/{activity}', [TourActivityController::class, 'destroy'])
        ->name('admin.activities.destroy');

    // Điểm đến
    Route::resource('destinations', DestinationController::class)
        ->except(['show'])
        ->names('admin.destinations');

    // Danh mục
    Route::resource('categories', CategoryController::class)
        ->except(['show'])
        ->names('admin.categories');

    // Banner
    Route::resource('banners', BannerController::class)
        ->except(['show'])
        ->names('admin.banners');

    // Tour
    Route::get('/tours', [TourController::class, 'index'])
        ->name('admin.tours.index');

    Route::get('/tours/create', [TourController::class, 'create'])
        ->name('admin.tours.create');

    Route::post('/tours', [TourController::class, 'store'])
        ->name('admin.tours.store');

    Route::get('/tours/{id}/edit', [TourController::class, 'edit'])
        ->name('admin.tours.edit');

    Route::put('/tours/{id}', [TourController::class, 'update'])
        ->name('admin.tours.update');

    Route::delete('/tours/{id}', [TourController::class, 'destroy'])
        ->name('admin.tours.destroy');

    // Thùng rác Tour
    Route::get('/tours/trash', [TourController::class, 'trash'])
        ->name('admin.tours.trash');

    Route::post('/tours/{id}/restore', [TourController::class, 'restore'])
        ->name('admin.tours.restore');

    Route::delete('/tours/{id}/force-delete', [TourController::class, 'forceDelete'])
        ->name('admin.tours.force-delete');

    // Lịch khởi hành Tour
    Route::get('/tours/{id}/schedules', [TourController::class, 'schedules'])
        ->name('admin.tours.schedules');

    Route::post('/tours/{id}/schedules', [TourController::class, 'storeSchedule'])
        ->name('admin.tours.schedules.store');

    // Ảnh Tour
    Route::get('/tours/{id}/images', [TourController::class, 'images'])
        ->name('admin.tours.images');

    Route::post('/tours/{id}/images', [TourController::class, 'storeImages'])
        ->name('admin.tours.images.store');

    Route::post('/tours/{tourId}/images/{imageId}/set-primary', [TourController::class, 'setPrimaryImage'])
        ->name('admin.tours.images.set-primary');

    Route::delete('/tours/{tourId}/images/{imageId}', [TourController::class, 'destroyImage'])
        ->name('admin.tours.images.destroy');

    // Hướng dẫn viên
    Route::resource('tour-guides', TourGuideController::class)
        ->except(['show'])
        ->names('admin.tour_guides');

    // Điều hành Tour
    Route::get('/ongoing-tours', [OngoingTourController::class, 'index'])
        ->name('admin.ongoing_tours.index');

    Route::post('/ongoing-tours/{schedule}/assign-guides', [OngoingTourController::class, 'assignGuides'])
        ->name('admin.ongoing_tours.assign_guides');

    // Holidays
    Route::resource('holidays', HolidayController::class)
        ->names('admin.holidays');

    // Addons
    Route::resource('addons', AddonController::class)
        ->names('admin.addons');
});

/*
|--------------------------------------------------------------------------
| Guide Interface
|--------------------------------------------------------------------------
*/
Route::prefix('guide')->middleware(['auth', 'guide'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Guide\DashboardController::class, 'index'])
        ->name('guide.dashboard');

    Route::get('/schedules', [ScheduleController::class, 'index'])
        ->name('guide.schedules.index');

    Route::get('/schedules/{id}', [ScheduleController::class, 'show'])
        ->name('guide.schedules.show');
});

Route::get('/admin/coupons', [CouponController::class, 'index'])
    ->name('admin.coupons.index');
Route::get('/admin/coupons/create', [CouponController::class, 'create'])
    ->name('admin.coupons.create');
Route::post('/admin/coupons', [CouponController::class, 'store'])
    ->name('admin.coupons.store');
Route::get('/admin/coupons/{coupon}/edit', [CouponController::class, 'edit'])
    ->name('admin.coupons.edit');
Route::put('/admin/coupons/{coupon}', [CouponController::class, 'update'])
    ->name('admin.coupons.update');
Route::delete('/admin/coupons/{coupon}', [CouponController::class, 'destroy'])
    ->name('admin.coupons.destroy');

Route::get('/coupons/trash', [CouponController::class, 'trash'])
    ->name('admin.coupons.trash');

Route::post('/coupons/{id}/restore', [CouponController::class, 'restore'])
    ->name('admin.coupons.restore');

Route::delete('/coupons/{id}/force-delete', [CouponController::class, 'forceDelete'])
    ->name('admin.coupons.forceDelete');
Route::resource('coupons', CouponController::class);
/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|*/

Route::get('/api/check-email', function (Request $request) {
    $exists = User::where('email', $request->email)->exists();

    return response()->json(['exists' => $exists]);
})->name('api.check-email');

require __DIR__.'/auth.php';
// CHAT ROUTES
Route::middleware(['auth'])->prefix('chat')->group(function () {
    Route::post('/start', [ChatController::class, 'startConversation'])->name('chat.start');
    Route::get('/conversations', [ChatController::class, 'getConversations'])->name('chat.conversations');
    Route::get('/{id}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/{id}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});
