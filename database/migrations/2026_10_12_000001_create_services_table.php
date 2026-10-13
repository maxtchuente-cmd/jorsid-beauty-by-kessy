<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->default('Coiffure');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('price_from')->default(false);
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->boolean('at_salon')->default(true);
            $table->boolean('at_home')->default(false);
            $table->decimal('travel_fee', 10, 2)->default(0);
            $table->text('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
