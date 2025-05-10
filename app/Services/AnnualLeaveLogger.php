<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AnnualLeaveLogger
{
    /**
     * Log channel name for annual leave operations
     * 
     * @var string
     */
    protected $channel = 'annual_leave';

    /**
     * Log the start of annual leave calculation
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @return void
     */
    public function logCalculationStart(int $karyawanId, string $karyawanName)
    {
        Log::channel($this->channel)->info('ANNUAL LEAVE CALCULATION STARTED', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'operation' => 'calculation_start',
        ]);
    }

    /**
     * Log employee eligibility check
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @param string $doh
     * @param bool $isEligible
     * @param int $yearsSinceDoh
     * @return void
     */
    public function logEligibilityCheck(int $karyawanId, string $karyawanName, string $doh, bool $isEligible, int $yearsSinceDoh)
    {
        Log::channel($this->channel)->info('ANNUAL LEAVE ELIGIBILITY CHECK', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'doh' => $doh,
            'years_since_doh' => $yearsSinceDoh,
            'is_eligible' => $isEligible,
            'message' => $isEligible ? 'Employee is eligible for annual leave' : 'Employee not eligible (requires minimum 1 year service)',
            'operation' => 'eligibility_check',
        ]);
    }

    /**
     * Log reset dates calculation
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @param string $doh
     * @param int $yearsSinceDoh
     * @param string $lastResetDate
     * @param string $nextResetDate
     * @return void
     */
    public function logResetDates(int $karyawanId, string $karyawanName, string $doh, int $yearsSinceDoh, string $lastResetDate, string $nextResetDate)
    {
        Log::channel($this->channel)->info('ANNUAL LEAVE RESET DATES CALCULATED', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'doh' => $doh,
            'years_since_doh' => $yearsSinceDoh,
            'last_reset_date' => $lastResetDate,
            'next_reset_date' => $nextResetDate,
            'operation' => 'reset_dates_calculation',
        ]);
    }

    /**
     * Log approved leave counting process
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @param int $jenisCutiId
     * @param string $startDate
     * @param string $endDate
     * @param array $approvedLeaves
     * @return void
     */
    public function logApprovedLeaveCounting(int $karyawanId, string $karyawanName, int $jenisCutiId, string $startDate, string $endDate, array $approvedLeaves)
    {
        Log::channel($this->channel)->info('ANNUAL LEAVE APPROVED LEAVES COUNTING', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'jenis_cuti_id' => $jenisCutiId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'count' => count($approvedLeaves),
            'leaves' => $approvedLeaves,
            'operation' => 'approved_leave_counting',
        ]);
    }

    /**
     * Log pending leave counting process
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @param int $jenisCutiId
     * @param string $startDate
     * @param string $endDate
     * @param array $pendingLeaves
     * @return void
     */
    public function logPendingLeaveCounting(int $karyawanId, string $karyawanName, int $jenisCutiId, string $startDate, string $endDate, array $pendingLeaves)
    {
        Log::channel($this->channel)->info('ANNUAL LEAVE PENDING LEAVES COUNTING', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'jenis_cuti_id' => $jenisCutiId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'count' => count($pendingLeaves),
            'leaves' => $pendingLeaves,
            'operation' => 'pending_leave_counting',
        ]);
    }

    /**
     * Log leave days calculation for a specific leave
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @param int $cutiId
     * @param string $leaveStart
     * @param string $leaveEnd
     * @param string $effectiveStart
     * @param string $effectiveEnd
     * @param int $totalLeaveDays
     * @param int $effectiveDays
     * @param float $proportion
     * @param bool $hasDetails
     * @param int $calculatedDays
     * @return void
     */
    public function logLeaveDaysCalculation(int $karyawanId, string $karyawanName, int $cutiId, string $leaveStart, string $leaveEnd, string $effectiveStart, string $effectiveEnd, int $totalLeaveDays, int $effectiveDays, float $proportion, bool $hasDetails, int $calculatedDays)
    {
        Log::channel($this->channel)->info('ANNUAL LEAVE DAYS CALCULATION', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'cuti_id' => $cutiId,
            'leave_start' => $leaveStart,
            'leave_end' => $leaveEnd,
            'effective_start' => $effectiveStart,
            'effective_end' => $effectiveEnd,
            'total_leave_days' => $totalLeaveDays,
            'effective_days' => $effectiveDays,
            'proportion' => $proportion,
            'has_details' => $hasDetails,
            'calculated_days' => $calculatedDays,
            'operation' => 'leave_days_calculation',
        ]);
    }

    /**
     * Log the final calculation result
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @param int $jenisCutiId
     * @param int $jatahHari
     * @param int $digunakan
     * @param int $sisa
     * @param string $lastResetDate
     * @param string $nextResetDate
     * @return void
     */
    public function logCalculationResult(int $karyawanId, string $karyawanName, int $jenisCutiId, int $jatahHari, int $digunakan, int $sisa, string $lastResetDate, string $nextResetDate)
    {
        Log::channel($this->channel)->info('ANNUAL LEAVE CALCULATION RESULT', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'jenis_cuti_id' => $jenisCutiId,
            'jatah_hari' => $jatahHari,
            'digunakan' => $digunakan,
            'sisa' => $sisa,
            'last_reset_date' => $lastResetDate,
            'next_reset_date' => $nextResetDate,
            'operation' => 'calculation_result',
        ]);
    }

    /**
     * Log event when employee has no DOH date
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @return void
     */
    public function logNoDohDate(int $karyawanId, string $karyawanName)
    {
        Log::channel($this->channel)->warning('ANNUAL LEAVE CALCULATION ERROR: NO DOH DATE', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'message' => 'Employee has no DOH (Date of Hire) recorded',
            'operation' => 'error_no_doh',
        ]);
    }

    /**
     * Log SQL query for debugging
     * 
     * @param int $karyawanId
     * @param string $karyawanName
     * @param string $sql
     * @param array $bindings
     * @param string $queryType
     * @return void
     */
    public function logSqlQuery(int $karyawanId, string $karyawanName, string $sql, array $bindings, string $queryType)
    {
        Log::channel($this->channel)->debug('ANNUAL LEAVE SQL QUERY', [
            'karyawan_id' => $karyawanId,
            'karyawan_name' => $karyawanName,
            'sql' => $sql,
            'bindings' => $bindings,
            'query_type' => $queryType,
            'operation' => 'sql_query',
        ]);
    }
}