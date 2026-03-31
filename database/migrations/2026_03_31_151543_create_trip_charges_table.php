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
        Schema::create('trip_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('trip_id');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->enum('charge_direction', ['add_to_bill', 'reduce_from_bill']);
            $table->string('charge_type');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_charges');
    }
};
