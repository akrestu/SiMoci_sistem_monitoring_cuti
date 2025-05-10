<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Services\CutiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TestAnnualLeaveBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:annual-leave-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test annual leave balance calculation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set up logging
        Log::info('Starting annual leave balance test');

        // Find a karyawan with annual leave data
        $karyawan = Karyawan::whereHas('cutis', function($query) {
            $query->whereHas('jenisCuti', function($q) {
                $q->where('nama_jenis', 'like', '%tahunan%');
            });
        })->first();

        if (!$karyawan) {
            $this->error("No karyawan found with annual leave data");
            return 1;
        }

        $this->info("Testing annual leave balance for karyawan: {$karyawan->nama} (ID: {$karyawan->id})");

        // Find the annual leave type
        $jenisCuti = JenisCuti::where('nama_jenis', 'like', '%tahunan%')->first();

        if (!$jenisCuti) {
            $this->error("No annual leave type found");
            return 1;
        }

        $this->info("Annual leave type: {$jenisCuti->nama_jenis} (ID: {$jenisCuti->id})");

        // Create a CutiService instance
        $cutiService = new CutiService();

        // Calculate the leave balance
        $balance = $cutiService->calculateLeaveBalance($karyawan, $jenisCuti->id);

        // Display the results
        $this->info("Annual leave balance:");
        $this->line("- Jatah: {$balance['jatah_hari']} days");
        $this->line("- Digunakan: {$balance['digunakan']} days");
        $this->line("- Sisa: {$balance['sisa']} days");
        $this->line("- Reset date: " . ($balance['reset_date'] ? $balance['reset_date']->format('Y-m-d') : 'N/A'));
        $this->line("- Next reset date: " . ($balance['next_reset_date'] ? $balance['next_reset_date']->format('Y-m-d') : 'N/A'));

        // Get all approved annual leave requests for this karyawan
        $approvedLeaves = \App\Models\Cuti::where('karyawan_id', $karyawan->id)
            ->where('jenis_cuti_id', $jenisCuti->id)
            ->where('status_cuti', 'disetujui')
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        $this->info("\nApproved annual leave requests:");
        foreach ($approvedLeaves as $cuti) {
            $this->line("- ID: {$cuti->id}, Start: {$cuti->tanggal_mulai}, End: {$cuti->tanggal_selesai}, Days: {$cuti->lama_hari}");
        }

        // Get all pending annual leave requests for this karyawan
        $pendingLeaves = \App\Models\Cuti::where('karyawan_id', $karyawan->id)
            ->where('jenis_cuti_id', $jenisCuti->id)
            ->where('status_cuti', 'pending')
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        $this->info("\nPending annual leave requests:");
        foreach ($pendingLeaves as $cuti) {
            $this->line("- ID: {$cuti->id}, Start: {$cuti->tanggal_mulai}, End: {$cuti->tanggal_selesai}, Days: {$cuti->lama_hari}");
        }

        $this->info("\nTest completed");
        return 0;
    }
}
