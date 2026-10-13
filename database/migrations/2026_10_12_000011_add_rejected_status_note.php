<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // The status column is a string, so the rejected status needs no schema alteration.
        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'status')) {
            DB::table('bookings')->where('status', 'refused')->update(['status' => 'rejected']);
        }
    }
    public function down(): void {}
};
