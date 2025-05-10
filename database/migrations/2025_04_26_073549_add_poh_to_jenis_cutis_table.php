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
        Schema::table('jenis_cutis', function (Blueprint $table) {
            $table->enum('jenis_poh', ['lokal', 'luar'])->default('lokal')->after('jatah_hari');
            $table->boolean('perlu_memo_kompensasi')->default(false)->after('jenis_poh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_cutis', function (Blueprint $table) {
            $table->dropColumn('jenis_poh');
            $table->dropColumn('perlu_memo_kompensasi');
        });
    }
};
