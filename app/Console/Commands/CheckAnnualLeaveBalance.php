<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Cuti;
use App\Services\AnnualLeaveCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAnnualLeaveBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:annual-leave-balance {karyawan_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check annual leave balance for a specific employee';

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
        
        $this->info("Checking annual leave balance for karyawan: {$karyawan->nama} (ID: {$karyawan->id})");
        $this->info("Annual leave type: {$jenisCuti->nama_jenis} (ID: {$jenisCuti->id})");
        
        // Get all leave requests for this employee
        $allLeaves = Cuti::where('karyawan_id', $karyawan->id)->get();
        $this->info("\nAll leave requests for this employee: " . $allLeaves->count());
        
        foreach ($allLeaves as $cuti) {
            $this->line("- ID: {$cuti->id}, Type: {$cuti->jenis_cuti_id}, Status: {$cuti->status_cuti}, Start: {$cuti->tanggal_mulai}, End: {$cuti->tanggal_selesai}, Days: {$cuti->lama_hari}");
        }
        
        // Get annual leave requests for this employee
        $annualLeaves = Cuti::where('karyawan_id', $karyawan->id)
            ->where('jenis_cuti_id', $jenisCuti->id)
            ->get();
        $this->info("\nAnnual leave requests for this employee: " . $annualLeaves->count());
        
        foreach ($annualLeaves as $cuti) {
            $this->line("- ID: {$cuti->id}, Status: {$cuti->status_cuti}, Start: {$cuti->tanggal_mulai}, End: {$cuti->tanggal_selesai}, Days: {$cuti->lama_hari}");
        }
        
        // Calculate annual leave balance
        $annualLeaveCalculator = new AnnualLeaveCalculator();
        $balance = $annualLeaveCalculator->calculateAnnualLeaveBalance($karyawan, $jenisCuti->id);
        
        $this->info("\nAnnual leave balance:");
        $this->line("- Jatah: {$balance['jatah_hari']} days");
        $this->line("- Digunakan: {$balance['digunakan']} days");
        $this->line("- Sisa: {$balance['sisa']} days");
        $this->line("- Reset date: " . ($balance['reset_date'] ? $balance['reset_date']->format('Y-m-d') : 'N/A'));
        $this->line("- Next reset date: " . ($balance['next_reset_date'] ? $balance['next_reset_date']->format('Y-m-d') : 'N/A'));
        
        // Get the calculation period
        $resetDate = $balance['reset_date'];
        $nextResetDate = $balance['next_reset_date'];
        
        if ($resetDate && $nextResetDate) {
            $this->info("\nCalculation period:");
            $this->line("- Start date: {$resetDate->format('Y-m-d')}");
            $this->line("- End date: " . Carbon::now()->format('Y-m-d'));
            
            // Get approved leaves within the calculation period
            $approvedLeaves = Cuti::where('karyawan_id', $karyawan->id)
                ->where('jenis_cuti_id', $jenisCuti->id)
                ->where('status_cuti', 'disetujui')
                ->where(function($query) use ($resetDate) {
                    $query->where('tanggal_mulai', '>=', $resetDate)
                        ->orWhere('tanggal_selesai', '>=', $resetDate);
                })
                ->get();
            
            $this->info("\nApproved annual leave requests within the calculation period: " . $approvedLeaves->count());
            
            foreach ($approvedLeaves as $cuti) {
                $this->line("- ID: {$cuti->id}, Start: {$cuti->tanggal_mulai}, End: {$cuti->tanggal_selesai}, Days: {$cuti->lama_hari}");
            }
            
            // Get pending leaves within the calculation period
            $pendingLeaves = Cuti::where('karyawan_id', $karyawan->id)
                ->where('jenis_cuti_id', $jenisCuti->id)
                ->where('status_cuti', 'pending')
                ->where(function($query) use ($resetDate) {
                    $query->where('tanggal_mulai', '>=', $resetDate)
                        ->orWhere('tanggal_selesai', '>=', $resetDate);
                })
                ->get();
            
            $this->info("\nPending annual leave requests within the calculation period: " . $pendingLeaves->count());
            
            foreach ($pendingLeaves as $cuti) {
                $this->line("- ID: {$cuti->id}, Start: {$cuti->tanggal_mulai}, End: {$cuti->tanggal_selesai}, Days: {$cuti->lama_hari}");
            }
        }
        
        return 0;
    }
}
