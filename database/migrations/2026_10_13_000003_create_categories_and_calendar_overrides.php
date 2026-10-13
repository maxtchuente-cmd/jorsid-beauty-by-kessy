<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('service_categories')) {
            Schema::create('service_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('name_de', 120)->nullable();
                $table->string('name_fr', 120)->nullable();
                $table->string('name_en', 120)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('gallery_categories')) {
            Schema::create('gallery_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('name_de', 120)->nullable();
                $table->string('name_fr', 120)->nullable();
                $table->string('name_en', 120)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
        DB::table('service_categories')->updateOrInsert(['name' => 'Coiffure'], ['updated_at' => now(), 'created_at' => now()]);
        DB::table('gallery_categories')->updateOrInsert(['name' => 'Coiffure'], ['updated_at' => now(), 'created_at' => now()]);
        if (Schema::hasTable('services')) {
            foreach (DB::table('services')->select('category')->distinct()->get() as $row) {
                $name = trim((string) ($row->category ?? 'Coiffure')) ?: 'Coiffure';
                DB::table('service_categories')->updateOrInsert(['name' => $name], ['updated_at' => now(), 'created_at' => now()]);
            }
            if (!Schema::hasColumn('services', 'service_category_id')) {
                Schema::table('services', function (Blueprint $table) { $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete(); });
            }
            foreach (DB::table('services')->select('id', 'category')->get() as $row) {
                $name = trim((string) ($row->category ?? 'Coiffure')) ?: 'Coiffure';
                $id = DB::table('service_categories')->where('name', $name)->value('id');
                if ($id) DB::table('services')->where('id', $row->id)->whereNull('service_category_id')->update(['service_category_id' => $id]);
            }
        }
        if (Schema::hasTable('gallery_items')) {
            foreach (DB::table('gallery_items')->select('category')->distinct()->get() as $row) {
                $name = trim((string) ($row->category ?? 'Coiffure')) ?: 'Coiffure';
                DB::table('gallery_categories')->updateOrInsert(['name' => $name], ['updated_at' => now(), 'created_at' => now()]);
            }
            if (!Schema::hasColumn('gallery_items', 'gallery_category_id')) {
                Schema::table('gallery_items', function (Blueprint $table) { $table->foreignId('gallery_category_id')->nullable()->constrained('gallery_categories')->nullOnDelete(); });
            }
            foreach (DB::table('gallery_items')->select('id', 'category')->get() as $row) {
                $name = trim((string) ($row->category ?? 'Coiffure')) ?: 'Coiffure';
                $id = DB::table('gallery_categories')->where('name', $name)->value('id');
                if ($id) DB::table('gallery_items')->where('id', $row->id)->whereNull('gallery_category_id')->update(['gallery_category_id' => $id]);
            }
        }
        if (!Schema::hasTable('business_date_overrides')) {
            Schema::create('business_date_overrides', function (Blueprint $table) {
                $table->id();
                $table->date('override_date')->unique();
                $table->boolean('is_open')->default(true);
                $table->time('open_time')->nullable();
                $table->time('close_time')->nullable();
                $table->string('label', 150)->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('business_date_breaks')) {
            Schema::create('business_date_breaks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_date_override_id')->constrained('business_date_overrides')->cascadeOnDelete();
                $table->time('start_time');
                $table->time('end_time');
                $table->timestamps();
            });
        }
        if (Schema::hasTable('testimonials') && !Schema::hasColumn('testimonials', 'title')) {
            Schema::table('testimonials', function (Blueprint $table) { $table->string('title', 160)->nullable()->after('customer_name'); });
        }
    }

    public function down(): void
    {
        // Intentionally preserve data and added nullable relations on rollback.
        Schema::dropIfExists('business_date_breaks');
        Schema::dropIfExists('business_date_overrides');
        // Category tables and references are kept to avoid destructive rollback of migrated legacy values.
    }
};
