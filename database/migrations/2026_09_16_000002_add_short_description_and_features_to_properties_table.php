<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('properties', 'features')) {
                $table->json('features')->nullable()->after('swimming_pool');
            }
            // Allow specs to be nullable for land / commercial properties
            $table->integer('bedrooms')->nullable()->default(null)->change();
            $table->integer('bathrooms')->nullable()->default(null)->change();
            $table->integer('land_size')->nullable()->default(null)->change();
            $table->integer('building_size')->nullable()->default(null)->change();
            $table->integer('garage')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'short_description')) {
                $table->dropColumn('short_description');
            }
            if (Schema::hasColumn('properties', 'features')) {
                $table->dropColumn('features');
            }
        });
    }
};
