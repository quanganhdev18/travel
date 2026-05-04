<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Tour;
use App\Models\TourSchedule;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'quản trị viên',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'phone' => '0987654321',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'khách hàng',
                'password' => Hash::make('12345678'),
                'role' => 'customer',
            ]
        );

        $cat_bien = Category::firstOrCreate(['slug' => 'du-lich-bien'], ['name' => 'Du Lịch Biển']);

        $dest_dn = Destination::firstOrCreate(
            ['name' => 'Đà Nẵng'],
            ['description' => 'Thành phố đáng sống', 'image_url' => 'https://images.unsplash.com/photo-1559592413-7ce4f0a0293d?q=80&w=800&auto=format&fit=crop']
        );

        $tour = Tour::firstOrCreate(
            ['slug' => 'tour-da-nang-3-ngay-2-dem'],
            [
                'destination_id' => $dest_dn->id,
                'title' => 'khám phá Đà Nẵng - Hội An',
                'description' => 'Hành trình khám phá miền Trung',
                'duration_days' => 3,
                'duration_nights' => 2,
                'base_price' => 3500000,
            ]
        );
        $tour->categories()->sync([$cat_bien->id]);

        TourSchedule::firstOrCreate(
            ['tour_id' => $tour->id, 'departure_date' => Carbon::now()->addDays(10)],
            [
                'return_date' => Carbon::now()->addDays(12),
                'capacity' => 20,
                'available_seats' => 20,
                'status' => 'available',
            ]
        );
    }
}
