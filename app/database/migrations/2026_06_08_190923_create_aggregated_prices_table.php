<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aggregated_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wholesaler_id')->constrained()->cascadeOnDelete();
            $table->decimal('avg_price', 10, 2);
            $table->decimal('median_price', 10, 2)->nullable();
            $table->decimal('min_price', 10, 2);
            $table->decimal('max_price', 10, 2);
            $table->unsignedInteger('datapoint_count');
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->unique(['product_id', 'wholesaler_id', 'period_start', 'period_end'], 'aggregated_prices_unique_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aggregated_prices');
    }
};
