<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'discount_type' => 'required',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'max_discount' => $request->max_discount,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'usage_limit' => $request->usage_limit,
            'used_count' => 0,
        ]);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Thêm mã giảm giá thành công!');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'discount_type' => 'required',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after_or_equal:valid_from',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'max_discount' => $request->max_discount,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'usage_limit' => $request->usage_limit,
        ]);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Đã chuyển mã giảm giá vào thùng rác.');
    }

    public function trash()
    {
        $coupons = Coupon::onlyTrashed()
            ->latest()
            ->paginate(10);

        return view('admin.coupons.trash', compact('coupons'));
    }

    public function restore($id)
    {
        Coupon::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        return redirect()
            ->route('admin.coupons.trash')
            ->with('success', 'Khôi phục mã giảm giá thành công.');
    }

    public function forceDelete($id)
    {
        Coupon::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return redirect()
            ->route('admin.coupons.trash')
            ->with('success', 'Đã xóa vĩnh viễn mã giảm giá.');
    }
}
