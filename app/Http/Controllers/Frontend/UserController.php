<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the user's bookings.
     */
    public function myBookings(): View
    {
        $bookings = Booking::with(['tour_schedule.tour', 'payments'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('frontend.user.bookings', compact('bookings'));
    }

    /**
     * Show the user profile page.
     */
    public function profile(): View
    {
        $user = Auth::user();
        $user->load(['bookings', 'wishlists', 'reviews', 'user_identity']);

        return view('frontend.user.profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Cập nhật thông tin cá nhân thành công.');
    }

    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        if ($request->hasFile('avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/'.$path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Cập nhật ảnh đại diện thành công.');
    }

    /**
     * Update the user's password.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        if (! Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back()->with('success', 'Cập nhật mật khẩu thành công.');
    }

    /**
     * Show booking details.
     */
    public function bookingDetail(int $id): View
    {
        $booking = Booking::with([
            'tour_schedule.tour.destination',
            'tour_schedule.tour.tour_images',
            'booking_passengers',
        ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $existingReview = null;
        if ($tour = $booking->tour_schedule?->tour) {
            $existingReview = Review::where('user_id', Auth::id())
                ->where('tour_id', $tour->id)
                ->first();
        }

        return view('frontend.user.booking-detail', compact('booking', 'existingReview'));
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(int $id): RedirectResponse
    {
        $booking = Booking::where('user_id', Auth::id())
            ->whereIn('booking_status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $booking->booking_status = 'cancelled';
        $booking->save();

        return redirect()->back()->with('success', 'Hủy đơn đặt tour thành công.');
    }

    /**
     * Store a tour review.
     */
    public function storeReview(Request $request): RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        Review::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'tour_id' => $request->tour_id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return redirect()->back()->with('success', 'Gửi đánh giá chuyến đi thành công.');
    }

    /**
     * Display a listing of the user's wishlists.
     */
    public function myWishlists(): View
    {
        $wishlists = Wishlist::with(['tour.destination', 'tour.tour_images'])
            ->where('user_id', Auth::id())
            ->get();

        return view('frontend.user.wishlists', compact('wishlists'));
    }

    /**
     * Toggle a tour's wishlist status.
     */
    public function toggleWishlist(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        $userId = Auth::id();
        $tourId = $request->tour_id;

        $wishlist = Wishlist::where('user_id', $userId)->where('tour_id', $tourId)->first();
        if ($wishlist) {
            $wishlist->delete();
            $added = false;
        } else {
            $wishlist = new Wishlist;
            $wishlist->user_id = $userId;
            $wishlist->tour_id = $tourId;
            $wishlist->save();
            $added = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $added ? 'Đã thêm vào danh sách yêu thích.' : 'Đã xóa khỏi danh sách yêu thích.',
            ]);
        }

        return redirect()->back()->with('success', $added ? 'Đã thêm vào danh sách yêu thích.' : 'Đã xóa khỏi danh sách yêu thích.');
    }

    /**
     * Remove a tour from the wishlist.
     */
    public function removeWishlist(Request $request): RedirectResponse
    {
        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        Wishlist::where('user_id', Auth::id())
            ->where('tour_id', $request->tour_id)
            ->delete();

        return redirect()->back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
    }
}
