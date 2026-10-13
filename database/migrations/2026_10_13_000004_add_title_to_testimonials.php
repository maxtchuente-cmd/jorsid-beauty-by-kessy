<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { if(Schema::hasTable('testimonials')&&!Schema::hasColumn('testimonials','title')) Schema::table('testimonials',fn(Blueprint $t)=>$t->string('title',150)->nullable()->after('customer_name')); }
 public function down(): void { if(Schema::hasTable('testimonials')&&Schema::hasColumn('testimonials','title')) Schema::table('testimonials',fn(Blueprint $t)=>$t->dropColumn('title')); }
};
