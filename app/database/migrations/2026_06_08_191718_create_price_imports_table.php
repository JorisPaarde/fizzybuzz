<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('source');
            $table->string('original_filename')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('wholesaler_id')->nullable()->constrained()->nullOnDelete();
            $table->date('effective_date')->nullable();
            $table->json('extracted_items')->nullable();
            $table->string('status')->default('review');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::table('price_submissions', function (Blueprint $table) {
            $table->foreignId('price_import_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('price_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('price_import_id');
        });

        Schema::dropIfExists('price_imports');
    }
};
