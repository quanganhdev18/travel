<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => User::count(),
            'admin' => User::where('role', UserRole::ADMIN->value)->count(),
            'staff' => User::where('role', UserRole::STAFF->value)->count(),
            'customer' => User::where('role', UserRole::CUSTOMER->value)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        $roles = UserRole::options();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', Password::min(8)],
            'role' => ['required', Rule::in(UserRole::values())],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Tạo tài khoản thành công!');
    }

    public function show(User $user)
    {
        $user->load(['bookings.tour_schedule.tour', 'ticket_bookings', 'reviews']);

        $bookingStats = [
            'total' => $user->bookings()->count(),
            'completed' => $user->bookings()->where('booking_status', 'completed')->count(),
            'pending' => $user->bookings()->whereIn('booking_status', ['pending', 'confirmed'])->count(),
            'cancelled' => $user->bookings()->where('booking_status', 'cancelled')->count(),
        ];

        return view('admin.users.show', compact('user', 'bookingStats'));
    }

    public function edit(User $user)
    {
        // Prevent editing yourself (use profile page instead)
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Vui lòng sử dụng trang Hồ sơ để chỉnh sửa tài khoản của bạn.');
        }

        $roles = UserRole::options();

        // Only admin can change roles or edit admin/staff
        if (! auth()->user()->isAdmin() && ($user->isAdmin() || $user->isStaff())) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Bạn không có quyền chỉnh sửa tài khoản quản trị/nhân viên.');
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Prevent editing yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể chỉnh sửa tài khoản của chính mình ở đây.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', Password::min(8)],
            'role' => ['required', Rule::in(UserRole::values())],
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa tài khoản của chính mình!');
        }

        // Prevent deleting admin if it's the last one
        if ($user->isAdmin() && User::where('role', UserRole::ADMIN->value)->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa quản trị viên cuối cùng!');
        }

        // Only allow deleting admin and staff accounts
        if ($user->isCustomer()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Không thể xóa tài khoản khách hàng. Vui lòng sử dụng chức năng khóa tài khoản.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa tài khoản thành công!');
    }

    public function toggleStatus(User $user)
    {
        // Prevent toggling yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi trạng thái tài khoản của chính mình!');
        }

        // Prevent locking last admin
        if ($user->isAdmin() && $user->is_active && User::where('role', UserRole::ADMIN->value)->where('is_active', true)->count() <= 1) {
            return back()->with('error', 'Không thể khóa quản trị viên cuối cùng!');
        }

        if ($user->is_active) {
            $user->deactivate();
            $message = 'Đã khóa tài khoản thành công!';
        } else {
            $user->activate();
            $message = 'Đã mở khóa tài khoản thành công!';
        }

        return back()->with('success', $message);
    }
}
