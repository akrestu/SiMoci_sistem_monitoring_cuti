<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Services\AnnualLeaveCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TestAnnualLeaveCalculator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:annual-leave-calculator {karyawan_id=353}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the new AnnualLeaveCalculator';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $karyawanId = $this->argument('karyawan_id');
        
        // Find the karyawan
        $karyawan = Karyawan::find($karyawanId);
        if (!$karyawan) {
            $this->error("Karyawan with ID {$karyawanId} not found");
            return 1;
        }
        
        // Find the annual leave type
        $jenisCuti = JenisCuti::where('nama_jenis', 'like', '%tahunan%')->first();
        if (!$jenisCuti) {
            $this->error("Annual leave type not found");
            return 1;
        }
        
        $this->info("Testing AnnualLeaveCalculator for karyawan: {$karyawan->nama} (ID: {$karyawan->id})");
        $this->info("Annual leave type: {$jenisCuti->nama_jenis} (ID: {$jenisCuti->id})");
        
        try {
            // Calculate leave balance using the old method
            $cutiService = new \App\Services\CutiService();
            $oldBalance = $cutiService->calculateLeaveBalance($karyawan, $jenisCuti->id);
            
            $this->info("\nAnnual leave balance using old method (CutiService):");
            $this->line("- Jatah: {$oldBalance['jatah_hari']} days");
            $this->line("- Digunakan: {$oldBalance['digunakan']} days");
            $this->line("- Sisa: {$oldBalance['sisa']} days");
            $this->line("- Reset date: " . ($oldBalance['reset_date'] ? $oldBalance['reset_date']->format('Y-m-d') : 'N/A'));
            $this->line("- Next reset date: " . ($oldBalance['next_reset_date'] ? $oldBalance['next_reset_date']->format('Y-m-d') : 'N/A'));
            
            // Calculate leave balance using the new method
            $annualLeaveCalculator = new AnnualLeaveCalculator();
            $newBalance = $annualLeaveCalculator->calculateAnnualLeaveBalance($karyawan, $jenisCuti->id);
            
            $this->info("\nAnnual leave balance using new method (AnnualLeaveCalculator):");
            $this->line("- Jatah: {$newBalance['jatah_hari']} days");
            $this->line("- Digunakan: {$newBalance['digunakan']} days");
            $this->line("- Sisa: {$newBalance['sisa']} days");
            $this->line("- Reset date: " . ($newBalance['reset_date'] ? $newBalance['reset_date']->format('Y-m-d') : 'N/A'));
            $this->line("- Next reset date: " . ($newBalance['next_reset_date'] ? $newBalance['next_reset_date']->format('Y-m-d') : 'N/A'));
            
            // Compare the results
            $this->info("\nComparison:");
            $this->line("- Jatah: " . ($oldBalance['jatah_hari'] == $newBalance['jatah_hari'] ? 'MATCH' : 'MISMATCH'));
            $this->line("- Digunakan: " . ($oldBalance['digunakan'] == $newBalance['digunakan'] ? 'MATCH' : 'MISMATCH'));
            $this->line("- Sisa: " . ($oldBalance['sisa'] == $newBalance['sisa'] ? 'MATCH' : 'MISMATCH'));
            
            // Get all leave balances using the Karyawan model's method
            $allBalances = $karyawan->getLeaveBalances();
            $modelBalance = $allBalances[$jenisCuti->id] ?? null;
            
            if ($modelBalance) {
                $this->info("\nAnnual leave balance using Karyawan model's getLeaveBalances method:");
                $this->line("- Jatah: {$modelBalance['jatah_hari']} days");
                $this->line("- Digunakan: {$modelBalance['digunakan']} days");
                $this->line("- Sisa: {$modelBalance['sisa']} days");
                $this->line("- Reset date: " . ($modelBalance['reset_date'] ? $modelBalance['reset_date']->format('Y-m-d') : 'N/A'));
                $this->line("- Next reset date: " . ($modelBalance['next_reset_date'] ? $modelBalance['next_reset_date']->format('Y-m-d') : 'N/A'));
                
                // Compare with the new method
                $this->info("\nComparison with Karyawan model's method:");
                $this->line("- Jatah: " . ($newBalance['jatah_hari'] == $modelBalance['jatah_hari'] ? 'MATCH' : 'MISMATCH'));
                $this->line("- Digunakan: " . ($newBalance['digunakan'] == $modelBalance['digunakan'] ? 'MATCH' : 'MISMATCH'));
                $this->line("- Sisa: " . ($newBalance['sisa'] == $modelBalance['sisa'] ? 'MATCH' : 'MISMATCH'));
            }
            
            // Get the sisa cuti attribute
            $sisaCuti = $karyawan->sisa_cuti;
            
            $this->info("\nAnnual leave balance using Karyawan model's getSisaCutiAttribute method:");
            $this->line("- Sisa: {$sisaCuti} days");
            
            // Compare with the new method
            $this->info("\nComparison with Karyawan model's getSisaCutiAttribute method:");
            $this->line("- Sisa: " . ($newBalance['sisa'] == $sisaCuti ? 'MATCH' : 'MISMATCH'));
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
