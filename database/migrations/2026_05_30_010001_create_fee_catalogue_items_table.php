<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('fee_catalogue_items')) {
            Schema::create('fee_catalogue_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fee_category_id')->constrained('fee_categories');
                $table->string('name');                    // "Boys Shirt — Primary" / "Lusaka Tour 2026"
                $table->decimal('amount', 10, 2);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['fee_category_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_catalogue_items');
    }
};
