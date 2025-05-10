<?php
// This script adds a test employee to the database

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;

try {
    // Check if test employee already exists
    $exists = Karyawan::where('nik', '9999')->exists();
    
    if (!$exists) {
        // Create test employee
        $employee = Karyawan::create([
            'nama' => 'Test Employee',
            'nik' => '9999',
            'departemen' => 'Testing',
            'jabatan' => 'Tester',
            'email' => 'test@example.com',
        ]);
        
        echo "Test employee created successfully with ID: " . $employee->id . PHP_EOL;
    } else {
        echo "Test employee already exists." . PHP_EOL;
    }
    
    // Display all employees
    $employees = Karyawan::all();
    echo "All employees in database:" . PHP_EOL;
    foreach ($employees as $emp) {
        echo "- " . $emp->nama . " (NIK: " . $emp->nik . ", ID: " . $emp->id . ")" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
} 