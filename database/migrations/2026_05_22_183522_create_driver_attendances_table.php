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
        Schema::create('driver_attendances', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('driver_id')
                  ->constrained()
                  ->cascadeOnDelete();
                  
            $table->date('attendance_date');
            
            $table->enum('status', [
                'present',
                'half_day',
                'absent',
                'holiday'
            ])->default('present');
            
            $table->timestamps();
            
            $table->unique([
                'driver_id',
                'attendance_date'
            ], 'driver_attendance_unique');
            
            $table->index('attendance_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_attendances');
    }
};
