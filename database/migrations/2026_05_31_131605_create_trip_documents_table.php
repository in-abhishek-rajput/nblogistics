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
        Schema::create('trip_documents', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedInteger('trip_id');
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            
            $table->enum('document_type', ['bilty', 'invoice', 'receipt']);
            $table->string('document_number')->nullable()->index();
            $table->date('document_date')->nullable();
            
            $table->json('data')->nullable(); // Stores all specific fields for this document
            
            $table->enum('status', ['draft', 'final'])->default('draft');
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            
            $table->unique(['trip_id', 'document_type'], 'unique_trip_doc_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_documents');
    }
};
