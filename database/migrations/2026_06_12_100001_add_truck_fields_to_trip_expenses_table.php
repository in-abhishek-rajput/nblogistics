<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trip_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('trip_expenses', 'current_km_reading')) {
                $table->integer('current_km_reading')->nullable()->after('payment_mode');
            }
            if (!Schema::hasColumn('trip_expenses', 'expense_image')) {
                $table->string('expense_image')->nullable()->after('current_km_reading');
            }
            if (!Schema::hasColumn('trip_expenses', 'shop_name')) {
                $table->string('shop_name')->nullable()->after('expense_image');
            }
            if (!Schema::hasColumn('trip_expenses', 'driver_id')) {
                $table->unsignedInteger('driver_id')->nullable()->after('shop_name');
            }
            if (!Schema::hasColumn('trip_expenses', 'driver_name')) {
                $table->string('driver_name')->nullable()->after('driver_id');
            }
            if (!Schema::hasColumn('trip_expenses', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('driver_name');
            }
            if (!Schema::hasColumn('trip_expenses', 'created_by')) {
                $table->unsignedInteger('created_by')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('trip_expenses', 'updated_by')) {
                $table->unsignedInteger('updated_by')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('trip_expenses', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('trip_expenses', 'deleted_by')) {
                $table->unsignedInteger('deleted_by')->nullable()->after('deleted_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trip_expenses', function (Blueprint $table) {
            $columns = [
                'current_km_reading',
                'expense_image',
                'shop_name',
                'driver_id',
                'driver_name',
                'transaction_id',
                'created_by',
                'updated_by',
                'deleted_at',
                'deleted_by',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('trip_expenses', $column)) {
                    if (in_array($column, ['driver_id', 'created_by', 'updated_by', 'deleted_by'], true)) {
                        $table->dropForeign([$column]);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
