<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_contents', function (Blueprint $table) {
            $table->id();
            $table->string('page')->default('homepage'); // homepage, about_us
            $table->string('section_key'); // hero, why_choose_us, stats, cta, company_story, etc.
            $table->json('content');
            $table->timestamps();
        });

        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->string('page')->default('homepage');
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('page')->default('homepage');
            $table->string('number'); // e.g. "120+", "15+", "99%"
            $table->string('label');  // e.g. "Properties Listed"
            $table->string('icon')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_contents');
        Schema::dropIfExists('benefits');
        Schema::dropIfExists('statistics');
    }
};
