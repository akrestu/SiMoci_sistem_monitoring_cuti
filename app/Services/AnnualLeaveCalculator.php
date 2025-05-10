<?php

namespace App\Services;

use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AnnualLeaveCalculator
{
    /**
     * The logger instance
     * 
     * @var AnnualLeaveLogger
     */
    protected $logger;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = new AnnualLeaveLogger();
    }

    /**
     * Calculate annual leave balance for a specific employee
     *
     * @param Karyawan $karyawan
     * @param int|null $jenisCutiId
     * @param Carbon|null $tanggal
     * @return array
     */
    public function calculateAnnualLeaveBalance(Karyawan $karyawan, $jenisCutiId = null, $tanggal = null)
    {
        // Log the start of the calculation process
        $this->logger->logCalculationStart($karyawan->id, $karyawan->nama);

        // If jenisCutiId is not provided, find the annual leave type
        if (!$jenisCutiId) {
            $jenisCuti = JenisCuti::where('nama_jenis', 'like', '%tahunan%')->first();
            if (!$jenisCuti) {
                return [
                    'jatah_hari' => 0,
                    'digunakan' => 0,
                    'sisa' => 0,
                    'eligible' => false,
                    'reset_date' => null,
                    'next_reset_date' => null,
                    'message' => 'Jenis cuti tahunan tidak ditemukan'
                ];
            }
            $jenisCutiId = $jenisCuti->id;
        }

        // If tanggal is not provided, use current date
        if (!$tanggal) {
            $tanggal = Carbon::now();
        } elseif (!($tanggal instanceof Carbon)) {
            $tanggal = Carbon::parse($tanggal);
        }

        // Get DOH date
        $dohDate = $karyawan->doh ? Carbon::parse($karyawan->doh) : null;
        if (!$dohDate) {
            // Log the missing DOH date
            $this->logger->logNoDohDate($karyawan->id, $karyawan->nama);
            
            return [
                'jatah_hari' => 0,
                'digunakan' => 0,
                'sisa' => 0,
                'eligible' => false,
                'reset_date' => null,
                'next_reset_date' => null,
                'message' => 'Tanggal DOH tidak ditemukan'
            ];
        }

        // Check if employee has worked for at least 1 year
        $yearsSinceDoh = $dohDate->diffInYears($tanggal);
        $isEligible = $yearsSinceDoh >= 1;
        
        // Log the eligibility check
        $this->logger->logEligibilityCheck(
            $karyawan->id, 
            $karyawan->nama, 
            $dohDate->format('Y-m-d'), 
            $isEligible, 
            $yearsSinceDoh
        );

        if (!$isEligible) {
            // Not yet eligible for annual leave
            return [
                'jatah_hari' => 0,
                'digunakan' => 0,
                'sisa' => 0,
                'eligible' => false,
                'reset_date' => null,
                'next_reset_date' => $dohDate->copy()->addYear(),
                'message' => 'Belum memenuhi syarat satu tahun kerja'
            ];
        }

        // Calculate annual leave period
        $yearsSinceDoh = $dohDate->diffInYears($tanggal);

        // Calculate last and next annual reset dates
        // Last reset date is the anniversary of DOH in the current period
        $lastResetDate = $dohDate->copy()->addYears($yearsSinceDoh);

        // If the last reset date is in the future (which shouldn't happen normally),
        // adjust it back one year
        if ($lastResetDate->isAfter($tanggal)) {
            $yearsSinceDoh--;
            $lastResetDate = $dohDate->copy()->addYears($yearsSinceDoh);
        }

        // Next reset date is exactly one year after the last reset date
        $nextResetDate = $lastResetDate->copy()->addYear();

        // Log reset dates calculation
        $this->logger->logResetDates(
            $karyawan->id,
            $karyawan->nama,
            $dohDate->format('Y-m-d'),
            $yearsSinceDoh,
            $lastResetDate->format('Y-m-d'),
            $nextResetDate->format('Y-m-d')
        );

        // Set annual leave quota to 14 days as per business rule
        $jatahHari = 14; // Fixed at 14 days as per requirement

        // Calculate used days since last annual reset
        // For annual leave, we need to count all approved leave requests within the current period,
        // including those with future dates (up to the next reset date)
        $digunakan = $this->getUsedLeaveDayForAnnualLeave($karyawan->id, $jenisCutiId, $lastResetDate, $nextResetDate);
        $sisa = $jatahHari - $digunakan;

        // Get the latest leave request for this type
        $latestCuti = $this->getLatestLeave($karyawan->id, $jenisCutiId);

        // Calculate difference between ideal schedule and actual leave dates
        $selisihInfo = $this->calculateSelisihCuti($latestCuti, $lastResetDate, $nextResetDate);

        // Log the final calculation result
        $this->logger->logCalculationResult(
            $karyawan->id,
            $karyawan->nama,
            $jenisCutiId,
            $jatahHari,
            $digunakan,
            $sisa,
            $lastResetDate->format('Y-m-d'),
            $nextResetDate->format('Y-m-d')
        );

        return [
            'jatah_hari' => $jatahHari,
            'digunakan' => $digunakan,
            'sisa' => $sisa,
            'eligible' => true,
            'reset_date' => $lastResetDate,
            'next_reset_date' => $nextResetDate,
            'selisih_hari' => $selisihInfo['selisih_hari'] ?? null,
            'status_selisih' => $selisihInfo['status_selisih'] ?? null,
            'latest_cuti_date' => $selisihInfo['latest_cuti_date'] ?? null
        ];
    }

    /**
     * Get the latest leave for a specific employee and leave type
     *
     * @param int $karyawanId
     * @param int $jenisCutiId
     * @return Cuti|null
     */
    public function getLatestLeave(int $karyawanId, int $jenisCutiId)
    {
        // Try to find based on primary jenis_cuti_id
        $query1 = Cuti::where('karyawan_id', $karyawanId)
            ->where('jenis_cuti_id', $jenisCutiId)
            ->where('status_cuti', 'disetujui')
            ->latest('tanggal_mulai');

        // Execute the query
        $latestCuti = $query1->first();

        // If not found, try to find in cuti_details
        if (!$latestCuti) {
            $query2 = Cuti::where('karyawan_id', $karyawanId)
                ->where('status_cuti', 'disetujui')
                ->whereHas('cutiDetails', function ($query) use ($jenisCutiId) {
                    $query->where('jenis_cuti_id', $jenisCutiId);
                })
                ->latest('tanggal_mulai');

            // Execute the query
            $latestCuti = $query2->first();
        }

        return $latestCuti;
    }

    /**
     * Calculate the difference between ideal leave schedule and actual leave date
     *
     * @param Cuti|null $latestCuti
     * @param Carbon|null $lastResetDate
     * @param Carbon|null $nextResetDate
     * @return array
     */
    public function calculateSelisihCuti($latestCuti, $lastResetDate, $nextResetDate)
    {
        if (!$latestCuti || !$lastResetDate) {
            return [
                'selisih_hari' => null,
                'status_selisih' => null,
                'latest_cuti_date' => null
            ];
        }

        $latestCutiDate = Carbon::parse($latestCuti->tanggal_mulai);

        // Compare against the last reset date (DOH anniversary)
        $expectedLeaveDate = $lastResetDate;

        // Calculate difference in days
        $selisihHari = null;
        $statusSelisih = null;

        // If the leave date is before the reset date, it's a "hutang" (debt)
        if ($latestCutiDate->lt($expectedLeaveDate)) {
            $selisihHari = $latestCutiDate->diffInDays($expectedLeaveDate);
            $statusSelisih = 'lebih_awal'; // Took leave earlier = hutang
        }
        // If the leave date is after the reset date, it's "good"
        else if ($latestCutiDate->gt($expectedLeaveDate)) {
            $selisihHari = $latestCutiDate->diffInDays($expectedLeaveDate);
            $statusSelisih = 'lebih_lambat'; // Took leave later = good
        }
        // If dates match exactly (took leave exactly on reset date)
        else {
            $selisihHari = 0;
            $statusSelisih = 'tepat_waktu'; // Took leave exactly on time
        }

        return [
            'selisih_hari' => $selisihHari,
            'status_selisih' => $statusSelisih,
            'latest_cuti_date' => $latestCutiDate
        ];
    }

    /**
     * Get used leave days for annual leave in a period
     * This method counts all approved leave requests within the current period,
     * including those with future dates (up to the next reset date)
     *
     * @param int $karyawanId
     * @param int $jenisCutiId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public function getUsedLeaveDayForAnnualLeave(int $karyawanId, int $jenisCutiId, Carbon $startDate, Carbon $endDate)
    {
        // Get employee name for logging
        $karyawan = Karyawan::find($karyawanId);
        $karyawanName = $karyawan ? $karyawan->nama : 'Unknown';

        // Get all approved leave requests within the current period
        $approvedLeaves = Cuti::where('karyawan_id', $karyawanId)
            ->where('status_cuti', 'disetujui')
            ->where(function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    // Leave starts within the period
                    $q->where('tanggal_mulai', '>=', $startDate)
                      ->where('tanggal_mulai', '<', $endDate);
                })
                ->orWhere(function($q) use ($startDate, $endDate) {
                    // Leave ends within the period
                    $q->where('tanggal_selesai', '>=', $startDate)
                      ->where('tanggal_selesai', '<', $endDate);
                })
                ->orWhere(function($q) use ($startDate, $endDate) {
                    // Leave spans across the entire period
                    $q->where('tanggal_mulai', '<', $startDate)
                      ->where('tanggal_selesai', '>=', $endDate);
                });
            })
            ->where(function($query) use ($jenisCutiId) {
                $query->where('jenis_cuti_id', $jenisCutiId)
                    ->orWhereHas('cutiDetails', function($q) use ($jenisCutiId) {
                        $q->where('jenis_cuti_id', $jenisCutiId);
                    });
            })
            ->get();

        // Log the approved leaves counted
        $approvedLeavesData = $approvedLeaves->map(function($cuti) {
            return [
                'id' => $cuti->id,
                'jenis_cuti_id' => $cuti->jenis_cuti_id,
                'tanggal_mulai' => $cuti->tanggal_mulai,
                'tanggal_selesai' => $cuti->tanggal_selesai,
                'lama_hari' => $cuti->lama_hari,
                'status_cuti' => $cuti->status_cuti
            ];
        })->all();
        
        $this->logger->logApprovedLeaveCounting(
            $karyawanId,
            $karyawanName,
            $jenisCutiId,
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $approvedLeavesData
        );

        // Calculate total days used
        $totalDays = 0;
        foreach ($approvedLeaves as $cuti) {
            // For annual leave, we need to calculate effective days within period
            $leaveStartDate = Carbon::parse($cuti->tanggal_mulai);
            $leaveEndDate = Carbon::parse($cuti->tanggal_selesai);

            // Adjust start and end dates to be within the calculation period
            $effectiveStartDate = $leaveStartDate->lt($startDate) ? $startDate : $leaveStartDate;
            $effectiveEndDate = $leaveEndDate->gt($endDate) ? $endDate : $leaveEndDate;

            // Calculate the number of days that fall within the period
            $effectiveDays = $effectiveStartDate->diffInDays($effectiveEndDate) + 1;

            // Calculate the proportion of days that fall within the period
            $totalLeaveDays = $leaveStartDate->diffInDays($leaveEndDate) + 1;
            $proportion = $totalLeaveDays > 0 ? $effectiveDays / $totalLeaveDays : 0;
            
            // Calculate days for this specific leave
            $hasDetails = $cuti->cutiDetails()->exists();
            $calculatedDays = 0;
            
            if ($cuti->jenis_cuti_id == $jenisCutiId) {
                // If leave doesn't have details but matches the main jenis_cuti_id
                $calculatedDays = round($cuti->lama_hari * $proportion);
                $totalDays += $calculatedDays;
            } else if ($hasDetails) {
                // If the leave has details, get days specifically for this leave type from details
                $details = $cuti->cutiDetails()->where('jenis_cuti_id', $jenisCutiId)->get();
                foreach ($details as $detail) {
                    // Check if this is a periodic leave type in a combination request
                    $jenisCuti = JenisCuti::find($jenisCutiId);
                    $isPeriodic = $jenisCuti && stripos($jenisCuti->nama_jenis, 'periodik') !== false;
                    
                    if ($isPeriodic && $cuti->cutiDetails()->count() > 1) {
                        // For periodic leaves in combination requests, use the exact days from details
                        $detailDays = $detail->jumlah_hari;
                    } else {
                        // For other leave types, apply the proportion
                        $detailDays = round($detail->jumlah_hari * $proportion);
                    }
                    
                    $calculatedDays += $detailDays;
                }
                $totalDays += $calculatedDays;
            }
            
            // Log the calculation for this specific leave
            $this->logger->logLeaveDaysCalculation(
                $karyawanId,
                $karyawanName,
                $cuti->id,
                $leaveStartDate->format('Y-m-d'),
                $leaveEndDate->format('Y-m-d'),
                $effectiveStartDate->format('Y-m-d'),
                $effectiveEndDate->format('Y-m-d'),
                $totalLeaveDays,
                $effectiveDays,
                $proportion,
                $hasDetails,
                $calculatedDays
            );
        }

        // Also check for pending leaves
        $pendingLeaves = Cuti::where('karyawan_id', $karyawanId)
            ->where('status_cuti', 'pending')
            ->where(function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    // Leave starts within the period
                    $q->where('tanggal_mulai', '>=', $startDate)
                      ->where('tanggal_mulai', '<', $endDate);
                })
                ->orWhere(function($q) use ($startDate, $endDate) {
                    // Leave ends within the period
                    $q->where('tanggal_selesai', '>=', $startDate)
                      ->where('tanggal_selesai', '<', $endDate);
                })
                ->orWhere(function($q) use ($startDate, $endDate) {
                    // Leave spans across the entire period
                    $q->where('tanggal_mulai', '<', $startDate)
                      ->where('tanggal_selesai', '>=', $endDate);
                });
            })
            ->where(function($query) use ($jenisCutiId) {
                $query->where('jenis_cuti_id', $jenisCutiId)
                    ->orWhereHas('cutiDetails', function($q) use ($jenisCutiId) {
                        $q->where('jenis_cuti_id', $jenisCutiId);
                    });
            })
            ->get();

        // Log the pending leaves counted
        $pendingLeavesData = $pendingLeaves->map(function($cuti) {
            return [
                'id' => $cuti->id,
                'jenis_cuti_id' => $cuti->jenis_cuti_id,
                'tanggal_mulai' => $cuti->tanggal_mulai,
                'tanggal_selesai' => $cuti->tanggal_selesai,
                'lama_hari' => $cuti->lama_hari,
                'status_cuti' => $cuti->status_cuti
            ];
        })->all();
        
        $this->logger->logPendingLeaveCounting(
            $karyawanId,
            $karyawanName,
            $jenisCutiId,
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $pendingLeavesData
        );

        // Calculate total days used for pending leaves
        foreach ($pendingLeaves as $cuti) {
            // For annual leave, we need to calculate effective days within period
            $leaveStartDate = Carbon::parse($cuti->tanggal_mulai);
            $leaveEndDate = Carbon::parse($cuti->tanggal_selesai);

            // Adjust start and end dates to be within the calculation period
            $effectiveStartDate = $leaveStartDate->lt($startDate) ? $startDate : $leaveStartDate;
            $effectiveEndDate = $leaveEndDate->gt($endDate) ? $endDate : $leaveEndDate;

            // Calculate the number of days that fall within the period
            $effectiveDays = $effectiveStartDate->diffInDays($effectiveEndDate) + 1;

            // Calculate the proportion of days that fall within the period
            $totalLeaveDays = $leaveStartDate->diffInDays($leaveEndDate) + 1;
            $proportion = $totalLeaveDays > 0 ? $effectiveDays / $totalLeaveDays : 0;
            
            // Calculate days for this specific leave
            $hasDetails = $cuti->cutiDetails()->exists();
            $calculatedDays = 0;
            
            if ($cuti->jenis_cuti_id == $jenisCutiId) {
                // If leave doesn't have details but matches the main jenis_cuti_id
                $calculatedDays = round($cuti->lama_hari * $proportion);
                $totalDays += $calculatedDays;
            } else if ($hasDetails) {
                // If the leave has details, get days specifically for this leave type from details
                $details = $cuti->cutiDetails()->where('jenis_cuti_id', $jenisCutiId)->get();
                foreach ($details as $detail) {
                    // Check if this is a periodic leave type in a combination request
                    $jenisCuti = JenisCuti::find($jenisCutiId);
                    $isPeriodic = $jenisCuti && stripos($jenisCuti->nama_jenis, 'periodik') !== false;
                    
                    if ($isPeriodic && $cuti->cutiDetails()->count() > 1) {
                        // For periodic leaves in combination requests, use the exact days from details
                        $detailDays = $detail->jumlah_hari;
                    } else {
                        // For other leave types, apply the proportion
                        $detailDays = round($detail->jumlah_hari * $proportion);
                    }
                    
                    $calculatedDays += $detailDays;
                }
                $totalDays += $calculatedDays;
            }
            
            // Log the calculation for this specific pending leave
            $this->logger->logLeaveDaysCalculation(
                $karyawanId,
                $karyawanName,
                $cuti->id,
                $leaveStartDate->format('Y-m-d'),
                $leaveEndDate->format('Y-m-d'),
                $effectiveStartDate->format('Y-m-d'),
                $effectiveEndDate->format('Y-m-d'),
                $totalLeaveDays,
                $effectiveDays,
                $proportion,
                $hasDetails,
                $calculatedDays
            );
        }

        return $totalDays;
    }

    /**
     * Get used leave days in a period
     *
     * @param int $karyawanId
     * @param int $jenisCutiId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public function getUsedLeaveDay(int $karyawanId, int $jenisCutiId, Carbon $startDate, Carbon $endDate)
    {
        // Forward to the more specific implementation for annual leave
        return $this->getUsedLeaveDayForAnnualLeave($karyawanId, $jenisCutiId, $startDate, $endDate);
    }
}
