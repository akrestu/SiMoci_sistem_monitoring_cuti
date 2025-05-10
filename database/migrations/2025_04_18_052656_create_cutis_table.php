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
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id');
            $table->unsignedBigInteger('jenis_cuti_id');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('lama_hari');
            $table->text('alasan');
            $table->unsignedBigInteger('transportasi_id')->nullable();
            $table->boolean('status_tiket')->default(false);
            $table->enum('status_cuti', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cutis');
    }
};
