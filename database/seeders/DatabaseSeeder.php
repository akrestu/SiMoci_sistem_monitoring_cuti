<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            JenisCutiSeeder::class,
            TransportasiSeeder::class,
            KaryawanSeeder::class,
        ]);
    }
}