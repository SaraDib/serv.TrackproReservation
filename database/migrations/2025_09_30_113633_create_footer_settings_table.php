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
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('TrackPro');
            $table->text('description')->nullable();
            $table->string('logo_image')->nullable();
            $table->json('quick_links')->nullable(); // Array of footer links
            $table->json('social_links')->nullable(); // Array of social media links
            $table->string('copyright_text')->nullable();
            $table->string('privacy_policy_url')->nullable();
            $table->string('terms_of_service_url')->nullable();
            $table->boolean('newsletter_enabled')->default(false);
            $table->string('newsletter_title')->nullable();
            $table->text('newsletter_description')->nullable();
            $table->string('background_color', 7)->nullable();
            $table->string('text_color', 7)->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_settings');
    }
};
