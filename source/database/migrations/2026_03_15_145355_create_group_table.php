<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('task_groups', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 255)->index(); // Indexed for search
            $table->string('description', 255);
            $table->integer('parent_id')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('task_groups')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('task_groups');
    }
};
