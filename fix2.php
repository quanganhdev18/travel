<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

App\Models\Booking::where('payment_status', 'paid_100')->get()->each(function($b) {
    if (in_array($b->booking_status, ['pending', 'confirmed'])) {
        $b->booking_status = 'paid';
        $b->save();
    }
});
App\Models\Booking::where('payment_status', 'paid_30')->get()->each(function($b) {
    if ($b->booking_status === 'pending') {
        $b->booking_status = 'confirmed';
        $b->save();
    }
});
echo "Done\n";
