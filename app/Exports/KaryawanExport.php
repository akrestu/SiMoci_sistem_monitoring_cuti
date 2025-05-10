<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Karyawan::all();
    }
    
    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIK', 
            'Departemen', 
            'Jabatan',
            'DOH (Tanggal Masuk)',
            'POH (Tempat Penerimaan)',
            'Status',
            'Email'
        ];
    }
    
    public function map($karyawan): array
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $karyawan->nama,
            $karyawan->nik,
            $karyawan->departemen,
            $karyawan->jabatan,
            $karyawan->doh ? date('d/m/Y', strtotime($karyawan->doh)) : '',
            $karyawan->poh,
            $karyawan->status,
            $karyawan->email
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'f0f0f0']]]
        ];
    }
}