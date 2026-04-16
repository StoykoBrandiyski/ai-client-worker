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
        Schema::create('process_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('process_id');
            $table->integer('engine_id');
            $table->string('engine_model_identifier');
            $table->string('status'); // 'ready' or 'error'
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->string('process_message')->nullable();
            $table->integer('task_id');
            $table->timestamps();

            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
            $table->foreign('engine_id')->references('id')->on('engines')->onDelete('cascade');
            $table->foreign('engine_model_identifier')->references('identifier')->on('engine_models')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_logs');
    }
};
