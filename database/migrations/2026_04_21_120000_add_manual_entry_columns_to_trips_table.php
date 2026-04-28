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
        Schema::table('trips', function (Blueprint $table) {
            $table->string('party_name')->nullable()->after('party_id');
            $table->string('truck_name')->nullable()->after('truck_id');
            $table->string('driver_name')->nullable()->after('driver_id');
            $table->boolean('party_manual_entry')->default(false)->after('party_name');
            $table->boolean('truck_manual_entry')->default(false)->after('truck_name');
            $table->boolean('driver_manual_entry')->default(false)->after('driver_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'party_name',
                'truck_name',
                'driver_name',
                'party_manual_entry',
                'truck_manual_entry',
                'driver_manual_entry'
            ]);
        });
    }
};