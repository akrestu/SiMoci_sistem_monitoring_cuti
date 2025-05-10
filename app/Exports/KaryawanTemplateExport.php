<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Comment;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class KaryawanTemplateExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        // Hanya mengembalikan satu baris contoh
        return new Collection([
            [
                'nama' => 'John Doe',
                'nik' => '12345678', // Pastikan NIK sebagai string
                'departemen' => 'IT',
                'jabatan' => 'Staff',
                'doh' => '15/01/2025', // Format DD/MM/YYYY
                'poh' => 'Jakarta',
                'status' => 'Staff', // Staff atau Non Staff
                'email' => 'john@example.com'
            ]
        ]);
    }
    
    public function headings(): array
    {
        return [
            'nama',
            'nik', 
            'departemen', 
            'jabatan',
            'doh',
            'poh',
            'status',
            'email'
        ];
    }
    
    public function map($row): array
    {
        return [
            $row['nama'],
            $row['nik'],
            $row['departemen'],
            $row['jabatan'],
            $row['doh'],
            $row['poh'],
            $row['status'],
            $row['email']
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        // Tambahkan komentar pada sel A1
        $comment = $sheet->getComment('A1');
        $comment->getText()->createTextRun('Petunjuk Pengisian:');
        $comment->getText()->createTextRun("\r\n1. Nama harus diisi dan tidak boleh duplikat");
        $comment->getText()->createTextRun("\r\n2. NIK harus diisi dan tidak boleh duplikat");
        $comment->getText()->createTextRun("\r\n3. Jika NIK berupa angka, pastikan formatnya adalah teks (awali dengan karakter ')");
        $comment->getText()->createTextRun("\r\n4. Departemen harus diisi");
        $comment->getText()->createTextRun("\r\n5. Jabatan harus diisi");
        $comment->getText()->createTextRun("\r\n6. DOH (Date Of Hire) format DD/MM/YYYY, misal: 15/01/2025");
        $comment->getText()->createTextRun("\r\n7. Status harus berisi 'Staff' atau 'Non Staff'");
        $comment->getText()->createTextRun("\r\n8. Email tidak wajib diisi, tapi jika diisi harus valid dan tidak duplikat");
        
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'f0f0f0']]]
        ];
    }
    
    // Tidak perlu lagi menggunakan metode export() karena kita akan menggunakan
    // Excel::download() langsung di controller
}