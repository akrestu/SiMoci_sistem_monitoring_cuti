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
        // Drop the existing column
        Schema::table('jenis_cutis', function (Blueprint $table) {
            $table->dropColumn('jenis_poh');
        });

        // Add the column back with the new enum values
        Schema::table('jenis_cutis', function (Blueprint $table) {
            $table->enum('jenis_poh', ['lokal', 'luar', 'lokal_luar'])->default('lokal')->after('jatah_hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the modified column
        Schema::table('jenis_cutis', function (Blueprint $table) {
            $table->dropColumn('jenis_poh');
        });

        // Add the column back with the original enum values
        Schema::table('jenis_cutis', function (Blueprint $table) {
            $table->enum('jenis_poh', ['lokal', 'luar'])->default('lokal')->after('jatah_hari');
        });
    }
};
