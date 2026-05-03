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
        Schema::create('analysis_itemsets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained('analysis_steps')->onDelete('cascade');
            $table->text('items');
            $table->integer('count');
            $table->decimal('support', 8, 2);
            $table->boolean('is_frequent')->default(false);
            $table->string('type'); // 'candidate' or 'frequent'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_itemsets');
    }
};
