<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tasks', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 255)->index(); // Indexed for search
            $table->text('request_content');
            $table->text('response_content')->nullable();
            $table->integer('user_id');
            $table->integer('prompt_template_id')->nullable();
            $table->integer('parent_id')->nullable()->index();
            $table->integer('group_id')->index();
            $table->string('status', 50)->index(); // Indexed for filtering
            $table->integer('executed_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('task_groups')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('tasks')->onDelete('set null');
        });

        Schema::create('uploaded_task_images', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('path', 255);
            $table->integer('task_id');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('uploaded_task_images');
        Schema::dropIfExists('tasks');
    }
};