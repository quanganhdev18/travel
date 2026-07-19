<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tự động chuyển trạng thái booking sang "Đang thực hiện" khi đến giờ khởi hành
Schedule::command('tours:update-status')->everyMinute();

// Tự động ẩn/đóng tour và gửi mail nhắc nhở khi còn 3 ngày trước khởi hành
Schedule::command('tours:reminder-three-days')->daily();

// Tự động cập nhật vòng đời của Tour (đang chạy, đã kết thúc)
Schedule::command('tours:update-lifecycle')->daily();
