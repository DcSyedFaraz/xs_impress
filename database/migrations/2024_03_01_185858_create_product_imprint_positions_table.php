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
        Schema::create('product_imprint_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained(
                table: 'products', indexName: 'product_imprint_positions_products_id'
            )->onDelete('cascade');
            $table->string('position_code')->nullable();
            $table->longText('unstructured_information')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_imprint_positions');
    }
};
