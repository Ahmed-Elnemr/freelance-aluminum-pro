<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('content')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('final_price', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('maintenance_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        DB::table('orders')->delete();

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('maintenance_id')->after('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::dropIfExists('service_inspections');
        Schema::dropIfExists('service_payment_methods');
        Schema::dropIfExists('services');
        Schema::dropIfExists('main_services');
        Schema::dropIfExists('category_services');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('category_services', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('main_services', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('content')->nullable();
            $table->string('type')->default('services');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('main_service_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->string('type')->default('services');
            $table->json('name');
            $table->json('content')->nullable();
            $table->decimal('price', 8, 2);
            $table->decimal('final_price', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('service_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('paymentmethod');
            $table->timestamps();
        });

        Schema::create('service_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['maintenance_id']);
            $table->dropColumn('maintenance_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('service_id')->after('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::dropIfExists('maintenance_inspections');
        Schema::dropIfExists('maintenances');
    }
};
