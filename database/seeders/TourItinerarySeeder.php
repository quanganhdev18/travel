<?php

namespace Database\Seeders;

use App\Models\Tour;
use App\Models\TourItinerary;
use App\Models\TourActivity;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TourItinerarySeeder extends Seeder
{
    public function run(): void
    {
        $tours = Tour::doesntHave('tour_itineraries')->with('destination')->get();

        if ($tours->isEmpty()) {
            $this->command->info('Tất cả các tour đã có lịch trình. Đang lấy toàn bộ tour để tạo mới lịch trình cho các tour chưa đầy đủ hoặc để làm mẫu (tuỳ chọn).');
            // Mở comment dòng dưới nếu muốn tạo lại lịch trình cho toàn bộ tours
            // $tours = Tour::with('destination')->get();
        }

        $faker = Faker::create('vi_VN');

        $this->command->info('Bắt đầu tạo lịch trình cho ' . $tours->count() . ' tours...');

        $destinationData = $this->getDestinationTemplates();

        foreach ($tours as $tour) {
            $destinationName = $tour->destination ? $tour->destination->name : 'Nơi này';
            
            // Tìm template phù hợp nhất với tên địa điểm
            $templateKey = 'generic';
            foreach (array_keys($destinationData) as $key) {
                if ($key !== 'generic' && stripos($destinationName, $key) !== false) {
                    $templateKey = $key;
                    break;
                }
            }
            
            $template = $destinationData[$templateKey];
            $days = $tour->duration_days ?: 3; // Mặc định 3 ngày nếu không có
            
            for ($i = 1; $i <= $days; $i++) {
                $dayTemplate = $template[$i - 1] ?? $template['default'];
                
                $itineraryTitle = str_replace('{destination}', $destinationName, $dayTemplate['title']);
                $itineraryDescription = str_replace('{destination}', $destinationName, $dayTemplate['description']);
                
                $itinerary = TourItinerary::create([
                    'tour_id' => $tour->id,
                    'day_number' => $i,
                    'title' => "Ngày $i: $itineraryTitle",
                    'description' => $itineraryDescription,
                ]);

                // Tạo các activity cho từng ngày
                foreach ($dayTemplate['activities'] as $actData) {
                    TourActivity::create([
                        'tour_itinerary_id' => $itinerary->id,
                        'activity_type' => $actData['type'],
                        'start_time' => $actData['start_time'],
                        'end_time' => $actData['end_time'],
                        'title' => str_replace('{destination}', $destinationName, $actData['title']),
                        'description' => str_replace('{destination}', $destinationName, $actData['description']),
                    ]);
                }
            }
        }

        $this->command->info('Đã tạo lịch trình thành công cho các tour!');
    }

    private function getDestinationTemplates(): array
    {
        return [
            'đà nẵng' => [
                0 => [
                    'title' => 'Đón khách & Tham quan Ngũ Hành Sơn, Hội An',
                    'description' => 'Xe đón quý khách tại sân bay Đà Nẵng, sau đó khởi hành tham quan Ngũ Hành Sơn, làng đá mỹ nghệ Non Nước và tiến về Phố cổ Hội An.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '14:00:00', 'end_time' => '16:30:00', 'title' => 'Ngũ Hành Sơn & Làng đá Non Nước', 'description' => 'Khám phá các hang động kỳ bí, viếng chùa thiêng và chiêm ngưỡng các tác phẩm đá mỹ nghệ tinh xảo.'],
                        ['type' => 'Vui chơi', 'start_time' => '17:30:00', 'end_time' => '21:00:00', 'title' => 'Phố cổ Hội An', 'description' => 'Dạo bộ ngắm đèn lồng, tham quan Chùa Cầu, nhà cổ Tân Kỳ và thưởng thức đặc sản Hội An.'],
                    ]
                ],
                1 => [
                    'title' => 'Khám phá Bà Nà Hills - Đường lên tiên cảnh',
                    'description' => 'Trải nghiệm cáp treo đạt nhiều kỷ lục Guinness, check-in Cầu Vàng nổi tiếng toàn thế giới và vui chơi tại Fantasy Park.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '08:00:00', 'end_time' => '10:00:00', 'title' => 'Cáp treo & Cầu Vàng', 'description' => 'Ngắm cảnh núi rừng Bà Nà từ trên cao và đi dạo trên Cầu Vàng lung linh trong sương.'],
                        ['type' => 'Vui chơi', 'start_time' => '10:30:00', 'end_time' => '15:30:00', 'title' => 'Fantasy Park & Làng Pháp', 'description' => 'Tham gia hàng trăm trò chơi mạo hiểm và dạo bước quanh khu Làng Pháp cổ kính.'],
                    ]
                ],
                2 => [
                    'title' => 'Bán đảo Sơn Trà & Mua sắm đặc sản',
                    'description' => 'Viếng Chùa Linh Ứng lớn nhất Đà Nẵng, tự do tắm biển Mỹ Khê và mua sắm đặc sản về làm quà.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '08:30:00', 'end_time' => '10:30:00', 'title' => 'Chùa Linh Ứng Bãi Bụt', 'description' => 'Chiêm bái tượng Phật Bà Quan Âm cao nhất Việt Nam, ngắm toàn cảnh thành phố.'],
                        ['type' => 'Nghỉ ngơi', 'start_time' => '11:00:00', 'end_time' => '12:30:00', 'title' => 'Chợ Hàn / Chợ Cồn', 'description' => 'Tự do mua sắm hải sản, chả bò và các đặc sản miền Trung tiêu biểu.'],
                    ]
                ],
                'default' => [
                    'title' => 'Tự do khám phá Đà Nẵng',
                    'description' => 'Quý khách tự do tắm biển, tham quan các cây cầu nổi tiếng của thành phố Đà Nẵng.',
                    'activities' => [
                        ['type' => 'Nghỉ ngơi', 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'title' => 'Tự do tham quan', 'description' => 'Khám phá ẩm thực địa phương và ngắm sông Hàn về đêm.'],
                    ]
                ]
            ],
            'phú quốc' => [
                0 => [
                    'title' => 'Đón sân bay & Tham quan Đông Đảo',
                    'description' => 'HDV đón khách tại sân bay Phú Quốc, nhận phòng resort. Chiều tham quan Dinh Cậu, Vườn Tiêu, Cơ sở nước mắm.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '14:00:00', 'end_time' => '15:30:00', 'title' => 'Vườn Tiêu & Cơ sở nước mắm', 'description' => 'Tìm hiểu quy trình sản xuất nước mắm truyền thống và chụp hình tại vườn tiêu xanh mướt.'],
                        ['type' => 'Vui chơi', 'start_time' => '16:00:00', 'end_time' => '17:30:00', 'title' => 'Dinh Cậu, Dinh Bà', 'description' => 'Điểm đến tâm linh biểu tượng của đảo ngọc và ngắm hoàng hôn tuyệt đẹp.'],
                    ]
                ],
                1 => [
                    'title' => 'Câu cá, lặn ngắm san hô Nam Đảo',
                    'description' => 'Lên tàu du lịch trải nghiệm câu cá trên biển, lặn ngắm hệ sinh thái san hô đa dạng của Phú Quốc.',
                    'activities' => [
                        ['type' => 'Trải nghiệm', 'start_time' => '09:00:00', 'end_time' => '12:00:00', 'title' => 'Lặn ngắm san hô & Câu cá biển', 'description' => 'Trải nghiệm câu cá giữa biển khơi và lặn bằng ống thở ngắm san hô nhiều màu sắc.'],
                        ['type' => 'Nghỉ ngơi', 'start_time' => '14:00:00', 'end_time' => '16:00:00', 'title' => 'Tắm biển Bãi Sao', 'description' => 'Thư giãn tại Bãi Sao - bãi biển cát trắng mịn đẹp nhất Phú Quốc.'],
                    ]
                ],
                2 => [
                    'title' => 'Khám phá Grand World / VinWonders',
                    'description' => 'Tham quan siêu quần thể nghỉ dưỡng và vui chơi giải trí hàng đầu Đông Nam Á.',
                    'activities' => [
                        ['type' => 'Vui chơi', 'start_time' => '09:00:00', 'end_time' => '16:00:00', 'title' => 'Vui chơi tự do VinWonders', 'description' => 'Chinh phục các trò chơi mạo hiểm, khám phá cung điện Hải Vương tuyệt đẹp.'],
                        ['type' => 'Tham quan', 'start_time' => '18:00:00', 'end_time' => '21:00:00', 'title' => 'Thành phố không ngủ Grand World', 'description' => 'Dạo bước trên phố Venice và xem show diễn Tinh Hoa Việt Nam.'],
                    ]
                ],
                'default' => [
                    'title' => 'Nghỉ dưỡng và mua sắm',
                    'description' => 'Tự do tắm biển, mua sắm đặc sản ngọc trai, rượu sim trước khi kết thúc hành trình.',
                    'activities' => [
                        ['type' => 'Mua sắm', 'start_time' => '08:00:00', 'end_time' => '10:00:00', 'title' => 'Cơ sở ngọc trai Phú Quốc', 'description' => 'Tham quan và mua sắm ngọc trai chính hiệu Phú Quốc về làm quà.'],
                    ]
                ]
            ],
            'sapa' => [
                0 => [
                    'title' => 'Hà Nội - Sapa & Bản Cát Cát',
                    'description' => 'Khởi hành lên Sapa qua cung đường đèo tuyệt đẹp. Tham quan bản làng của người H\'Mông - Bản Cát Cát.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '14:30:00', 'end_time' => '17:00:00', 'title' => 'Khám phá Bản Cát Cát', 'description' => 'Tản bộ tìm hiểu văn hóa bản địa, chụp hình tại thác nước và bánh xe nước khổng lồ.'],
                    ]
                ],
                1 => [
                    'title' => 'Chinh phục đỉnh Fansipan - Nóc nhà Đông Dương',
                    'description' => 'Đi cáp treo lên đỉnh Fansipan, chiêm bái quần thể kiến trúc tâm linh và ngắm biển mây vĩ đại.',
                    'activities' => [
                        ['type' => 'Trải nghiệm', 'start_time' => '08:30:00', 'end_time' => '12:30:00', 'title' => 'Cáp treo Fansipan', 'description' => 'Chạm tay vào cột mốc 3143m và săn mây từ trên đỉnh núi cao nhất 3 nước Đông Dương.'],
                        ['type' => 'Vui chơi', 'start_time' => '18:00:00', 'end_time' => '21:00:00', 'title' => 'Dạo phố đêm Sapa', 'description' => 'Thưởng thức đồ nướng đặc trưng và tham quan nhà thờ đá Sapa sương mù.'],
                    ]
                ],
                2 => [
                    'title' => 'KDL Núi Hàm Rồng & Trở về',
                    'description' => 'Tham quan Núi Hàm Rồng, vườn lan Đông Dương, cổng trời và ngắm toàn cảnh thị trấn Sapa.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '08:30:00', 'end_time' => '11:00:00', 'title' => 'Núi Hàm Rồng', 'description' => 'Khám phá vườn hoa đa dạng, check-in mỏm đá Sân Mây.'],
                    ]
                ],
                'default' => [
                    'title' => 'Tham quan thung lũng Mường Hoa',
                    'description' => 'Đi tàu hỏa leo núi ngắm thung lũng Mường Hoa kỳ vĩ, tận hưởng khí hậu mát mẻ.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '09:00:00', 'end_time' => '11:30:00', 'title' => 'Tàu hỏa leo núi', 'description' => 'Trải nghiệm tàu hỏa leo núi dài nhất Việt Nam.'],
                    ]
                ]
            ],
            'nha trang' => [
                0 => [
                    'title' => 'Đến Nha Trang - Tháp Bà Ponagar',
                    'description' => 'Nhận phòng khách sạn, tham quan quần thể di tích Chăm Pa cổ kính.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '15:00:00', 'end_time' => '16:30:00', 'title' => 'Tháp Bà Ponagar', 'description' => 'Chiêm ngưỡng kiến trúc Chăm Pa cổ, nghe truyền thuyết về nữ thần Thiên Y A Na.'],
                    ]
                ],
                1 => [
                    'title' => 'Tour khám phá 3 đảo VIP',
                    'description' => 'Trải nghiệm cano cao tốc, tắm biển trong xanh tại Hòn Mun, Hòn Tằm và lặn biển ngắm san hô.',
                    'activities' => [
                        ['type' => 'Hoạt động biển', 'start_time' => '08:30:00', 'end_time' => '11:30:00', 'title' => 'Lặn biển Hòn Mun', 'description' => 'Khám phá hệ sinh thái san hô đa dạng nhất Việt Nam.'],
                        ['type' => 'Vui chơi', 'start_time' => '14:00:00', 'end_time' => '16:00:00', 'title' => 'Tắm bùn khoáng Hòn Tằm', 'description' => 'Thư giãn cơ thể với liệu trình tắm bùn khoáng thiên nhiên cao cấp.'],
                    ]
                ],
                2 => [
                    'title' => 'VinWonders Nha Trang',
                    'description' => 'Đi cáp treo vượt biển dài nhất thế giới sang đảo Hòn Tre vui chơi giải trí.',
                    'activities' => [
                        ['type' => 'Vui chơi', 'start_time' => '09:00:00', 'end_time' => '16:00:00', 'title' => 'Vui chơi VinWonders', 'description' => 'Trải nghiệm Đồi vạn hoa, Bánh xe bầu trời và công viên nước khổng lồ.'],
                    ]
                ],
                'default' => [
                    'title' => 'Tự do tắm biển, mua yến sào',
                    'description' => 'Mua sắm đặc sản Yến Sào Khánh Hòa, hải sản khô làm quà.',
                    'activities' => [
                        ['type' => 'Mua sắm', 'start_time' => '09:00:00', 'end_time' => '11:00:00', 'title' => 'Chợ Đầm Nha Trang', 'description' => 'Chợ trung tâm lớn nhất, mua sắm đồ lưu niệm và đặc sản địa phương.'],
                    ]
                ]
            ],
            'đà lạt' => [
                0 => [
                    'title' => 'Chào Đà Lạt mộng mơ - Quảng trường Lâm Viên',
                    'description' => 'Check-in các biểu tượng của thành phố sương mù, hít thở không khí trong lành.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '15:00:00', 'end_time' => '17:00:00', 'title' => 'Quảng trường Lâm Viên & Hồ Xuân Hương', 'description' => 'Chụp hình với nụ hoa Atiso và hoa Dã Quỳ khổng lồ.'],
                        ['type' => 'Ẩm thực', 'start_time' => '19:00:00', 'end_time' => '21:00:00', 'title' => 'Chợ đêm Đà Lạt', 'description' => 'Thưởng thức bánh tráng nướng, sữa đậu nành nóng giữa thời tiết se lạnh.'],
                    ]
                ],
                1 => [
                    'title' => 'Đồi chè Cầu Đất - Nông trại cún',
                    'description' => 'Săn mây tại đồi chè, tham quan các nông trại hoa và thú cưng.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '06:00:00', 'end_time' => '08:30:00', 'title' => 'Đồi chè Cầu Đất', 'description' => 'Đón bình minh rực rỡ và săn biển mây bồng bềnh tuyệt đẹp.'],
                        ['type' => 'Vui chơi', 'start_time' => '14:00:00', 'end_time' => '16:00:00', 'title' => 'Nông trại Puppy Farm', 'description' => 'Vui chơi với hàng chục giống chó cảnh đáng yêu, tham quan vườn dâu tây.'],
                    ]
                ],
                2 => [
                    'title' => 'Thác Datanla - Thiền viện Trúc Lâm',
                    'description' => 'Trải nghiệm máng trượt xuyên rừng và tịnh tâm tại ngôi thiền viện lớn nhất tỉnh Lâm Đồng.',
                    'activities' => [
                        ['type' => 'Vui chơi', 'start_time' => '09:00:00', 'end_time' => '11:30:00', 'title' => 'Thác Datanla & Máng trượt', 'description' => 'Trải nghiệm hệ thống máng trượt dài nhất Đông Nam Á qua các tán rừng thông.'],
                        ['type' => 'Tham quan', 'start_time' => '14:00:00', 'end_time' => '15:30:00', 'title' => 'Thiền viện Trúc Lâm', 'description' => 'Ngắm cảnh Hồ Tuyền Lâm từ trên cao, chiêm bái thiền viện uy nghi.'],
                    ]
                ],
                'default' => [
                    'title' => 'Sống ảo tại các quán cafe đẹp',
                    'description' => 'Thưởng thức cafe phố núi, thả dáng tại các địa điểm check-in cực hot.',
                    'activities' => [
                        ['type' => 'Nghỉ ngơi', 'start_time' => '09:00:00', 'end_time' => '11:00:00', 'title' => 'Thưởng thức Cafe Đà Lạt', 'description' => 'Nhâm nhi cafe, ngắm thung lũng thông reo rì rào.'],
                    ]
                ]
            ],
            'hạ long' => [
                0 => [
                    'title' => 'Đến bến cảng - Lên du thuyền 5 sao',
                    'description' => 'Check-in du thuyền sang trọng, thưởng thức bữa trưa hải sản giữa vịnh di sản.',
                    'activities' => [
                        ['type' => 'Ẩm thực', 'start_time' => '12:30:00', 'end_time' => '14:00:00', 'title' => 'Ăn trưa trên du thuyền', 'description' => 'Vừa thưởng thức bữa trưa búp phê vừa ngắm nhìn hàng ngàn đảo đá vôi.'],
                        ['type' => 'Trải nghiệm', 'start_time' => '15:30:00', 'end_time' => '17:00:00', 'title' => 'Chèo Kayak hang Luồn', 'description' => 'Tự tay chèo thuyền len lỏi qua các vách núi đá tuyệt đẹp.'],
                    ]
                ],
                1 => [
                    'title' => 'Hang Sửng Sốt - Đảo Ti Tốp',
                    'description' => 'Khám phá hang động kỳ vĩ nhất vịnh và tắm biển, leo núi ngắm toàn cảnh Vịnh Hạ Long.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '08:00:00', 'end_time' => '09:30:00', 'title' => 'Hang Sửng Sốt', 'description' => 'Chiêm ngưỡng những khối nhũ đá lấp lánh với nhiều hình thù độc đáo.'],
                        ['type' => 'Trải nghiệm', 'start_time' => '10:00:00', 'end_time' => '11:30:00', 'title' => 'Đảo Ti Tốp', 'description' => 'Tắm biển trong xanh, leo hơn 400 bậc thang để ngắm toàn cảnh Vịnh.'],
                    ]
                ],
                'default' => [
                    'title' => 'Sun World Hạ Long Park',
                    'description' => 'Trải nghiệm cáp treo Nữ Hoàng và công viên giải trí hàng đầu miền Bắc.',
                    'activities' => [
                        ['type' => 'Vui chơi', 'start_time' => '14:00:00', 'end_time' => '17:00:00', 'title' => 'Công viên Rồng & Nước', 'description' => 'Thỏa thích vui chơi hàng chục trò chơi cảm giác mạnh hiện đại.'],
                    ]
                ]
            ],
            'generic' => [
                0 => [
                    'title' => 'Chào đón tới {destination} & Tham quan trung tâm',
                    'description' => 'Hướng dẫn viên đón đoàn tại điểm hẹn, đưa về nhận phòng. Chiều tản bộ, tham quan các di tích tiêu biểu tại {destination}.',
                    'activities' => [
                        ['type' => 'Tham quan', 'start_time' => '14:00:00', 'end_time' => '17:00:00', 'title' => 'Tham quan điểm di tích', 'description' => 'Tìm hiểu lịch sử và văn hóa đặc trưng của {destination}.'],
                        ['type' => 'Ẩm thực', 'start_time' => '18:30:00', 'end_time' => '20:30:00', 'title' => 'Thưởng thức đặc sản địa phương', 'description' => 'Trải nghiệm tinh hoa ẩm thực mang đậm bản sắc vùng miền.'],
                    ]
                ],
                1 => [
                    'title' => 'Khám phá thiên nhiên {destination}',
                    'description' => 'Lên xe đi tới các khu du lịch sinh thái, danh lam thắng cảnh ngoạn mục của {destination}.',
                    'activities' => [
                        ['type' => 'Trải nghiệm', 'start_time' => '08:30:00', 'end_time' => '11:30:00', 'title' => 'Khám phá cảnh quan thiên nhiên', 'description' => 'Hòa mình vào thiên nhiên, tham gia các hoạt động dã ngoại hấp dẫn.'],
                        ['type' => 'Tham quan', 'start_time' => '14:00:00', 'end_time' => '16:30:00', 'title' => 'Tìm hiểu làng nghề truyền thống', 'description' => 'Giao lưu với người dân bản địa, xem quy trình sản xuất các sản phẩm thủ công.'],
                    ]
                ],
                2 => [
                    'title' => 'Chinh phục đỉnh cao và thư giãn',
                    'description' => 'Tham gia các hoạt động vui chơi giải trí ngoài trời hoặc thư giãn tại khu nghỉ dưỡng.',
                    'activities' => [
                        ['type' => 'Vui chơi', 'start_time' => '09:00:00', 'end_time' => '15:00:00', 'title' => 'Khu du lịch giải trí', 'description' => 'Tham gia các trò chơi sôi động, phù hợp cho mọi lứa tuổi.'],
                    ]
                ],
                'default' => [
                    'title' => 'Mua sắm đặc sản {destination} & Tạm biệt',
                    'description' => 'Tự do tham quan chợ địa phương, mua sắm quà lưu niệm trước khi chia tay {destination}.',
                    'activities' => [
                        ['type' => 'Mua sắm', 'start_time' => '08:30:00', 'end_time' => '10:30:00', 'title' => 'Chợ trung tâm {destination}', 'description' => 'Mua sắm các món quà ý nghĩa dành tặng người thân.'],
                        ['type' => 'Nghỉ ngơi', 'start_time' => '11:00:00', 'end_time' => '12:00:00', 'title' => 'Chuẩn bị hành lý, trả phòng', 'description' => 'Kiểm tra lại tư trang, HDV đưa đoàn ra điểm xuất phát.'],
                    ]
                ]
            ]
        ];
    }
}
