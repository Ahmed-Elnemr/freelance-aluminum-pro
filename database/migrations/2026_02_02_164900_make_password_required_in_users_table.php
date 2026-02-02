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
        Schema::table('users', function (Blueprint $table) {
            // Make password required (remove nullable)
            $table->string('password')->nullable(false)->change();
            
            // Make email required (remove nullable)
            $table->string('email', 191)->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert password to nullable
            $table->string('password')->nullable()->change();
            
            // Revert email to nullable
            $table->string('email', 191)->unique()->nullable()->change();
        });
    }
};
