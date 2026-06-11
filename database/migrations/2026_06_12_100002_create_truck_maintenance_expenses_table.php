<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('truck_maintenance_expenses');

        Schema::create('truck_maintenance_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('truck_id');
            $table->string('expense_type');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->enum('payment_mode', ['cash', 'credit', 'paid_by_driver', 'online'])->default('cash');
            $table->string('shop_name')->nullable();
            $table->unsignedInteger('driver_id')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('transaction_id')->nullable();
            $table->integer('current_km_reading')->nullable();
            $table->text('notes')->nullable();
            $table->string('expense_image')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('deleted_by')->nullable();

            $table->index('truck_id');
            $table->index('expense_date');
            $table->index('due_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('truck_maintenance_expenses');
    }
};
