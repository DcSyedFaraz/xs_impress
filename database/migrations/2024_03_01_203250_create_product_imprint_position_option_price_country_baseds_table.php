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
        Schema::create('product_imprint_position_option_price_country_based', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_imprint_position_option_id')->constrained(
                table: 'product_imprint_position_options', indexName: 'pipopcb_product_imprint_position_option_id'
            )->onDelete('cascade');
            $table->string('country_currency')->nullable();
            $table->string('type')->nullable();
            $table->decimal('price', 20, 4)->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('on_request')->nullable();
            $table->string('valuta')->nullable();
            $table->integer('quantity_increments')->nullable();
            $table->float('vat_percentage')->nullable();
            $table->integer('minimum_order_quantity')->nullable();
            $table->integer('vat_setting_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_imprint_position_option_price_country_baseds');
    }
};
