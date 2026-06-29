<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\Destination;
use App\Models\TourSchedule;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SampleToursSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Prepare some destinations
        $destinations = [
            'Hà Nội' => Destination::firstOrCreate(['name' => 'Hà Nội'], ['description' => 'Thủ đô ngàn năm văn hiến', 'image_url' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b']),
            'Đà Nẵng' => Destination::firstOrCreate(['name' => 'Đà Nẵng'], ['description' => 'Thành phố đáng sống', 'image_url' => 'https://images.unsplash.com/photo-1555921015-c262060f5899']),
            'Phú Quốc' => Destination::firstOrCreate(['name' => 'Phú Quốc'], ['description' => 'Đảo ngọc', 'image_url' => 'https://images.unsplash.com/photo-1596395819057-cbcf88eb0dfb']),
            'Đà Lạt' => Destination::firstOrCreate(['name' => 'Đà Lạt'], ['description' => 'Thành phố ngàn hoa', 'image_url' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b']),
            'Sapa' => Destination::firstOrCreate(['name' => 'Sapa'], ['description' => 'Thành phố trong sương', 'image_url' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b']),
            'Nha Trang' => Destination::firstOrCreate(['name' => 'Nha Trang'], ['description' => 'Thành phố biển', 'image_url' => 'https://images.unsplash.com/photo-1596395819057-cbcf88eb0dfb']),
            'Hạ Long' => Destination::firstOrCreate(['name' => 'Hạ Long'], ['description' => 'Di sản thiên nhiên thế giới', 'image_url' => 'https://images.unsplash.com/photo-1528127269322-539801943592']),
            'Hồ Chí Minh' => Destination::firstOrCreate(['name' => 'Hồ Chí Minh'], ['description' => 'Thành phố mang tên Bác', 'image_url' => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482']),
        ];

        // 2. Prepare 10 sample tours with varied filters and images
        $samples = [
            [
                'title' => 'Nghỉ dưỡng 5 sao Đảo Ngọc Phú Quốc',
                'dest' => 'Phú Quốc', 'dep' => 'Hồ Chí Minh',
                'price' => 12500000, 'transport' => 'bay', 'stars' => 5,
                'image' => 'https://images.unsplash.com/photo-1596395819057-cbcf88eb0dfb?q=80&w=800'
            ],
            [
                'title' => 'Khám phá Hà Nội - Vịnh Hạ Long',
                'dest' => 'Hạ Long', 'dep' => 'Hồ Chí Minh',
                'price' => 8500000, 'transport' => 'bay', 'stars' => 4,
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800'
            ],
            [
                'title' => 'Phượt Sapa mùa lúa chín bằng Ô Tô',
                'dest' => 'Sapa', 'dep' => 'Hà Nội',
                'price' => 3200000, 'transport' => 'xe', 'stars' => 3,
                'image' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?q=80&w=800'
            ],
            [
                'title' => 'Trải nghiệm cáp treo Bà Nà Hills',
                'dest' => 'Đà Nẵng', 'dep' => 'Hà Nội',
                'price' => 6500000, 'transport' => 'bay', 'stars' => 4,
                'image' => 'https://images.unsplash.com/photo-1555921015-c262060f5899?q=80&w=800'
            ],
            [
                'title' => 'Vi vu Thành phố ngàn hoa Đà Lạt',
                'dest' => 'Đà Lạt', 'dep' => 'Hồ Chí Minh',
                'price' => 4500000, 'transport' => 'xe', 'stars' => 3,
                'image' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?q=80&w=800'
            ],
            [
                'title' => 'Tour Siêu Tiết Kiệm Nha Trang 3 Ngày',
                'dest' => 'Nha Trang', 'dep' => 'Hồ Chí Minh',
                'price' => 2800000, 'transport' => 'xe', 'stars' => 2,
                'image' => 'https://images.unsplash.com/photo-1582715014902-530932da6687?q=80&w=800'
            ],
            [
                'title' => 'Tour Cao Cấp Sài Gòn - Đà Nẵng 4 Ngày',
                'dest' => 'Đà Nẵng', 'dep' => 'Hồ Chí Minh',
                'price' => 11000000, 'transport' => 'bay', 'stars' => 5,
                'image' => 'https://images.unsplash.com/photo-1555921015-c262060f5899?q=80&w=800'
            ],
            [
                'title' => 'Tuần Trăng Mật Đỉnh Cao Phú Quốc',
                'dest' => 'Phú Quốc', 'dep' => 'Hà Nội',
                'price' => 25000000, 'transport' => 'bay', 'stars' => 5,
                'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=800'
            ],
            [
                'title' => 'Du Lịch Gia Đình Hạ Long Cuối Tuần',
                'dest' => 'Hạ Long', 'dep' => 'Hà Nội',
                'price' => 4000000, 'transport' => 'xe', 'stars' => 4,
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=800'
            ],
            [
                'title' => 'Hành Trình Di Sản Miền Trung (Huế - Đà Nẵng)',
                'dest' => 'Đà Nẵng', 'dep' => 'Hồ Chí Minh',
                'price' => 7800000, 'transport' => 'bay', 'stars' => 4,
                'image' => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?q=80&w=800'
            ],
        ];

        foreach ($samples as $index => $sample) {
            $slug = Str::slug($sample['title']) . '-' . rand(100, 999);
            
            $tour = Tour::updateOrCreate(
                ['slug' => Str::slug($sample['title'])], // search by base slug
                [
                    'title' => ['vi' => $sample['title'], 'en' => $sample['title']],
                    'destination_id' => $destinations[$sample['dest']]->id ?? null,
                    'departure_location_id' => $destinations[$sample['dep']]->id ?? null,
                    'description' => ['vi' => 'Mô tả chi tiết cho tour ' . $sample['title'], 'en' => 'Description for ' . $sample['title']],
                    'duration_days' => rand(2, 5),
                    'duration_nights' => rand(1, 4),
                    'base_price' => $sample['price'],
                    'transport_type' => $sample['transport'],
                    'hotel_stars' => $sample['stars'],
                ]
            );

            // Ensure the tour has the unique full slug if newly created
            if (empty($tour->getOriginal('slug'))) {
                $tour->slug = $slug;
                $tour->save();
            }

            // Insert image
            \App\Models\TourImage::updateOrCreate(
                ['tour_id' => $tour->id],
                [
                    'image_url' => $sample['image'],
                    'is_primary' => 1,
                ]
            );

            // Create an active schedule for the tour (in next 15 days)
            TourSchedule::updateOrCreate(
                ['tour_id' => $tour->id],
                [
                    'departure_date' => Carbon::now()->addDays(rand(5, 20)),
                    'return_date' => Carbon::now()->addDays(rand(25, 30)),
                    'capacity' => 20,
                    'available_seats' => 20,
                    'status' => 'available',
                ]
            );
        }
    }
}
