<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;">
        <h2 style="color: #007CE8; text-align: center;">Xác Nhận Đặt Tour Thành Công</h2>

        <p>Xin chào {{ $customerName }},</p>
        <p>Hệ thống đã ghi nhận đơn đặt tour của anh/chị {{ $customerName }}. Dưới đây là thông tin chi tiết về chuyến
            đi:</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;">
            <p style="margin: 0 0 10px 0; font-size: 18px; color: #1a2b4c;">
                <strong>{{ $schedule->tour->title ?? 'Tên tour' }}</strong>
            </p>
            <p style="margin: 5px 0;">Mã đơn hàng: #{{ $booking->id }}</p>
            <p style="margin: 5px 0;">Ngày khởi hành:
                {{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}</p>
            <p style="margin: 5px 0;">Số lượng khách: {{ $booking->adults_count + $booking->children_count }} người</p>
            <p style="margin: 5px 0;">Tổng thanh toán: {{ number_format($booking->total_price, 0, ',', '.') }} VNĐ</p>
            <p style="margin: 5px 0;">Tình trạng: <span style="color: #d97706;">Chờ xử lý</span></p>
        </div>

        <p style="margin-top: 20px;">Chúng tôi sẽ sớm liên hệ lại qua số điện thoại anh/chị {{ $customerPhone }} đã cung
            cấp để xác nhận các thủ
            tục tiếp theo.</p>

        <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
    </div>
</body>

</html>