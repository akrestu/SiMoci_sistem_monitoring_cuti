<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportasi extends Model
{
    use HasFactory;
    
    protected $fillable = ['jenis', 'keterangan'];
    
    public function cutis()
    {
        return $this->hasMany(Cuti::class);
    }
    
    public function transportasiDetails()
    {
        return $this->hasMany(TransportasiDetail::class);
    }
}