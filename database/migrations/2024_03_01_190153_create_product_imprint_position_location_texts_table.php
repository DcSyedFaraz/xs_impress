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
        Schema::create('product_imprint_position_location_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_imprint_position_id')->constrained(
                table: 'product_imprint_positions', indexName: 'product_imprint_positions_product_imprint_position_id'
            )->onDelete('cascade');
            $table->string('language')->nullable();
            $table->longText('images')->nullable();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_imprint_position_location_texts');
    }
};
