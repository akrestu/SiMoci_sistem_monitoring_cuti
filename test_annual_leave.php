<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Services\CutiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

// Set up logging
Log::info('Starting annual leave balance test');

// Find a karyawan with annual leave data
$karyawan = Karyawan::whereHas('cutis', function($query) {
    $query->whereHas('jenisCuti', function($q) {
        $q->where('nama_jenis', 'like', '%tahunan%');
    });
})->first();

if (!$karyawan) {
    echo "No karyawan found with annual leave data\n";
    exit;
}

echo "Testing annual leave balance for karyawan: {$karyawan->nama} (ID: {$karyawan->id})\n";

// Find the annual leave type
$jenisCuti = JenisCuti::where('nama_jenis', 'like', '%tahunan%')->first();

if (!$jenisCuti) {
    echo "No annual leave type found\n";
    exit;
}

echo "Annual leave type: {$jenisCuti->nama_jenis} (ID: {$jenisCuti->id})\n";

// Create a CutiService instance
$cutiService = new CutiService();

// Calculate the leave balance
$balance = $cutiService->calculateLeaveBalance($karyawan, $jenisCuti->id);

// Display the results
echo "Annual leave balance:\n";
echo "- Jatah: {$balance['jatah_hari']} days\n";
echo "- Digunakan: {$balance['digunakan']} days\n";
echo "- Sisa: {$balance['sisa']} days\n";
echo "- Reset date: " . ($balance['reset_date'] ? $balance['reset_date']->format('Y-m-d') : 'N/A') . "\n";
echo "- Next reset date: " . ($balance['next_reset_date'] ? $balance['next_reset_date']->format('Y-m-d') : 'N/A') . "\n";

// Get all approved annual leave requests for this karyawan
$approvedLeaves = \App\Models\Cuti::where('karyawan_id', $karyawan->id)
    ->where('jenis_cuti_id', $jenisCuti->id)
    ->where('status_cuti', 'disetujui')
    ->orderBy('tanggal_mulai', 'asc')
    ->get();

echo "\nApproved annual leave requests:\n";
foreach ($approvedLeaves as $cuti) {
    echo "- ID: {$cuti->id}, Start: {$cuti->tanggal_mulai}, End: {$cuti->tanggal_selesai}, Days: {$cuti->lama_hari}\n";
}

// Get all pending annual leave requests for this karyawan
$pendingLeaves = \App\Models\Cuti::where('karyawan_id', $karyawan->id)
    ->where('jenis_cuti_id', $jenisCuti->id)
    ->where('status_cuti', 'pending')
    ->orderBy('tanggal_mulai', 'asc')
    ->get();

echo "\nPending annual leave requests:\n";
foreach ($pendingLeaves as $cuti) {
    echo "- ID: {$cuti->id}, Start: {$cuti->tanggal_mulai}, End: {$cuti->tanggal_selesai}, Days: {$cuti->lama_hari}\n";
}

echo "\nTest completed\n";
