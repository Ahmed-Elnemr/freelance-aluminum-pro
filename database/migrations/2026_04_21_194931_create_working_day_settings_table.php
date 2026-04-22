<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_day_settings', function (Blueprint $table) {
            $table->id();
            $table->string('day')->unique(); // saturday, sunday, monday, tuesday, wednesday, thursday, friday
            $table->time('start_time')->default('09:00:00');
            $table->time('end_time')->default('21:00:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_day_settings');
    }
};
