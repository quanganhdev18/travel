<?php

use App\Models\Addon;
use App\Models\Ticket;
use App\Models\Tour;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$tours = Tour::all();
foreach ($tours as $tour) {
    $tour->addons()->detach();
    $tour->tickets()->detach();
    $tour->addons()->attach(Addon::inRandomOrder()->limit(2)->pluck('id'));
    $tour->tickets()->attach(Ticket::inRandomOrder()->limit(1)->pluck('id'));
}
echo 'Done randomizing tickets and addons';
