<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CreateTestAnnualLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-annual-leave {karyawan_id} {days=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test annual leave request for a specific employee';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $karyawanId = $this->argument('karyawan_id');
        $days = (int)$this->argument('days');

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

        $this->info("Creating annual leave request for {$karyawan->nama} ({$karyawan->id})");
        $this->info("Leave type: {$jenisCuti->nama_jenis} ({$jenisCuti->id})");

        try {
            // Create a test annual leave request
            $startDate = Carbon::now()->addDays(1);
            $endDate = Carbon::now()->addDays($days);

            $cuti = new Cuti([
                'karyawan_id' => $karyawan->id,
                'jenis_cuti_id' => $jenisCuti->id,
                'tanggal_mulai' => $startDate->format('Y-m-d'),
                'tanggal_selesai' => $endDate->format('Y-m-d'),
                'lama_hari' => $days,
                'alasan' => 'Test annual leave',
                'status_cuti' => 'disetujui',
            ]);

            $cuti->save();

            $this->info("Created annual leave request:");
            $this->line("- ID: {$cuti->id}");
            $this->line("- Start date: {$cuti->tanggal_mulai}");
            $this->line("- End date: {$cuti->tanggal_selesai}");
            $this->line("- Days: {$cuti->lama_hari}");
            $this->line("- Status: {$cuti->status_cuti}");

            // Calculate leave balance
            $cutiService = new \App\Services\CutiService();
            $balance = $cutiService->calculateLeaveBalance($karyawan, $jenisCuti->id);

            $this->info("\nAnnual leave balance:");
            $this->line("- Jatah: {$balance['jatah_hari']} days");
            $this->line("- Digunakan: {$balance['digunakan']} days");
            $this->line("- Sisa: {$balance['sisa']} days");
            $this->line("- Reset date: " . ($balance['reset_date'] ? $balance['reset_date']->format('Y-m-d') : 'N/A'));
            $this->line("- Next reset date: " . ($balance['next_reset_date'] ? $balance['next_reset_date']->format('Y-m-d') : 'N/A'));

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
