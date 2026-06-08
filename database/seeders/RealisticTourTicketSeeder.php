<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\TicketOption;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourImage;
use App\Models\TourItinerary;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealisticTourTicketSeeder extends Seeder
{
    public function run(): void
    {
        // Clear old data
        TourActivity::query()->delete();
        TourItinerary::query()->delete();
        TourSchedule::query()->delete();
        TourImage::query()->delete();
        TicketOption::query()->delete();
        TicketImage::query()->delete();
        Tour::withTrashed()->forceDelete();
        Ticket::query()->delete();

        $this->command->info('Đã xóa dữ liệu cũ. Bắt đầu seed dữ liệu mới...');

        // Build name→id maps (handles Spatie translatable JSON fields)
        $destinations = collect();
        foreach (Destination::all() as $dest) {
            $destinations[$dest->name] = $dest->id;
        }

        $categories = collect();
        foreach (Category::all() as $cat) {
            $categories[$cat->name] = $cat->id;
        }

        if ($destinations->isEmpty() || $categories->isEmpty()) {
            $this->command->error('Cần có Destinations và Categories trước. Chạy DatabaseSeeder trước.');

            return;
        }

        $this->seedTours($destinations, $categories);
        $this->seedTickets($destinations);

        $this->command->info('Seed hoàn tất! Đã tạo dữ liệu tour và vé tham quan chân thực.');
    }

    private function seedTours($destinations, $categories): void
    {
        $toursData = [
            // ====== ĐÀ NẴNG ======
            [
                'title' => 'Đà Nẵng - Hội An - Bà Nà Hills 4 ngày 3 đêm',
                'destination' => 'Đà Nẵng',
                'departure' => 'Hà Nội',
                'category' => 'Du lịch biển',
                'days' => 4,
                'nights' => 3,
                'base_price' => 5990000,
                'child_price' => 3490000,
                'description' => "Khám phá thành phố đáng sống nhất Việt Nam với hành trình Đà Nẵng - Hội An - Bà Nà Hills đầy ấn tượng. Tận hưởng bãi biển Mỹ Khê tuyệt đẹp, dạo bước trên phố cổ Hội An lung linh đèn lồng, và chinh phục đỉnh Bà Nà với Cầu Vàng nổi tiếng thế giới.\n\nTour bao gồm vé máy bay khứ hồi, khách sạn 4 sao, xe đưa đón và hướng dẫn viên chuyên nghiệp.",
                'images' => [
                    'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                    'https://images.unsplash.com/photo-1583417319070-4a69db38a482?q=80&w=800',
                    'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Hà Nội → Đà Nẵng - Bán đảo Sơn Trà', 'description' => "Đón khách tại sân bay Nội Bài, khởi hành chuyến bay đến Đà Nẵng. Đến nơi, xe và HDV đón đoàn tại sân bay Đà Nẵng.\n\nBuổi chiều: Tham quan Bán đảo Sơn Trà - ngắm toàn cảnh thành phố từ trên cao, ghé thăm chùa Linh Ứng với tượng Phật Quan Thế Âm cao 67m.\n\nBuổi tối: Tự do dạo biển Mỹ Khê, thưởng thức hải sản tươi sống tại các nhà hàng ven biển. Nhận phòng khách sạn, nghỉ ngơi."],
                    ['day' => 2, 'title' => 'Bà Nà Hills - Cầu Vàng - Fantasy Park', 'description' => "Sáng sớm: Khởi hành đến khu du lịch Bà Nà Hills. Trải nghiệm cáp treo dài nhất Đông Nam Á với tầm nhìn ngoạn mục xuống thung lũng.\n\nTham quan Cầu Vàng (Golden Bridge) - biểu tượng du lịch Việt Nam, check-in tại Làng Pháp với kiến trúc châu Âu cổ kính.\n\nBuổi chiều: Vui chơi tại Fantasy Park - khu giải trí trong nhà lớn nhất Việt Nam với hàng trăm trò chơi hấp dẫn. Quay về khách sạn nghỉ ngơi."],
                    ['day' => 3, 'title' => 'Phố cổ Hội An - Làng rau Trà Quế', 'description' => "Buổi sáng: Di chuyển đến phố cổ Hội An (cách Đà Nẵng 30km). Tham quan Chùa Cầu Nhật Bản, Nhà cổ Tấn Ký, Hội quán Phúc Kiến.\n\nBuổi trưa: Thưởng thức đặc sản Hội An: Cao lầu, mì Quảng, bánh bao bánh vạc (White Rose).\n\nBuổi chiều: Trải nghiệm làm nông dân tại Làng rau Trà Quế - trồng rau, tưới nước, thu hoạch.\n\nBuổi tối: Thả hoa đăng trên sông Hoài, dạo phố đêm Hội An lung linh ánh đèn lồng."],
                    ['day' => 4, 'title' => 'Ngũ Hành Sơn - Chợ Hàn - Về Hà Nội', 'description' => "Buổi sáng: Tham quan danh thắng Ngũ Hành Sơn (Marble Mountains) - khám phá các hang động và chùa cổ bên trong núi đá. Ghé làng đá mỹ nghệ Non Nước.\n\nBuổi trưa: Mua sắm đặc sản tại chợ Hàn - khu chợ sầm uất nhất Đà Nẵng.\n\nBuổi chiều: Xe đưa đoàn ra sân bay, khởi hành về Hà Nội. Kết thúc chuyến đi, hẹn gặp lại!"],
                ],
            ],
            [
                'title' => 'Đà Nẵng - Cù Lao Chàm lặn ngắm san hô 3 ngày 2 đêm',
                'destination' => 'Đà Nẵng',
                'departure' => 'Đà Nẵng',
                'category' => 'Tour phiêu lưu',
                'days' => 3,
                'nights' => 2,
                'base_price' => 3890000,
                'child_price' => 2290000,
                'description' => "Hành trình khám phá Cù Lao Chàm - Khu dự trữ sinh quyển thế giới UNESCO với hệ sinh thái biển đa dạng. Trải nghiệm lặn ngắm san hô, bơi cùng cá nhiệt đới và khám phá đảo hoang sơ.\n\nPhù hợp cho những ai yêu thích biển đảo và hoạt động ngoài trời. Tour khởi hành hàng tuần vào thứ 6.",
                'images' => [
                    'https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=800',
                    'https://images.unsplash.com/photo-1682687220742-aba13b6e50ba?q=80&w=800',
                    'https://images.unsplash.com/photo-1559827260-dc66d52bef19?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Đà Nẵng - Bãi biển An Bàng', 'description' => "Tập trung tại khách sạn, nhận phòng và nghỉ ngơi. Buổi chiều tự do tắm biển tại bãi biển An Bàng - một trong những bãi biển đẹp nhất châu Á.\n\nBuổi tối: Họp mặt đoàn, HDV phổ biến lịch trình và hướng dẫn an toàn khi lặn biển."],
                    ['day' => 2, 'title' => 'Cù Lao Chàm - Lặn ngắm san hô', 'description' => "6h30: Khởi hành ra Cù Lao Chàm bằng ca nô cao tốc (khoảng 20 phút).\n\nSáng: Lặn ngắm san hô tại vùng biển bảo tồn, ngắm rạn san hô tự nhiên và hệ sinh vật biển phong phú.\n\nTrưa: Thưởng thức hải sản tươi sống do ngư dân đánh bắt, ăn trưa trên đảo.\n\nChiều: Tham quan Bãi Bắc, bơi lội tự do, chèo thuyền kayak quanh đảo.\n\n16h: Về lại Đà Nẵng, tự do nghỉ ngơi."],
                    ['day' => 3, 'title' => 'Khám phá Đà Nẵng - Tiễn khách', 'description' => "Buổi sáng: Tham quan cầu Rồng, cầu Tình Yêu, bảo tàng điêu khắc Chăm.\n\nTrưa: Thưởng thức bún chả cá Đà Nẵng, bánh tráng cuốn thịt heo.\n\nChiều: Tự do mua sắm, kết thúc tour."],
                ],
            ],

            // ====== PHÚ QUỐC ======
            [
                'title' => 'Phú Quốc All-Inclusive Resort 5 sao 4 ngày 3 đêm',
                'destination' => 'Phú Quốc',
                'departure' => 'Hà Nội',
                'category' => 'Du lịch biển',
                'days' => 4,
                'nights' => 3,
                'base_price' => 8990000,
                'child_price' => 5490000,
                'description' => "Nghỉ dưỡng đẳng cấp tại đảo ngọc Phú Quốc với resort 5 sao bên bờ biển. Trọn gói vé máy bay, phòng nghỉ view biển, buffet sáng, spa và các hoạt động giải trí.\n\nĐây là lựa chọn hoàn hảo cho kỳ nghỉ gia đình hoặc trăng mật lãng mạn.",
                'images' => [
                    'https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=800',
                    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800',
                    'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Bay đến Phú Quốc - Check-in Resort', 'description' => "Khởi hành từ sân bay Nội Bài, bay thẳng đến Phú Quốc (khoảng 2h15p). Xe resort đón tại sân bay.\n\nCheck-in resort 5 sao, nhận phòng Ocean View. Tự do tận hưởng hồ bơi infinity, bãi biển riêng tư và các tiện ích resort.\n\nBuổi tối: Welcome Dinner với thực đơn hải sản đặc biệt của resort."],
                    ['day' => 2, 'title' => 'Tour 4 đảo - Câu cá - Lặn biển', 'description' => "7h00: Xuất phát tour 4 đảo bằng tàu gỗ truyền thống.\n\nDừng chân tại Hòn Thơm - bãi cát trắng mịn, nước biển trong vắt. Lặn ngắm san hô tại Hòn Gầm Ghì.\n\nCâu cá trên thuyền giữa biển khơi, thưởng thức cá tươi nướng ngay trên tàu.\n\nChiều: Về resort, thư giãn tại Spa với gói massage 60 phút (đã bao gồm trong tour)."],
                    ['day' => 3, 'title' => 'VinWonders - Grand World - Chợ đêm', 'description' => "Buổi sáng: Tham quan công viên giải trí VinWonders Phú Quốc với hơn 100 trò chơi cảm giác mạnh, thủy cung Vinpearl.\n\nBuổi chiều: Khám phá Grand World - thị trấn không ngủ với kiến trúc Venice thu nhỏ, chèo thuyền gondola trên kênh đào.\n\nBuổi tối: Dạo chợ đêm Phú Quốc - thưởng thức nhum nướng mỡ hành, ghẹ hấp, nước mắm Phú Quốc chính gốc."],
                    ['day' => 4, 'title' => 'Vườn tiêu - Nhà thùng nước mắm - Về Hà Nội', 'description' => "Buổi sáng: Trải nghiệm check-out muộn (đến 14h). Tham quan vườn tiêu Phú Quốc - đặc sản nổi tiếng của đảo.\n\nGhé nhà thùng nước mắm truyền thống, tìm hiểu quy trình sản xuất nước mắm Phú Quốc - di sản phi vật thể.\n\nMua sắm quà lưu niệm: tiêu xanh, nước mắm, rượu sim.\n\nChiều: Ra sân bay, bay về Hà Nội. Kết thúc hành trình."],
                ],
            ],
            [
                'title' => 'Phú Quốc mạo hiểm: Cano, Dù lượn, Sunset Party 3N2Đ',
                'destination' => 'Phú Quốc',
                'departure' => 'Đà Nẵng',
                'category' => 'Tour phiêu lưu',
                'days' => 3,
                'nights' => 2,
                'base_price' => 6490000,
                'child_price' => 4190000,
                'description' => "Tour phiêu lưu dành cho những tâm hồn thích khám phá và trải nghiệm mạnh. Lướt cano tốc độ cao, dù lượn trên biển, và kết thúc bằng bữa tiệc hoàng hôn đáng nhớ bên bờ biển Phú Quốc.\n\nTour dành cho du khách từ 16 tuổi trở lên. Nhóm tối thiểu 4 người.",
                'images' => [
                    'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?q=80&w=800',
                    'https://images.unsplash.com/photo-1530053969600-caed2596d242?q=80&w=800',
                    'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Đến Phú Quốc - Sunset Party', 'description' => "Bay từ Đà Nẵng đến Phú Quốc. Check-in khách sạn boutique ven biển.\n\nChiều: Tập trung tại Beach Club, làm quen đoàn.\n\nTối: Sunset Party bên bờ biển với DJ, cocktail, BBQ hải sản. Ngắm hoàng hôn tuyệt đẹp trên biển Tây."],
                    ['day' => 2, 'title' => 'Cano tốc độ - Dù lượn - Kayak', 'description' => "Sáng: Trải nghiệm lướt cano tốc độ cao vòng quanh vịnh, cảm giác phấn khích tột độ.\n\nTiếp theo: Bay dù lượn (parasailing) trên biển ở độ cao 100m, ngắm toàn cảnh đảo Phú Quốc từ trên không.\n\nChiều: Chèo kayak khám phá các bãi biển hoang sơ, ghé Bãi Sao - bãi biển đẹp nhất Phú Quốc.\n\nTối: Tiệc BBQ hải sản tự nướng trên bãi biển dưới ánh trăng."],
                    ['day' => 3, 'title' => 'Trekking rừng nguyên sinh - Về', 'description' => "Sáng sớm: Trekking xuyên Vườn Quốc gia Phú Quốc - rừng nguyên sinh với hệ sinh thái đa dạng.\n\nTắm suối Đá Bàn giữa rừng nhiệt đới.\n\nTrưa: Ăn trưa tại quán ăn địa phương, thưởng thức bún quậy - đặc sản chỉ có ở Phú Quốc.\n\nChiều: Ra sân bay, bay về. Chia tay đoàn, hẹn gặp lại ở hành trình tiếp theo!"],
                ],
            ],

            // ====== HÀ NỘI ======
            [
                'title' => 'Hà Nội văn hóa: Phố cổ - Hoàng Thành - Văn Miếu 2N1Đ',
                'destination' => 'Hà Nội',
                'departure' => 'Đà Nẵng',
                'category' => 'Khám phá di sản',
                'days' => 2,
                'nights' => 1,
                'base_price' => 2490000,
                'child_price' => 1490000,
                'description' => "Hành trình khám phá Hà Nội ngàn năm văn hiến - thủ đô nghìn năm tuổi với bề dày lịch sử và văn hóa đặc sắc. Dạo phố cổ 36 phố phường, tham quan Hoàng Thành Thăng Long - Di sản Thế giới UNESCO và Văn Miếu Quốc Tử Giám.\n\nTour phù hợp cho mọi lứa tuổi, đặc biệt là học sinh sinh viên và người yêu lịch sử.",
                'images' => [
                    'https://images.unsplash.com/photo-1583417319070-4a69db38a482?q=80&w=800',
                    'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
                    'https://images.unsplash.com/photo-1598321436786-a75e2b4ad770?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Đến Hà Nội - Phố cổ - Hoàng Thành', 'description' => "Đón khách tại sân bay Nội Bài, di chuyển vào trung tâm thành phố.\n\nBuổi sáng: Tham quan Hoàng Thành Thăng Long - di tích lịch sử được UNESCO công nhận là Di sản Thế giới năm 2010.\n\nBuổi chiều: Dạo bộ phố cổ Hà Nội - 36 phố phường sầm uất. Ghé Nhà thờ Lớn, Hồ Gươm, Đền Ngọc Sơn, cầu Thê Húc.\n\nBuổi tối: Xem biểu diễn múa rối nước tại Nhà hát Múa rối Thăng Long. Thưởng thức phở bò Lý Quốc Sư."],
                    ['day' => 2, 'title' => 'Văn Miếu - Lăng Bác - Về', 'description' => "Buổi sáng: Viếng Lăng Chủ tịch Hồ Chí Minh (thứ 2 và thứ 6 đóng cửa), tham quan Phủ Chủ tịch, nhà sàn Bác Hồ.\n\nTiếp theo: Tham quan Văn Miếu Quốc Tử Giám - trường đại học đầu tiên của Việt Nam, biểu tượng giáo dục.\n\nBuổi trưa: Thưởng thức bún chả Hàng Mành, cà phê trứng Giảng.\n\nChiều: Mua sắm quà lưu niệm tại phố Hàng Gai, phố Hàng Bạc. Tiễn khách ra sân bay."],
                ],
            ],

            // ====== HẠ LONG ======
            [
                'title' => 'Du thuyền Hạ Long 5 sao 2 ngày 1 đêm',
                'destination' => 'Hạ Long',
                'departure' => 'Hà Nội',
                'category' => 'Du lịch biển',
                'days' => 2,
                'nights' => 1,
                'base_price' => 4590000,
                'child_price' => 2790000,
                'description' => "Trải nghiệm đẳng cấp trên du thuyền 5 sao giữa Vịnh Hạ Long - Di sản Thiên nhiên Thế giới UNESCO. Ngủ đêm trên vịnh, ngắm bình minh giữa hàng nghìn hòn đảo đá vôi, thưởng thức ẩm thực cao cấp.\n\nDu thuyền trang bị phòng ngủ riêng, nhà hàng, quầy bar, sân thượng ngắm cảnh và dịch vụ spa.",
                'images' => [
                    'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                    'https://images.unsplash.com/photo-1573790387438-4da905039392?q=80&w=800',
                    'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Hà Nội → Hạ Long - Lên du thuyền', 'description' => "7h30: Xe đón tại khách sạn Hà Nội, khởi hành đi Hạ Long (khoảng 2h30 đi cao tốc).\n\n12h00: Lên du thuyền tại cảng Tuần Châu. Nhận phòng, thưởng thức bữa trưa trên vịnh.\n\nChiều: Du thuyền di chuyển qua các hòn đảo nổi tiếng: Hòn Gà Chọi, Hòn Đỉnh Hương. Tham quan động Sửng Sốt - một trong những hang động đẹp nhất Hạ Long.\n\nHoàng hôn: Thư giãn trên sân thượng, ngắm mặt trời lặn giữa vịnh. Tham gia lớp học nấu ăn Việt Nam trên tàu.\n\nTối: Câu mực đêm trên vịnh, thưởng thức cocktail tại Sky Bar."],
                    ['day' => 2, 'title' => 'Bình minh trên vịnh - Về Hà Nội', 'description' => "5h30: Thức dậy ngắm bình minh trên vịnh - khoảnh khắc đáng nhớ nhất của hành trình.\n\n6h00: Tập Tai Chi trên sân thượng cùng huấn luyện viên chuyên nghiệp.\n\n7h00: Buffet sáng đa dạng trên du thuyền.\n\n8h30: Chèo kayak hoặc đi thuyền nan khám phá làng chài Cửa Vạn - làng chài nổi cổ nhất Hạ Long.\n\n10h30: Check-out, thưởng thức brunch nhẹ. Du thuyền về cảng.\n\n12h00: Lên xe về Hà Nội, kết thúc hành trình."],
                ],
            ],
            [
                'title' => 'Hạ Long - Cát Bà - Đảo Khỉ phiêu lưu 3N2Đ',
                'destination' => 'Hạ Long',
                'departure' => 'Hà Nội',
                'category' => 'Tour phiêu lưu',
                'days' => 3,
                'nights' => 2,
                'base_price' => 4290000,
                'child_price' => 2590000,
                'description' => "Kết hợp du thuyền Hạ Long với khám phá đảo Cát Bà hoang sơ. Trekking rừng nguyên sinh Vườn Quốc gia Cát Bà, ghé thăm Đảo Khỉ và tận hưởng biển xanh cát trắng bãi tắm Cát Cò.\n\nPhù hợp cho nhóm bạn trẻ yêu thích thiên nhiên và hoạt động ngoài trời.",
                'images' => [
                    'https://images.unsplash.com/photo-1573790387438-4da905039392?q=80&w=800',
                    'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                    'https://images.unsplash.com/photo-1559827260-dc66d52bef19?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Hà Nội → Hạ Long - Du thuyền', 'description' => 'Xe đón tại Hà Nội, di chuyển đến bến tàu Hạ Long. Lên du thuyền, ăn trưa trên vịnh. Tham quan hang Thiên Cung, hòn Chó Đá. Tối ngủ trên tàu giữa vịnh.'],
                    ['day' => 2, 'title' => 'Cát Bà - Đảo Khỉ - Trekking', 'description' => "Di chuyển bằng tàu cao tốc sang đảo Cát Bà. Sáng: Tham quan Đảo Khỉ - đảo tự nhiên với hàng trăm con khỉ vàng hoang dã.\n\nChiều: Trekking nhẹ trong Vườn Quốc gia Cát Bà - khu dự trữ sinh quyển thế giới, ngắm cảnh từ đỉnh Ngự Lâm.\n\nTối: Nghỉ tại khách sạn trên đảo Cát Bà, tự do dạo phố và thưởng thức hải sản."],
                    ['day' => 3, 'title' => 'Bãi tắm Cát Cò - Về Hà Nội', 'description' => "Sáng: Tắm biển tại bãi Cát Cò 1 - bãi biển đẹp nhất Cát Bà với nước biển trong xanh.\n\nTrưa: Thưởng thức hải sản tươi đặc sản: tôm hùm, sam biển, hàu sữa.\n\nChiều: Di chuyển về Hà Nội qua phà Gót - Phù Long, ngắm cảnh đồng quê Bắc Bộ trên đường về."],
                ],
            ],

            // ====== SAPA ======
            [
                'title' => 'Sapa trekking bản làng dân tộc 3 ngày 2 đêm',
                'destination' => 'Sapa',
                'departure' => 'Hà Nội',
                'category' => 'Nghỉ dưỡng núi',
                'days' => 3,
                'nights' => 2,
                'base_price' => 3290000,
                'child_price' => 1990000,
                'description' => "Hành trình trekking qua những bản làng dân tộc H'Mông, Dao Đỏ với ruộng bậc thang tuyệt đẹp. Trải nghiệm homestay tại nhà người dân bản địa, tìm hiểu văn hóa truyền thống và chinh phục đỉnh Fansipan - Nóc nhà Đông Dương.\n\nTour khởi hành thứ 5 hàng tuần. Mang giày trekking và áo khoác ấm.",
                'images' => [
                    'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                    'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                    'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Hà Nội → Sapa - Bản Cát Cát', 'description' => "Sáng sớm: Xuất phát từ Hà Nội đi Sapa bằng xe limousine VIP (khoảng 5h qua cao tốc Nội Bài - Lào Cai).\n\nĐến Sapa, nhận phòng khách sạn.\n\nChiều: Trekking xuống bản Cát Cát - bản du lịch nổi tiếng của người H'Mông. Ngắm thác Cát Cát, tìm hiểu nghề dệt thổ cẩm truyền thống.\n\nTối: Ăn tối tại nhà hàng bản địa với lợn cắp nách nướng, thắng cố ngựa, rượu ngô Sapa."],
                    ['day' => 2, 'title' => 'Chinh phục đỉnh Fansipan', 'description' => "Sáng: Di chuyển đến ga cáp treo Fansipan. Trải nghiệm cáp treo 3 dây hiện đại nhất thế giới lên gần đỉnh.\n\nLeo 600 bậc đá để chinh phục nóc nhà Đông Dương ở độ cao 3.143m. Chụp ảnh check-in tại cột mốc đỉnh Fansipan.\n\nTham quan quần thể tâm linh trên đỉnh: tượng Phật Bà Quan Âm, chùa Bích Vân Thiền Tự.\n\nChiều: Xuống núi, nghỉ ngơi. Tối: Tự do dạo phố Sapa, mua quà thổ cẩm."],
                    ['day' => 3, 'title' => 'Bản Tả Phìn - Về Hà Nội', 'description' => "Sáng: Trekking đến bản Tả Phìn - bản người Dao Đỏ. Trải nghiệm tắm thuốc lá người Dao với 120 loại thảo dược rừng.\n\nTham quan nhà truyền thống người Dao, tìm hiểu nghề nhuộm chàm và thêu thổ cẩm.\n\nTrưa: Ăn trưa với món đặc sản Sapa: cá hồi Sa Pa, rau cải mèo xào tỏi.\n\nChiều: Lên xe limousine về Hà Nội. Đến Hà Nội khoảng 21h, kết thúc hành trình."],
                ],
            ],

            // ====== ĐÀ LẠT ======
            [
                'title' => 'Đà Lạt lãng mạn: Hoa, Cà phê & Thác nước 3N2Đ',
                'destination' => 'Đà Lạt',
                'departure' => 'Đà Nẵng',
                'category' => 'Nghỉ dưỡng núi',
                'days' => 3,
                'nights' => 2,
                'base_price' => 3590000,
                'child_price' => 2190000,
                'description' => "Đà Lạt - thành phố ngàn hoa, xứ sở mộng mơ với khí hậu mát mẻ quanh năm. Tham quan vườn hoa, đồi chè, thác nước và thưởng thức cà phê thượng hạng giữa rừng thông.\n\nTour đặc biệt phù hợp cho các cặp đôi, gia đình nhỏ hoặc nhóm bạn thân.",
                'images' => [
                    'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                    'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
                    'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Đến Đà Lạt - Hồ Xuân Hương - Chợ đêm', 'description' => "Đón khách tại sân bay Liên Khương (cách Đà Lạt 30km). Di chuyển vào trung tâm thành phố.\n\nChiều: Dạo quanh Hồ Xuân Hương - trái tim của Đà Lạt, ghé thăm Vườn hoa Thành phố với hàng trăm loài hoa rực rỡ.\n\nTối: Khám phá chợ đêm Đà Lạt - thiên đường ẩm thực với bánh tráng nướng, sữa đậu nành nóng, kem bơ Đà Lạt."],
                    ['day' => 2, 'title' => 'Đồi chè Cầu Đất - Thác Datanla - Cà phê rừng', 'description' => "Sáng: Xuất phát đến đồi chè Cầu Đất Farm - đồi chè đẹp nhất Đà Lạt, check-in sống ảo với thảm chè xanh mướt bất tận.\n\nTiếp theo: Khám phá thác Datanla - trải nghiệm đường trượt Alpine Coaster xuyên rừng thông.\n\nChiều: Thưởng thức cà phê chồn tại quán cà phê giữa rừng thông Đà Lạt, view thung lũng tuyệt đẹp.\n\nTối: Tự do. Gợi ý: thưởng thức lẩu gà lá é - đặc sản không thể bỏ lỡ."],
                    ['day' => 3, 'title' => 'Dinh Bảo Đại - Vườn dâu - Về', 'description' => "Sáng: Tham quan Dinh III Bảo Đại - cung điện mùa hè của vua Bảo Đại với kiến trúc Art Deco.\n\nTiếp theo: Vào vườn dâu tự tay hái dâu tây Đà Lạt tươi, thưởng thức ngay tại vườn.\n\nMua đặc sản Đà Lạt: mứt dâu, atiso, cà phê weasel.\n\nChiều: Ra sân bay Liên Khương, bay về. Chia tay Đà Lạt mộng mơ."],
                ],
            ],
            [
                'title' => 'Đà Lạt cắm trại săn mây đỉnh Langbiang 2N1Đ',
                'destination' => 'Đà Lạt',
                'departure' => 'Đà Lạt',
                'category' => 'Tour phiêu lưu',
                'days' => 2,
                'nights' => 1,
                'base_price' => 1890000,
                'child_price' => 1290000,
                'description' => "Trải nghiệm cắm trại qua đêm trên đỉnh Langbiang ở độ cao 2.167m và săn mây lúc bình minh. Tour bao gồm lều trại, túi ngủ, đồ ăn nhẹ và HDV chuyên nghiệp.\n\nTour dành cho người có sức khỏe tốt, thích hoạt động ngoài trời. Nhóm 6-12 người.",
                'images' => [
                    'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                    'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                    'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                ],
                'itineraries' => [
                    ['day' => 1, 'title' => 'Leo núi Langbiang - Cắm trại', 'description' => "13h00: Tập trung tại chân núi Langbiang, HDV phổ biến lịch trình và kiểm tra trang thiết bị.\n\n13h30: Bắt đầu trekking lên đỉnh núi qua con đường mòn xuyên rừng thông (khoảng 2-3 tiếng).\n\n16h30: Đến đỉnh, dựng lều trại. Ngắm hoàng hôn tuyệt đẹp với biển mây bao phủ Đà Lạt.\n\nTối: Đốt lửa trại, BBQ ngoài trời, ngắm sao trời và kể chuyện cùng nhóm."],
                    ['day' => 2, 'title' => 'Săn mây bình minh - Về', 'description' => "4h30: Thức dậy, chuẩn bị camera. 5h00: Săn mây bình minh - khoảnh khắc biển mây trải dài bất tận dưới chân núi, ánh nắng vàng xuyên qua lớp sương.\n\n6h30: Ăn sáng nhẹ trên đỉnh núi với cà phê nóng và bánh mì.\n\n7h30: Thu dọn lều trại, trekking xuống núi.\n\n10h00: Về đến chân núi, kết thúc hành trình. Tặng ảnh chụp chuyên nghiệp cho đoàn."],
                ],
            ],
        ];

        foreach ($toursData as $data) {
            $destId = $destinations[$data['destination']] ?? null;
            $departureId = $destinations[$data['departure']] ?? $destId;
            $catId = $categories[$data['category']] ?? null;

            if (! $destId) {
                continue;
            }

            $tour = Tour::create([
                'slug' => Str::slug($data['title']).'-'.uniqid(),
                'destination_id' => $destId,
                'departure_location_id' => $departureId,
                'title' => $data['title'],
                'description' => $data['description'],
                'duration_days' => $data['days'],
                'duration_nights' => $data['nights'],
                'base_price' => $data['base_price'],
                'child_price' => $data['child_price'],
            ]);

            if ($catId) {
                $tour->categories()->sync([$catId]);
            }

            // Images
            foreach ($data['images'] as $i => $url) {
                TourImage::create([
                    'tour_id' => $tour->id,
                    'image_url' => $url,
                    'is_primary' => $i === 0 ? 1 : 0,
                ]);
            }

            // Itineraries
            foreach ($data['itineraries'] as $it) {
                TourItinerary::create([
                    'tour_id' => $tour->id,
                    'day_number' => $it['day'],
                    'title' => $it['title'],
                    'description' => $it['description'],
                ]);
            }

            // Schedules - 4 upcoming dates spaced out
            $scheduleOffsets = [5, 12, 20, 28];
            foreach ($scheduleOffsets as $offset) {
                $depDate = Carbon::now()->addDays($offset);
                $retDate = (clone $depDate)->addDays($data['days']);
                $capacity = rand(25, 40);

                TourSchedule::create([
                    'tour_id' => $tour->id,
                    'departure_date' => $depDate->toDateTimeString(),
                    'return_date' => $retDate->toDateTimeString(),
                    'capacity' => $capacity,
                    'available_seats' => rand(3, $capacity),
                    'status' => 'available',
                ]);
            }

            $this->command->info("  ✓ Tour: {$data['title']}");
        }
    }

    private function seedTickets($destinations): void
    {
        $ticketsData = [
            // ====== ĐÀ NẴNG ======
            [
                'title' => 'Vé Bà Nà Hills trọn gói (cáp treo + Fantasy Park)',
                'destination' => 'Đà Nẵng',
                'provider' => 'Sun World Bà Nà Hills',
                'cancellation' => 'Hủy miễn phí trước 24 giờ',
                'description' => "Vé trọn gói tham quan khu du lịch Sun World Bà Nà Hills bao gồm cáp treo 2 chiều, vào cổng Fantasy Park và tất cả các trò chơi trong nhà.\n\nKhám phá Cầu Vàng, Làng Pháp, vườn hoa Le Jardin D'Amour và tận hưởng không khí se lạnh trên đỉnh núi quanh năm.",
                'images' => [
                    'https://images.unsplash.com/photo-1583417319070-4a69db38a482?q=80&w=800',
                    'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Vé Người Lớn', 'desc' => 'Áp dụng cho khách từ 1m4 trở lên. Bao gồm cáp treo, Fantasy Park, tất cả trò chơi.', 'price' => 900000, 'original' => 1000000],
                    ['name' => 'Vé Trẻ Em (1m - 1m4)', 'desc' => 'Áp dụng cho trẻ em cao từ 1m đến dưới 1m4. Bao gồm cáp treo và vào cổng.', 'price' => 700000, 'original' => 750000],
                    ['name' => 'Combo Gia Đình (2 NL + 1 TE)', 'desc' => 'Tiết kiệm 15% so với mua lẻ. 2 vé người lớn + 1 vé trẻ em.', 'price' => 2100000, 'original' => 2750000],
                ],
            ],
            [
                'title' => 'Vé Công viên suối khoáng nóng Núi Thần Tài',
                'destination' => 'Đà Nẵng',
                'provider' => 'Núi Thần Tài Resort',
                'cancellation' => 'Hủy miễn phí trước 48 giờ',
                'description' => "Tắm suối khoáng nóng thiên nhiên giữa núi rừng Bà Nà, trải nghiệm tắm bùn thảo dược và vui chơi tại công viên nước.\n\nKhu du lịch nằm trong thung lũng xanh mát với hơn 20 hạng mục giải trí: trượt nước, hồ bơi sóng, massage thác nước.",
                'images' => [
                    'https://images.unsplash.com/photo-1571509930722-df38ccfb8611?q=80&w=800',
                    'https://images.unsplash.com/photo-1582653291997-079a1c04e5d1?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Vé Người Lớn', 'desc' => 'Vào cổng + tắm suối khoáng + công viên nước.', 'price' => 500000, 'original' => 600000],
                    ['name' => 'Vé Trẻ Em', 'desc' => 'Trẻ em từ 1m đến dưới 1m4.', 'price' => 300000, 'original' => 400000],
                    ['name' => 'Combo VIP (Suối khoáng + Tắm bùn)', 'desc' => 'Bao gồm suối khoáng + 1 lần tắm bùn thảo dược 30 phút.', 'price' => 750000, 'original' => 900000],
                ],
            ],

            // ====== PHÚ QUỐC ======
            [
                'title' => 'Vé VinWonders Phú Quốc',
                'destination' => 'Phú Quốc',
                'provider' => 'VinWonders Phú Quốc',
                'cancellation' => 'Hủy miễn phí trước 24 giờ',
                'description' => "Vé tham quan công viên giải trí VinWonders Phú Quốc - một trong những công viên chủ đề lớn nhất Đông Nam Á với 6 vùng chơi mang phong cách khác nhau.\n\nBao gồm thủy cung Vinpearl với hơn 300 loài sinh vật biển, công viên nước, trò chơi cảm giác mạnh và các show diễn nghệ thuật.",
                'images' => [
                    'https://images.unsplash.com/photo-1513889961551-628c1e5e2ee9?q=80&w=800',
                    'https://images.unsplash.com/photo-1605810230434-7631ac76ec81?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Vé Người Lớn', 'desc' => 'Vào cổng trọn gói tất cả trò chơi và show diễn.', 'price' => 880000, 'original' => 980000],
                    ['name' => 'Vé Trẻ Em (1m - 1m4)', 'desc' => 'Áp dụng cho trẻ em cao từ 1m đến dưới 1m4.', 'price' => 680000, 'original' => 780000],
                ],
            ],
            [
                'title' => 'Vé cáp treo Hòn Thơm Nature Park',
                'destination' => 'Phú Quốc',
                'provider' => 'Sun Group',
                'cancellation' => 'Không hoàn hủy sau khi xuất vé',
                'description' => "Trải nghiệm tuyến cáp treo vượt biển dài nhất thế giới (7.899,9m) từ An Thới ra đảo Hòn Thơm.\n\nNgắm toàn cảnh quần đảo An Thới từ trên cao với nước biển xanh ngọc bích. Tại Hòn Thơm Nature Park, tận hưởng bãi biển riêng tư, công viên nước và các hoạt động biển.",
                'images' => [
                    'https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=800',
                    'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Vé Cáp treo khứ hồi', 'desc' => 'Cáp treo 2 chiều An Thới - Hòn Thơm.', 'price' => 350000, 'original' => 500000],
                    ['name' => 'Combo Cáp treo + Aquatopia', 'desc' => 'Cáp treo 2 chiều + vé công viên nước Aquatopia.', 'price' => 650000, 'original' => 850000],
                    ['name' => 'Combo All-in-one', 'desc' => 'Cáp treo + Aquatopia + buffet trưa hải sản.', 'price' => 900000, 'original' => 1200000],
                ],
            ],

            // ====== HẠ LONG ======
            [
                'title' => 'Vé tham quan Vịnh Hạ Long bằng tàu du lịch',
                'destination' => 'Hạ Long',
                'provider' => 'Ban Quản lý Vịnh Hạ Long',
                'cancellation' => 'Hủy miễn phí trước 48 giờ',
                'description' => "Vé tham quan Vịnh Hạ Long trên tàu du lịch đi trong ngày (4-6 tiếng). Lộ trình ghé thăm các hang động nổi tiếng: động Sửng Sốt, hang Đầu Gỗ, và ngắm cảnh Hòn Gà Chọi.\n\nBao gồm bữa trưa hải sản trên tàu và thời gian tắm biển tại Ti Tốp.",
                'images' => [
                    'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800',
                    'https://images.unsplash.com/photo-1573790387438-4da905039392?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Vé Người Lớn (tàu ghép)', 'desc' => 'Ghép đoàn, bao gồm ăn trưa, HDV, vé tham quan hang.', 'price' => 650000, 'original' => 800000],
                    ['name' => 'Vé Trẻ Em (dưới 1m4)', 'desc' => 'Trẻ em cao dưới 1m4. Bao gồm ăn trưa.', 'price' => 400000, 'original' => 500000],
                    ['name' => 'Vé VIP (tàu riêng nhóm 10 khách)', 'desc' => 'Tàu riêng cho nhóm tối đa 10 khách, lộ trình tùy chỉnh.', 'price' => 5500000, 'original' => 7000000],
                ],
            ],

            // ====== SAPA ======
            [
                'title' => 'Vé cáp treo Fansipan Legend',
                'destination' => 'Sapa',
                'provider' => 'Sun Group - Fansipan Legend',
                'cancellation' => 'Hủy miễn phí trước 24 giờ',
                'description' => "Vé cáp treo Fansipan - chinh phục nóc nhà Đông Dương ở độ cao 3.143m chỉ trong 15 phút. Tuyến cáp treo 3 dây tiên tiến nhất thế giới với cabin rộng rãi, tầm nhìn panorama.\n\nTại đỉnh Fansipan, tham quan quần thể tâm linh bao gồm Đại Hồng Chung, tượng Phật A Di Đà và chùa Bích Vân Thiền Tự.",
                'images' => [
                    'https://images.unsplash.com/photo-1589139587422-be16c31bfda4?q=80&w=800',
                    'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Vé Người Lớn', 'desc' => 'Cáp treo 2 chiều lên đỉnh Fansipan.', 'price' => 800000, 'original' => 900000],
                    ['name' => 'Vé Trẻ Em (1m - 1m3)', 'desc' => 'Trẻ em cao từ 1m đến dưới 1m3.', 'price' => 500000, 'original' => 600000],
                    ['name' => 'Combo Cáp treo + Tàu hỏa leo núi', 'desc' => 'Cáp treo 2 chiều + trải nghiệm tàu hỏa Mường Hoa.', 'price' => 1100000, 'original' => 1350000],
                ],
            ],

            // ====== ĐÀ LẠT ======
            [
                'title' => 'Vé QUÉ Garden - Vườn hoa Đà Lạt',
                'destination' => 'Đà Lạt',
                'provider' => 'QUÉ Garden Đà Lạt',
                'cancellation' => 'Hủy miễn phí trước 24 giờ',
                'description' => "Tham quan vườn hoa QUÉ Garden - vườn hoa tư nhân lớn nhất Đà Lạt với hơn 500 loài hoa từ khắp nơi trên thế giới.\n\nKhông gian rộng hơn 7.000m² với các tiểu cảnh Nhật Bản, Hà Lan, Pháp. Lý tưởng để chụp ảnh và thư giãn.",
                'images' => [
                    'https://images.unsplash.com/photo-1509060464153-44667396260f?q=80&w=800',
                    'https://images.unsplash.com/photo-1583212292454-1fe6229603b7?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Vé Tham Quan', 'desc' => 'Vào cổng tham quan tất cả khu vực vườn hoa.', 'price' => 100000, 'original' => 150000],
                    ['name' => 'Combo Vé + Trà chiều', 'desc' => 'Vào cổng + 1 set trà hoa và bánh ngọt Đà Lạt.', 'price' => 250000, 'original' => 350000],
                ],
            ],
            [
                'title' => 'Vé trượt máng Alpine Coaster Datanla',
                'destination' => 'Đà Lạt',
                'provider' => 'Khu du lịch thác Datanla',
                'cancellation' => 'Không hoàn hủy',
                'description' => "Trải nghiệm trượt máng xuyên rừng thông Datanla - đường trượt Alpine Coaster dài nhất Việt Nam (2.400m). Tốc độ tối đa 40km/h, cảm giác tuyệt vời giữa không gian rừng thông xanh mát.\n\nKết hợp tham quan thác Datanla hùng vĩ nằm sâu trong thung lũng.",
                'images' => [
                    'https://images.unsplash.com/photo-1571509930722-df38ccfb8611?q=80&w=800',
                    'https://images.unsplash.com/photo-1504457047772-27faf1c00561?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Vé trượt 1 chiều', 'desc' => 'Trượt máng 1 chiều xuống thác + đi bộ lên.', 'price' => 100000, 'original' => 120000],
                    ['name' => 'Vé trượt 2 chiều', 'desc' => 'Trượt máng khứ hồi, trải nghiệm trọn vẹn.', 'price' => 150000, 'original' => 200000],
                    ['name' => 'Combo Trượt + Vé thác', 'desc' => 'Trượt 2 chiều + vé vào cổng tham quan thác Datanla.', 'price' => 200000, 'original' => 280000],
                ],
            ],

            // ====== HÀ NỘI ======
            [
                'title' => 'Vé xem múa rối nước Thăng Long',
                'destination' => 'Hà Nội',
                'provider' => 'Nhà hát Múa rối Thăng Long',
                'cancellation' => 'Hủy trước 12 giờ hoàn 80%',
                'description' => "Thưởng thức nghệ thuật múa rối nước truyền thống Việt Nam tại Nhà hát Múa rối Thăng Long - nhà hát múa rối nước nổi tiếng nhất Hà Nội, bên bờ Hồ Gươm.\n\nShow diễn kéo dài 50 phút với 17 tiết mục kể câu chuyện đồng quê Việt Nam qua nghệ thuật rối nước độc đáo có một không hai trên thế giới.",
                'images' => [
                    'https://images.unsplash.com/photo-1598321436786-a75e2b4ad770?q=80&w=800',
                    'https://images.unsplash.com/photo-1583417319070-4a69db38a482?q=80&w=800',
                ],
                'options' => [
                    ['name' => 'Ghế Thường', 'desc' => 'Hàng ghế từ hàng 5 trở đi. Show 50 phút.', 'price' => 100000, 'original' => 150000],
                    ['name' => 'Ghế VIP (Hàng 1-4)', 'desc' => 'Hàng ghế VIP gần sân khấu, góc nhìn tốt nhất.', 'price' => 200000, 'original' => 250000],
                ],
            ],
        ];

        foreach ($ticketsData as $data) {
            $destId = $destinations[$data['destination']] ?? null;
            if (! $destId) {
                continue;
            }

            $ticket = Ticket::create([
                'slug' => Str::slug($data['title']).'-'.uniqid(),
                'title' => $data['title'],
                'destination_id' => $destId,
                'description' => $data['description'],
                'provider_name' => $data['provider'],
                'cancellation_policy' => $data['cancellation'],
            ]);

            // Images
            foreach ($data['images'] as $i => $url) {
                TicketImage::create([
                    'ticket_id' => $ticket->id,
                    'image_url' => $url,
                    'is_primary' => $i === 0 ? 1 : 0,
                ]);
            }

            // Options
            foreach ($data['options'] as $opt) {
                TicketOption::create([
                    'ticket_id' => $ticket->id,
                    'name' => $opt['name'],
                    'description' => $opt['desc'],
                    'price' => $opt['price'],
                    'original_price' => $opt['original'],
                ]);
            }

            $this->command->info("  ✓ Vé: {$data['title']}");
        }
    }
}
