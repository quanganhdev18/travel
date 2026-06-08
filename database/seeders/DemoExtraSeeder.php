<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;
use App\Models\TicketOption;
use App\Models\Tour;
use App\Models\TourImage;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoExtraSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $destinations = Destination::all();
        $categories = Category::all();

        if ($destinations->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('No destinations or categories found. Please run DatabaseSeeder first.');

            return;
        }

        // Seed 10 Tours
        $tourAdjectives = ['Khám phá', 'Nghỉ dưỡng', 'Chinh phục', 'Hành trình', 'Trải nghiệm', 'Du ngoạn', 'Thưởng ngoạn'];
        $tourSuffixes = ['Tuyệt Hảo', 'Giá Rẻ', 'Cao Cấp', 'Siêu Tiết Kiệm', 'Đẳng Cấp 5 Sao', 'Mùa Thu', 'Mùa Hè Sôi Động'];

        $images = [
            'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
            'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
            'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
            'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
            'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
            'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
        ];

        for ($i = 0; $i < 10; $i++) {
            $dest = $destinations->random();
            $cat = $categories->random();

            $title = $faker->randomElement($tourAdjectives).' '.$dest->name.' - '.$faker->randomElement($tourSuffixes);
            $days = $faker->numberBetween(2, 5);
            $nights = $days - 1;

            $tour = Tour::create([
                'slug' => Str::slug($title).'-'.time().rand(10, 99),
                'destination_id' => $dest->id,
                'title' => $title,
                'description' => $faker->paragraphs(3, true),
                'duration_days' => $days,
                'duration_nights' => $nights,
                'base_price' => $faker->numberBetween(2000, 10000) * 1000,
                'child_price' => $faker->numberBetween(1000, 5000) * 1000,
            ]);

            $tour->categories()->sync([$cat->id]);

            TourImage::create([
                'tour_id' => $tour->id,
                'image_url' => $faker->randomElement($images),
                'is_primary' => 1,
            ]);

            // Schedules
            for ($j = 1; $j <= 3; $j++) {
                $depDate = Carbon::now()->addDays($faker->numberBetween(2, 30));
                $retDate = (clone $depDate)->addDays($days);

                $capacity = rand(20, 40);
                TourSchedule::create([
                    'tour_id' => $tour->id,
                    'departure_date' => $depDate->toDateTimeString(),
                    'return_date' => $retDate->toDateTimeString(),
                    'capacity' => $capacity,
                    'available_seats' => rand(5, $capacity),
                    'status' => 'available',
                ]);
            }
        }

        // Seed 5 Tickets
        $ticketTypes = ['Vé tham quan', 'Vé vui chơi', 'Combo vé', 'Vé show diễn', 'Vé cáp treo'];
        for ($i = 0; $i < 5; $i++) {
            $dest = $destinations->random();

            $title = $faker->randomElement($ticketTypes).' tại '.$dest->name.' '.$faker->word;

            $ticket = Ticket::create([
                'slug' => Str::slug($title).'-'.time().rand(10, 99),
                'title' => $title,
                'destination_id' => $dest->id,
                'description' => $faker->paragraphs(2, true),
                'provider_name' => $faker->company,
                'cancellation_policy' => $faker->randomElement(['Không hoàn hủy', 'Hủy trước 24h miễn phí', 'Hủy trước 48h miễn phí']),
            ]);

            // Options
            for ($j = 0; $j < 2; $j++) {
                $price = $faker->numberBetween(200, 1500) * 1000;
                TicketOption::create([
                    'ticket_id' => $ticket->id,
                    'name' => $j == 0 ? 'Vé Người Lớn' : 'Vé Trẻ Em',
                    'description' => 'Vé vào cổng tiêu chuẩn',
                    'price' => $price,
                    'original_price' => $price + 50000,
                ]);
            }
        }

        $this->command->info('Đã seed thêm 10 tours và 5 vé tham quan thành công!');
    }
}
