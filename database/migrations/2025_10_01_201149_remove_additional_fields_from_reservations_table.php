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
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['service_type', 'timeline', 'priority', 'additional_notes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('service_type', 100)->nullable();
            $table->string('timeline', 100)->nullable();
            $table->string('priority', 50)->nullable();
            $table->text('additional_notes')->nullable();
        });
    }
};
