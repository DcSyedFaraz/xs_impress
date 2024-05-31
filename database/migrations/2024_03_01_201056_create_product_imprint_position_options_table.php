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
        Schema::create('product_imprint_position_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_imprint_position_id')->constrained(
                table: 'product_imprint_positions', indexName: 'product_imprint_position_options_product_imprint_position_id'
            )->onDelete('cascade');
            $table->longText('child_imprints')->nullable();
            $table->longText('print_color_as_text')->nullable();
            $table->string('dimension')->nullable();
            $table->longText('imprint_texts')->nullable();
            $table->string('sku')->nullable();
            $table->string('supplier_sku')->nullable();
            $table->string('dimensions_height')->nullable();
            $table->string('dimensions_diameter')->nullable();
            $table->string('dimensions_width')->nullable();
            $table->string('dimensions_depth')->nullable();
            $table->string('imprint_type')->nullable();
            $table->longText('unstructured_information')->nullable();
            $table->integer('print_color')->nullable();
            $table->longText('is_active_region_based')->nullable();
            $table->longText('is_active_country_based')->nullable();
            $table->longText('important_information')->nullable();
            $table->longText('price_region_based')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_imprint_position_options');
    }
};
