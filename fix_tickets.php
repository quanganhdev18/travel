<?php

use App\Models\Tour;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$tours = Tour::with('destination')->get();

foreach ($tours as $tour) {
    // Clear old
    $tour->addons()->detach();
    $tour->tickets()->detach();

    $title = mb_strtolower($tour->title, 'UTF-8');
    $dest = $tour->destination ? mb_strtolower($tour->destination->name, 'UTF-8') : '';
    $search = $title.' '.$dest;

    $ticketIds = [];
    $addonIds = [1, 5]; // Generic addons: xe điện, xe lăn

    // Check location for Tickets & Addons
    if (str_contains($search, 'đà nẵng') || str_contains($search, 'bà nà')) {
        $ticketIds = [15, 16]; // Bà Nà, Núi Thần Tài
        $addonIds[] = 3; // Dù lượn
        $addonIds[] = 4; // Lặn biển (Cù Lao Chàm)
    } elseif (str_contains($search, 'phú quốc')) {
        $ticketIds = [17, 18]; // Vinwonders, Hòn Thơm
        $addonIds[] = 4; // Lặn biển
    } elseif (str_contains($search, 'hạ long') || str_contains($search, 'quảng ninh')) {
        $ticketIds = [19]; // Vịnh Hạ Long
        $addonIds[] = 3; // Dù lượn
    } elseif (str_contains($search, 'sapa') || str_contains($search, 'lào cai') || str_contains($search, 'fansipan')) {
        $ticketIds = [20]; // Fansipan
    } elseif (str_contains($search, 'đà lạt') || str_contains($search, 'lâm đồng')) {
        $ticketIds = [21, 22]; // QUE Garden, Datanla
    } elseif (str_contains($search, 'hà nội')) {
        $ticketIds = [23]; // Múa rối
        $addonIds[] = 2; // Xích lô
    } elseif (str_contains($search, 'nha trang') || str_contains($search, 'khánh hòa')) {
        $addonIds[] = 3; // Dù lượn
        $addonIds[] = 4; // Lặn biển
    } elseif (str_contains($search, 'huế') || str_contains($search, 'hội an')) {
        $addonIds[] = 2; // Xích lô
    }

    // Attach
    if (count($ticketIds) > 0) {
        $tour->tickets()->attach($ticketIds);
    }
    if (count($addonIds) > 0) {
        $tour->addons()->attach(array_unique($addonIds));
    }
}
echo 'Done fixing mapping';
