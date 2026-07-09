<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\Booking::where('payment_status', 'paid_100')->get()->each(function($b) {
    $b->paid_amount = $b->total_price;
    $b->save();
});
App\Models\Booking::where('payment_status', 'paid_30')->get()->each(function($b) {
    $b->paid_amount = $b->total_price * 0.3;
    $b->save();
});
echo "Done\n";
