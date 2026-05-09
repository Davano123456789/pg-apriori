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
        Schema::table('apriori_results', function (Blueprint $table) {
            $table->dropColumn('lift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apriori_results', function (Blueprint $table) {
            $table->decimal('lift', 8, 4)->nullable()->after('confidence');
        });
    }
};
