<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourImage;
use App\Models\TourItinerary;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourDetailedSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get Categories
        $categories = Category::all()->keyBy('slug');
        if ($categories->isEmpty()) {
            $this->command->warn('Chưa có Categories. Vui lòng chạy DatabaseSeeder trước.');

            return;
        }

        // 2. Get Destinations
        $destinations = Destination::all()->keyBy('name');
        if ($destinations->isEmpty()) {
            $this->command->warn('Chưa có Destinations. Vui lòng chạy DatabaseSeeder trước.');

            return;
        }

        // Xóa data cũ của tours (schedules, itineraries, activities, images sẽ bị xóa theo cascade nếu cấu hình DB chuẩn, nhưng để chắc chắn ta có thể truncate hoặc chỉ delete)
        DB::table('tour_activities')->delete();
        DB::table('tour_itineraries')->delete();
        DB::table('tour_schedules')->delete();
        DB::table('tour_images')->delete();
        DB::table('tours')->delete();

        // 3. Define Rich Data
        $toursData = [
            [
                'destination_name' => 'Đà Nẵng',
                'title' => 'Khám phá Đà Nẵng - Hội An - Bà Nà Hills Tuyệt Tranh',
                'slug' => 'tour-da-nang-3-ngay-2-dem-vip',
                'description' => "Trải nghiệm kỳ nghỉ 3 ngày 2 đêm tuyệt vời tại thành phố đáng sống nhất Việt Nam. \n\nTham quan Cầu Vàng nổi tiếng, lạc bước trong không gian cổ kính của Phố Cổ Hội An lung linh sắc đèn lồng và thưởng thức nền ẩm thực miền Trung vô cùng đặc sắc. Chuyến đi hứa hẹn mang lại những khoảnh khắc đáng nhớ cùng gia đình và người thân.",
                'duration_days' => 3,
                'duration_nights' => 2,
                'base_price' => 3990000,
                'child_price' => 2990000,
                'category_slug' => 'du-lich-bien',
                'image_url' => 'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                'sub_images' => [
                    'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                    'https://images.unsplash.com/photo-1583417319070-4a69db38a482?q=80&w=800',
                ],
                'itineraries' => [
                    [
                        'day_number' => 1,
                        'title' => 'Đà Nẵng - Ngũ Hành Sơn - Hội An',
                        'description' => 'Xe và HDV đón quý khách tại sân bay Đà Nẵng, khởi hành đi tham quan danh thắng Ngũ Hành Sơn, Làng đá mỹ nghệ Non Nước. Buổi chiều tối di chuyển vào Hội An, dạo bộ ngắm phố đèn lồng và thưởng thức đặc sản.',
                        'activities' => [
                            ['activity_type' => 'Tham quan', 'start_time' => '14:00:00', 'end_time' => '16:00:00', 'title' => 'Tham quan Ngũ Hành Sơn', 'description' => 'Khám phá các hang động kỳ bí, viếng chùa cổ trên núi và ngắm nhìn cảnh biển Non Nước từ trên cao.'],
                            ['activity_type' => 'Vui chơi', 'start_time' => '18:00:00', 'end_time' => '21:00:00', 'title' => 'Dạo đêm Phố Cổ Hội An', 'description' => 'Ngắm đèn lồng rực rỡ, đi thuyền thả hoa đăng trên dòng sông Hoài thơ mộng và thưởng thức Cao Lầu, Mì Quảng.'],
                        ],
                    ],
                    [
                        'day_number' => 2,
                        'title' => 'Khám phá Bà Nà Hills - Đường lên tiên cảnh',
                        'description' => 'Lên cáp treo đạt 4 kỷ lục Guinness thế giới đến với Bà Nà Hills. Check-in tại Cầu Vàng huyền thoại, tham gia hàng trăm trò chơi tại công viên Fantasy Park và thăm Làng Pháp cổ kính.',
                        'activities' => [
                            ['activity_type' => 'Vui chơi', 'start_time' => '09:00:00', 'end_time' => '15:00:00', 'title' => 'Vui chơi tại Sun World Ba Na Hills', 'description' => 'Thỏa sức trải nghiệm các trò chơi cảm giác mạnh, thăm Hầm rượu Debay trăm tuổi và dạo bước trên Cầu Vàng tuyệt đẹp.'],
                        ],
                    ],
                    [
                        'day_number' => 3,
                        'title' => 'Bán Đảo Sơn Trà - Chợ Hàn - Tiễn khách',
                        'description' => 'Viếng thăm Chùa Linh Ứng Bãi Bụt, nơi có tượng Phật Bà Quan Âm cao nhất Việt Nam. Sau đó tự do mua sắm đặc sản tại Chợ Hàn trước khi xe đưa đoàn ra sân bay.',
                        'activities' => [
                            ['activity_type' => 'Tham quan', 'start_time' => '08:30:00', 'end_time' => '10:30:00', 'title' => 'Viếng Chùa Linh Ứng', 'description' => 'Cầu bình an tại ngôi chùa linh thiêng bậc nhất Đà Nẵng và ngắm toàn cảnh thành phố từ Bán đảo Sơn Trà.'],
                        ],
                    ],
                ],
            ],
            [
                'destination_name' => 'Phú Quốc',
                'title' => 'Nghỉ dưỡng Thiên đường Đảo Ngọc Phú Quốc',
                'slug' => 'tour-phu-quoc-4-ngay-3-dem-resort',
                'description' => "Trải nghiệm tour du lịch nghỉ dưỡng 4 ngày 3 đêm tại resort 5 sao đẳng cấp.\n\nTham quan các hòn đảo hoang sơ tuyệt đẹp bằng cano, lặn ngắm san hô, vui chơi tại VinWonders & Vinpearl Safari và thưởng thức hải sản tươi ngon bậc nhất. Đảo Ngọc Phú Quốc chắc chắn sẽ làm say lòng bất kỳ vị khách nào.",
                'duration_days' => 4,
                'duration_nights' => 3,
                'base_price' => 5890000,
                'child_price' => 4500000,
                'category_slug' => 'du-lich-bien',
                'image_url' => 'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
                'sub_images' => [
                    'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
                    'https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?q=80&w=800',
                ],
                'itineraries' => [
                    [
                        'day_number' => 1,
                        'title' => 'Đón sân bay - Check-in Resort 5 Sao',
                        'description' => 'Đón quý khách tại sân bay Phú Quốc, đưa về resort nhận phòng nghỉ ngơi. Chiều tự do tắm biển và ngắm hoàng hôn rực rỡ trên Bãi Trường.',
                        'activities' => [
                            ['activity_type' => 'Nghỉ ngơi', 'start_time' => '15:00:00', 'end_time' => '18:00:00', 'title' => 'Tự do tắm biển, ngắm hoàng hôn', 'description' => 'Tận hưởng bãi biển riêng của resort và ngắm cảnh mặt trời lặn lãng mạn.'],
                        ],
                    ],
                    [
                        'day_number' => 2,
                        'title' => 'Khám phá Nam Đảo - Tour Canô 4 Đảo',
                        'description' => 'Lên canô cao tốc khám phá các hòn đảo hoang sơ: Hòn Móng Tay, Hòn Gầm Ghì, Hòn Mây Rút. Trải nghiệm câu cá, lặn ngắm san hô tuyệt đẹp.',
                        'activities' => [
                            ['activity_type' => 'Hoạt động biển', 'start_time' => '09:00:00', 'end_time' => '16:00:00', 'title' => 'Tour Canô 4 Đảo & Lặn ngắm san hô', 'description' => 'Chụp ảnh bằng flycam, chèo ván SUP và chiêm ngưỡng hệ sinh thái san hô đa dạng.'],
                        ],
                    ],
                    [
                        'day_number' => 3,
                        'title' => 'Vui chơi VinWonders & Grand World',
                        'description' => 'Tham quan công viên chủ đề VinWonders rộng lớn nhất Việt Nam. Buổi tối khám phá "Thành phố không ngủ" Grand World và xem show tinh hoa Việt Nam.',
                        'activities' => [
                            ['activity_type' => 'Vui chơi', 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'title' => 'Oanh tạc VinWonders Phú Quốc', 'description' => 'Chinh phục hơn 100 trò chơi, tham quan Thủy cung hình rùa biển độc đáo.'],
                            ['activity_type' => 'Tham quan', 'start_time' => '19:00:00', 'end_time' => '22:00:00', 'title' => 'Khám phá Grand World', 'description' => 'Đi thuyền trên sông Venice, xem show nhạc nước Sắc màu Venice hoành tráng.'],
                        ],
                    ],
                    [
                        'day_number' => 4,
                        'title' => 'Mua sắm đặc sản - Tiễn sân bay',
                        'description' => 'Tham quan Vườn tiêu, Cơ sở sản xuất nước mắm truyền thống, Rượu sim. Mua sắm đặc sản làm quà trước khi ra sân bay.',
                        'activities' => [],
                    ],
                ],
            ],
            [
                'destination_name' => 'Sapa',
                'title' => 'Chinh phục nóc nhà Đông Dương - Sapa Mù Sương',
                'slug' => 'tour-sapa-fansipan-3-ngay-2-dem-cao-cap',
                'description' => "Đến với Sapa để cảm nhận cái lạnh của vùng cao Tây Bắc, chiêm ngưỡng những thửa ruộng bậc thang kỳ vĩ và tìm hiểu bản sắc văn hóa của đồng bào dân tộc thiểu số.\n\nĐặc biệt, chuyến hành trình sẽ đưa bạn lên đỉnh Fansipan - Nóc nhà Đông Dương, chạm tay vào mây trời ở độ cao 3.143m.",
                'duration_days' => 3,
                'duration_nights' => 2,
                'base_price' => 2990000,
                'child_price' => 2200000,
                'category_slug' => 'nghi-duong-nui',
                'image_url' => 'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                'sub_images' => [
                    'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                    'https://images.unsplash.com/photo-1520630656645-5d9c2cbff4e0?q=80&w=800',
                ],
                'itineraries' => [
                    [
                        'day_number' => 1,
                        'title' => 'Hà Nội - Sapa - Bản Cát Cát',
                        'description' => 'Khởi hành từ Hà Nội đi Sapa theo đường cao tốc. Chiều tham quan Bản Cát Cát của người H’Mông, tìm hiểu nghề dệt nhuộm và xem biểu diễn văn nghệ truyền thống.',
                        'activities' => [
                            ['activity_type' => 'Tham quan', 'start_time' => '14:30:00', 'end_time' => '17:00:00', 'title' => 'Khám phá Bản Cát Cát', 'description' => 'Trekking nhẹ nhàng xuống thung lũng, check-in tại các guồng nước và suối Tiên Sa.'],
                        ],
                    ],
                    [
                        'day_number' => 2,
                        'title' => 'Chinh phục Fansipan - Khám phá Sapa về đêm',
                        'description' => 'Trải nghiệm cáp treo 3 dây dài nhất thế giới lên đỉnh Fansipan. Viếng quần thể tâm linh Kim Sơn Bảo Thắng Tự. Buổi tối tự do thưởng thức đồ nướng Sapa.',
                        'activities' => [
                            ['activity_type' => 'Trải nghiệm', 'start_time' => '08:00:00', 'end_time' => '12:00:00', 'title' => 'Cáp treo Fansipan Legend', 'description' => 'Chạm tay vào cột mốc 3.143m, săn mây và ngắm toàn cảnh thung lũng Mường Hoa.'],
                        ],
                    ],
                    [
                        'day_number' => 3,
                        'title' => 'Núi Hàm Rồng - Trở về Hà Nội',
                        'description' => 'Khám phá khu du lịch Núi Hàm Rồng: Vườn Lan Đông Dương, Cổng Trời, Sân Mây. Trưa trả phòng và lên xe khởi hành về lại thủ đô.',
                        'activities' => [
                            ['activity_type' => 'Tham quan', 'start_time' => '09:00:00', 'end_time' => '11:30:00', 'title' => 'Thăm Núi Hàm Rồng', 'description' => 'Ngắm hoa lan đua sắc và ngắm nhìn toàn cảnh thị trấn Sapa từ trên cao.'],
                        ],
                    ],
                ],
            ],
            [
                'destination_name' => 'Hạ Long',
                'title' => 'Du thuyền 5 Sao Vịnh Hạ Long - Kỳ quan thế giới',
                'slug' => 'tour-du-thuyen-ha-long-2-ngay-1-dem-5-sao',
                'description' => "Trải nghiệm sang trọng và đẳng cấp tuyệt đối khi lênh đênh trên Vịnh Hạ Long bằng siêu du thuyền 5 sao.\n\nTham gia các hoạt động thú vị như chèo thuyền kayak qua các hang động rực rỡ, tham gia lớp học nấu ăn trên Sundeck và ngắm hoàng hôn lãng mạn giữa hàng nghìn đảo đá vôi kỳ vĩ.",
                'duration_days' => 2,
                'duration_nights' => 1,
                'base_price' => 3490000,
                'child_price' => 2690000,
                'category_slug' => 'kham-pha-di-san',
                'image_url' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                'sub_images' => [
                    'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                ],
                'itineraries' => [
                    [
                        'day_number' => 1,
                        'title' => 'Hà Nội - Cảng Tuần Châu - Khám phá Hang Luồn',
                        'description' => 'Xe đón khách tại Hà Nội, di chuyển đến cảng Tuần Châu. Lên du thuyền nhận phòng, thưởng thức bữa trưa búp-phê. Chiều chèo Kayak khám phá Hang Luồn và bơi lội tại đảo Ti Tốp.',
                        'activities' => [
                            ['activity_type' => 'Trải nghiệm', 'start_time' => '15:00:00', 'end_time' => '16:30:00', 'title' => 'Chèo Kayak Hang Luồn', 'description' => 'Tự tay chèo thuyền kayak len lỏi qua vòm hang đá vôi tuyệt đẹp và xem khỉ hoang dã.'],
                            ['activity_type' => 'Ẩm thực', 'start_time' => '19:00:00', 'end_time' => '21:00:00', 'title' => 'Tiệc tối BBQ trên boong tàu', 'description' => 'Thưởng thức hải sản nướng cao cấp trong không gian lãng mạn giữa Vịnh.'],
                        ],
                    ],
                    [
                        'day_number' => 2,
                        'title' => 'Tập Thái Cực Quyền - Hang Sửng Sốt - Hà Nội',
                        'description' => 'Đón bình minh với bài tập Thái Cực Quyền trên Sundeck. Khám phá Hang Sửng Sốt - hang động lớn và đẹp nhất Vịnh. Thưởng thức bữa trưa sớm trước khi cập bến về Hà Nội.',
                        'activities' => [
                            ['activity_type' => 'Tham quan', 'start_time' => '08:00:00', 'end_time' => '09:30:00', 'title' => 'Khám phá Hang Sửng Sốt', 'description' => 'Chiêm ngưỡng hệ thống nhũ đá kỳ vĩ, đa dạng hình thù do thiên nhiên kiến tạo.'],
                        ],
                    ],
                ],
            ],
            [
                'destination_name' => 'Đà Lạt',
                'title' => 'Đà Lạt - Thành phố Ngàn Hoa mộng mơ',
                'slug' => 'tour-da-lat-thanh-pho-ngan-hoa-3n2d',
                'description' => "Bỏ lại cái nóng oi ả của phố thị để đến với Đà Lạt sương mù lãng mạn.\n\nChương trình đưa bạn ghé thăm những địa danh check-in cực hot: Thung Lũng Tình Yêu, Dinh Bảo Đại, Quảng trường Lâm Viên và trải nghiệm thưởng thức cà phê, dạo bước quanh Hồ Xuân Hương thơ mộng.",
                'duration_days' => 3,
                'duration_nights' => 2,
                'base_price' => 2790000,
                'child_price' => 2090000,
                'category_slug' => 'nghi-duong-nui',
                'image_url' => 'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                'sub_images' => [
                    'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                ],
                'itineraries' => [
                    [
                        'day_number' => 1,
                        'title' => 'Sân bay Liên Khương - Dinh Bảo Đại - Quảng trường',
                        'description' => 'Đón khách tại sân bay, tham quan Dinh 1 Bảo Đại với kiến trúc Châu Âu độc đáo. Buổi tối tản bộ Quảng trường Lâm Viên, check-in Nụ Hoa Dã Quỳ khổng lồ.',
                        'activities' => [],
                    ],
                    [
                        'day_number' => 2,
                        'title' => 'Đồi Chè Cầu Đất - Nông Trại Cún - Vườn Dâu',
                        'description' => 'Săn mây sớm tại Đồi Chè Cầu Đất xanh ngút ngàn. Tham quan nông trại Puppy Farm siêu đáng yêu và tự tay hái dâu tây sạch tại vườn.',
                        'activities' => [
                            ['activity_type' => 'Tham quan', 'start_time' => '08:00:00', 'end_time' => '10:00:00', 'title' => 'Đồi chè Cầu Đất', 'description' => 'Hít thở không khí trong lành, chụp những bức ảnh thanh xuân tuyệt đẹp bên những đồi chè.'],
                            ['activity_type' => 'Trải nghiệm', 'start_time' => '14:00:00', 'end_time' => '16:00:00', 'title' => 'Trải nghiệm hái dâu', 'description' => 'Tham quan công nghệ trồng dâu tây thủy canh và thưởng thức những trái dâu chín mọng.'],
                        ],
                    ],
                    [
                        'day_number' => 3,
                        'title' => 'Thiền Viện Trúc Lâm - Thác Đatanla - Tiễn khách',
                        'description' => 'Viếng Thiền Viện Trúc Lâm yên bình, đi cáp treo đồi Robin ngắm cảnh. Trải nghiệm máng trượt xuyên rừng tại Thác Đatanla trước khi kết thúc chuyến đi.',
                        'activities' => [
                            ['activity_type' => 'Vui chơi', 'start_time' => '10:00:00', 'end_time' => '12:00:00', 'title' => 'Máng trượt Thác Đatanla', 'description' => 'Hòa mình vào thiên nhiên với hệ thống máng trượt alpine coaster hiện đại nhất Đông Nam Á.'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($toursData as $tData) {
            $dest = $destinations[$tData['destination_name']];

            // 1. Create Tour
            $tour = Tour::create([
                'slug' => $tData['slug'],
                'destination_id' => $dest->id,
                'title' => $tData['title'],
                'description' => $tData['description'],
                'duration_days' => $tData['duration_days'],
                'duration_nights' => $tData['duration_nights'],
                'base_price' => $tData['base_price'],
                'child_price' => $tData['child_price'],
            ]);

            // Sync category
            if (isset($categories[$tData['category_slug']])) {
                $tour->categories()->sync([$categories[$tData['category_slug']]->id]);
            }

            // 2. Create Tour Images
            TourImage::create([
                'tour_id' => $tour->id,
                'image_url' => $tData['image_url'],
                'is_primary' => 1,
            ]);

            if (isset($tData['sub_images'])) {
                foreach ($tData['sub_images'] as $subImg) {
                    TourImage::create([
                        'tour_id' => $tour->id,
                        'image_url' => $subImg,
                        'is_primary' => 0,
                    ]);
                }
            }

            // 3. Create Itineraries and Activities
            foreach ($tData['itineraries'] as $itinData) {
                $itinerary = TourItinerary::create([
                    'tour_id' => $tour->id,
                    'day_number' => $itinData['day_number'],
                    'title' => $itinData['title'],
                    'description' => $itinData['description'],
                ]);

                if (isset($itinData['activities'])) {
                    foreach ($itinData['activities'] as $actData) {
                        TourActivity::create([
                            'tour_itinerary_id' => $itinerary->id,
                            'activity_type' => $actData['activity_type'],
                            'start_time' => $actData['start_time'],
                            'end_time' => $actData['end_time'],
                            'title' => $actData['title'],
                            'description' => $actData['description'],
                        ]);
                    }
                }
            }

            // 4. Create Tour Schedules (5 schedules per tour, starting from tomorrow)
            for ($i = 1; $i <= 5; $i++) {
                $depDate = Carbon::now()->addDays($i * 4);
                $retDate = (clone $depDate)->addDays($tData['duration_days']);

                // Giả lập random chỗ trống
                $capacity = rand(20, 30);
                $available = rand(0, $capacity);

                TourSchedule::create([
                    'tour_id' => $tour->id,
                    'departure_date' => $depDate->toDateTimeString(),
                    'return_date' => $retDate->toDateTimeString(),
                    'capacity' => $capacity,
                    'available_seats' => $available,
                    'status' => 'available',
                ]);
            }
        }

        $this->command->info('Đã seed dữ liệu Tour siêu chi tiết thành công!');
    }
}
