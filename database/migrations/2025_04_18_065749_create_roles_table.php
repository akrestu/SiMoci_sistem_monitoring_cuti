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
        // Skip creating the roles table if it already exists
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
            
            // Create role_user pivot table if it doesn't exist
            if (!Schema::hasTable('role_user')) {
                Schema::create('role_user', function (Blueprint $table) {
                    $table->unsignedBigInteger('role_id');
                    $table->unsignedBigInteger('user_id');
                    $table->timestamps();
                    
                    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    
                    $table->primary(['role_id', 'user_id']);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing to avoid unintentionally dropping existing tables
    }
};
