<?php

namespace App\Exports;

use App\Models\Cuti;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CutisExports implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Cuti::with(['karyawan', 'jenisCuti', 'transportasi'])
            ->get()
            ->map(function ($cuti) {
                return [
                    'id' => $cuti->id,
                    'karyawan' => $cuti->karyawan->nama,
                    'jenis_cuti' => $cuti->jenisCuti->nama_jenis,
                    'tanggal_mulai' => $cuti->tanggal_mulai,
                    'tanggal_selesai' => $cuti->tanggal_selesai,
                    'lama_hari' => $cuti->lama_hari,
                    'alasan' => $cuti->alasan,
                    'transportasi' => $cuti->transportasi ? $cuti->transportasi->jenis : '-',
                    'status_tiket' => $cuti->status_tiket ? 'Sudah Dibeli' : 'Belum Dibeli',
                    'status_cuti' => $cuti->status_cuti,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Karyawan',
            'Jenis Cuti',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Lama Hari',
            'Alasan',
            'Transportasi',
            'Status Tiket',
            'Status Cuti',
        ];
    }
}