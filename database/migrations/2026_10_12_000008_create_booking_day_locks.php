<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration{public function up():void{if(!Schema::hasTable('booking_day_locks'))Schema::create('booking_day_locks',function(Blueprint $t){$t->id();$t->date('lock_date')->unique();$t->timestamps();});}public function down():void{Schema::dropIfExists('booking_day_locks');}};
