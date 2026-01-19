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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Driver's full name - required for identification
            $table->string('email')->nullable()->unique(); // Email address - optional but unique if provided
            $table->string('mobile'); // Mobile number
            $table->string('status')->default('available'); // Status: available, not_available, hold - from config
            $table->decimal('opening_balance', 15, 2)->default(0); // Opening balance in rupees - for financial tracking
            $table->timestamps();

            // Indexes for searchable columns to improve query performance
            $table->index('name'); // For searching by name
            $table->index('email'); // For searching by email
            $table->index('mobile'); // For searching by mobile
            $table->index('status'); // For filtering by status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
