<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = \App\Models\Tour::all();
$tks = \App\Models\Ticket::pluck('id')->toArray();
foreach($t as $tour) {
    $tour->tickets()->syncWithoutDetaching($tks);
}
echo "Done syncing tickets";
