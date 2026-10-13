<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { if (!Schema::hasTable('business_date_overrides')) Schema::create('business_date_overrides', function(Blueprint $t) {
  $t->id(); $t->date('override_date')->unique(); $t->boolean('is_open')->default(true);
  $t->time('open_time')->nullable(); $t->time('close_time')->nullable(); $t->json('breaks')->nullable();
  $t->string('label',150)->nullable(); $t->timestamps();
 }); }
 public function down(): void { Schema::dropIfExists('business_date_overrides'); }
};
