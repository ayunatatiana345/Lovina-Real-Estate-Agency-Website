<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained('property_categories')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->decimal('price', 15, 2);
            $table->string('ownership_type')->default('Freehold'); // Freehold, Leasehold
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->boolean('is_featured')->default(false);
            $table->text('description')->nullable();
            
            // Specifications
            $table->integer('bedrooms')->default(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('land_size')->default(0); // in m2
            $table->integer('building_size')->default(0); // in m2
            $table->integer('garage')->default(0);
            $table->boolean('swimming_pool')->default(false);
            $table->string('electricity')->nullable(); // e.g. 5500 VA
            $table->string('water_supply')->nullable(); // e.g. PDAM / Deep Well

            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
