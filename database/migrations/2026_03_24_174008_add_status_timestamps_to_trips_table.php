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
            $table->datetime('completed_date')->nullable()->after('end_km');
            $table->datetime('pod_received_date')->nullable()->after('completed_date');
            $table->datetime('pod_submitted_date')->nullable()->after('pod_received_date');
            $table->datetime('settled_date')->nullable()->after('pod_submitted_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn(['completed_date', 'pod_received_date', 'pod_submitted_date', 'settled_date']);
        });
    }
};
