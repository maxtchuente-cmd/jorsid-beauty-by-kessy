<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { if (Schema::hasTable('bookings') && !Schema::hasColumn('bookings','customer_language')) Schema::table('bookings', function(Blueprint $table){ $table->string('customer_language',2)->default('de')->after('customer_email'); }); }
 public function down(): void { if (Schema::hasTable('bookings') && Schema::hasColumn('bookings','customer_language')) Schema::table('bookings', fn(Blueprint $table)=>$table->dropColumn('customer_language')); }
};
