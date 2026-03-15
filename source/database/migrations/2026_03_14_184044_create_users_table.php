<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Using standard integer as requested in the SQL schema
            $table->integer('id')->autoIncrement(); 
            $table->string('username', 255)->unique()->index();
            $table->string('password', 255);
            $table->string('email', 255)->index();
            $table->string('remember_token', 255)->nullable();
            $table->integer('role_id')->default(1); // Default role
            $table->integer('is_active')->default(0);
            
            // created_at and updated_at
            $table->timestamps();

            // Foreign Key
            // $table->foreign('role_id')->references('id')->on('user_roles')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};