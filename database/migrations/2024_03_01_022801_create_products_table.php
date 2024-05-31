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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->text('hash')->nullable();
            $table->text('supplier_sequence')->nullable();
            $table->string('type');
            $table->integer('parent_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('supplier_sku')->nullable();
            $table->string('a_number')->nullable();
            $table->longText('non_language_depended_product_details')->nullable();
            $table->longText('battery_information')->nullable();
            $table->string('ean')->nullable();
            $table->text('video_url')->nullable();
            $table->longText('forbidden_regions')->nullable();
            $table->longText('imprint_references')->nullable();
            $table->longText('product_costs')->nullable();
            $table->longText('sample_price_country_based')->nullable();
            $table->longText('product_price_region_based')->nullable();
            $table->longText('unstructured_information')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
