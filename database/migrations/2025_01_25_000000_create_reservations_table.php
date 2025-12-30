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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_size')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->string('service_type');
            $table->text('project_description');
            $table->string('budget_range')->nullable();
            $table->string('timeline')->nullable();
            $table->string('priority')->nullable();
            $table->text('additional_notes')->nullable();
            $table->enum('status', ['pending', 'contacted', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};