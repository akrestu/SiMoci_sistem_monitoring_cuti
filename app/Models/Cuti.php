<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'karyawan_id',
        'jenis_cuti_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama_hari',
        'alasan',
        'status_cuti',
        'memo_kompensasi_status',
        'memo_kompensasi_nomor',
        'memo_kompensasi_tanggal'
    ];
    
    protected $casts = [
        'memo_kompensasi_status' => 'boolean',
    ];
    
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
    
    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti_id');
    }
    
    public function transportasiDetails()
    {
        return $this->hasMany(TransportasiDetail::class);
    }
    
    public function cutiDetails()
    {
        return $this->hasMany(CutiDetail::class);
    }
    
    public function hasTransportasi()
    {
        return $this->transportasiDetails()->count() > 0;
    }
    
    public function isPerluMemoKompensasi()
    {
        // Only consider explicit memo_kompensasi_status values (true or false)
        // and ignore inferred values from jenis_cuti properties
        return $this->memo_kompensasi_status === true || $this->memo_kompensasi_status === false;
    }
}