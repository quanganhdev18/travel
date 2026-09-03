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
        Schema::create('insurance_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('fullname');
            $table->string('phone');
            $table->string('email');
            $table->string('package_code');
            $table->string('package_name');
            $table->decimal('price_per_day', 12, 2)->default(0);
            $table->integer('total_days')->default(1);
            $table->decimal('total_price', 12, 2)->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_registrations');
    }
};
