<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_hour_blocked_slots', function (Blueprint $table) {
            $table->id();
            $table->string('day'); // saturday, sunday, ...
            $table->time('slot_time'); // e.g. 09:00, 09:30
            $table->timestamps();

            $table->unique(['day', 'slot_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_hour_blocked_slots');
    }
};
