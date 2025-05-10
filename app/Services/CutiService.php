<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Cuti;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CutiService
{
    /**
     * Calculate leave balance for a given employee
     *
     * @param Karyawan $karyawan
     * @param int $jenisCutiId
     * @param string|null $tanggal Reference date for calculation (default: now)
     * @return array
     */
    public function calculateLeaveBalance(Karyawan $karyawan, int $jenisCutiId, $tanggal = null)
    {
        $jenisCuti = JenisCuti::findOrFail($jenisCutiId);
        $tanggal = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $dohDate = Carbon::parse($karyawan->doh);

        // Default values
        $jatahHari = 0;
        $digunakan = 0;
        $sisa = 0;
        $resetDate = null;
        $nextResetDate = null;
        $selisihHari = null;
        $statusSelisih = null;

        // Get jenis_cuti type
        $jenisCutiName = strtolower($jenisCuti->nama_jenis);

        // 1. For periodic leave: "Cuti Periodik (Lokal)" and "Cuti Periodik (Luar)"
        if (stripos($jenisCutiName, 'periodik') !== false) {
            // Calculate reset period based on employee status
            $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;
            $resetDays = $resetWeeks * 7; // Convert weeks to days for precise calculation

            if (!$dohDate->isValid() || $dohDate->isFuture()) {
                return [
                    'jatah_hari' => 0,
                    'digunakan' => 0,
                    'sisa' => 0,
                    'eligible' => false,
                    'reset_date' => null,
                    'next_reset_date' => null,
                    'message' => 'Tanggal DOH tidak valid atau belum tiba'
                ];
            }

            // Calculate exact periods since DOH (using days for more precision)
            $daysSinceDoh = $dohDate->diffInDays($tanggal);
            $periodsSinceDoh = floor($daysSinceDoh / $resetDays);

            // Calculate last and next reset dates with day precision
            // This ensures we're always calculating from DOH exactly
            $lastResetDate = $dohDate->copy()->addDays($periodsSinceDoh * $resetDays);

            // If the last reset date is in the future (which shouldn't happen normally),
            // adjust it back one period
            if ($lastResetDate->isAfter($tanggal)) {
                $periodsSinceDoh--;
                $lastResetDate = $dohDate->copy()->addDays($periodsSinceDoh * $resetDays);
            }

            // Next reset date is exactly one period after the last reset date
            $nextResetDate = $dohDate->copy()->addDays(($periodsSinceDoh + 1) * $resetDays);

            // Default jatah for periodic leave
            $jatahHari = $jenisCuti->jatah_hari ?? 1;

            // Calculate used days since last reset, including combination leaves
            $digunakan = $this->getUsedLeaveDay($karyawan->id, $jenisCutiId, $lastResetDate, $tanggal);
            $sisa = $jatahHari - $digunakan;

            // Get the latest leave request for this type to calculate difference
            $latestCuti = $this->getLatestLeave($karyawan->id, $jenisCutiId);

            // Calculate both types of recommendation dates
            $recommendationDates = $this->calculateRecommendationDates(
                $karyawan->id,
                $jenisCutiId,
                $dohDate,
                $latestCuti,
                $resetWeeks,
                $tanggal
            );

            // Store status information
            $statusInfo = [
                'periode_jenis' => $karyawan->status === 'Staff' ? 'Staff (7 minggu)' : 'Non Staff (12 minggu)',
                'periode_hari' => $resetDays,
                'periode_minggu' => $resetWeeks,
                'periode_ke' => $periodsSinceDoh + 1, // Current period number (1-based)
                'recommendation_based_on_doh' => $recommendationDates['based_on_doh'],
                'recommendation_based_on_previous' => $recommendationDates['based_on_previous'],
                'recommendation_source' => $recommendationDates['source'],
            ];

        // 2. For annual leave: "Cuti Tahunan"
        } elseif (stripos($jenisCutiName, 'tahunan') !== false) {
            // Log for debugging
            Log::info('Calculating annual leave balance', [
                'karyawan_id' => $karyawan->id,
                'karyawan_name' => $karyawan->nama,
                'doh' => $dohDate->format('Y-m-d'),
                'tanggal' => $tanggal->format('Y-m-d'),
                'years_since_doh' => $dohDate->diffInYears($tanggal)
            ]);

            // Check if employee has worked for at least 1 year
            if ($dohDate->diffInYears($tanggal) < 1) {
                // Not yet eligible for annual leave
                Log::info('Employee not eligible for annual leave', [
                    'karyawan_id' => $karyawan->id,
                    'karyawan_name' => $karyawan->nama,
                    'doh' => $dohDate->format('Y-m-d'),
                    'tanggal' => $tanggal->format('Y-m-d'),
                    'years_since_doh' => $dohDate->diffInYears($tanggal),
                    'message' => 'Belum memenuhi syarat satu tahun kerja'
                ]);

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

            // Log reset dates for debugging
            Log::info('Annual leave reset dates', [
                'karyawan_id' => $karyawan->id,
                'karyawan_name' => $karyawan->nama,
                'doh' => $dohDate->format('Y-m-d'),
                'years_since_doh' => $yearsSinceDoh,
                'last_reset_date' => $lastResetDate->format('Y-m-d'),
                'next_reset_date' => $nextResetDate->format('Y-m-d')
            ]);

            // Set annual leave quota to 14 days as per business rule
            $jatahHari = 14; // Fixed at 14 days as per requirement

            // Calculate used days since last annual reset
            Log::info('Calculating used days for annual leave', [
                'karyawan_id' => $karyawan->id,
                'karyawan_name' => $karyawan->nama,
                'jenis_cuti_id' => $jenisCutiId,
                'last_reset_date' => $lastResetDate->format('Y-m-d'),
                'tanggal' => $tanggal->format('Y-m-d')
            ]);

            $digunakan = $this->getUsedLeaveDay($karyawan->id, $jenisCutiId, $lastResetDate, $tanggal);
            $sisa = $jatahHari - $digunakan;

            // Log the result for debugging
            Log::info('Annual leave balance calculated', [
                'karyawan_id' => $karyawan->id,
                'karyawan_name' => $karyawan->nama,
                'jatah_hari' => $jatahHari,
                'digunakan' => $digunakan,
                'sisa' => $sisa,
                'last_reset_date' => $lastResetDate->format('Y-m-d'),
                'next_reset_date' => $nextResetDate->format('Y-m-d')
            ]);

            // Get the latest leave request for this type to calculate difference
            $latestCuti = $this->getLatestLeave($karyawan->id, $jenisCutiId);

            // Log the latest leave request for debugging
            Log::info('Latest annual leave request', [
                'karyawan_id' => $karyawan->id,
                'karyawan_name' => $karyawan->nama,
                'latest_cuti' => $latestCuti ? [
                    'id' => $latestCuti->id,
                    'tanggal_mulai' => $latestCuti->tanggal_mulai,
                    'tanggal_selesai' => $latestCuti->tanggal_selesai,
                    'lama_hari' => $latestCuti->lama_hari,
                    'status_cuti' => $latestCuti->status_cuti
                ] : null
            ]);

            // No special status info for annual leave
            $statusInfo = null;
        }

        // Calculate difference between ideal schedule and actual leave dates
        $selisihInfo = $this->calculateSelisihCuti($latestCuti ?? null, $lastResetDate, $nextResetDate);

        $result = [
            'jatah_hari' => $jatahHari,
            'digunakan' => $digunakan,
            'sisa' => $sisa,
            'eligible' => true,
            'reset_date' => $lastResetDate ?? null,
            'next_reset_date' => $nextResetDate ?? null,
            'selisih_hari' => $selisihInfo['selisih_hari'] ?? null,
            'status_selisih' => $selisihInfo['status_selisih'] ?? null,
            'latest_cuti_date' => $selisihInfo['latest_cuti_date'] ?? null
        ];

        // Add status info if available
        if ($statusInfo) {
            $result['status_info'] = $statusInfo;
        }

        // Calculate adjusted next reset date if there's a leave debt
        if ($selisihInfo['status_selisih'] === 'lebih_awal' && $selisihInfo['selisih_hari'] > 0 && $nextResetDate) {
            $adjustedNextResetDate = $nextResetDate->copy()->addDays($selisihInfo['selisih_hari']);
            $result['adjusted_next_reset_date'] = $adjustedNextResetDate;
        }

        return $result;
    }

    /**
     * Get the latest leave request for a specific employee and leave type
     *
     * @param int $karyawanId
     * @param int $jenisCutiId
     * @return Cuti|null
     */
    public function getLatestLeave(int $karyawanId, int $jenisCutiId)
    {
        // Log for debugging
        Log::info('Getting latest leave', [
            'karyawan_id' => $karyawanId,
            'jenis_cuti_id' => $jenisCutiId
        ]);

        // Try to find based on primary jenis_cuti_id
        $query1 = Cuti::where('karyawan_id', $karyawanId)
            ->where('jenis_cuti_id', $jenisCutiId)
            ->where('status_cuti', 'disetujui')
            ->latest('tanggal_mulai');

        // Get the SQL query for debugging
        $sql1 = $query1->toSql();
        $bindings1 = $query1->getBindings();

        // Log the SQL query and bindings
        Log::debug('SQL query for latest leave (primary)', [
            'sql' => $sql1,
            'bindings' => $bindings1
        ]);

        // Execute the query
        $latestCuti = $query1->first();

        // Log the result
        Log::debug('Latest leave result (primary)', [
            'found' => $latestCuti ? true : false,
            'latest_leave' => $latestCuti ? [
                'id' => $latestCuti->id,
                'tanggal_mulai' => $latestCuti->tanggal_mulai,
                'tanggal_selesai' => $latestCuti->tanggal_selesai,
                'lama_hari' => $latestCuti->lama_hari,
                'status_cuti' => $latestCuti->status_cuti
            ] : null
        ]);

        // If not found, try to find in cuti_details
        if (!$latestCuti) {
            $query2 = Cuti::where('karyawan_id', $karyawanId)
                ->where('status_cuti', 'disetujui')
                ->whereHas('cutiDetails', function ($query) use ($jenisCutiId) {
                    $query->where('jenis_cuti_id', $jenisCutiId);
                })
                ->latest('tanggal_mulai');

            // Get the SQL query for debugging
            $sql2 = $query2->toSql();
            $bindings2 = $query2->getBindings();

            // Log the SQL query and bindings
            Log::debug('SQL query for latest leave (details)', [
                'sql' => $sql2,
                'bindings' => $bindings2
            ]);

            // Execute the query
            $latestCuti = $query2->first();

            // Log the result
            Log::debug('Latest leave result (details)', [
                'found' => $latestCuti ? true : false,
                'latest_leave' => $latestCuti ? [
                    'id' => $latestCuti->id,
                    'tanggal_mulai' => $latestCuti->tanggal_mulai,
                    'tanggal_selesai' => $latestCuti->tanggal_selesai,
                    'lama_hari' => $latestCuti->lama_hari,
                    'status_cuti' => $latestCuti->status_cuti
                ] : null
            ]);
        }

        // Also check for all leaves for this employee
        $allLeaves = Cuti::where('karyawan_id', $karyawanId)
            ->get();

        // Log all leaves for debugging
        Log::debug('All leaves for this employee', [
            'count' => $allLeaves->count(),
            'leaves' => $allLeaves->map(function($cuti) {
                return [
                    'id' => $cuti->id,
                    'jenis_cuti_id' => $cuti->jenis_cuti_id,
                    'tanggal_mulai' => $cuti->tanggal_mulai,
                    'tanggal_selesai' => $cuti->tanggal_selesai,
                    'lama_hari' => $cuti->lama_hari,
                    'status_cuti' => $cuti->status_cuti
                ];
            })
        ]);

        return $latestCuti;
    }

    /**
     * Calculate the difference between ideal leave schedule and actual leave date
     * based on DOH reset date
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

        // Get the karyawan DOH from the cuti's relationship
        $karyawan = $latestCuti->karyawan;
        $doh = $karyawan && $karyawan->doh ? Carbon::parse($karyawan->doh) : null;

        if (!$doh) {
            return [
                'selisih_hari' => null,
                'status_selisih' => null,
                'latest_cuti_date' => $latestCutiDate
            ];
        }

        // Compare against the last reset date (DOH anniversary) instead of calculating a new date
        // This ensures we're properly checking if cuti was taken before or after the reset period
        $expectedLeaveDate = $lastResetDate;

        // Calculate difference in days
        $selisihHari = null;
        $statusSelisih = null;

        // If the leave date is before the reset date, it's a "hutang" (debt)
        // The employee took leave before they were supposed to
        if ($latestCutiDate->lt($expectedLeaveDate)) {
            $selisihHari = $latestCutiDate->diffInDays($expectedLeaveDate);
            $statusSelisih = 'lebih_awal'; // Took leave earlier = hutang
        }
        // If the leave date is after the reset date, it's "good"
        // The employee took leave when they were supposed to or later
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
     * Calculate all leave balances for a given employee
     *
     * @param Karyawan $karyawan
     * @param string|null $tanggal
     * @return array
     */
    public function calculateAllLeaveBalances(Karyawan $karyawan, $tanggal = null)
    {
        // Log for debugging
        Log::info('Calculating all leave balances', [
            'karyawan_id' => $karyawan->id,
            'karyawan_name' => $karyawan->nama,
            'tanggal' => $tanggal ? Carbon::parse($tanggal)->format('Y-m-d') : Carbon::now()->format('Y-m-d')
        ]);

        $jenisCutis = JenisCuti::all();
        $result = [];

        foreach ($jenisCutis as $jenisCuti) {
            // Log for debugging
            Log::info('Calculating leave balance for jenis_cuti', [
                'karyawan_id' => $karyawan->id,
                'jenis_cuti_id' => $jenisCuti->id,
                'jenis_cuti_name' => $jenisCuti->nama_jenis
            ]);

            $result[$jenisCuti->id] = $this->calculateLeaveBalance($karyawan, $jenisCuti->id, $tanggal);
            $result[$jenisCuti->id]['nama_jenis'] = $jenisCuti->nama_jenis;

            // Log the result for debugging
            Log::info('Leave balance calculated', [
                'karyawan_id' => $karyawan->id,
                'jenis_cuti_id' => $jenisCuti->id,
                'jenis_cuti_name' => $jenisCuti->nama_jenis,
                'jatah_hari' => $result[$jenisCuti->id]['jatah_hari'],
                'digunakan' => $result[$jenisCuti->id]['digunakan'],
                'sisa' => $result[$jenisCuti->id]['sisa']
            ]);
        }

        return $result;
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
        // Check if this is an annual leave type
        $jenisCuti = JenisCuti::find($jenisCutiId);
        $isAnnualLeave = $jenisCuti && stripos($jenisCuti->nama_jenis, 'tahunan') !== false;
        $isPeriodic = $jenisCuti && stripos($jenisCuti->nama_jenis, 'periodik') !== false;

        // Log for debugging
        Log::debug('Calculating used leave days', [
            'karyawanId' => $karyawanId,
            'jenisCutiId' => $jenisCutiId,
            'jenisCutiName' => $jenisCuti ? $jenisCuti->nama_jenis : 'Unknown',
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'isAnnualLeave' => $isAnnualLeave,
            'isPeriodic' => $isPeriodic
        ]);

        $totalDays = 0;

        // Calculate end date for queries - for periodic leave, we need to include future approved leaves
        // For other types, we'll use the provided end date
        $queryEndDate = $isPeriodic ? 
            ($endDate->copy()->addYear()) : // Look one year ahead for periodic leave
            $endDate;

        // IMPROVED: Directly query for leaves with details relating to this leave type
        // This ensures we properly capture combination leaves
        $combinationLeaves = Cuti::where('karyawan_id', $karyawanId)
            ->where('status_cuti', 'disetujui')
            ->whereHas('cutiDetails', function($query) use ($jenisCutiId) {
                $query->where('jenis_cuti_id', $jenisCutiId);
            })
            ->where(function($query) use ($startDate, $queryEndDate) {
                $query->where(function($q) use ($startDate, $queryEndDate) {
                    // Leave starts within the period (including future approved leaves)
                    $q->where('tanggal_mulai', '>=', $startDate);
                })
                ->orWhere(function($q) use ($startDate, $queryEndDate) {
                    // Leave ends within the period
                    $q->where('tanggal_selesai', '>=', $startDate);
                });
            })
            ->with(['cutiDetails' => function($query) use ($jenisCutiId) {
                $query->where('jenis_cuti_id', $jenisCutiId);
            }])
            ->get();
        
        // Log the combination leaves found
        Log::debug('Combination leaves found', [
            'count' => $combinationLeaves->count(),
            'leaves' => $combinationLeaves->map(function($cuti) {
                return [
                    'id' => $cuti->id,
                    'tanggal_mulai' => $cuti->tanggal_mulai,
                    'tanggal_selesai' => $cuti->tanggal_selesai,
                    'details' => $cuti->cutiDetails->map(function($detail) {
                        return [
                            'jenis_cuti_id' => $detail->jenis_cuti_id,
                            'jumlah_hari' => $detail->jumlah_hari
                        ];
                    })
                ];
            })
        ]);

        // Process combination leaves first (both current and future approved leaves)
        foreach ($combinationLeaves as $cuti) {
            // For combination leaves, get the specific days for this leave type
            $details = $cuti->cutiDetails;
            
            foreach ($details as $detail) {
                // For cuti details matching our target leave type, count the days
                if ($detail->jenis_cuti_id == $jenisCutiId) {
                    // Use the exact days from details for combination leaves
                    $effectiveDetailDays = $detail->jumlah_hari;
                    
                    Log::debug('Counting combination leave details', [
                        'cuti_id' => $cuti->id,
                        'detail_id' => $detail->id,
                        'jenis_cuti_id' => $detail->jenis_cuti_id,
                        'days' => $effectiveDetailDays,
                        'is_periodic' => $isPeriodic,
                        'tanggal_mulai' => $cuti->tanggal_mulai,
                        'is_future' => Carbon::parse($cuti->tanggal_mulai)->gt(Carbon::now())
                    ]);
                    
                    $totalDays += $effectiveDetailDays;
                }
            }
        }
        
        // Now get standard single-type leaves 
        $standardLeaves = Cuti::where('karyawan_id', $karyawanId)
            ->where('status_cuti', 'disetujui')
            ->where('jenis_cuti_id', $jenisCutiId)
            ->where(function($query) use ($startDate, $queryEndDate) {
                $query->where(function($q) use ($startDate, $queryEndDate) {
                    // Leave starts within the period (including future approved leaves)
                    $q->where('tanggal_mulai', '>=', $startDate);
                })
                ->orWhere(function($q) use ($startDate, $queryEndDate) {
                    // Leave ends within the period
                    $q->where('tanggal_selesai', '>=', $startDate);
                });
            })
            ->whereDoesntHave('cutiDetails') // Only get leaves without details (not combination)
            ->get();
            
        // Log standard leaves
        Log::debug('Standard leaves found', [
            'count' => $standardLeaves->count(),
            'leaves' => $standardLeaves->map(function($cuti) {
                return [
                    'id' => $cuti->id,
                    'tanggal_mulai' => $cuti->tanggal_mulai,
                    'tanggal_selesai' => $cuti->tanggal_selesai,
                    'lama_hari' => $cuti->lama_hari
                ];
            })
        ]);

        // Process standard leaves
        foreach ($standardLeaves as $cuti) {
            // For standard leaves, use the full day count
            $effectiveDays = $cuti->lama_hari;
            
            Log::debug('Standard leave calculation', [
                'cuti_id' => $cuti->id,
                'leave_start' => $cuti->tanggal_mulai,
                'leave_end' => $cuti->tanggal_selesai,
                'effective_days' => $effectiveDays,
                'is_future' => Carbon::parse($cuti->tanggal_mulai)->gt(Carbon::now())
            ]);
            
            $totalDays += $effectiveDays;
        }

        // For annual leave, handle pending leaves as well if needed
        if ($isAnnualLeave) {
            // Additional code for pending annual leaves if needed...
        }

        Log::debug('Total used days calculated', [
            'totalDays' => $totalDays
        ]);

        return $totalDays;
    }

    /**
     * Calculate Tanggal Ideal Actual based on leave start dates for periodic leave
     *
     * @param array $allLeaveStartDates Array of all leave start dates for this leave type
     * @param Carbon $currentDate Current date to calculate period for
     * @param int $resetWeeks
     * @return Carbon|null
     */
    public function calculateIdealActualDate(array $allLeaveStartDates, Carbon $currentDate, int $resetWeeks)
    {
        if (empty($allLeaveStartDates)) {
            return null;
        }

        $resetDays = $resetWeeks * 7;
        $idealActualDate = null;

        // Get the current leave's position in the sequence
        $currentLeaveIndex = 0;
        foreach ($allLeaveStartDates as $index => $leaveDate) {
            if ($leaveDate->equalTo($currentDate) || $leaveDate->gt($currentDate)) {
                break;
            }
            $currentLeaveIndex = $index + 1;
        }

        // Determine which period this leave belongs to
        if ($currentLeaveIndex == 0) {
            // First leave - use the first leave start date itself
            $idealActualDate = $allLeaveStartDates[0]->copy();
            $calculationMethod = 'Using first leave start date directly';
        } else if ($currentLeaveIndex == 1) {
            // Second leave - use the first leave start date + period
            $idealActualDate = $allLeaveStartDates[0]->copy()->addDays($resetDays);
            $calculationMethod = 'Using first leave start date + ' . $resetDays . ' days';
        } else if ($currentLeaveIndex < count($allLeaveStartDates)) {
            // Third and subsequent leaves - use the previous leave start date + period
            $previousLeaveIndex = $currentLeaveIndex - 1;
            $idealActualDate = $allLeaveStartDates[$previousLeaveIndex]->copy()->addDays($resetDays);
            $calculationMethod = 'Using leave start date at index ' . $previousLeaveIndex . ' + ' . $resetDays . ' days';
        } else {
            // Beyond the last known leave - use the last leave start date + period
            $lastLeaveDate = end($allLeaveStartDates);
            $idealActualDate = $lastLeaveDate->copy()->addDays($resetDays);
            $calculationMethod = 'Using last leave start date + ' . $resetDays . ' days';
        }

        // Log for debugging
        Log::debug('Calculating Ideal Actual Date', [
            'all_leave_start_dates' => array_map(function($date) { return $date->format('Y-m-d'); }, $allLeaveStartDates),
            'current_date' => $currentDate->format('Y-m-d'),
            'reset_weeks' => $resetWeeks,
            'reset_days' => $resetDays,
            'current_leave_index' => $currentLeaveIndex,
            'ideal_actual_date' => $idealActualDate ? $idealActualDate->format('Y-m-d') : null,
            'calculation_method' => $calculationMethod
        ]);

        return $idealActualDate;
    }

    /**
     * Get all leave start dates for a specific leave type
     *
     * @param int $karyawanId
     * @param int $jenisCutiId
     * @return array
     */
    public function getAllLeaveStartDates(int $karyawanId, int $jenisCutiId)
    {
        // Find all cuti of this type for this employee
        $allCuti = Cuti::where('karyawan_id', $karyawanId)
            ->where('jenis_cuti_id', $jenisCutiId)
            ->where('status_cuti', 'disetujui')
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        if ($allCuti->isEmpty()) {
            return [];
        }

        // Create an array of all leave start dates
        $allStartDates = [];
        foreach ($allCuti as $cuti) {
            $allStartDates[] = Carbon::parse($cuti->tanggal_mulai);
        }

        return $allStartDates;
    }

    /**
     * Calculate recommendation dates for periodic leave
     *
     * @param int $karyawanId
     * @param int $jenisCutiId
     * @param Carbon $dohDate
     * @param Cuti|null $latestCuti
     * @param int $resetWeeks
     * @param Carbon $referenceDate
     * @return array
     */
    public function calculateRecommendationDates($karyawanId, $jenisCutiId, $dohDate, $latestCuti, $resetWeeks, $referenceDate)
    {
        $resetDays = $resetWeeks * 7;

        // 1. Calculate recommendation based on DOH
        $daysSinceDoh = $dohDate->diffInDays($referenceDate);
        $periodsSinceDoh = floor($daysSinceDoh / $resetDays);

        // Calculate the first ideal date (first period) - this should be the DOH itself
        $firstIdealDate = $dohDate->copy(); // First period is DOH itself

        // Calculate the current period ideal date based on DOH and periods passed
        $currentPeriodIdealDate = null;

        // We'll calculate this after generating all ideal dates to ensure consistency

        // Calculate all ideal dates for each period
        $allIdealDates = [];

        // Debug information
        Log::debug('Calculating ideal dates', [
            'doh' => $dohDate->format('Y-m-d'),
            'resetDays' => $resetDays,
            'resetWeeks' => $resetWeeks,
            'periodsSinceDoh' => $periodsSinceDoh
        ]);

        // IMPORTANT: We need to make sure the first period date is calculated correctly
        // For DOH 2023-03-20, the first period date should be the DOH itself (2023-03-20)

        // For the first period, we use the DOH date itself
        $firstPeriodDate = $dohDate->copy();

        Log::debug('Calculating first period date', [
            'doh' => $dohDate->format('Y-m-d'),
            'resetDays' => $resetDays,
            'firstPeriodDate' => $firstPeriodDate->format('Y-m-d'),
            'calculation' => 'Using DOH date directly'
        ]);

        Log::debug('First period date', [
            'doh' => $dohDate->format('Y-m-d'),
            'resetDays' => $resetDays,
            'date' => $firstPeriodDate->format('Y-m-d'),
            'calculation' => 'Using DOH date directly'
        ]);

        // Add the first period
        $allIdealDates[] = [
            'period' => 1,
            'date' => $firstPeriodDate->copy(),
            'is_current' => ($periodsSinceDoh == 0)
        ];

        // Calculate at least 3 periods or enough to cover the current period
        $periodsToCalculate = max(2, $periodsSinceDoh + 1);

        for ($i = 1; $i <= $periodsToCalculate; $i++) {
            // For each period after the first, calculate directly from DOH + (period * resetDays)
            // This ensures each period is calculated directly from DOH, not from the previous period
            $periodDate = $dohDate->copy()->addDays($i * $resetDays);

            Log::debug('Calculating period ' . ($i + 1) . ' date', [
                'periodDate' => $periodDate->format('Y-m-d'),
                'calculation' => 'DOH + ' . ($i * $resetDays) . ' days',
                'doh' => $dohDate->format('Y-m-d')
            ]);

            Log::debug('Period ' . ($i + 1) . ' date', [
                'date' => $periodDate->format('Y-m-d'),
                'calculation' => 'DOH + ' . ($i * $resetDays) . ' days'
            ]);

            $allIdealDates[] = [
                'period' => $i + 1,
                'date' => $periodDate->copy(), // Make a copy to avoid reference issues
                'is_current' => ($i == $periodsSinceDoh)
            ];
        }

        // Update firstIdealDate and currentPeriodIdealDate from the calculated ideal dates
        if (count($allIdealDates) > 0) {
            // First ideal date is the first period's date
            $firstIdealDate = $allIdealDates[0]['date'];

            // Find current period ideal date
            foreach ($allIdealDates as $idealDate) {
                if ($idealDate['is_current']) {
                    $currentPeriodIdealDate = $idealDate['date'];
                    break;
                }
            }

            // If no current period found, use the last calculated period
            if ($currentPeriodIdealDate === null && count($allIdealDates) > 0) {
                $currentPeriodIdealDate = $allIdealDates[count($allIdealDates) - 1]['date'];
            }
        }

        // Calculate the next period from DOH based on current date
        // If we're in period 0 (first period), the next period is period 1
        // Otherwise, it's the period after the current one
        $nextPeriodFromDoh = $currentPeriodIdealDate ?
            $dohDate->copy()->addDays(($periodsSinceDoh + 1) * $resetDays) :
            $dohDate->copy()->addDays($resetDays);

        // 2. Calculate recommendation based on previous leave application
        $recommendationBasedOnPrevious = null;
        $source = 'doh'; // Default source is DOH

        if ($latestCuti) {
            $latestCutiDate = Carbon::parse($latestCuti->tanggal_mulai);

            // Calculate the next period date from the previous leave
            $recommendationBasedOnPrevious = $latestCutiDate->copy()->addDays($resetDays);

            // Check if this recommendation is valid (not in the past)
            if ($recommendationBasedOnPrevious->isPast()) {
                // Calculate how many periods we need to add to make it future
                $daysSincePreviousCuti = $latestCutiDate->diffInDays($referenceDate);
                $periodsToAdd = ceil($daysSincePreviousCuti / $resetDays);
                $recommendationBasedOnPrevious = $latestCutiDate->copy()->addDays($periodsToAdd * $resetDays);
            }

            $source = 'previous_application';
        } else {
            // If no previous leave application exists, use DOH date as a basis
            $recommendationBasedOnPrevious = $nextPeriodFromDoh;
        }

        // Get historical leave data for trend analysis
        $historicalLeaves = Cuti::where('karyawan_id', $karyawanId)
            ->where('jenis_cuti_id', $jenisCutiId)
            ->where('status_cuti', 'disetujui')
            ->orderBy('tanggal_mulai', 'desc')
            ->take(5) // Get last 5 leaves for analysis
            ->get();

        // Calculate average days between leave applications
        $avgDaysBetweenLeaves = null;
        if ($historicalLeaves->count() > 1) {
            $totalDays = 0;
            $count = 0;

            for ($i = 0; $i < $historicalLeaves->count() - 1; $i++) {
                $currentLeave = Carbon::parse($historicalLeaves[$i]->tanggal_mulai);
                $previousLeave = Carbon::parse($historicalLeaves[$i + 1]->tanggal_mulai);

                $daysBetween = $currentLeave->diffInDays($previousLeave);
                $totalDays += $daysBetween;
                $count++;
            }

            if ($count > 0) {
                $avgDaysBetweenLeaves = round($totalDays / $count);
            }
        }

        // Calculate recommendation based on historical average (if available)
        $recommendationBasedOnAverage = null;
        if ($latestCuti && $avgDaysBetweenLeaves) {
            $latestCutiDate = Carbon::parse($latestCuti->tanggal_mulai);
            $recommendationBasedOnAverage = $latestCutiDate->copy()->addDays($avgDaysBetweenLeaves);

            // Ensure it's not in the past
            if ($recommendationBasedOnAverage->isPast()) {
                $recommendationBasedOnAverage = $referenceDate->copy()->addDays(7); // Default to 1 week from now
            }
        }

        return [
            'based_on_doh' => $nextPeriodFromDoh,
            'based_on_previous' => $recommendationBasedOnPrevious,
            'based_on_average' => $recommendationBasedOnAverage,
            'first_ideal_date' => $firstIdealDate,
            'current_period_ideal_date' => $currentPeriodIdealDate,
            'all_ideal_dates' => $allIdealDates,
            'periods_since_doh' => $periodsSinceDoh,
            'avg_days_between_leaves' => $avgDaysBetweenLeaves,
            'source' => $source,
            'historical_leaves_count' => $historicalLeaves->count()
        ];
    }

    /**
     * Check if a leave application date is within the allowed period
     *
     * @param Karyawan $karyawan
     * @param int $jenisCutiId
     * @param Carbon $applicationDate
     * @return array
     */
    public function isWithinLeavePeriod(Karyawan $karyawan, int $jenisCutiId, Carbon $applicationDate)
    {
        $jenisCuti = JenisCuti::findOrFail($jenisCutiId);
        $jenisCutiName = strtolower($jenisCuti->nama_jenis);

        // Only check for periodic leave types, explicitly exclude "Cuti Tahunan"
        if (stripos($jenisCutiName, 'periodik') === false || stripos($jenisCutiName, 'tahunan') !== false) {
            return [
                'within_period' => true,
                'message' => 'Not a periodic leave type or is Annual Leave, no period restrictions',
                'is_annual_leave' => stripos($jenisCutiName, 'tahunan') !== false
            ];
        }

        $dohDate = Carbon::parse($karyawan->doh);

        // Calculate reset period based on employee status
        $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;
        $resetDays = $resetWeeks * 7;

        if (!$dohDate->isValid() || $dohDate->isFuture()) {
            return [
                'within_period' => false,
                'message' => 'DOH date is invalid or in the future'
            ];
        }

        // Calculate exact periods since DOH
        $daysSinceDoh = $dohDate->diffInDays($applicationDate);
        $periodsSinceDoh = floor($daysSinceDoh / $resetDays);

        // Calculate the start and end of the current period
        // For period 0 (first period), start is DOH itself
        // For subsequent periods, calculate based on DOH + (period * resetDays)
        if ($periodsSinceDoh == 0) {
            // First period starts at DOH
            $currentPeriodStart = $dohDate->copy();
        } else {
            // Subsequent periods start at DOH + (period * resetDays)
            $currentPeriodStart = $dohDate->copy()->addDays($periodsSinceDoh * $resetDays);
        }
        $currentPeriodEnd = $currentPeriodStart->copy()->addDays($resetDays - 1);

        // Calculate the start and end of the next period
        $nextPeriodStart = $currentPeriodEnd->copy()->addDay();
        $nextPeriodEnd = $nextPeriodStart->copy()->addDays($resetDays - 1);

        // Check if application date is within the current period
        $isWithinCurrentPeriod = $applicationDate->between($currentPeriodStart, $currentPeriodEnd);

        // Check if application date is within the next period
        $isWithinNextPeriod = $applicationDate->between($nextPeriodStart, $nextPeriodEnd);

        // Calculate days until next period
        $daysUntilNextPeriod = $applicationDate->diffInDays($nextPeriodStart);

        // Calculate days since period start
        $daysSincePeriodStart = $applicationDate->diffInDays($currentPeriodStart);

        // Determine if application is within allowed period
        // We'll allow applications within the current period or the next period
        $isWithinAllowedPeriod = $isWithinCurrentPeriod || $isWithinNextPeriod;

        // Get the latest leave for this type to check if already taken in this period
        $latestCuti = $this->getLatestLeave($karyawan->id, $jenisCutiId);
        $alreadyTakenInPeriod = false;

        if ($latestCuti) {
            $latestCutiDate = Carbon::parse($latestCuti->tanggal_mulai);
            $alreadyTakenInPeriod = $latestCutiDate->between($currentPeriodStart, $currentPeriodEnd);
        }

        // Calculate recommendation dates
        $recommendationDates = $this->calculateRecommendationDates(
            $karyawan->id,
            $jenisCutiId,
            $dohDate,
            $latestCuti,
            $resetWeeks,
            $applicationDate
        );

        return [
            'within_period' => $isWithinAllowedPeriod && !$alreadyTakenInPeriod,
            'current_period_start' => $currentPeriodStart,
            'current_period_end' => $currentPeriodEnd,
            'next_period_start' => $nextPeriodStart,
            'next_period_end' => $nextPeriodEnd,
            'is_within_current_period' => $isWithinCurrentPeriod,
            'is_within_next_period' => $isWithinNextPeriod,
            'days_until_next_period' => $daysUntilNextPeriod,
            'days_since_period_start' => $daysSincePeriodStart,
            'already_taken_in_period' => $alreadyTakenInPeriod,
            'recommendation_dates' => $recommendationDates,
            'message' => $this->generatePeriodMessage($alreadyTakenInPeriod, $isWithinCurrentPeriod, $isWithinNextPeriod, $daysUntilNextPeriod, $resetWeeks)
        ];
    }

    /**
     * Generate a human-readable message about the leave period status
     *
     * @param bool $alreadyTakenInPeriod
     * @param bool $isWithinCurrentPeriod
     * @param bool $isWithinNextPeriod
     * @param int $daysUntilNextPeriod
     * @param int $resetWeeks
     * @return string
     */
    private function generatePeriodMessage($alreadyTakenInPeriod, $isWithinCurrentPeriod, $isWithinNextPeriod, $daysUntilNextPeriod, $resetWeeks)
    {
        if ($alreadyTakenInPeriod) {
            return "You have already taken leave in this period. Please wait for the next period.";
        }

        if ($isWithinCurrentPeriod) {
            return "You are in the correct leave period. Your application can be processed.";
        }

        if ($isWithinNextPeriod) {
            return "You are applying for the next leave period. Your application can be processed.";
        }

        if ($daysUntilNextPeriod > 0) {
            return "You are not yet in a leave period. There are still {$daysUntilNextPeriod} days before the next leave period begins.";
        } else {
            return "You are outside the allowed leave period. Please wait for the next period in {$resetWeeks} weeks.";
        }
    }
}