<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if table is empty first
        if (Karyawan::count() === 0) {
            // Insert sample data
            $karyawans = [
                [
                    'nama' => 'Ahmad Rizki',
                    'nik' => '1001',
                    'departemen' => 'IT',
                    'jabatan' => 'Programmer',
                    'email' => 'ahmad@example.com',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Budi Santoso',
                    'nik' => '1002',
                    'departemen' => 'HR',
                    'jabatan' => 'Staff',
                    'email' => 'budi@example.com',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Citra Dewi',
                    'nik' => '1003',
                    'departemen' => 'Finance',
                    'jabatan' => 'Manager',
                    'email' => 'citra@example.com',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Dimas Prayoga',
                    'nik' => '1004',
                    'departemen' => 'Marketing',
                    'jabatan' => 'Supervisor',
                    'email' => 'dimas@example.com',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Eka Putri',
                    'nik' => '1005',
                    'departemen' => 'IT',
                    'jabatan' => 'System Analyst',
                    'email' => 'eka@example.com',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];
            
            // Insert data into the database
            foreach ($karyawans as $karyawan) {
                Karyawan::create($karyawan);
            }
            
            $this->command->info('Sample karyawan data seeded successfully.');
        } else {
            $this->command->info('Karyawan table already has data, skipping seeding.');
        }
    }
} 