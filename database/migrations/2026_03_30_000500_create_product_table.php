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
        Schema::create('product', function (Blueprint $table) {
            $table->string('id_product')->primary();
            $table->string('product_name')->nullable();
            $table->string('id_brand')->nullable();
            $table->string('id_category')->nullable();
            $table->string('id_skintype')->nullable();
            $table->string('id_problem')->nullable();
            $table->string('id_notable')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->string('picture_src')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('id_brand')->references('id_brand')->on('brand')->onDelete('set null');
            $table->foreign('id_category')->references('id_category')->on('category')->onDelete('set null');
            $table->foreign('id_skintype')->references('id_skintype')->on('skintype')->onDelete('set null');
            $table->foreign('id_problem')->references('id_problem')->on('skin_problem')->onDelete('set null');
            $table->foreign('id_notable')->references('id_notable')->on('notable_effect')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
