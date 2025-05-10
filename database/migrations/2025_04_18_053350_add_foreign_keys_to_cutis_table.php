<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->foreign('karyawan_id')->references('id')->on('karyawans');
            $table->foreign('jenis_cuti_id')->references('id')->on('jenis_cutis');
            $table->foreign('transportasi_id')->references('id')->on('transportasis');
        });
    }
    
    public function down()
    {
        Schema::table('cutis', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
            $table->dropForeign(['jenis_cuti_id']);
            $table->dropForeign(['transportasi_id']);
        });
    }
};
