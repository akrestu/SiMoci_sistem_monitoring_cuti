<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Hapus user dengan email admin@example.com jika sudah ada
        User::where('email', 'admin@example.com')->delete();
        
        // Buat ulang user admin dengan username sicuti25
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'username' => 'sicuti25',
            'password' => Hash::make('Kristanto1')
        ]);

        // User tambahan (dapat disesuaikan)
        User::firstOrCreate(
            ['email' => 'kriztantz@gmail.com'],
            [
                'name' => 'kriztantz',
                'username' => 'kriztantz',
                'password' => Hash::make('11223344')
            ]
        );
    }
}