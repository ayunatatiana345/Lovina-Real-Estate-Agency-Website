<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('property_images') && !Schema::hasColumn('property_images', 'image_alt')) {
            Schema::table('property_images', function (Blueprint $table) {
                $table->string('image_alt')->nullable()->after('image_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('property_images') && Schema::hasColumn('property_images', 'image_alt')) {
            Schema::table('property_images', function (Blueprint $table) {
                $table->dropColumn('image_alt');
            });
        }
    }
};
