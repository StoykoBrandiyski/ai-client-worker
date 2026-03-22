<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Process Conditions
        Schema::create('process_conditions', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 255);
            $table->string('entity_type', 50);
            $table->string('field_key', 255);
            $table->string('operator', 255);
            $table->string('value', 255);
            $table->timestamps();
        });

        // 2. Processes
        Schema::create('processes', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 255)->index();
            $table->string('status', 255)->index();
            $table->integer('is_enabled')->default(0);
            $table->integer('condition_id');
            $table->string('schedule', 255);
            $table->integer('timeout');
            $table->integer('limit_tasks');
            $table->timestamps();

            $table->foreign('condition_id')->references('id')->on('process_conditions')->onDelete('cascade');
        });

        // 3. Process Models (Mapping to engine_models)
        Schema::create('processes_models', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('process_id');
            $table->string('model_id', 255); // Links to engine_models.identifier
            $table->integer('sort_order')->default(1);
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
            $table->foreign('model_id')->references('identifier')->on('engine_models')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('processes_models');
        Schema::dropIfExists('processes');
        Schema::dropIfExists('process_conditions');
    }
};
