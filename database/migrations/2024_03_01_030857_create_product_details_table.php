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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained(
                table: 'products', indexName: 'products_id'
            )->onDelete('cascade');
            $table->string('language')->nullable();
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('meta_name')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('is_active')->nullable();
            $table->longText('pimv1_information')->nullable();
            $table->longText('unstructured_information')->nullable();
            $table->longText('web_shop_information')->nullable();
            $table->longText('important_information')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
