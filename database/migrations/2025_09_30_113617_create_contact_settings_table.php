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
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('Contact Us');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('office_hours')->nullable();
            $table->text('map_embed')->nullable();
            $table->boolean('contact_form_enabled')->default(true);
            $table->boolean('auto_reply_enabled')->default(false);
            $table->text('auto_reply_message')->nullable();
            $table->json('social_links')->nullable(); // Array of social media links
            $table->json('form_settings')->nullable(); // Form configuration
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};
