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
        if (! Schema::hasTable('maintenances')) {
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
        }

        if (! Schema::hasTable('maintenance_inspections')) {
            Schema::create('maintenance_inspections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('maintenance_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('orders')) {
            DB::table('orders')->delete();

            if (Schema::hasColumn('orders', 'service_id')) {
                $driver = Schema::getConnection()->getDriverName();
                if ($driver === 'mysql') {
                    $tableName = Schema::getConnection()->getTablePrefix().'orders';
                    $fk = DB::selectOne(
                        "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
                         WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = 'service_id' 
                         AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1",
                        [Schema::getConnection()->getDatabaseName(), $tableName]
                    );
                    if ($fk && ! empty($fk->CONSTRAINT_NAME)) {
                        DB::statement("ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                    }
                } else {
                    Schema::table('orders', function (Blueprint $table) {
                        $table->dropForeign(['service_id']);
                    });
                }

                Schema::table('orders', function (Blueprint $table) {
                    $table->dropColumn('service_id');
                });
            }

            if (! Schema::hasColumn('orders', 'maintenance_id')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->unsignedBigInteger('maintenance_id')->after('user_id');
                    $table->foreign('maintenance_id')->references('id')->on('maintenances')->cascadeOnDelete();
                });
            }
        }

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

        if (Schema::hasColumn('orders', 'maintenance_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['maintenance_id']);
                $table->dropColumn('maintenance_id');
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('service_id')->after('user_id')->constrained()->cascadeOnDelete();
            });
        }

        Schema::dropIfExists('maintenance_inspections');
        Schema::dropIfExists('maintenances');
    }
};
