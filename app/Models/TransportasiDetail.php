<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportasiDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'cuti_id', 
        'transportasi_id', 
        'jenis_perjalanan',
        'nomor_tiket', 
        'rute_asal',
        'rute_tujuan',
        'waktu_berangkat',
        'waktu_kembali',
        'provider',
        'biaya_aktual',
        'perlu_hotel',
        'hotel_nama',
        'hotel_biaya',
        'status_pemesanan',
        'catatan'
    ];
    
    public function cuti()
    {
        return $this->belongsTo(Cuti::class);
    }
    
    public function transportasi()
    {
        return $this->belongsTo(Transportasi::class);
    }
    
    public function getTotalBiayaAttribute()
    {
        return $this->biaya_aktual + ($this->perlu_hotel ? $this->hotel_biaya : 0);
    }
}