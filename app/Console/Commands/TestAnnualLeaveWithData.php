<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Cuti;
use App\Services\CutiService;
use App\Services\AnnualLeaveCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TestAnnualLeaveWithData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:annual-leave-with-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test annual leave balance calculation with test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set up logging
        Log::info('Starting annual leave balance test with test data');

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

        // Create an AnnualLeaveCalculator instance
        $annualLeaveCalculator = new AnnualLeaveCalculator();

        // Calculate the leave balance before adding test data
        $balanceBefore = $annualLeaveCalculator->calculateAnnualLeaveBalance($karyawan, $jenisCuti->id);

        // Display the results
        $this->info("Annual leave balance BEFORE adding test data:");
        $this->line("- Jatah: {$balanceBefore['jatah_hari']} days");
        $this->line("- Digunakan: {$balanceBefore['digunakan']} days");
        $this->line("- Sisa: {$balanceBefore['sisa']} days");
        $this->line("- Reset date: " . ($balanceBefore['reset_date'] ? $balanceBefore['reset_date']->format('Y-m-d') : 'N/A'));
        $this->line("- Next reset date: " . ($balanceBefore['next_reset_date'] ? $balanceBefore['next_reset_date']->format('Y-m-d') : 'N/A'));

        // Create a test annual leave request
        try {
            // Create a test annual leave request with dates within the calculation period
            // Use dates between the reset date and the current date
            $resetDate = $balanceBefore['reset_date'];
            $startDate = $resetDate->copy()->addDays(5); // 5 days after reset date
            $endDate = $startDate->copy()->addDays(4); // 5 days of leave
            $leaveDays = 5;

            Log::info('Creating test annual leave request', [
                'karyawan_id' => $karyawan->id,
                'jenis_cuti_id' => $jenisCuti->id,
                'tanggal_mulai' => $startDate->format('Y-m-d'),
                'tanggal_selesai' => $endDate->format('Y-m-d'),
                'lama_hari' => $leaveDays,
            ]);

            $cuti = new Cuti([
                'karyawan_id' => $karyawan->id,
                'jenis_cuti_id' => $jenisCuti->id,
                'tanggal_mulai' => $startDate->format('Y-m-d'),
                'tanggal_selesai' => $endDate->format('Y-m-d'),
                'lama_hari' => $leaveDays,
                'alasan' => 'Test annual leave',
                'status_cuti' => 'disetujui',
            ]);

            $cuti->save();

            Log::info('Created test annual leave request', [
                'cuti_id' => $cuti->id,
                'tanggal_mulai' => $cuti->tanggal_mulai,
                'tanggal_selesai' => $cuti->tanggal_selesai,
                'lama_hari' => $cuti->lama_hari,
            ]);

            $this->info("\nCreated test annual leave request:");
            $this->line("- ID: {$cuti->id}");
            $this->line("- Start date: {$cuti->tanggal_mulai}");
            $this->line("- End date: {$cuti->tanggal_selesai}");
            $this->line("- Days: {$cuti->lama_hari}");

            // Calculate the leave balance after adding test data
            Log::info('Calculating leave balance after adding test data', [
                'karyawan_id' => $karyawan->id,
                'jenis_cuti_id' => $jenisCuti->id,
                'reset_date' => $balanceBefore['reset_date'] ? $balanceBefore['reset_date']->format('Y-m-d') : null,
                'next_reset_date' => $balanceBefore['next_reset_date'] ? $balanceBefore['next_reset_date']->format('Y-m-d') : null,
            ]);

            $balanceAfter = $annualLeaveCalculator->calculateAnnualLeaveBalance($karyawan, $jenisCuti->id);

            Log::info('Leave balance after adding test data', [
                'jatah_hari' => $balanceAfter['jatah_hari'],
                'digunakan' => $balanceAfter['digunakan'],
                'sisa' => $balanceAfter['sisa'],
                'reset_date' => $balanceAfter['reset_date'] ? $balanceAfter['reset_date']->format('Y-m-d') : null,
                'next_reset_date' => $balanceAfter['next_reset_date'] ? $balanceAfter['next_reset_date']->format('Y-m-d') : null,
            ]);

            // Display the results
            $this->info("\nAnnual leave balance AFTER adding test data:");
            $this->line("- Jatah: {$balanceAfter['jatah_hari']} days");
            $this->line("- Digunakan: {$balanceAfter['digunakan']} days");
            $this->line("- Sisa: {$balanceAfter['sisa']} days");
            $this->line("- Reset date: " . ($balanceAfter['reset_date'] ? $balanceAfter['reset_date']->format('Y-m-d') : 'N/A'));
            $this->line("- Next reset date: " . ($balanceAfter['next_reset_date'] ? $balanceAfter['next_reset_date']->format('Y-m-d') : 'N/A'));

            // Check if the balance has been updated correctly
            if ($balanceAfter['digunakan'] == $balanceBefore['digunakan'] + $leaveDays) {
                $this->info("\nTest PASSED: Annual leave balance has been updated correctly.");
            } else {
                $this->error("\nTest FAILED: Annual leave balance has not been updated correctly.");
                $this->line("Expected: " . ($balanceBefore['digunakan'] + $leaveDays) . " days used");
                $this->line("Actual: " . $balanceAfter['digunakan'] . " days used");
            }

            // Clean up test data
            $cuti->delete();
            $this->info("\nTest data has been cleaned up.");
        } catch (\Exception $e) {
            // Try to clean up if the cuti was created
            if (isset($cuti) && $cuti->id) {
                $cuti->delete();
            }
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        $this->info("\nTest completed");
        return 0;
    }
}
