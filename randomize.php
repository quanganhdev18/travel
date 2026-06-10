<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tours = \App\Models\Tour::all();
foreach($tours as $tour) {
    $tour->addons()->detach();
    $tour->tickets()->detach();
    $tour->addons()->attach(\App\Models\Addon::inRandomOrder()->limit(2)->pluck('id'));
    $tour->tickets()->attach(\App\Models\Ticket::inRandomOrder()->limit(1)->pluck('id'));
}
echo "Done randomizing tickets and addons";
