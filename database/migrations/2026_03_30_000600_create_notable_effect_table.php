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
        Schema::create('notable_effect', function (Blueprint $table) {
            $table->string('id_notable')->primary();
            $table->string('effect_name')->nullable();
            $table->boolean('acne_free')->default(0);
            $table->boolean('soothing')->default(0);
            $table->boolean('brightening')->default(0);
            $table->boolean('moisturizing')->default(0);
            $table->boolean('hydrating')->default(0);
            $table->boolean('pore_care')->default(0);
            $table->boolean('anti_aging')->default(0);
            $table->boolean('balancing')->default(0);
            $table->boolean('uv_protection')->default(0);
            $table->boolean('skin_barrier')->default(0);
            $table->boolean('refreshing')->default(0);
            $table->boolean('oil_control')->default(0);
            $table->boolean('no_whitecast')->default(0);
            $table->boolean('black_spot')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notable_effect');
    }
};
