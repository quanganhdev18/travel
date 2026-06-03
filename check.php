<?php

use App\Models\Tour;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$count = Tour::count();
echo 'Total tours: '.$count."\n";
$all = Tour::all();
foreach ($all as $t) {
    echo 'Tour ID: '.$t->id.' - Title: '.$t->title."\n";
}
