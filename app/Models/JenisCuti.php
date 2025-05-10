<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    use HasFactory;
    
    protected $fillable = ['nama_jenis', 'jatah_hari', 'keterangan', 'jenis_poh', 'perlu_memo_kompensasi'];
    
    public function cutis()
    {
        return $this->hasMany(Cuti::class, 'jenis_cuti_id');
    }
}