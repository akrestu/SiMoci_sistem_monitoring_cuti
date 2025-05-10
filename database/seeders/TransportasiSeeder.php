<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transportasi;

class TransportasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transportasis = [
            [
                'jenis' => 'Pesawat',
                'keterangan' => 'Transportasi udara'
            ],
            [
                'jenis' => 'Kereta Api',
                'keterangan' => 'Transportasi darat via rel'
            ],
            [
                'jenis' => 'Bus',
                'keterangan' => 'Transportasi darat'
            ],
            [
                'jenis' => 'Kapal Laut',
                'keterangan' => 'Transportasi air'
            ]
        ];
        
        foreach($transportasis as $transportasi) {
            Transportasi::create($transportasi);
        }
    }
}