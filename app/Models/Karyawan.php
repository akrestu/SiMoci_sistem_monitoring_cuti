<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\CutiService;
use Carbon\Carbon;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'nik', 'departemen', 'jabatan', 'email', 'poh', 'doh', 'status'];

    public function cutis()
    {
        return $this->hasMany(Cuti::class);
    }

    /**
     * Get leave balance for "Cuti Tahunan"
     *
     * @return int
     */
    public function getSisaCutiAttribute()
    {
        $cutiTahunanId = JenisCuti::where('nama_jenis', 'like', '%Tahunan%')->first()?->id;

        if (!$cutiTahunanId) {
            return 0;
        }

        // Use the new AnnualLeaveCalculator for annual leave calculations
        $annualLeaveCalculator = new \App\Services\AnnualLeaveCalculator();
        $balance = $annualLeaveCalculator->calculateAnnualLeaveBalance($this, $cutiTahunanId);

        return $balance['sisa'];
    }

    /**
     * Get all leave balances for this employee
     *
     * @return array
     */
    public function getLeaveBalances()
    {
        $cutiService = new CutiService();
        $balances = $cutiService->calculateAllLeaveBalances($this);

        // Use the AnnualLeaveCalculator for annual leave calculations
        $annualLeaveCalculator = new \App\Services\AnnualLeaveCalculator();

        // Special handling for different leave types
        foreach ($balances as $jenisCutiId => &$balance) {
            $jenisCutiName = $balance['nama_jenis'] ?? '';
            
            if (stripos($jenisCutiName, 'tahunan') !== false) {
                // Replace the annual leave balance with the one calculated by AnnualLeaveCalculator
                $annualLeaveBalance = $annualLeaveCalculator->calculateAnnualLeaveBalance($this, $jenisCutiId);

                // Preserve the nama_jenis field
                $annualLeaveBalance['nama_jenis'] = $jenisCutiName;

                // Update the balance
                $balance = $annualLeaveBalance;
            } 
            // Specifically recalculate for periodic leave types to ensure proper combination calculation
            elseif (stripos($jenisCutiName, 'periodik') !== false) {
                $recalculatedBalance = $cutiService->calculateLeaveBalance($this, $jenisCutiId);
                
                // Preserve the nama_jenis field
                $recalculatedBalance['nama_jenis'] = $jenisCutiName;
                
                // Update the balance
                $balance = $recalculatedBalance;
            }
        }

        return $balances;
    }

    /**
     * Check if employee is eligible for a specific leave type
     *
     * @param int $jenisCutiId
     * @return bool
     */
    public function isEligibleForLeave($jenisCutiId)
    {
        // Check if this is an annual leave type
        $jenisCuti = JenisCuti::find($jenisCutiId);
        $isAnnualLeave = $jenisCuti && stripos($jenisCuti->nama_jenis, 'tahunan') !== false;

        if ($isAnnualLeave) {
            // Use the AnnualLeaveCalculator for annual leave eligibility checks
            $annualLeaveCalculator = new \App\Services\AnnualLeaveCalculator();
            $balance = $annualLeaveCalculator->calculateAnnualLeaveBalance($this, $jenisCutiId);
        } else {
            // Use the regular CutiService for other leave types
            $cutiService = new CutiService();
            $balance = $cutiService->calculateLeaveBalance($this, $jenisCutiId);
        }

        return $balance['eligible'] ?? false;
    }
}