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
        Schema::table('analysis_sessions', function (Blueprint $table) {
            // Menghapus kolom yang tidak lagi digunakan
            $table->dropColumn(['name', 'step_by_step_data', 'transformation_data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analysis_sessions', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->longText('step_by_step_data')->nullable()->after('total_transactions');
            $table->longText('transformation_data')->nullable()->after('step_by_step_data');
        });
    }
};
