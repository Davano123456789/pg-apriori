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
        Schema::create('analysis_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->decimal('min_support', 8, 2);
            $table->decimal('min_confidence', 8, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_transactions')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_sessions');
    }
};
