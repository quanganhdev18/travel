<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InsuranceRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InsuranceController extends Controller
{
    /**
     * Hiển thị danh sách đăng ký bảo hiểm du lịch trong Admin
     */
    public function index(Request $request): View
    {
        $query = InsuranceRegistration::with('user')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('registration_code', 'like', "%{$search}%")
                    ->orWhere('fullname', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($package = $request->input('package_code')) {
            $query->where('package_code', $package);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $registrations = $query->paginate(15)->withQueryString();

        // Thống kê tổng quan
        $totalRegistrations = InsuranceRegistration::count();
        $totalRevenue = InsuranceRegistration::where('status', 'confirmed')->sum('total_price');
        $confirmedCount = InsuranceRegistration::where('status', 'confirmed')->count();
        $pendingCount = InsuranceRegistration::where('status', 'pending')->count();

        return view('admin.insurance.index', compact(
            'registrations',
            'totalRegistrations',
            'totalRevenue',
            'confirmedCount',
            'pendingCount'
        ));
    }

    /**
     * Cập nhật trạng thái đăng ký bảo hiểm
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $registration = InsuranceRegistration::findOrFail($id);
        $registration->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', "Đã cập nhật trạng thái đơn bảo hiểm {$registration->registration_code} thành công!");
    }

    /**
     * Xóa đơn đăng ký bảo hiểm
     */
    public function destroy(int $id): RedirectResponse
    {
        $registration = InsuranceRegistration::findOrFail($id);
        $code = $registration->registration_code;
        $registration->delete();

        return redirect()->back()->with('success', "Đã xóa đơn bảo hiểm {$code} thành công!");
    }
}
