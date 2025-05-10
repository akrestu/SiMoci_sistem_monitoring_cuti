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
        Schema::create('transportasi_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuti_id');
            $table->unsignedBigInteger('transportasi_id');
            $table->string('nomor_tiket')->nullable();
            $table->string('rute_asal');
            $table->string('rute_tujuan');
            $table->dateTime('waktu_berangkat')->nullable();
            $table->dateTime('waktu_kembali')->nullable();
            $table->string('provider')->nullable();
            $table->decimal('biaya_aktual', 10, 2)->default(0);
            $table->boolean('perlu_hotel')->default(false);
            $table->string('hotel_nama')->nullable();
            $table->decimal('hotel_biaya', 10, 2)->default(0);
            $table->string('status_pemesanan')->default('belum_dipesan');
            $table->string('catatan')->nullable();
            $table->timestamps();
            
            $table->foreign('cuti_id')->references('id')->on('cutis')->onDelete('cascade');
            $table->foreign('transportasi_id')->references('id')->on('transportasis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportasi_details');
    }
};