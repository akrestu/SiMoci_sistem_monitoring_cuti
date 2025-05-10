<?php

namespace App\Exports;

use App\Models\TransportasiDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransportasiDetailExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return TransportasiDetail::with(['cuti.karyawan', 'transportasi'])->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Departemen',
            'Jenis Transportasi',
            'Jenis Perjalanan',
            'Rute Asal',
            'Rute Tujuan',
            'Provider/Maskapai',
            'Nomor Tiket',
            'Waktu Berangkat',
            'Biaya Aktual',
            'Hotel',
            'Biaya Hotel',
            'Total Biaya',
            'Status Pemesanan',
            'Catatan'
        ];
    }

    public function map($transportasiDetail): array
    {
        return [
            $transportasiDetail->cuti->karyawan->nama,
            $transportasiDetail->cuti->karyawan->departemen,
            $transportasiDetail->transportasi->jenis,
            ucfirst($transportasiDetail->jenis_perjalanan),
            $transportasiDetail->rute_asal,
            $transportasiDetail->rute_tujuan,
            $transportasiDetail->provider ?? '-',
            $transportasiDetail->nomor_tiket ?? '-',
            $transportasiDetail->waktu_berangkat ? date('d/m/Y H:i', strtotime($transportasiDetail->waktu_berangkat)) : '-',
            number_format($transportasiDetail->biaya_aktual, 0, ',', '.'),
            $transportasiDetail->perlu_hotel ? ($transportasiDetail->hotel_nama ?? 'Ya') : 'Tidak',
            $transportasiDetail->perlu_hotel ? number_format($transportasiDetail->hotel_biaya, 0, ',', '.') : '-',
            number_format($transportasiDetail->biaya_aktual + ($transportasiDetail->perlu_hotel ? $transportasiDetail->hotel_biaya : 0), 0, ',', '.'),
            ucwords(str_replace('_', ' ', $transportasiDetail->status_pemesanan)),
            $transportasiDetail->catatan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:O1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ]
        ];
    }
}