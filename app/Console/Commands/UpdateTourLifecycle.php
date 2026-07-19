<?php

namespace App\Console\Commands;

use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateTourLifecycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:update-lifecycle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically updates the status of tour schedules based on departure and return dates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // 1. Pending -> Operating
        $operatingCount = TourSchedule::where(function ($q) {
            $q->whereNull('status')->orWhere('status', 'pending');
        })
            ->where('departure_date', '<=', $today)
            ->where('return_date', '>=', $today)
            ->update(['status' => 'operating']);

        // 2. Operating -> Completed
        $completedCount = TourSchedule::where(function ($q) {
            $q->whereNull('status')->orWhereIn('status', ['pending', 'operating']);
        })
            ->where('return_date', '<', $today)
            ->update(['status' => 'completed']);

        $this->info("Updated {$operatingCount} tours to Operating.");
        $this->info("Updated {$completedCount} tours to Completed.");
    }
}
