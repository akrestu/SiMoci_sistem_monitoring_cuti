<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Cuti;
use App\Services\CutiService;
use App\Services\AnnualLeaveCalculator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KaryawanController extends Controller
{
    /**
     * Search for karyawan by nik
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $nik = $request->query('nik');

        if (!$nik) {
            return response()->json([], 200);
        }

        $karyawans = Karyawan::where('nik', 'like', "%{$nik}%")->get(['id', 'nama', 'nik', 'departemen', 'jabatan', 'poh', 'doh', 'status']);

        return response()->json($karyawans, 200);
    }

    /**
     * Get leave analysis data for a karyawan
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLeaveAnalysis($id)
    {
        $karyawan = Karyawan::find($id);

        if (!$karyawan) {
            return response()->json(['message' => 'Karyawan not found'], 404);
        }

        // Get all leave balances using the Karyawan model's getLeaveBalances method
        // This method will use AnnualLeaveCalculator for annual leave
        $leaveBalances = $karyawan->getLeaveBalances();

        // Initialize the periodicLeaveAnalysis variable
        $periodicLeaveAnalysis = [];
        $cutiService = new CutiService();

        // Get analysis data for periodic leave types
        foreach ($leaveBalances as $jenisCutiId => $balanceData) {
            $jenisCuti = JenisCuti::find($jenisCutiId);

            // Skip if jenis_cuti not found
            if (!$jenisCuti) continue;

            // For periodic leave types, get additional analysis data
            if (stripos($jenisCuti->nama_jenis, 'periodik') !== false) {
                $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;
                $resetDays = $resetWeeks * 7;

                // Calculate next period's ideal date based on DOH
                $dohDate = Carbon::parse($karyawan->doh);
                $latestCuti = $cutiService->getLatestLeave($karyawan->id, $jenisCutiId);

                // Calculate ideal DOH dates (current period)
                $now = Carbon::now();
                $daysSinceDoh = $dohDate->diffInDays($now);
                $periodsSinceDoh = floor($daysSinceDoh / $resetDays);

                // Current period date based on DOH - this will be used for ALL periodic leave types
                $currentIdealDohDate = $dohDate->copy()->addDays($periodsSinceDoh * $resetDays);

                // Calculate next period's ideal actual date
                $nextIdealActualDate = null;

                // Flag to indicate if the employee has ever applied for this leave type
                $hasAppliedForLeave = false;

                if ($latestCuti) {
                    // Employee has applied for this leave type
                    $hasAppliedForLeave = true;

                    // Get all leave start dates for this leave type
                    $allLeaveStartDates = $cutiService->getAllLeaveStartDates($karyawan->id, $jenisCutiId);

                    // Calculate next ideal actual date based on previous leaves
                    if (!empty($allLeaveStartDates)) {
                        $latestLeaveDate = end($allLeaveStartDates);

                        // Calculate the period that the latest leave belongs to
                        $latestLeaveDaysSinceDoh = $dohDate->diffInDays($latestLeaveDate);
                        $latestLeavePeriodsSinceDoh = floor($latestLeaveDaysSinceDoh / $resetDays);

                        // If the latest leave is in the current period, calculate the next ideal actual date
                        // for the next period (current period + 1)
                        if ($latestLeavePeriodsSinceDoh == $periodsSinceDoh) {
                            // Latest leave is in the current period, so next ideal actual date should be for next period
                            $nextIdealActualDate = $latestLeaveDate->copy()->addDays($resetDays);

                            Log::debug('Latest leave is in current period, calculating next ideal actual date for next period', [
                                'latest_leave_date' => $latestLeaveDate->format('Y-m-d'),
                                'next_ideal_actual_date' => $nextIdealActualDate->format('Y-m-d'),
                                'reset_days' => $resetDays
                            ]);
                        } else {
                            // Latest leave is not in the current period, calculate based on standard formula
                            $nextIdealActualDate = $latestLeaveDate->copy()->addDays($resetDays);

                            Log::debug('Latest leave is not in current period, using standard formula', [
                                'latest_leave_date' => $latestLeaveDate->format('Y-m-d'),
                                'next_ideal_actual_date' => $nextIdealActualDate->format('Y-m-d'),
                                'reset_days' => $resetDays
                            ]);
                        }
                    }
                }

                // Check if the employee has already taken leave in the current period
                $currentPeriodStart = $dohDate->copy()->addDays($periodsSinceDoh * $resetDays);
                $currentPeriodEnd = $currentPeriodStart->copy()->addDays($resetDays - 1);
                $alreadyTakenInCurrentPeriod = false;

                if ($latestCuti) {
                    $latestCutiDate = Carbon::parse($latestCuti->tanggal_mulai);
                    $alreadyTakenInCurrentPeriod = $latestCutiDate->between($currentPeriodStart, $currentPeriodEnd);

                    // Check if any other leave type (Lokal or Luar) has been taken in this period
                    // This ensures that if an employee has taken Cuti periodik (Lokal), it affects Cuti periodik (Luar) and vice versa
                    if (!$alreadyTakenInCurrentPeriod) {
                        // Get the other periodic leave type ID (if Lokal, check Luar and vice versa)
                        $otherPeriodicLeaveType = null;
                        if (stripos($jenisCuti->nama_jenis, 'lokal') !== false) {
                            // Current is Lokal, check for Luar
                            $otherPeriodicLeaveType = JenisCuti::where('nama_jenis', 'like', '%periodik%')
                                ->where('nama_jenis', 'like', '%luar%')
                                ->first();
                        } else if (stripos($jenisCuti->nama_jenis, 'luar') !== false) {
                            // Current is Luar, check for Lokal
                            $otherPeriodicLeaveType = JenisCuti::where('nama_jenis', 'like', '%periodik%')
                                ->where('nama_jenis', 'like', '%lokal%')
                                ->first();
                        }

                        if ($otherPeriodicLeaveType) {
                            $otherLatestCuti = $cutiService->getLatestLeave($karyawan->id, $otherPeriodicLeaveType->id);
                            if ($otherLatestCuti) {
                                $otherLatestCutiDate = Carbon::parse($otherLatestCuti->tanggal_mulai);
                                $alreadyTakenInCurrentPeriod = $otherLatestCutiDate->between($currentPeriodStart, $currentPeriodEnd);
                            }
                        }
                    }
                }

                // If the employee has already taken leave in the current period, use the next period date
                // Otherwise, use the current period date
                $idealDohDate = $alreadyTakenInCurrentPeriod
                    ? $dohDate->copy()->addDays(($periodsSinceDoh + 1) * $resetDays)  // Next period
                    : $currentIdealDohDate;  // Current period

                // Debug log
                Log::debug('Calculating ideal DOH date for periodic leave type', [
                    'karyawan_id' => $karyawan->id,
                    'karyawan_nik' => $karyawan->nik,
                    'jenis_cuti' => $jenisCuti->nama_jenis,
                    'current_period_date' => $currentIdealDohDate->format('Y-m-d'),
                    'already_taken_in_current_period' => $alreadyTakenInCurrentPeriod,
                    'ideal_doh_date' => $idealDohDate->format('Y-m-d'),
                    'periods_since_doh' => $periodsSinceDoh,
                    'is_local' => stripos($jenisCuti->nama_jenis, 'lokal') !== false,
                    'doh' => $dohDate->format('Y-m-d'),
                    'resetWeeks' => $resetWeeks,
                    'resetDays' => $resetDays
                ]);

                // Add to periodic leave analysis
                $periodicLeaveAnalysis[$jenisCutiId] = [
                    'nama_jenis' => $jenisCuti->nama_jenis,
                    'next_ideal_doh_date' => $idealDohDate->format('Y-m-d'),
                    'next_ideal_actual_date' => $nextIdealActualDate ? $nextIdealActualDate->format('Y-m-d') : null,
                    'period_weeks' => $resetWeeks,
                    'has_applied_for_leave' => $hasAppliedForLeave,
                    'already_taken_in_current_period' => $alreadyTakenInCurrentPeriod,
                    'current_period_start' => $currentPeriodStart->format('Y-m-d'),
                    'current_period_end' => $currentPeriodEnd->format('Y-m-d'),
                    'is_local' => stripos($jenisCuti->nama_jenis, 'lokal') !== false
                ];

                // Add this to the main leave balances
                $leaveBalances[$jenisCutiId] = array_merge(
                    $leaveBalances[$jenisCutiId] ?? ['nama_jenis' => $jenisCuti->nama_jenis],
                    $periodicLeaveAnalysis[$jenisCutiId]
                );
            }
        }

        return response()->json($leaveBalances, 200);
    }
}