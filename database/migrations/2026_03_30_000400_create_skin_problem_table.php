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
        Schema::create('skin_problem', function (Blueprint $table) {
            $table->string('id_problem')->primary();
            $table->string('problem_name')->nullable();
            $table->boolean('kulit_kusam')->default(0);
            $table->boolean('jerawat')->default(0);
            $table->boolean('bekas_jerawat')->default(0);
            $table->boolean('pori_pori_besar')->default(0);
            $table->boolean('flek_hitam')->default(0);
            $table->boolean('garis_halus_dan_kerutan')->default(0);
            $table->boolean('komedo')->default(0);
            $table->boolean('warna_kulit_tidak_merata')->default(0);
            $table->boolean('kemerahan')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skin_problem');
    }
};
