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
        Schema::table('transportasi_details', function (Blueprint $table) {
            $table->enum('jenis_perjalanan', ['pergi', 'kembali'])->default('pergi')->after('transportasi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transportasi_details', function (Blueprint $table) {
            $table->dropColumn('jenis_perjalanan');
        });
    }
};
