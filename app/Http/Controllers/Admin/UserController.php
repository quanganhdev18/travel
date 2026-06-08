<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ];

        // Only admin can assign roles
        if (auth()->user()->role === 'admin') {
            $rules['role'] = ['required', Rule::in(['admin', 'staff', 'guide', 'customer'])];
        }

        $validated = $request->validate($rules);

        $validated['password'] = Hash::make($validated['password']);

        if (auth()->user()->role !== 'admin') {
            $validated['role'] = 'customer'; // default role if staff creates
        }

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        // Only admin can assign/change roles
        if (auth()->user()->role === 'admin') {
            $rules['role'] = ['required', Rule::in(['admin', 'staff', 'guide', 'customer'])];
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (auth()->user()->role !== 'admin') {
            unset($validated['role']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật tài khoản thành công.');
    }

    public function destroy(User $user)
    {
        // prevent deleting oneself
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Không thể xóa tài khoản của chính mình.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xóa tài khoản thành công.');
    }
}
