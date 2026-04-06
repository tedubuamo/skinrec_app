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
        Schema::create('skintype', function (Blueprint $table) {
            $table->string('id_skintype')->primary();
            $table->string('skintype_name')->nullable();
            $table->boolean('sensitive')->default(0);
            $table->boolean('combination')->default(0);
            $table->boolean('oily')->default(0);
            $table->boolean('dry')->default(0);
            $table->boolean('normal')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skintype');
    }
};
