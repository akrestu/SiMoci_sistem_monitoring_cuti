<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Cuti Tahunan jatah_hari to 14 days
        DB::table('jenis_cutis')
            ->where('nama_jenis', 'Cuti Tahunan')
            ->update(['jatah_hari' => 14]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Cuti Tahunan jatah_hari back to 12 days
        DB::table('jenis_cutis')
            ->where('nama_jenis', 'Cuti Tahunan')
            ->update(['jatah_hari' => 12]);
    }
};
