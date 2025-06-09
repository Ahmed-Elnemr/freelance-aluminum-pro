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
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('main_service_id')->nullable()->after('category_service_id');
            $table->foreign('main_service_id')->references('id')->on('main_services')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['main_service_id']);
            $table->dropColumn('main_service_id');
        });
    }
};
