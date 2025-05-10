<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $roles = ['admin', 'user'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        // Assign admin role to existing users
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        
        // Assign admin role to first user (default admin)
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->roles()->sync([$adminRole->id]);
        }
        
        // Assign user role to other users
        User::whereNotIn('email', ['admin@example.com'])->get()->each(function($user) use ($userRole) {
            $user->roles()->sync([$userRole->id]);
        });
        
        $this->command->info('Roles seeded successfully.');
    }
} 