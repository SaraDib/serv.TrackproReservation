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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Template name (e.g., "Contact Confirmation", "Reservation Confirmation")
            $table->string('type'); // Template type (e.g., "contact_confirmation", "reservation_confirmation")
            $table->string('subject'); // Email subject
            $table->text('html_content'); // HTML content of the email
            $table->text('text_content')->nullable(); // Plain text version (optional)
            $table->json('variables')->nullable(); // Available variables for the template
            $table->boolean('is_active')->default(true); // Whether the template is active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
