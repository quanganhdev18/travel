<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\TourSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UpdateTourStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động chuyển trạng thái booking sang "in_progress" khi tour đến ngày khởi hành';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->startOfDay();

        /** @var Collection<int, int> $scheduleIds */
        $scheduleIds = TourSchedule::where('departure_date', '<=', $today)
            ->where('return_date', '>=', $today)
            ->pluck('id');

        if ($scheduleIds->isEmpty()) {
            $this->info('Không có lịch trình nào đang diễn ra hôm nay.');

            return self::SUCCESS;
        }

        $updatedCount = 0;

        Booking::whereIn('tour_schedule_id', $scheduleIds)
            ->where('tour_status', Booking::TOUR_UPCOMING)
            ->chunkById(100, function ($bookings) use (&$updatedCount) {
                foreach ($bookings as $booking) {
                    $booking->tour_status = Booking::TOUR_IN_PROGRESS;
                    $booking->save();
                    $updatedCount++;
                }
            });

        $message = "Đã cập nhật {$updatedCount} booking sang trạng thái 'in_progress'.";

        $this->info($message);
        Log::info('[tours:update-status] '.$message);

        return self::SUCCESS;
    }
}
