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
            $table->boolean('memo_kompensasi_status')->nullable()->after('status_cuti')->comment('Status pengajuan memo kompensasi');
            $table->string('memo_kompensasi_nomor')->nullable()->after('memo_kompensasi_status')->comment('Nomor memo kompensasi');
            $table->date('memo_kompensasi_tanggal')->nullable()->after('memo_kompensasi_nomor')->comment('Tanggal memo kompensasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropColumn('memo_kompensasi_status');
            $table->dropColumn('memo_kompensasi_nomor');
            $table->dropColumn('memo_kompensasi_tanggal');
        });
    }
};