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
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Party's full name - required for identification
            $table->string('email')->nullable()->unique(); // Email address - optional but unique if provided
            $table->string('mobile')->nullable(); // Mobile number - optional
            $table->string('status')->default('active'); // Status: active, inactive - from config
            $table->decimal('opening_balance', 15, 2)->default(0); // Opening balance in rupees - for financial tracking
            $table->date('opening_balance_date')->nullable(); // Date of opening balance
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
        Schema::dropIfExists('parties');
    }
};
