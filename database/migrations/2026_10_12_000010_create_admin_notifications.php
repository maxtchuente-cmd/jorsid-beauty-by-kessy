<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('admin_notifications')) {
            Schema::create('admin_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
                $table->string('type', 40)->default('info');
                $table->string('title', 180);
                $table->text('message');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['read_at', 'created_at']);
            });
        }
    }
    public function down(): void { Schema::dropIfExists('admin_notifications'); }
};
