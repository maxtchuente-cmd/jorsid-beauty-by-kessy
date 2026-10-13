<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('travel_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('city', 100)->nullable();
            $table->string('postal_code_prefix', 12)->nullable();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('notes', 500)->nullable();
            $table->timestamps();
            $table->index(['is_active', 'city', 'postal_code_prefix']);
        });
    }

    public function down(): void { Schema::dropIfExists('travel_zones'); }
};
