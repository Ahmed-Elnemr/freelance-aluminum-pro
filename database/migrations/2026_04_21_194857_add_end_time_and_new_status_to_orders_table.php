<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->time('end_time')->nullable()->after('scheduled_time');
        });

        // Update existing statuses: current -> new, expired -> completed
        DB::table('orders')->where('status', 'current')->update(['status' => 'new']);
        DB::table('orders')->where('status', 'expired')->update(['status' => 'completed']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('end_time');
        });
    }
};
