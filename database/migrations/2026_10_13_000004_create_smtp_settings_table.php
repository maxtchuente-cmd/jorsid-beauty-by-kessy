<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('smtp_settings')) Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id(); $table->string('host',190); $table->unsignedSmallInteger('port')->default(587); $table->string('encryption',10)->default('tls');
            $table->string('username',190)->nullable(); $table->text('password_ciphertext')->nullable(); $table->string('from_address',190); $table->string('from_name',190);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('smtp_settings'); }
};
