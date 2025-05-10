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
            // Add tanggal_persetujuan (approval date) column
            $table->timestamp('tanggal_persetujuan')->nullable();
            
            // Add approved_by column to store the user ID who approved the leave
            $table->unsignedBigInteger('approved_by')->nullable();
            
            // Add foreign key to users table if needed
            // $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            // Drop columns if migration needs to be rolled back
            $table->dropColumn('tanggal_persetujuan');
            $table->dropColumn('approved_by');
            
            // Drop foreign key if added
            // $table->dropForeign(['approved_by']);
        });
    }
};
