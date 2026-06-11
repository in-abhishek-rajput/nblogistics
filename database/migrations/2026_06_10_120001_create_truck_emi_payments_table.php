<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('truck_emi_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_emi_id')->constrained('truck_emis')->cascadeOnDelete();
            $table->date('due_date');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('truck_emi_id');
            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('truck_emi_payments');
    }
};
