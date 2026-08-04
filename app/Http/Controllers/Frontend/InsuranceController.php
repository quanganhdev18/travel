<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\InsuranceRegistration;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InsuranceController extends Controller
{
    /**
     * Danh sách thông tin các gói bảo hiểm
     */
    protected function getPackages(): array
    {
        return [
            'co_ban' => [
                'code' => 'co_ban',
                'name' => 'Cơ bản',
                'price' => 99000,
                'price_formatted' => '99.000đ',
                'summary' => 'Tai nạn, hỗ trợ y tế',
                'badge' => 'Tiết kiệm',
                'is_popular' => false,
                'features' => [
                    'Sơ cứu & hỗ trợ y tế cơ bản',
                    'Bảo vệ sự cố tai nạn cá nhân',
                    'Hỗ trợ khẩn cấp 12/7',
                ],
            ],
            'tieu_chuan' => [
                'code' => 'tieu_chuan',
                'name' => 'Tiêu chuẩn',
                'price' => 199000,
                'price_formatted' => '199.000đ',
                'summary' => 'Tai nạn, y tế, mất hành lý',
                'badge' => 'Khuyên dùng',
                'is_popular' => true,
                'features' => [
                    'Bao gồm toàn bộ gói Cơ bản',
                    'Chi trả y tế chuyên sâu & nằm viện',
                    'Bồi thường mất / thất lạc hành lý',
                    'Bồi thường chậm chuyến bay (>4 tiếng)',
                ],
            ],
            'cao_cap' => [
                'code' => 'cao_cap',
                'name' => 'Cao cấp',
                'price' => 399000,
                'price_formatted' => '399.000đ',
                'summary' => 'Toàn bộ quyền lợi, hỗ trợ 24/7',
                'badge' => 'VIP 24/7',
                'is_popular' => false,
                'features' => [
                    'Tối đa hạn mức bồi thường',
                    'Hỗ trợ y tế quốc tế & xe cấp cứu',
                    'Hành lý & đồ dùng cá nhân giá trị',
                    'Độc quyền Hotline tư vấn VIP 24/7',
                ],
            ],
        ];
    }

    /**
     * Danh sách Quyền lợi nổi bật
     */
    protected function getHighlights(): array
    {
        return [
            [
                'icon' => 'bi-hospital-fill',
                'title' => 'Hỗ trợ y tế khẩn cấp',
                'description' => 'Chi trả chi phí cấp cứu, điều trị y tế và viện phí phát sinh trong quá trình di chuyển.',
            ],
            [
                'icon' => 'bi-luggage-fill',
                'title' => 'Bồi thường mất hành lý',
                'description' => 'Hỗ trợ tài chính kịp thời khi hành lý, đồ dùng cá nhân bị thất lạc hoặc hư hỏng.',
            ],
            [
                'icon' => 'bi-clock-history',
                'title' => 'Hỗ trợ khi hủy hoặc chậm chuyến',
                'description' => 'Bồi thường tổn thất chi phí phát sinh do hoãn chuyến, đổi lịch hoặc hủy chuyến bay.',
            ],
            [
                'icon' => 'bi-headset',
                'title' => 'Hỗ trợ khách hàng 24/7',
                'description' => 'Tổng đài phản ứng nhanh sẵn sàng hỗ trợ giải quyết sự cố mọi lúc, mọi nơi.',
            ],
        ];
    }

    /**
     * Danh sách FAQ
     */
    protected function getFaqs(): array
    {
        return [
            [
                'id' => 'faq-1',
                'question' => 'Bảo hiểm có hiệu lực khi nào?',
                'answer' => 'Bảo hiểm du lịch có hiệu lực ngay khi bạn hoàn tất đăng ký, thanh toán thành công và mốc thời gian bắt đầu từ 00:00 của Ngày khởi hành ghi trên hợp đồng.',
            ],
            [
                'id' => 'faq-2',
                'question' => 'Tôi có thể hủy gói bảo hiểm không?',
                'answer' => 'Có. Bạn hoàn toàn có thể yêu cầu hủy gói bảo hiểm trước ngày khởi hành tối thiểu 24 giờ và nhận lại 100% chi phí đã thanh toán.',
            ],
            [
                'id' => 'faq-3',
                'question' => 'Làm thế nào để yêu cầu bồi thường?',
                'answer' => 'Khi phát sinh sự cố, hãy liên hệ Hotline 24/7 hoặc nộp hồ sơ gồm: Giấy xác nhận bảo hiểm, biên bản sự cố / hóa đơn y tế. Hồ sơ sẽ được thẩm định & chi trả trong vòng 24 - 48h.',
            ],
        ];
    }

    /**
     * Hiển thị trang Bảo hiểm du lịch
     */
    public function index(Request $request): View
    {
        $packages = $this->getPackages();
        $highlights = $this->getHighlights();
        $faqs = $this->getFaqs();

        $selectedPackage = $request->query('package', 'tieu_chuan');
        if (! array_key_exists($selectedPackage, $packages)) {
            $selectedPackage = 'tieu_chuan';
        }

        $user = Auth::user();

        return view('frontend.insurance.index', compact(
            'packages',
            'highlights',
            'faqs',
            'selectedPackage',
            'user'
        ));
    }

    /**
     * Xử lý đăng ký bảo hiểm du lịch
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'package_code' => 'required|string|in:co_ban,tieu_chuan,cao_cap',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'fullname.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'email.required' => 'Vui lòng nhập địa chỉ email hợp lệ.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'package_code.required' => 'Vui lòng chọn gói bảo hiểm.',
            'package_code.in' => 'Gói bảo hiểm đã chọn không hợp lệ.',
            'start_date.required' => 'Vui lòng chọn ngày khởi hành.',
            'start_date.after_or_equal' => 'Ngày khởi hành không được nằm trong quá khứ.',
            'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày khởi hành.',
        ]);

        $packages = $this->getPackages();
        $pkgInfo = $packages[$validated['package_code']];

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDays = max(1, $startDate->diffInDays($endDate) + 1);

        $pricePerDay = $pkgInfo['price'];
        $totalPrice = $pricePerDay * $totalDays;

        $registrationCode = 'INS-'.strtoupper(Str::random(8));

        $registration = InsuranceRegistration::create([
            'registration_code' => $registrationCode,
            'user_id' => Auth::id(),
            'fullname' => $validated['fullname'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'package_code' => $pkgInfo['code'],
            'package_name' => $pkgInfo['name'],
            'price_per_day' => $pricePerDay,
            'total_days' => $totalDays,
            'total_price' => $totalPrice,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'confirmed',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký bảo hiểm du lịch thành công!',
                'data' => [
                    'code' => $registration->registration_code,
                    'fullname' => $registration->fullname,
                    'package' => $registration->package_name,
                    'total_days' => $registration->total_days,
                    'total_price' => number_format($registration->total_price, 0, ',', '.').'đ',
                    'start_date' => $registration->start_date->format('d/m/Y'),
                    'end_date' => $registration->end_date->format('d/m/Y'),
                ],
            ]);
        }

        return redirect()->route('frontend.insurance.index')->with('success_registration', [
            'code' => $registration->registration_code,
            'fullname' => $registration->fullname,
            'package' => $registration->package_name,
            'total_days' => $registration->total_days,
            'total_price' => number_format($registration->total_price, 0, ',', '.').'đ',
            'start_date' => $registration->start_date->format('d/m/Y'),
            'end_date' => $registration->end_date->format('d/m/Y'),
        ]);
    }
}
