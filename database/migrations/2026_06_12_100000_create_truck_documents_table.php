<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('truck_documents')) {
            return;
        }

        Schema::create('truck_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('truck_id');
            $table->string('document_type');
            $table->string('document_name');
            $table->string('document_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('document_file')->nullable();
            $table->decimal('expense_amount', 15, 2)->nullable();
            $table->date('expense_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedInteger('deleted_by')->nullable();

            $table->index('truck_id');
            $table->index('document_type');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('truck_documents');
    }
};
