<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('engine_models', function (Blueprint $table) {
            $table->string('identifier', 255)->primary();
            $table->string('name', 255);
            $table->integer('engine_id');
            $table->string('url', 255)->nullable();
            $table->string('initial_prompt', 255)->nullable();
            $table->integer('use_chat')->default(0);
            $table->timestamps();

            $table->foreign('engine_id')->references('id')->on('engines')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('engine_models');
    }
};
