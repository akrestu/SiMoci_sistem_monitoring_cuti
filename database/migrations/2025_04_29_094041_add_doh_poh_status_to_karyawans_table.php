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
        Schema::table('karyawans', function (Blueprint $table) {
            $table->date('doh')->nullable()->comment('Date Of Hire - Tanggal masuk kerja');
            $table->string('poh')->nullable()->comment('Place Of Hire - Tempat penerimaan kerja');
            $table->enum('status', ['Staff', 'Non Staff'])->nullable()->comment('Status kepegawaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['doh', 'poh', 'status']);
        });
    }
};
