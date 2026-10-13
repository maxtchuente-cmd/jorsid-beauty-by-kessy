<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->unique();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->string('customer_name', 120);
            $table->string('customer_email', 190);
            $table->string('customer_phone', 40);
            $table->string('service_mode', 10);
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('address_line', 190)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address_note', 500)->nullable();
            $table->text('customer_note')->nullable();
            $table->decimal('service_price', 10, 2);
            $table->decimal('travel_fee', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->string('status', 24)->default('pending');
            $table->timestamps();
            $table->index(['appointment_date', 'appointment_time', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
