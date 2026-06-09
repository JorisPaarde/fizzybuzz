<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aggregated_prices', function (Blueprint $table) {
            $table->dropUnique('aggregated_prices_unique_period');
        });

        Schema::table('aggregated_prices', function (Blueprint $table) {
            $table->string('purchase_size', 16)->default('medium')->after('wholesaler_id');
            $table->unique(
                ['product_id', 'wholesaler_id', 'purchase_size', 'period_start', 'period_end'],
                'aggregated_prices_unique_period'
            );
        });
    }

    public function down(): void
    {
        Schema::table('aggregated_prices', function (Blueprint $table) {
            $table->dropUnique('aggregated_prices_unique_period');
            $table->dropColumn('purchase_size');
            $table->unique(
                ['product_id', 'wholesaler_id', 'period_start', 'period_end'],
                'aggregated_prices_unique_period'
            );
        });
    }
};
