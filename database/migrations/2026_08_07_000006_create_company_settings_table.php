<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('PT Lovina North Bali Real Estate Agency');
            $table->string('site_title')->default('PT Lovina North Bali Real Estate Agency');
            $table->string('tagline')->default('Your Trusted Property Partner in North Bali');
            $table->text('site_description')->nullable();
            
            // Branding
            $table->string('logo_primary')->nullable();
            $table->string('logo_alt')->nullable();
            $table->string('favicon')->nullable();
            $table->string('office_photo')->nullable();

            // Contact info
            $table->string('phone')->default('+62 812 3456 7890');
            $table->string('whatsapp')->default('+62 812 3456 7890');
            $table->string('email')->default('info@lovinanorthbali.com');
            $table->text('address')->nullable();

            // Social media
            $table->string('instagram_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('youtube_url')->nullable();

            // Business hours (JSON or string array)
            $table->text('business_hours')->nullable();

            // Google Maps
            $table->text('google_maps_embed_url')->nullable();
            $table->text('google_maps_direction_url')->nullable();

            // SEO Defaults
            $table->string('seo_meta_title')->nullable();
            $table->text('seo_meta_description')->nullable();
            $table->string('seo_social_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
