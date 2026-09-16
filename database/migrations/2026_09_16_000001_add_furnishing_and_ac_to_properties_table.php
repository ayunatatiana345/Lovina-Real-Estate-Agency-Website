<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (!Schema::hasColumn('properties', 'furnishing')) {
                $table->string('furnishing')->nullable()->after('water_supply');
            }
            if (!Schema::hasColumn('properties', 'air_conditioning')) {
                $table->string('air_conditioning')->nullable()->after('furnishing');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (Schema::hasColumn('properties', 'furnishing')) {
                $table->dropColumn('furnishing');
            }
            if (Schema::hasColumn('properties', 'air_conditioning')) {
                $table->dropColumn('air_conditioning');
            }
        });
    }
};
