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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->string('truck_number')->unique(); // Truck number plate
            $table->string('truck_type'); // Type: mini-truck, open-truck, etc.
            $table->enum('ownership', ['market', 'self'])->default('self'); // Ownership: market or self
            $table->string('status')->default('available'); // Status: available, not_available, hold
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->onDelete('set null'); // Foreign key to drivers table
            $table->timestamps();

            // Indexes for searchable columns to improve query performance
            $table->index('truck_number'); // For searching by truck number
            $table->index('truck_type'); // For filtering by type
            $table->index('ownership'); // For filtering by ownership
            $table->index('status'); // For filtering by status
            $table->index('driver_id'); // For filtering by driver
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
