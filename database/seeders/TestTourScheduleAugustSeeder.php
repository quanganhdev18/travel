<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestTourScheduleAugustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tours = Tour::all();

        foreach ($tours as $tour) {
            // Generate future schedules: 5, 12, 19, 26, 33 days from now
            $dayOffsets = [5, 12, 19, 26, 33];

            foreach ($dayOffsets as $offset) {
                $depDate = Carbon::now()->addDays($offset)->setHour(8)->setMinute(0)->setSecond(0);
                $duration = $tour->duration_days ?: 3;
                $retDate = (clone $depDate)->addDays($duration);

                TourSchedule::updateOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'departure_date' => $depDate->toDateTimeString(),
                    ],
                    [
                        'return_date' => $retDate->toDateTimeString(),
                        'capacity' => 20,
                        'available_seats' => 20,
                        'status' => 'available',
                    ]
                );
            }
        }

        $this->command->info('Successfully seeded future tour schedules.');
    }
}
