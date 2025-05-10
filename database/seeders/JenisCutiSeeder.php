<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisCuti;

class JenisCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jenisCutis = [
            [
                'nama_jenis' => 'Cuti Tahunan',
                'jatah_hari' => 14,
                'keterangan' => 'Cuti reguler yang diberikan setiap tahun (14 hari)'
            ],
            [
                'nama_jenis' => 'Cuti Sakit',
                'jatah_hari' => 14,
                'keterangan' => 'Cuti karena sakit dengan surat dokter'
            ],
            [
                'nama_jenis' => 'Cuti Melahirkan',
                'jatah_hari' => 90,
                'keterangan' => 'Cuti untuk karyawan wanita yang melahirkan'
            ],
            [
                'nama_jenis' => 'Cuti Penting',
                'jatah_hari' => 5,
                'keterangan' => 'Cuti untuk urusan penting (pernikahan, kematian keluarga, dll)'
            ],
            [
                'nama_jenis' => 'Cuti Tanpa Dibayar',
                'jatah_hari' => 30,
                'keterangan' => 'Cuti tanpa dibayar untuk keperluan pribadi'
            ]
        ];

        foreach($jenisCutis as $jenisCuti) {
            JenisCuti::create($jenisCuti);
        }
    }
}