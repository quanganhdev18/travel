<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Ticket;
use App\Models\TicketOption;
use App\Models\Tour;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $ticketsData = [
            [
                'keyword' => 'Đà Nẵng',
                'title' => 'Vé Cáp Treo Bà Nà Hills',
                'description' => 'Khám phá chốn bồng lai tiên cảnh tại Sun World Ba Na Hills. Vé bao gồm cáp treo khứ hồi, tham quan Cầu Vàng, Làng Pháp và nhiều trò chơi tại Fantasy Park.',
                'provider_name' => 'Sun World',
                'cancellation_policy' => 'Không hoàn hủy',
                'options' => [
                    ['name' => 'Vé Người Lớn', 'price' => 950000, 'original_price' => 950000],
                    ['name' => 'Vé Trẻ Em (1m - 1.4m)', 'price' => 750000, 'original_price' => 750000],
                ],
            ],
            [
                'keyword' => 'Hội An',
                'title' => 'Vé VinWonders Nam Hội An',
                'description' => 'Trải nghiệm văn hóa, kiến trúc và thiên nhiên đặc sắc tại VinWonders Nam Hội An. Tham quan River Safari độc đáo.',
                'provider_name' => 'VinWonders',
                'cancellation_policy' => 'Hủy trước 24h miễn phí',
                'options' => [
                    ['name' => 'Vé Người Lớn', 'price' => 600000, 'original_price' => 600000],
                    ['name' => 'Vé Trẻ Em', 'price' => 450000, 'original_price' => 450000],
                ],
            ],
            [
                'keyword' => 'Phú Quốc',
                'title' => 'Vé Vinpearl Safari Phú Quốc',
                'description' => 'Khám phá công viên chăm sóc và bảo tồn động vật bán hoang dã lớn nhất Việt Nam.',
                'provider_name' => 'VinWonders',
                'cancellation_policy' => 'Hủy trước 24h miễn phí',
                'options' => [
                    ['name' => 'Vé Người Lớn', 'price' => 650000, 'original_price' => 650000],
                    ['name' => 'Vé Trẻ Em', 'price' => 490000, 'original_price' => 490000],
                ],
            ],
            [
                'keyword' => 'Hạ Long',
                'title' => 'Vé Sun World Hạ Long',
                'description' => 'Tổ hợp vui chơi giải trí hàng đầu Việt Nam với Công viên Rồng, Công viên Nước và Cáp treo Nữ Hoàng.',
                'provider_name' => 'Sun World',
                'cancellation_policy' => 'Không hoàn hủy',
                'options' => [
                    ['name' => 'Cáp Treo Nữ Hoàng - Người Lớn', 'price' => 350000, 'original_price' => 350000],
                    ['name' => 'Cáp Treo Nữ Hoàng - Trẻ Em', 'price' => 250000, 'original_price' => 250000],
                ],
            ],
            [
                'keyword' => 'Sapa',
                'title' => 'Vé Cáp Treo Fansipan Legend',
                'description' => 'Chinh phục nóc nhà Đông Dương với hệ thống cáp treo 3 dây dài nhất thế giới.',
                'provider_name' => 'Sun World',
                'cancellation_policy' => 'Không hoàn hủy',
                'options' => [
                    ['name' => 'Vé Khứ Hồi - Người lớn', 'price' => 800000, 'original_price' => 800000],
                    ['name' => 'Vé Khứ Hồi - Trẻ em', 'price' => 550000, 'original_price' => 550000],
                ],
            ],
            [
                'keyword' => 'Nha Trang',
                'title' => 'Vé VinWonders Nha Trang',
                'description' => 'Công viên giải trí của những kỷ lục, nơi mang đến những trải nghiệm độc đáo và niềm vui bất tận.',
                'provider_name' => 'VinWonders',
                'cancellation_policy' => 'Hủy trước 24h miễn phí',
                'options' => [
                    ['name' => 'Vé Người Lớn', 'price' => 800000, 'original_price' => 800000],
                    ['name' => 'Vé Trẻ Em', 'price' => 600000, 'original_price' => 600000],
                ],
            ],
        ];

        $tours = Tour::with('destination')->get();
        $destinations = Destination::all();

        foreach ($ticketsData as $data) {
            // Find a destination that matches the keyword roughly
            $dest = $destinations->filter(function ($d) use ($data) {
                return str_contains(strtolower($d->name), strtolower($data['keyword']));
            })->first();

            $destId = $dest ? $dest->id : ($destinations->first()->id ?? 1);

            $ticket = Ticket::firstOrCreate([
                'slug' => Str::slug($data['title']),
            ], [
                'title' => $data['title'],
                'destination_id' => $destId,
                'description' => $data['description'],
                'provider_name' => $data['provider_name'],
                'cancellation_policy' => $data['cancellation_policy'],
            ]);

            foreach ($data['options'] as $opt) {
                TicketOption::firstOrCreate([
                    'ticket_id' => $ticket->id,
                    'name' => $opt['name'],
                ], [
                    'description' => 'Vé vào cổng tiêu chuẩn',
                    'price' => $opt['price'],
                    'original_price' => $opt['original_price'],
                ]);
            }

            // Connect ticket to Tours that match the keyword
            $matchingTours = $tours->filter(function ($t) use ($data) {
                $destName = $t->destination ? strtolower($t->destination->name) : '';
                $title = strtolower($t->getTranslation('title', 'vi') ?? $t->title);

                return str_contains($destName, strtolower($data['keyword'])) || str_contains($title, strtolower($data['keyword']));
            });

            foreach ($matchingTours as $tour) {
                $tour->tickets()->syncWithoutDetaching([$ticket->id]);
            }
        }
    }
}
