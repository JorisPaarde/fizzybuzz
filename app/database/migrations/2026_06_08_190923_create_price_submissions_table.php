<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wholesaler_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->string('unit');
            $table->string('quantity_per_unit')->nullable();
            $table->string('specification')->nullable();
            $table->date('effective_date');
            $table->string('source')->default('manual');
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['product_id', 'wholesaler_id', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_submissions');
    }
};
