<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'cuti_id',
        'jenis_cuti_id',
        'jumlah_hari'
    ];
    
    public function cuti()
    {
        return $this->belongsTo(Cuti::class);
    }
    
    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class);
    }
}
