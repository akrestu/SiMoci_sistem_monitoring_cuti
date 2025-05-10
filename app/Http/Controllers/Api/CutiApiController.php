<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Services\CutiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CutiApiController extends Controller
{
    /**
     * Validate if a leave application date is within the allowed period
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validatePeriod(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'jenis_cuti_id' => 'required|exists:jenis_cutis,id',
            'tanggal_mulai' => 'required|date',
        ]);

        $karyawan = Karyawan::findOrFail($request->karyawan_id);
        $jenisCuti = JenisCuti::findOrFail($request->jenis_cuti_id);
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);

        $cutiService = new CutiService();
        $result = $cutiService->isWithinLeavePeriod($karyawan, $request->jenis_cuti_id, $tanggalMulai);

        // Ensure we're using the correct ideal date for recommendations
        if (isset($result['recommendation_dates']) && isset($result['recommendation_dates']['all_ideal_dates']) && count($result['recommendation_dates']['all_ideal_dates']) > 0) {
            // Check if this is Cuti Periodik (Lokal) - we need special handling
            $isPeridikLokal = stripos($jenisCuti->nama_jenis, 'periodik') !== false && 
                              stripos($jenisCuti->nama_jenis, 'lokal') !== false;
                
            // For Cuti Periodik (Lokal), use the current period's date instead of next period's date
            if ($isPeridikLokal) {
                // Find the current period date in all_ideal_dates
                $currentPeriodDate = null;
                foreach ($result['recommendation_dates']['all_ideal_dates'] as $idealDateInfo) {
                    if (isset($idealDateInfo['is_current']) && $idealDateInfo['is_current']) {
                        $currentPeriodDate = $idealDateInfo['date'];
                        break;
                    }
                }
                
                // If found, use it; otherwise fall back to the first date
                if ($currentPeriodDate) {
                    $result['recommendation_dates']['ideal_date'] = $currentPeriodDate;
                } else {
                    $result['recommendation_dates']['ideal_date'] = $result['recommendation_dates']['all_ideal_dates'][0]['date'];
                }
            } else {
                // For other leave types, use the first period's date (next period)
                $result['recommendation_dates']['ideal_date'] = $result['recommendation_dates']['all_ideal_dates'][0]['date'];
            }

            // Calculate recommended date for early applications (Tanggal Ideal + 7 days)
            // First check if we have Tanggal Ideal Actual, otherwise use Tanggal Ideal (DOH)
            if (isset($result['recommendation_dates']['based_on_previous'])) {
                // Use Tanggal Ideal Actual + 7 days
                $recommendedDate = $result['recommendation_dates']['based_on_previous']->copy()->addDays(7);
                $result['recommendation_dates']['recommended_date'] = $recommendedDate;
                $source = 'based_on_previous';
            } else {
                // Use Tanggal Ideal (DOH) + 7 days
                $recommendedDate = $result['recommendation_dates']['based_on_doh']->copy()->addDays(7);
                $result['recommendation_dates']['recommended_date'] = $recommendedDate;
                $source = 'based_on_doh';
            }

            // Log for debugging
            Log::debug('API validation - Using recommended date', [
                'karyawan_id' => $karyawan->id,
                'karyawan_nik' => $karyawan->nik,
                'karyawan_status' => $karyawan->status,
                'doh' => $karyawan->doh,
                'first_period_date' => $result['recommendation_dates']['all_ideal_dates'][0]['date']->format('Y-m-d'),
                'recommended_date' => $recommendedDate->format('Y-m-d'),
                'source' => $source,
                'periods_count' => count($result['recommendation_dates']['all_ideal_dates']),
                'reset_weeks' => $karyawan->status === 'Staff' ? 7 : 12,
                'is_periodik_lokal' => $isPeridikLokal
            ]);

            // Also add a debug field to help troubleshoot
            $result['recommendation_dates']['debug_info'] = [
                'first_period_date' => $result['recommendation_dates']['all_ideal_dates'][0]['date']->format('Y-m-d'),
                'recommended_date' => $recommendedDate->format('Y-m-d'),
                'source' => $source,
                'periods_count' => count($result['recommendation_dates']['all_ideal_dates']),
                'doh' => $karyawan->doh,
                'status' => $karyawan->status,
                'reset_weeks' => $karyawan->status === 'Staff' ? 7 : 12,
                'is_periodik_lokal' => $isPeridikLokal,
                'jenis_cuti' => $jenisCuti->nama_jenis
            ];
        } elseif (isset($result['recommendation_dates']) && isset($result['recommendation_dates']['first_ideal_date'])) {
            $result['recommendation_dates']['ideal_date'] = $result['recommendation_dates']['first_ideal_date'];

            // Calculate recommended date (Tanggal Ideal + 7 days)
            $recommendedDate = $result['recommendation_dates']['first_ideal_date']->copy()->addDays(7);
            $result['recommendation_dates']['recommended_date'] = $recommendedDate;
        }

        return response()->json($result);
    }
}
