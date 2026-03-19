<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('engines', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 255)->index(); // Indexed for fast searching
            $table->string('base_url', 255);
            $table->string('auth_token', 255)->nullable();
            $table->integer('max_tasks_count')->default(0);
            $table->integer('task_timeout')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('engines');
    }
};
