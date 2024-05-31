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
        Schema::create('deleted_products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->foreignId('deleted_by')->constrained(
                table: 'users', indexName: 'deleted_products_deleted_by'
            )->onDelete('cascade');
            $table->string('deleted_at')->nullable();
            $table->string('recovered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_products');
    }
};
