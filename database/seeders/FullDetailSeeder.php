<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\TicketOption;
use App\Models\Tour;
use App\Models\TourImage;
use App\Models\TourItinerary;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class FullDetailSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        // Images pool
        $tourImages = [
            'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
            'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
            'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
            'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
            'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
            'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
        ];

        $ticketImages = [
            'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=800',
            'https://images.unsplash.com/photo-1582653291997-079a1c04e5d1?q=80&w=800',
            'https://images.unsplash.com/photo-1571509930722-df38ccfb8611?q=80&w=800',
            'https://images.unsplash.com/photo-1605810230434-7631ac76ec81?q=80&w=800',
        ];

        $this->command->info('Đang bổ sung dữ liệu chi tiết cho Tours...');
        $tours = Tour::all();
        foreach ($tours as $tour) {
            // Check images (add secondary images)
            $imageCount = $tour->tour_images()->count();
            if ($imageCount < 3) {
                for ($i = 0; $i < (3 - $imageCount); $i++) {
                    TourImage::create([
                        'tour_id' => $tour->id,
                        'image_url' => $faker->randomElement($tourImages),
                        'is_primary' => $imageCount == 0 && $i == 0 ? 1 : 0,
                    ]);
                }
            }

            // Check itineraries
            $itineraryCount = $tour->tour_itineraries()->count();
            if ($itineraryCount == 0) {
                $days = $tour->duration_days ?: rand(2, 4);
                for ($d = 1; $d <= $days; $d++) {
                    TourItinerary::create([
                        'tour_id' => $tour->id,
                        'day_number' => $d,
                        'title' => 'Ngày '.$d.': Khám phá cảnh đẹp',
                        'description' => $faker->paragraphs(2, true),
                    ]);
                }
            }

            // Check schedules
            $scheduleCount = $tour->tour_schedules()->count();
            if ($scheduleCount == 0) {
                for ($j = 1; $j <= 3; $j++) {
                    $depDate = Carbon::now()->addDays($faker->numberBetween(2, 30));
                    $days = $tour->duration_days ?: rand(2, 4);
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
        }

        $this->command->info('Đang bổ sung dữ liệu chi tiết cho Tickets...');
        $tickets = Ticket::all();
        foreach ($tickets as $ticket) {
            // Check images
            $imageCount = $ticket->ticket_images()->count();
            if ($imageCount == 0) {
                for ($i = 0; $i < 3; $i++) {
                    TicketImage::create([
                        'ticket_id' => $ticket->id,
                        'image_url' => $faker->randomElement($ticketImages),
                        'is_primary' => $i == 0 ? 1 : 0,
                    ]);
                }
            }

            // Check options
            $optionCount = $ticket->ticket_options()->count();
            if ($optionCount == 0) {
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
        }

        $this->command->info('Đã bổ sung chi tiết đầy đủ cho tất cả tour và vé!');
    }
}
