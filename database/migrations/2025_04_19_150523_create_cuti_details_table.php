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
        Schema::create('cuti_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuti_id');
            $table->unsignedBigInteger('jenis_cuti_id');
            $table->integer('jumlah_hari');
            $table->timestamps();
            
            $table->foreign('cuti_id')->references('id')->on('cutis')->onDelete('cascade');
            $table->foreign('jenis_cuti_id')->references('id')->on('jenis_cutis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti_details');
    }
};
