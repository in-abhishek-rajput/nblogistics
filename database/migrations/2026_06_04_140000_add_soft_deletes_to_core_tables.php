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
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('drivers', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('parties', function (Blueprint $table) {
            if (!Schema::hasColumn('parties', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('parties', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('trucks', function (Blueprint $table) {
            if (!Schema::hasColumn('trucks', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('trucks', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable();
                $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('deleted_by');
            $table->dropSoftDeletes();
        });

        Schema::table('parties', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('deleted_by');
            $table->dropSoftDeletes();
        });

        Schema::table('trucks', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn('deleted_by');
            $table->dropSoftDeletes();
        });
    }
};
