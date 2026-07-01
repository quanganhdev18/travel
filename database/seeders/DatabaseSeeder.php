<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;
use App\Models\TicketOption;
use App\Models\Tour;
use App\Models\TourImage;
use App\Models\TourSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Note: TicketSeeder will be called later after destinations are created
        // $this->call([
        //     TicketSeeder::class,
        // ]);

        // 1. Seed Users
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Quản trị viên',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'phone' => '0987654321',
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'name' => 'Nhân viên',
                'password' => Hash::make('12345678'),
                'role' => 'staff',
                'phone' => '0912345678',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Khách hàng',
                'password' => Hash::make('12345678'),
                'role' => 'customer',
            ]
        );

        // 2. Seed Banners
        $heroBanners = [
            [
                'title' => 'Hành trình khám phá Việt Nam',
                'image_url' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=1600',
                'position' => 'hero',
                'sort_order' => 1,
            ],
            [
                'title' => 'Nghỉ dưỡng đẳng cấp tại Phú Quốc',
                'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1600',
                'position' => 'hero',
                'sort_order' => 2,
            ],
            [
                'title' => 'Chinh phục đỉnh Fansipan - Sapa',
                'image_url' => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=1600',
                'position' => 'hero',
                'sort_order' => 3,
            ],
        ];

        foreach ($heroBanners as $banner) {
            Banner::firstOrCreate(
                ['title' => $banner['title']],
                [
                    'image_url' => $banner['image_url'],
                    'position' => $banner['position'],
                    'sort_order' => $banner['sort_order'],
                    'is_active' => 1,
                ]
            );
        }

        $adBanners = [
            [
                'title' => 'Giảm giá 30% khi đặt sớm',
                'image_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=600',
                'target_url' => '/tour-tron-goi?budget=under_5m',
                'position' => 'home_ads',
                'sort_order' => 1,
            ],
            [
                'title' => 'Combo vé máy bay & khách sạn',
                'image_url' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=600',
                'target_url' => '/flights',
                'position' => 'home_ads',
                'sort_order' => 2,
            ],
            [
                'title' => 'Khám phá ẩm thực miền Trung',
                'image_url' => 'https://images.unsplash.com/photo-1563897539633-7374c276c212?q=80&w=600',
                'target_url' => '/tour-tron-goi',
                'position' => 'home_ads',
                'sort_order' => 3,
            ],
        ];

        foreach ($adBanners as $banner) {
            Banner::firstOrCreate(
                ['title' => $banner['title']],
                [
                    'image_url' => $banner['image_url'],
                    'target_url' => $banner['target_url'],
                    'position' => $banner['position'],
                    'sort_order' => $banner['sort_order'],
                    'is_active' => 1,
                ]
            );
        }

        // 3. Seed Categories
        $categoriesData = [
            ['name' => 'Du lịch biển', 'slug' => 'du-lich-bien'],
            ['name' => 'Khám phá di sản', 'slug' => 'kham-pha-di-san'],
            ['name' => 'Nghỉ dưỡng núi', 'slug' => 'nghi-duong-nui'],
            ['name' => 'Tour phiêu lưu', 'slug' => 'tour-phieu-luu'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name']]
            );
        }

        // 4. Seed Destinations
        $destinationsData = [
            [
                'name' => 'Đà Nẵng',
                'description' => 'Thành phố đáng sống với biển Mỹ Khê và Cầu Vàng nổi tiếng.',
                'image_url' => 'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
            ],
            [
                'name' => 'Phú Quốc',
                'description' => 'Đảo ngọc thiên đường với những bãi cát trắng và làn nước xanh ngọc.',
                'image_url' => 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
            ],
            [
                'name' => 'Hà Nội',
                'description' => 'Thủ đô nghìn năm văn hiến, cổ kính và yên bình.',
                'image_url' => 'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
            ],
            [
                'name' => 'Hạ Long',
                'description' => 'Kỳ quan thiên nhiên thế giới với hàng nghìn đảo đá vôi kỳ vĩ.',
                'image_url' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
            ],
            [
                'name' => 'Sapa',
                'description' => 'Thị trấn trong sương với những thửa ruộng bậc thang tuyệt đẹp.',
                'image_url' => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
            ],
            [
                'name' => 'Đà Lạt',
                'description' => 'Thành phố ngàn hoa với không khí se lạnh quanh năm.',
                'image_url' => 'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
            ],
        ];

        $destinations = [];
        foreach ($destinationsData as $dest) {
            $destinations[$dest['name']] = Destination::firstOrCreate(
                ['name' => $dest['name']],
                [
                    'description' => $dest['description'],
                    'image_url' => $dest['image_url'],
                ]
            );
        }

        // 5. Seed Tours & TourImages & TourSchedules
        $toursData = [
            [
                'destination_name' => 'Đà Nẵng',
                'title' => 'Khám phá Đà Nẵng - Hội An - Bà Nà Hills',
                'slug' => 'tour-da-nang-3-ngay-2-dem',
                'description' => 'Trải nghiệm Cầu Vàng, Phố cổ Hội An lung linh sắc màu và ẩm thực miền Trung đặc sắc.',
                'duration_days' => 3,
                'duration_nights' => 2,
                'base_price' => 3990000,
                'category_slug' => 'du-lich-bien',
                'image_url' => 'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
            ],
            [
                'destination_name' => 'Phú Quốc',
                'title' => 'Thiên đường biển xanh Phú Quốc',
                'slug' => 'tour-phu-quoc-4-ngay-3-dem',
                'description' => 'Nghỉ dưỡng 4 ngày 3 đêm tại resort 5 sao, tham quan đảo hoang sơ và thưởng thức hải sản tươi ngon.',
                'duration_days' => 4,
                'duration_nights' => 3,
                'base_price' => 5890000,
                'category_slug' => 'du-lich-bien',
                'image_url' => 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
            ],
            [
                'destination_name' => 'Hà Nội',
                'title' => 'Hành trình di sản Hà Nội - Tràng An - Bái Đính',
                'slug' => 'tour-ha-noi-trang-an-2-ngay-1-dem',
                'description' => 'Hành trình về miền di sản văn hóa tâm linh Tràng An, viếng chùa Bái Đính lớn nhất Việt Nam.',
                'duration_days' => 2,
                'duration_nights' => 1,
                'base_price' => 2490000,
                'category_slug' => 'kham-pha-di-san',
                'image_url' => 'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
            ],
            [
                'destination_name' => 'Hạ Long',
                'title' => 'Du thuyền đẳng cấp 5 sao Vịnh Hạ Long',
                'slug' => 'tour-du-thuyen-ha-long-2-ngay-1-dem',
                'description' => 'Trải nghiệm nghỉ đêm sang trọng trên du thuyền đẳng cấp giữa lòng kỳ quan thiên nhiên thế giới.',
                'duration_days' => 2,
                'duration_nights' => 1,
                'base_price' => 3490000,
                'category_slug' => 'kham-pha-di-san',
                'image_url' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
            ],
            [
                'destination_name' => 'Sapa',
                'title' => 'Chinh phục đỉnh Fansipan - Sapa mùa lúa chín',
                'slug' => 'tour-sapa-fansipan-3-ngay-2-dem',
                'description' => 'Khám phá thị trấn sương mù, leo Fansipan - nóc nhà Đông Dương bằng cáp treo hiện đại.',
                'duration_days' => 3,
                'duration_nights' => 2,
                'base_price' => 2990000,
                'category_slug' => 'nghi-duong-nui',
                'image_url' => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
            ],
            [
                'destination_name' => 'Đà Lạt',
                'title' => 'Đà Lạt - Thành phố ngàn hoa mộng mơ',
                'slug' => 'tour-da-lat-thanh-pho-ngan-hoa',
                'description' => 'Thăm hồ Tuyền Lâm, Thung lũng Tình Yêu, những đồi thông reo và thưởng thức cà phê ngắm sương mù.',
                'duration_days' => 3,
                'duration_nights' => 2,
                'base_price' => 2790000,
                'category_slug' => 'nghi-duong-nui',
                'image_url' => 'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
            ],
        ];

        foreach ($toursData as $tData) {
            $dest = $destinations[$tData['destination_name']];

            $tour = Tour::firstOrCreate(
                ['slug' => $tData['slug']],
                [
                    'destination_id' => $dest->id,
                    'title' => $tData['title'],
                    'description' => $tData['description'],
                    'duration_days' => $tData['duration_days'],
                    'duration_nights' => $tData['duration_nights'],
                    'base_price' => $tData['base_price'],
                    'departure_time' => sprintf('%02d:00:00', rand(6, 10)),
                    'meeting_point' => 'Sân bay / Bến xe trung tâm',
                ]
            );

            // Sync category
            if (isset($categories[$tData['category_slug']])) {
                $tour->categories()->sync([$categories[$tData['category_slug']]->id]);
            }

            // Create Tour Image
            TourImage::firstOrCreate(
                [
                    'tour_id' => $tour->id,
                    'image_url' => $tData['image_url'],
                ],
                [
                    'is_primary' => 1,
                ]
            );

            // Create Tour Schedule (3 schedules in the future)
            for ($i = 1; $i <= 3; $i++) {
                $depDate = Carbon::now()->addDays($i * 10);
                $retDate = (clone $depDate)->addDays($tData['duration_days']);

                TourSchedule::firstOrCreate(
                    [
                        'tour_id' => $tour->id,
                        'departure_date' => $depDate->toDateTimeString(),
                    ],
                    [
                        'return_date' => $retDate->toDateTimeString(),
                        'capacity' => 30,
                        'available_seats' => 30,
                        'status' => 'available',
                    ]
                );
            }
        }

        // 6. Seed Tickets & TicketOptions
        $ticketsData = [
            [
                'destination_name' => 'Đà Nẵng',
                'title' => 'Vé vui chơi Sun World Ba Na Hills Đà Nẵng',
                'slug' => 've-sun-world-ba-na-hills',
                'description' => 'Trải nghiệm cáp treo đạt nhiều kỷ lục thế giới, check-in Cầu Vàng nổi tiếng toàn cầu.',
                'provider_name' => 'Sun World Group',
                'cancellation_policy' => 'Hủy trước 24h miễn phí.',
                'options' => [
                    [
                        'name' => 'Vé vào cổng & Cáp treo - Khách ngoại tỉnh',
                        'description' => 'Đã bao gồm cáp treo khứ hồi và hầu hết trò chơi tại Fantasy Park.',
                        'price' => 900000,
                        'original_price' => 950000,
                    ],
                    [
                        'name' => 'Vé vào cổng & Cáp treo + Buffet Trưa',
                        'description' => 'Combo bao gồm vé cáp treo và buffet ăn trưa tại nhà hàng trên đỉnh Bà Nà.',
                        'price' => 1250000,
                        'original_price' => 1300000,
                    ],
                ],
            ],
            [
                'destination_name' => 'Phú Quốc',
                'title' => 'Vé công viên chủ đề VinWonders Phú Quốc',
                'slug' => 've-vinwonders-phu-quoc',
                'description' => 'Công viên chủ đề lớn nhất Việt Nam với 6 phân khu độc đáo cùng hàng trăm trò chơi kỷ lục.',
                'provider_name' => 'Vinpearl Group',
                'cancellation_policy' => 'Không hoàn hủy.',
                'options' => [
                    [
                        'name' => 'Vé vào cổng tiêu chuẩn - Người lớn',
                        'description' => 'Áp dụng cho khách hàng cao từ 140cm trở lên.',
                        'price' => 950000,
                        'original_price' => 1000000,
                    ],
                    [
                        'name' => 'Vé vào cổng tiêu chuẩn - Trẻ em / Người cao tuổi',
                        'description' => 'Áp dụng cho trẻ em 100cm - 139cm hoặc người cao tuổi từ 60 tuổi.',
                        'price' => 710000,
                        'original_price' => 750000,
                    ],
                ],
            ],
            [
                'destination_name' => 'Sapa',
                'title' => 'Vé cáp treo Sun World Fansipan Legend Sapa',
                'slug' => 've-cap-treo-fansipan-legend',
                'description' => 'Hành trình chinh phục Nóc nhà Đông Dương ở độ cao 3.143m tuyệt đẹp.',
                'provider_name' => 'Sun World Group',
                'cancellation_policy' => 'Hủy trước 48h miễn phí.',
                'options' => [
                    [
                        'name' => 'Vé cáp treo khứ hồi Fansipan - Người lớn',
                        'description' => 'Vé cáp treo khứ hồi đi đỉnh Fansipan.',
                        'price' => 800000,
                        'original_price' => 850000,
                    ],
                ],
            ],
        ];

        foreach ($ticketsData as $tkData) {
            $dest = $destinations[$tkData['destination_name']];

            $ticket = Ticket::firstOrCreate(
                ['slug' => $tkData['slug']],
                [
                    'destination_id' => $dest->id,
                    'title' => $tkData['title'],
                    'description' => $tkData['description'],
                    'provider_name' => $tkData['provider_name'],
                    'cancellation_policy' => $tkData['cancellation_policy'],
                ]
            );

            foreach ($tkData['options'] as $opt) {
                TicketOption::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'name' => $opt['name'],
                    ],
                    [
                        'description' => $opt['description'],
                        'price' => $opt['price'],
                        'original_price' => $opt['original_price'],
                    ]
                );
            }
        }

        // 7. Seed Roles & Permissions (phải chạy sau khi users đã được tạo)
        $this->call([
            RolePermissionSeeder::class,
        ]);
    }
}
