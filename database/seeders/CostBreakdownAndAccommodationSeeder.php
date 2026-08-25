<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Addon;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class CostBreakdownAndAccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accommodations = Accommodation::all();

        if ($accommodations->isEmpty()) {
            $this->call(AccommodationSeeder::class);
            $accommodations = Accommodation::all();
        }

        $tours = Tour::all();

        foreach ($tours as $tour) {
            // Randomly assign 1 to 3 accommodations to this tour
            $tour->accommodations()->sync(
                $accommodations->random(rand(1, 3))->pluck('id')->toArray()
            );

            // Generate fake cost breakdown
            $basePrice = $tour->base_price > 0 ? $tour->base_price : rand(1000000, 5000000);
            
            // Assuming base_price in current db was total. Let's split it.
            $transport = $basePrice * 0.2;
            $meal = $basePrice * 0.3;
            $insurance = $basePrice * 0.05;
            $service = $basePrice * 0.15; // 70% total. The rest 30% could be tickets or something. We just update the fields.

            $tour->update([
                'cost_transport' => $transport,
                'cost_meal' => $meal,
                'cost_insurance' => $insurance,
                'cost_service_fee' => $service,
            ]);
        }

        // Update addons type
        $addons = Addon::all();
        foreach ($addons as $index => $addon) {
            // Half included, half extra
            $addon->update([
                'type' => $index % 2 == 0 ? 'included' : 'extra',
            ]);
        }
    }
}
