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
        Schema::table('about_section', function (Blueprint $table) {
            $table->string('flip_prefix')->nullable()->after('features');
            $table->string('flip_suffix')->nullable()->after('flip_prefix');
            $table->json('flip_lines')->nullable()->after('flip_suffix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_section', function (Blueprint $table) {
            $table->dropColumn(['flip_prefix', 'flip_suffix', 'flip_lines']);
        });
    }
};