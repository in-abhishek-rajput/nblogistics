<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('truck_fuel_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_id')->constrained('trucks')->cascadeOnDelete();
            $table->decimal('expense_amount', 15, 2);
            $table->decimal('fuel_quantity', 12, 2)->nullable();
            $table->decimal('rate_per_litre', 12, 2)->nullable();
            $table->boolean('is_full_tank')->default(false);
            $table->integer('current_km_reading')->nullable();
            $table->enum('payment_mode', ['cash', 'credit', 'paid_by_driver', 'online'])->default('cash');
            $table->string('shop_name')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->string('driver_name')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('diesel_pump_name')->nullable();
            $table->date('expense_date');
            $table->string('bill_file')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('truck_id');
            $table->index('expense_date');
            $table->index('payment_mode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('truck_fuel_expenses');
    }
};
