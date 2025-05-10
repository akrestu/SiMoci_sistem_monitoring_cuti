<?php

namespace App\Exports;

use App\Models\JenisCuti;
use App\Models\Transportasi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CutiTemplateExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function collection()
    {
        // Template kosong dengan contoh data dalam format dd/mm/yyyy
        $today = Carbon::now();
        $twoLaterDay = Carbon::now()->addDays(2);
        
        return new Collection([
            [
                'nik' => '123456789', 
                'jenis_cuti' => 'Cuti Tahunan',
                'tanggal_mulai' => $today->format('d/m/Y'), 
                'tanggal_selesai' => $twoLaterDay->format('d/m/Y'),
                'alasan' => 'Contoh alasan cuti',
                'status_cuti' => 'pending',
                'perlu_memo_kompensasi' => 'tidak',
                'memo_nomor' => '',
                'memo_tanggal' => '',
                'transportasi_jenis' => 'Pesawat',
                'transportasi_rute_pergi_asal' => 'Jakarta',
                'transportasi_rute_pergi_tujuan' => 'Bali',
                'transportasi_rute_kembali_asal' => 'Bali',
                'transportasi_rute_kembali_tujuan' => 'Jakarta'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'nik',
            'jenis_cuti',
            'tanggal_mulai',
            'tanggal_selesai',
            'alasan',
            'status_cuti',
            'perlu_memo_kompensasi',
            'memo_nomor',
            'memo_tanggal',
            'transportasi_jenis',
            'transportasi_rute_pergi_asal',
            'transportasi_rute_pergi_tujuan',
            'transportasi_rute_kembali_asal',
            'transportasi_rute_kembali_tujuan'
        ];
    }

    public function title(): string
    {
        return 'Template Pengajuan Cuti';
    }
    
    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
        ]);
        
        // Memberikan lebar kolom yang sesuai
        $sheet->getColumnDimension('A')->setWidth(15); // NIK
        $sheet->getColumnDimension('B')->setWidth(20); // Jenis Cuti
        $sheet->getColumnDimension('C')->setWidth(15); // Tanggal Mulai
        $sheet->getColumnDimension('D')->setWidth(15); // Tanggal Selesai
        $sheet->getColumnDimension('E')->setWidth(30); // Alasan
        $sheet->getColumnDimension('F')->setWidth(15); // Status Cuti
        $sheet->getColumnDimension('G')->setWidth(20); // Perlu Memo Kompensasi
        $sheet->getColumnDimension('H')->setWidth(15); // Nomor Memo
        $sheet->getColumnDimension('I')->setWidth(15); // Tanggal Memo
        $sheet->getColumnDimension('J')->setWidth(20); // Jenis Transportasi
        $sheet->getColumnDimension('K')->setWidth(25); // Rute Pergi Asal
        $sheet->getColumnDimension('L')->setWidth(25); // Rute Pergi Tujuan
        $sheet->getColumnDimension('M')->setWidth(25); // Rute Kembali Asal
        $sheet->getColumnDimension('N')->setWidth(25); // Rute Kembali Tujuan

        // Tambahkan keterangan jenis cuti yang tersedia
        $jenisCutis = JenisCuti::all(['nama_jenis'])->pluck('nama_jenis')->toArray();
        
        $row = 4;
        $sheet->setCellValue('P' . $row, 'Jenis Cuti yang Tersedia:');
        $sheet->getStyle('P' . $row)->getFont()->setBold(true);
        
        foreach ($jenisCutis as $index => $jenisCuti) {
            $row++;
            $sheet->setCellValue('P' . $row, ($index + 1) . '. ' . $jenisCuti);
        }
        
        // Tambahkan keterangan jenis transportasi yang tersedia
        $row += 2;
        $sheet->setCellValue('P' . $row, 'Jenis Transportasi yang Tersedia:');
        $sheet->getStyle('P' . $row)->getFont()->setBold(true);
        
        $transportasis = Transportasi::all(['jenis'])->pluck('jenis')->toArray();
        foreach ($transportasis as $index => $transportasi) {
            $row++;
            $sheet->setCellValue('P' . $row, ($index + 1) . '. ' . $transportasi);
        }
        
        // Tambahkan catatan untuk pengisian template
        $row += 2;
        $sheet->setCellValue('P' . $row, 'Catatan:');
        $sheet->getStyle('P' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('P' . $row, '• Format tanggal: DD/MM/YYYY (contoh: 26/04/2025)');
        $row++;
        $sheet->setCellValue('P' . $row, '• Status cuti: pending, disetujui, atau ditolak');
        $row++;
        $sheet->setCellValue('P' . $row, '• NIK harus terdaftar dalam sistem');
        $row++;
        $sheet->setCellValue('P' . $row, '• Jenis cuti harus sesuai dengan daftar di samping');
        $row++;
        $sheet->setCellValue('P' . $row, '• Perlu memo kompensasi: ya/tidak');
        $row++;
        $sheet->setCellValue('P' . $row, '• Format tanggal memo: DD/MM/YYYY');
        $row++;
        $sheet->setCellValue('P' . $row, '• Jenis transportasi harus sesuai dengan daftar di samping');
        
        return $sheet;
    }
}