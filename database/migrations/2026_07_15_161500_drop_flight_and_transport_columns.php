<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop flight_bookings table
        Schema::dropIfExists('flight_bookings');

        // 2. Drop columns from tours table
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'transport_type')) {
                $table->dropColumn('transport_type');
            }
            if (Schema::hasColumn('tours', 'hotel_stars')) {
                $table->dropColumn('hotel_stars');
            }
        });

        // 3. Drop columns from bookings table
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'pnr_code')) {
                $table->dropColumn('pnr_code');
            }
            if (Schema::hasColumn('bookings', 'transport_type')) {
                $table->dropColumn('transport_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('pnr_code')->nullable();
            $table->string('transport_type')->nullable();
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->string('transport_type')->nullable()->comment('xe or bay');
            $table->tinyInteger('hotel_stars')->nullable()->comment('1 to 5 stars');
        });
    }
};
