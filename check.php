<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \App\Models\Tour::count();
echo "Total tours: " . $count . "\n";
$all = \App\Models\Tour::all();
foreach($all as $t) {
    echo "Tour ID: " . $t->id . " - Title: " . $t->title . "\n";
}
