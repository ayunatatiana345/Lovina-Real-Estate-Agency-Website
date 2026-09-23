<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('category_property')) {
            Schema::create('category_property', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
                $table->foreignId('category_id')->constrained('property_categories')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['property_id', 'category_id']);
            });

            // Seed existing relationships from properties.category_id safely
            $existing = DB::table('properties')
                ->whereNotNull('category_id')
                ->select('id as property_id', 'category_id')
                ->get();

            $now = now();
            $records = [];
            foreach ($existing as $item) {
                $records[] = [
                    'property_id' => $item->property_id,
                    'category_id' => $item->category_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (!empty($records)) {
                DB::table('category_property')->insertOrIgnore($records);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_property');
    }
};
