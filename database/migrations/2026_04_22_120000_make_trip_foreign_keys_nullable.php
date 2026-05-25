<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Make foreign key columns nullable to support manual entry.
     */
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedInteger('party_id')->nullable()->change();
            $table->unsignedInteger('truck_id')->nullable()->change();
            $table->unsignedInteger('driver_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedInteger('party_id')->change();
            $table->unsignedInteger('truck_id')->change();
            $table->unsignedInteger('driver_id')->change();
        });
    }
};