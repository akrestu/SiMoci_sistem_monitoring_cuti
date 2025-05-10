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
        Schema::table('cutis', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['transportasi_id']);
            
            // Then drop the columns
            $table->dropColumn('transportasi_id');
            $table->dropColumn('status_tiket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->unsignedBigInteger('transportasi_id')->nullable();
            $table->boolean('status_tiket')->default(false);
            
            // Add foreign key constraint
            $table->foreign('transportasi_id')->references('id')->on('transportasis');
        });
    }
}; 