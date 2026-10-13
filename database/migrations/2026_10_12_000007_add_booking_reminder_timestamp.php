<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{if(Schema::hasTable('bookings')&&!Schema::hasColumn('bookings','reminder_sent_at'))Schema::table('bookings',fn(Blueprint $t)=>$t->timestamp('reminder_sent_at')->nullable());}public function down():void{if(Schema::hasTable('bookings')&&Schema::hasColumn('bookings','reminder_sent_at'))Schema::table('bookings',fn(Blueprint $t)=>$t->dropColumn('reminder_sent_at'));}};
