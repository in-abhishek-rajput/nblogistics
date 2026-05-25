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
        Schema::table('trip_expenses', function (Blueprint $table) {
            $table->enum('expense_category', ['trip', 'truck', 'office'])->default('trip')->after('id');
            $table->unsignedBigInteger('truck_id')->nullable()->after('expense_category');
            $table->unsignedInteger('trip_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trip_expenses', function (Blueprint $table) {
            $table->dropColumn(['expense_category', 'truck_id']);
            $table->unsignedInteger('trip_id')->nullable(false)->change();
        });
    }
};
