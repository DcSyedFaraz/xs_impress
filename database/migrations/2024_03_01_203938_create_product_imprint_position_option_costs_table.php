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
        Schema::create('product_imprint_position_option_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_imprint_position_option_id')->constrained(
                table: 'product_imprint_position_options', indexName: 'pipoc_product_imprint_position_option_id'
            )->onDelete('cascade');
            $table->string('sku')->nullable();
            $table->string('supplier_sku')->nullable();
            $table->longText('texts')->nullable();
            $table->longText('price_region_based')->nullable();
            $table->longText('price_country_based')->nullable();
            $table->longText('is_active_region_based')->nullable();
            $table->longText('is_active_country_based')->nullable();
            $table->string('calculation_type')->nullable();
            $table->string('calculation_amount')->nullable();
            $table->longText('requirement')->nullable();
            $table->longText('unstructured_information')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_imprint_position_option_costs');
    }
};
