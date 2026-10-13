<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('smtp_settings') && !Schema::hasColumn('smtp_settings', 'admin_notification_email')) {
            Schema::table('smtp_settings', function (Blueprint $table) { $table->string('admin_notification_email', 190)->nullable()->after('from_name'); });
        }
    }
    public function down(): void {
        if (Schema::hasTable('smtp_settings') && Schema::hasColumn('smtp_settings', 'admin_notification_email')) {
            Schema::table('smtp_settings', function (Blueprint $table) { $table->dropColumn('admin_notification_email'); });
        }
    }
};
