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
        Schema::create('trip_advances', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('trip_id');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'cheque', 'upi', 'bank_transfer', 'fuel', 'others']);
            $table->date('payment_date');
            $table->boolean('received_by_driver')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_advances');
    }
};
