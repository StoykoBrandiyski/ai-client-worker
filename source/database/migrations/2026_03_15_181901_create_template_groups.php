<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('template_groups', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 255);
            $table->string('description', 255);
            $table->timestamps();
        });

        Schema::create('prompt_templates', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('name', 255);
            $table->integer('template_group_id');
            $table->text('content');
            $table->timestamps();

            $table->foreign('template_group_id')->references('id')->on('template_groups')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('prompt_templates');
        Schema::dropIfExists('template_groups');
    }
};