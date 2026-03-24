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
        Schema::create('trips', function (Blueprint $table) {
            $table->unsignedInteger('id', true)->primary();
            $table->unsignedInteger('party_id');
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('cascade');
            $table->unsignedInteger('truck_id');
            $table->foreign('truck_id')->references('id')->on('trucks')->onDelete('cascade');
            $table->string('origin', 100);
            $table->string('destination', 100);
            $table->enum('billing_type', array_keys(config('trip.billing_types')))->default('fixed');
            $table->float('freight_amount', 10, 2)->default(0.00);
            $table->datetime('start_date');
            $table->unsignedInteger('start_km');
            $table->datetime('end_date')->nullable();
            $table->unsignedInteger('end_km')->nullable();
            $table->enum('status', array_keys(config('trip.statuses')))->default(config('trip.default_status'));
            $table->timestamps();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->unsignedInteger('deleted_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
