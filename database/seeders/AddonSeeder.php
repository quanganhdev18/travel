<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            [
                'name' => 'Thuê xe điện tham quan',
                'description' => 'Xe điện đi dạo quanh khu vực trung tâm, tối đa 4 người.',
                'price' => 150000,
                'is_active' => true,
            ],
            [
                'name' => 'Đi xích lô dạo phố',
                'description' => 'Trải nghiệm ngắm cảnh đường phố bằng xích lô truyền thống (1 giờ).',
                'price' => 100000,
                'is_active' => true,
            ],
            [
                'name' => 'Trải nghiệm Dù lượn (Paragliding)',
                'description' => 'Ngắm cảnh từ trên cao cùng phi công chuyên nghiệp.',
                'price' => 1200000,
                'is_active' => true,
            ],
            [
                'name' => 'Tour lặn biển ngắm san hô (Scuba Diving)',
                'description' => 'Bao gồm thiết bị lặn và huấn luyện viên theo kèm.',
                'price' => 850000,
                'is_active' => true,
            ],
            [
                'name' => 'Thuê xe lăn cho người cao tuổi',
                'description' => 'Xe lăn gấp gọn, hỗ trợ di chuyển trong suốt hành trình.',
                'price' => 200000,
                'is_active' => true,
            ],
        ];

        foreach ($addons as $addonData) {
            Addon::firstOrCreate(['name' => $addonData['name']], $addonData);
        }

        $allAddonIds = Addon::pluck('id')->toArray();
        Tour::chunk(100, function ($tours) use ($allAddonIds) {
            foreach ($tours as $tour) {
                // Link 2-3 random addons to make it realistic, or all? Let's link all for testing.
                $tour->addons()->syncWithoutDetaching($allAddonIds);
            }
        });
    }
}
