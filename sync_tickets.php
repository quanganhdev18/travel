<?php

use App\Models\Ticket;
use App\Models\Tour;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$t = Tour::all();
$tks = Ticket::pluck('id')->toArray();
foreach ($t as $tour) {
    $tour->tickets()->syncWithoutDetaching($tks);
}
echo 'Done syncing tickets';
