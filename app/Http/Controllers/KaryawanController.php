<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Exports\KaryawanExport;
use App\Exports\KaryawanTemplateExport;
use App\Imports\KaryawanImport;
use App\Services\CutiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query();

        // Filter berdasarkan departemen
        if ($request->has('departemen') && $request->departemen) {
            $query->where('departemen', $request->departemen);
        }

        // Pencarian berdasarkan nama atau NIK
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortField = $request->sort_by ?? 'nama';
        $sortDirection = $request->sort_direction ?? 'asc';

        if (in_array($sortField, ['nama', 'nik', 'departemen', 'jabatan'])) {
            $query->orderBy($sortField, $sortDirection);
        }

        // Ambil daftar departemen untuk filter
        $departemen = Karyawan::select('departemen')->distinct()->pluck('departemen');

        // Pagination - default 10 items per page, configurable via perPage parameter
        $perPage = $request->perPage ?? 10;
        $karyawans = $query->paginate($perPage);

        return view('karyawan.index', compact('karyawans', 'departemen'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:255|unique:karyawans',
            'departemen' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'email' => 'nullable|email|unique:karyawans',
            'doh' => 'nullable|date',
            'poh' => 'nullable|string|max:255',
            'status' => 'nullable|in:Staff,Non Staff',
        ]);

        Karyawan::create($validated);

        return redirect()->route('karyawans.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function show(Karyawan $karyawan)
    {
        // Get leave history
        $cutiHistory = $karyawan->cutis()
            ->with(['jenisCuti', 'cutiDetails.jenisCuti'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Prepare detailed leave analysis data for the table
        $cutiAnalytics = [];

        foreach ($cutiHistory as $cuti) {
            if ($cuti->status_cuti == 'disetujui') {
                // If the leave has multiple cuti details, create a separate entry for each detail
                if ($cuti->cutiDetails->count() > 0) {
                    foreach ($cuti->cutiDetails as $detail) {
                        // Skip details with 0 days
                        if ($detail->jumlah_hari <= 0) continue;

                        $jenisCuti = $detail->jenisCuti->nama_jenis;
                        $tanggalMulai = Carbon::parse($cuti->tanggal_mulai);
                        $tanggalSelesai = Carbon::parse($cuti->tanggal_selesai);

                        // Use tanggal_pengajuan if available, otherwise fall back to created_at
                        $tanggalPengajuan = null;
                        if (isset($cuti->tanggal_pengajuan)) {
                            $tanggalPengajuan = Carbon::parse($cuti->tanggal_pengajuan);
                        } else {
                            // Fall back to created_at
                            $tanggalPengajuan = Carbon::parse($cuti->created_at);
                        }

                        // Get employee's Date of Hire (DOH)
                        $doh = $karyawan->doh ? Carbon::parse($karyawan->doh) : null;

                        // Calculate expected leave date based on DOH for the current year
                        $expectedLeaveDate = null;
                        $selisihHari = null;
                        $statusSelisih = null;

                        if ($doh) {
                            // For periodic leave types, use the new period calculation
                            if (stripos($jenisCuti, 'periodik') !== false) {
                                // Calculate reset period based on employee status
                                $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;

                                // Get period information
                                $cutiService = new CutiService();
                                $periodInfo = $cutiService->isWithinLeavePeriod($karyawan, $detail->jenis_cuti_id, $tanggalMulai);

                                // Calculate the correct ideal date based on the period of this leave
                                $idealActualDate = null;
                                if (isset($periodInfo['recommendation_dates'])) {
                                    $allIdealDates = $periodInfo['recommendation_dates']['all_ideal_dates'] ?? [];
                                    $allStartDates = $cutiService->getAllLeaveStartDates($karyawan->id, $detail->jenis_cuti_id);
                                    if (!empty($allStartDates)) {
                                        $idealActualDate = $cutiService->calculateIdealActualDate($allStartDates, $tanggalMulai, $resetWeeks);
                                    } elseif (!empty($allIdealDates)) {
                                        // If no historical leaves, use the calculated ideal dates from period info
                                        foreach ($allIdealDates as $idealDateInfo) {
                                            if ($idealDateInfo['is_current']) {
                                                $idealActualDate = $idealDateInfo['date'];
                                                break;
                                            }
                                        }
                                    }
                                }

                                if ($idealActualDate) {
                                    // Compare actual leave date with ideal date
                                    $expectedLeaveDate = $idealActualDate;
                                    if ($tanggalMulai->lt($expectedLeaveDate)) {
                                        $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                        $statusSelisih = 'lebih_awal'; // hutang
                                    } elseif ($tanggalMulai->gt($expectedLeaveDate)) {
                                        $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                        $statusSelisih = 'lebih_lambat'; // baik
                                    } else {
                                        $selisihHari = 0;
                                        $statusSelisih = 'tepat_waktu';
                                    }
                                }
                            } else { // For annual leave
                                $yearsSinceDoh = $doh->diffInYears($tanggalMulai);
                                $expectedLeaveDate = $doh->copy()->addYears($yearsSinceDoh);

                                // Compare actual leave date with DOH anniversary
                                if ($tanggalMulai->lt($expectedLeaveDate)) {
                                    $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                    $statusSelisih = 'lebih_awal'; // hutang
                                } elseif ($tanggalMulai->gt($expectedLeaveDate)) {
                                    $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                    $statusSelisih = 'lebih_lambat'; // baik
                                } else {
                                    $selisihHari = 0;
                                    $statusSelisih = 'tepat_waktu';
                                }
                            }
                        }

                        // Calculate days between request and leave
                        $daysBetweenRequestAndLeave = $tanggalPengajuan->diffInDays($tanggalMulai);
                        $isSubmissionAfterLeaveStart = $tanggalPengajuan->gt($tanggalMulai);

                        $cutiAnalytics[] = [
                            'cuti_id' => $cuti->id,
                            'jenis_cuti' => $jenisCuti,
                            'jenis_cuti_id' => $detail->jenis_cuti_id,
                            'jumlah_hari' => $detail->jumlah_hari,
                            'tanggal_mulai' => $tanggalMulai,
                            'tanggal_mulai_formatted' => $tanggalMulai->format('d M Y'),
                            'tanggal_selesai' => $tanggalSelesai,
                            'tanggal_selesai_formatted' => $tanggalSelesai->format('d M Y'),
                            'tanggal_pengajuan' => $tanggalPengajuan,
                            'tanggal_pengajuan_formatted' => $tanggalPengajuan->format('d M Y'),
                            'hari_pengajuan_sebelum_mulai' => $daysBetweenRequestAndLeave,
                            'is_submission_after_leave_start' => $isSubmissionAfterLeaveStart,
                            'expected_leave_date' => $expectedLeaveDate,
                            'expected_leave_date_formatted' => $expectedLeaveDate ? $expectedLeaveDate->format('d M Y') : null,
                            'selisih_hari' => $selisihHari,
                            'status_selisih' => $statusSelisih
                        ];
                    }
                } else {
                    // Process as before for single leave type
                    $jenisCuti = $cuti->jenisCuti ? $cuti->jenisCuti->nama_jenis : 'Unknown';
                    $tanggalMulai = Carbon::parse($cuti->tanggal_mulai);
                    $tanggalSelesai = Carbon::parse($cuti->tanggal_selesai);

                    // Use tanggal_pengajuan if available, otherwise fall back to created_at
                    $tanggalPengajuan = null;
                    if (isset($cuti->tanggal_pengajuan)) {
                        $tanggalPengajuan = Carbon::parse($cuti->tanggal_pengajuan);
                    } else {
                        // Fall back to created_at
                        $tanggalPengajuan = Carbon::parse($cuti->created_at);
                    }

                    // Get employee's Date of Hire (DOH)
                    $doh = $karyawan->doh ? Carbon::parse($karyawan->doh) : null;

                    // Calculate expected leave date based on DOH for the current year
                    $expectedLeaveDate = null;
                    $selisihHari = null;
                    $statusSelisih = null;

                    if ($doh) {
                        // For periodic leave types, use the new period calculation
                        if (stripos($jenisCuti, 'periodik') !== false) {
                            // Calculate reset period based on employee status
                            $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;

                            // Get period information
                            $cutiService = new CutiService();
                            $periodInfo = $cutiService->isWithinLeavePeriod($karyawan, $cuti->jenis_cuti_id, $tanggalMulai);

                            // Calculate Tanggal Ideal Actual for periodic leave types
                            $idealActualDate = null;
                            $selisihHariIdealActual = null;
                            $statusSelisihIdealActual = null;

                            if (stripos($jenisCuti, 'periodik') !== false) {
                                // Calculate reset period based on employee status
                                $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;

                                // Get all leave start dates for this leave type
                                $allLeaveStartDates = $cutiService->getAllLeaveStartDates($karyawan->id, $cuti->jenis_cuti_id);

                                if (!empty($allLeaveStartDates)) {
                                    // Calculate Tanggal Ideal Actual based on all leave start dates
                                    $idealActualDate = $cutiService->calculateIdealActualDate($allLeaveStartDates, $tanggalMulai, $resetWeeks);
                                } elseif (isset($periodInfo['recommendation_dates']['all_ideal_dates'])) {
                                    // If no historical leaves, use the calculated ideal dates from period info
                                    $allIdealDates = $periodInfo['recommendation_dates']['all_ideal_dates'];
                                    foreach ($allIdealDates as $idealDateInfo) {
                                        if ($idealDateInfo['is_current']) {
                                            $idealActualDate = $idealDateInfo['date'];
                                            break;
                                        }
                                    }
                                }
                            }

                            if ($idealActualDate) {
                                // Compare actual leave date with ideal date
                                $expectedLeaveDate = $idealActualDate;
                                if ($tanggalMulai->lt($expectedLeaveDate)) {
                                    $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                    $statusSelisih = 'lebih_awal'; // hutang
                                } elseif ($tanggalMulai->gt($expectedLeaveDate)) {
                                    $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                    $statusSelisih = 'lebih_lambat'; // baik
                                } else {
                                    $selisihHari = 0;
                                    $statusSelisih = 'tepat_waktu';
                                }
                            }
                        } else { // For annual leave
                            $yearsSinceDoh = $doh->diffInYears($tanggalMulai);
                            $expectedLeaveDate = $doh->copy()->addYears($yearsSinceDoh);

                            // Compare actual leave date with DOH anniversary
                            if ($tanggalMulai->lt($expectedLeaveDate)) {
                                $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                $statusSelisih = 'lebih_awal'; // hutang
                            } elseif ($tanggalMulai->gt($expectedLeaveDate)) {
                                $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                $statusSelisih = 'lebih_lambat'; // baik
                            } else {
                                $selisihHari = 0;
                                $statusSelisih = 'tepat_waktu';
                            }
                        }
                    }

                    // Calculate days between request and leave
                    $daysBetweenRequestAndLeave = $tanggalPengajuan->diffInDays($tanggalMulai);
                    $isSubmissionAfterLeaveStart = $tanggalPengajuan->gt($tanggalMulai);

                    $cutiAnalytics[] = [
                        'cuti_id' => $cuti->id,
                        'jenis_cuti' => $jenisCuti,
                        'jenis_cuti_id' => $cuti->jenis_cuti_id,
                        'jumlah_hari' => $cuti->lama_hari,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_mulai_formatted' => $tanggalMulai->format('d M Y'),
                        'tanggal_selesai' => $tanggalSelesai,
                        'tanggal_selesai_formatted' => $tanggalSelesai->format('d M Y'),
                        'tanggal_pengajuan' => $tanggalPengajuan,
                        'tanggal_pengajuan_formatted' => $tanggalPengajuan->format('d M Y'),
                        'hari_pengajuan_sebelum_mulai' => $daysBetweenRequestAndLeave,
                        'is_submission_after_leave_start' => $isSubmissionAfterLeaveStart,
                        'expected_leave_date' => $expectedLeaveDate,
                        'expected_leave_date_formatted' => $expectedLeaveDate ? $expectedLeaveDate->format('d M Y') : null,
                        'selisih_hari' => $selisihHari,
                        'status_selisih' => $statusSelisih
                    ];
                }
            }
        }

        return view('karyawan.show', compact('karyawan', 'cutiAnalytics'));
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:255|unique:karyawans,nik,' . $karyawan->id,
            'departemen' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'email' => 'nullable|email|unique:karyawans,email,' . $karyawan->id,
            'doh' => 'nullable|date',
            'poh' => 'nullable|string|max:255',
            'status' => 'nullable|in:Staff,Non Staff',
        ]);

        $karyawan->update($validated);

        return redirect()->route('karyawans.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('karyawans.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }

    public function cutiMonitoring(Karyawan $karyawan)
    {
        $tahun = date('Y');
        $cutiService = new CutiService();

        // Get leave history
        $cutiHistory = $karyawan->cutis()
            ->with(['jenisCuti'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Prepare detailed leave analysis data for the table
        $cutiAnalytics = [];

        foreach ($cutiHistory as $cuti) {
            if ($cuti->status_cuti == 'disetujui') {
                $jenisCuti = $cuti->jenisCuti->nama_jenis;
                $tanggalMulai = Carbon::parse($cuti->tanggal_mulai);
                $tanggalSelesai = Carbon::parse($cuti->tanggal_selesai);

                // Use tanggal_pengajuan if available, otherwise fall back to created_at
                // Check if tanggal_pengajuan exists on the model
                $tanggalPengajuan = null;
                if (isset($cuti->tanggal_pengajuan)) {
                    $tanggalPengajuan = Carbon::parse($cuti->tanggal_pengajuan);
                } else {
                    // Fall back to created_at
                    $tanggalPengajuan = Carbon::parse($cuti->created_at);
                }

                // Get employee's Date of Hire (DOH)
                $doh = $karyawan->doh ? Carbon::parse($karyawan->doh) : null;

                // Calculate expected leave date based on DOH for the current year
                $expectedLeaveDate = null;
                $selisihHari = null;
                $statusSelisih = null;

                if ($doh) {
                    // For periodic leave types, use the new period calculation
                    if (stripos($jenisCuti, 'periodik') !== false) {
                        // Calculate reset period based on employee status
                        $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;

                        // Get period information
                        $periodInfo = $cutiService->isWithinLeavePeriod($karyawan, $cuti->jenis_cuti_id, $tanggalMulai);

                        // Calculate the correct ideal date based on the period of this leave
                        if (isset($periodInfo['recommendation_dates']) && isset($periodInfo['recommendation_dates']['all_ideal_dates']) && count($periodInfo['recommendation_dates']['all_ideal_dates']) > 0) {
                            // Get all ideal dates
                            $allIdealDates = $periodInfo['recommendation_dates']['all_ideal_dates'];

                            // Log for debugging
                            Log::debug('Analyzing leave period for cuti', [
                                'karyawan_id' => $karyawan->id,
                                'karyawan_nik' => $karyawan->nik,
                                'karyawan_status' => $karyawan->status,
                                'doh' => $doh->format('Y-m-d'),
                                'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                                'jenis_cuti' => $jenisCuti,
                                'resetWeeks' => $resetWeeks,
                                'all_ideal_dates_count' => count($allIdealDates),
                                'first_ideal_date' => isset($allIdealDates[0]) ? $allIdealDates[0]['date']->format('Y-m-d') : 'N/A'
                            ]);

                            // For historical analysis, we want to use the correct period's date
                            // First, calculate how many periods have passed between DOH and this leave
                            $leaveDaysSinceDoh = $doh->diffInDays($tanggalMulai);
                            $leavePeriodsSinceDoh = floor($leaveDaysSinceDoh / ($resetWeeks * 7));

                            Log::debug('Leave period calculation', [
                                'leaveDaysSinceDoh' => $leaveDaysSinceDoh,
                                'leavePeriodsSinceDoh' => $leavePeriodsSinceDoh,
                                'resetDays' => $resetWeeks * 7
                            ]);

                            // Find the matching period in all_ideal_dates
                            $matchingPeriod = null;
                            foreach ($allIdealDates as $idealDate) {
                                if ($idealDate['period'] == $leavePeriodsSinceDoh + 1) {
                                    $matchingPeriod = $idealDate;
                                    Log::debug('Found matching period', [
                                        'period' => $idealDate['period'],
                                        'date' => $idealDate['date']->format('Y-m-d')
                                    ]);
                                    break;
                                }
                            }

                            // If we found a matching period, use its date as the expected date
                            if ($matchingPeriod) {
                                $expectedLeaveDate = $matchingPeriod['date'];
                                Log::debug('Using matching period date', [
                                    'expectedLeaveDate' => $expectedLeaveDate->format('Y-m-d')
                                ]);
                            } else {
                                // If no matching period found, use the first period's date
                                $expectedLeaveDate = $allIdealDates[0]['date'];
                                Log::debug('Using first period date as fallback', [
                                    'expectedLeaveDate' => $expectedLeaveDate->format('Y-m-d')
                                ]);
                            }


                        }

                        // Calculate selisih (difference) between actual and expected date
                        if ($expectedLeaveDate) {
                            if ($tanggalMulai->lt($expectedLeaveDate)) {
                                $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                $statusSelisih = 'lebih_awal'; // Took leave earlier = hutang
                            }
                            else if ($tanggalMulai->gt($expectedLeaveDate)) {
                                $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                                $statusSelisih = 'lebih_lambat'; // Took leave later = good
                            }
                            else {
                                $selisihHari = 0;
                                $statusSelisih = 'tepat_waktu'; // Took leave exactly on time
                            }
                        }
                    }
                    // For non-periodic leave types, use the old calculation
                    else {
                        // Calculate the reset date based on DOH anniversary in the current year
                        $yearsSinceDoh = $doh->diffInYears($tanggalMulai);
                        $expectedLeaveDate = $doh->copy()->addYears($yearsSinceDoh);

                        // If the leave date is before the reset date, it's a "hutang" (debt)
                        if ($tanggalMulai->lt($expectedLeaveDate)) {
                            $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                            $statusSelisih = 'lebih_awal'; // Took leave earlier = hutang
                        }
                        // If the leave date is after the reset date, it's "good"
                        else if ($tanggalMulai->gt($expectedLeaveDate)) {
                            $selisihHari = $tanggalMulai->diffInDays($expectedLeaveDate);
                            $statusSelisih = 'lebih_lambat'; // Took leave later = good
                        }
                        // If dates match exactly
                        else {
                            $selisihHari = 0;
                            $statusSelisih = 'tepat_waktu'; // Took leave exactly on time
                        }
                    }
                }

                // Calculate days between leave request submission and actual leave start date
                $daysBetweenRequestAndLeave = $tanggalPengajuan->diffInDays($tanggalMulai);
                $isSubmissionAfterLeaveStart = $tanggalPengajuan->gt($tanggalMulai);



                // Calculate Tanggal Ideal Actual for periodic leave types
                $idealActualDate = null;
                $selisihHariIdealActual = null;
                $statusSelisihIdealActual = null;

                if (stripos($jenisCuti, 'periodik') !== false) {
                    // Calculate reset period based on employee status
                    $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;

                    // Get all leave start dates for this leave type
                    $allLeaveStartDates = $cutiService->getAllLeaveStartDates($karyawan->id, $cuti->jenis_cuti_id);

                    if (!empty($allLeaveStartDates)) {
                        // Calculate Tanggal Ideal Actual based on all leave start dates
                        $idealActualDate = $cutiService->calculateIdealActualDate($allLeaveStartDates, $tanggalMulai, $resetWeeks);

                        // Calculate selisih (difference) between actual and ideal actual date
                        if ($idealActualDate) {
                            if ($tanggalMulai->lt($idealActualDate)) {
                                $selisihHariIdealActual = $tanggalMulai->diffInDays($idealActualDate);
                                $statusSelisihIdealActual = 'lebih_awal'; // Took leave earlier = hutang
                            }
                            else if ($tanggalMulai->gt($idealActualDate)) {
                                $selisihHariIdealActual = $tanggalMulai->diffInDays($idealActualDate);
                                $statusSelisihIdealActual = 'lebih_lambat'; // Took leave later = good
                            }
                            else {
                                $selisihHariIdealActual = 0;
                                $statusSelisihIdealActual = 'tepat_waktu'; // Took leave exactly on time
                            }
                        }

                        // Log for debugging
                        Log::debug('Ideal Actual Date for leave', [
                            'karyawan_id' => $karyawan->id,
                            'karyawan_nik' => $karyawan->nik,
                            'jenis_cuti' => $jenisCuti,
                            'all_leave_start_dates' => array_map(function($date) { return $date->format('Y-m-d'); }, $allLeaveStartDates),
                            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
                            'ideal_actual_date' => $idealActualDate ? $idealActualDate->format('Y-m-d') : null,
                            'selisih_hari_ideal_actual' => $selisihHariIdealActual,
                            'status_selisih_ideal_actual' => $statusSelisihIdealActual
                        ]);
                    }
                }

                // Add to analytics array
                $cutiAnalytics[] = [
                    'jenis_cuti' => $jenisCuti,
                    'jenis_cuti_id' => $cuti->jenis_cuti_id,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                    'durasi' => (int)$cuti->lama_hari,
                    'tanggal_pengajuan' => $tanggalPengajuan,
                    'hari_pengajuan_sebelum_cuti' => (int)$daysBetweenRequestAndLeave,
                    'isSubmissionAfterLeaveStart' => $isSubmissionAfterLeaveStart,
                    'expected_date' => $expectedLeaveDate,
                    'ideal_actual_date' => $idealActualDate,
                    'selisih_hari' => $selisihHari ? (int)$selisihHari : null,
                    'status_selisih' => $statusSelisih,
                    'selisih_hari_ideal_actual' => $selisihHariIdealActual ? (int)$selisihHariIdealActual : null,
                    'status_selisih_ideal_actual' => $statusSelisihIdealActual,
                    'is_periodic' => stripos($jenisCuti, 'periodik') !== false,
                ];
            }
        }

        // Monthly leave usage for chart (keeping for backward compatibility)
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[$i] = 0;
        }

        // Upcoming leave information
        $upcomingCuti = $karyawan->cutis()
            ->with(['jenisCuti'])
            ->where('status_cuti', 'disetujui')
            ->where('tanggal_mulai', '>', now()->format('Y-m-d'))
            ->orderBy('tanggal_mulai', 'asc')
            ->first();

        // Current leave information
        $currentCuti = $karyawan->cutis()
            ->with(['jenisCuti'])
            ->where('status_cuti', 'disetujui')
            ->where('tanggal_mulai', '<=', now()->format('Y-m-d'))
            ->where('tanggal_selesai', '>=', now()->format('Y-m-d'))
            ->first();

        if ($currentCuti) {
            // Calculate the correct remaining days by finding how many days are left until end date
            $today = Carbon::now()->startOfDay();
            $endDate = Carbon::parse($currentCuti->tanggal_selesai)->startOfDay();

            // When calculating remaining days, ensure we're getting the correct value regardless of direction
            if ($today->lte($endDate)) {
                $sisaHariCuti = $today->diffInDays($endDate) + 1;  // +1 to include today
            } else {
                $sisaHariCuti = 0; // If past end date, no days remaining
            }

            // For percentage calculation, get elapsed and total days
            $startDate = Carbon::parse($currentCuti->tanggal_mulai)->startOfDay();
            $totalDays = $startDate->diffInDays($endDate) + 1;
            $elapsedDays = $startDate->diffInDays($today);

            // Add the current day to elapsed days if we're within or past the leave period
            if ($today->gte($startDate)) {
                $elapsedDays += 1;
            }

            // Ensure elapsed days doesn't exceed total days
            $elapsedDays = min($elapsedDays, $totalDays);

            // Calculate progress percentage
            $progressPercentage = ($elapsedDays / $totalDays) * 100;
        } else {
            $sisaHariCuti = null;
            $totalDays = null;
            $elapsedDays = null;
            $progressPercentage = null;
        }

        // Use CutiService to calculate leave balances
        $sisaCutiPerType = $karyawan->getLeaveBalances();

        // Enhance leave balances with period information for periodic leave types
        foreach ($sisaCutiPerType as $jenisCutiId => &$cutiInfo) {
            if (stripos($cutiInfo['nama_jenis'], 'periodik') !== false) {
                $doh = $karyawan->doh ? Carbon::parse($karyawan->doh) : null;

                if ($doh) {
                    // Get the latest leave for this type
                    $latestCuti = $cutiService->getLatestLeave($karyawan->id, $jenisCutiId);

                    // Calculate reset period based on employee status
                    $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;

                    // Get period information
                    $periodInfo = $cutiService->isWithinLeavePeriod($karyawan, $jenisCutiId, Carbon::now());

                    // Add period information to the leave balance
                    $cutiInfo['status_info'] = $periodInfo;
                    $cutiInfo['current_period_start'] = $periodInfo['current_period_start'] ?? null;
                    $cutiInfo['current_period_end'] = $periodInfo['current_period_end'] ?? null;
                    $cutiInfo['next_period_start'] = $periodInfo['next_period_start'] ?? null;
                    $cutiInfo['next_period_end'] = $periodInfo['next_period_end'] ?? null;
                    $cutiInfo['is_within_current_period'] = $periodInfo['is_within_current_period'] ?? false;
                    $cutiInfo['is_within_next_period'] = $periodInfo['is_within_next_period'] ?? false;
                    $cutiInfo['days_until_next_period'] = $periodInfo['days_until_next_period'] ?? null;
                    $cutiInfo['days_since_period_start'] = $periodInfo['days_since_period_start'] ?? null;
                    $cutiInfo['already_taken_in_period'] = $periodInfo['already_taken_in_period'] ?? false;
                    $cutiInfo['period_message'] = $periodInfo['message'] ?? null;
                }
            }
        }

        return view('karyawan.cuti_monitoring', compact(
            'karyawan',
            'sisaCutiPerType',
            'cutiHistory',
            'cutiAnalytics',
            'chartData',
            'upcomingCuti',
            'currentCuti',
            'sisaHariCuti'
        ));
    }

    /**
     * Manually refresh leave balances for a specific employee
     *
     * @param Karyawan $karyawan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshLeaveBalances(Karyawan $karyawan)
    {
        // Log for debugging
        Log::info('Manually refreshing leave balances', [
            'karyawan_id' => $karyawan->id,
            'karyawan_name' => $karyawan->nama
        ]);

        // Clear any cached leave balances
        // (No cache implementation yet, but we'll add this for future-proofing)

        // Get all leave balances using the Karyawan model's method
        // This will use the AnnualLeaveCalculator for annual leave
        $balances = $karyawan->getLeaveBalances();

        // Log the results
        Log::info('Leave balances refreshed', [
            'karyawan_id' => $karyawan->id,
            'karyawan_name' => $karyawan->nama,
            'balances' => array_map(function($balance) {
                return [
                    'jenis_cuti' => $balance['nama_jenis'],
                    'jatah_hari' => $balance['jatah_hari'],
                    'digunakan' => $balance['digunakan'],
                    'sisa' => $balance['sisa']
                ];
            }, $balances)
        ]);

        return redirect()->route('karyawans.cuti-monitoring', $karyawan->id)
            ->with('success', 'Saldo cuti berhasil diperbarui.');
    }

    public function exportExcel()
    {
        return Excel::download(new KaryawanExport(), 'Data_Karyawan_' . date('Y-m-d') . '.xlsx');
    }

    public function exportTemplate()
    {
        return Excel::download(new KaryawanTemplateExport(), 'Template_Import_Karyawan.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $importer = new KaryawanImport();
        $result = $importer->import($request->file('file'));

        if ($result['success']) {
            $message = '';

            if ($result['imported'] > 0) {
                $message .= $result['imported'] . ' data karyawan baru berhasil ditambahkan. ';
            }

            if ($result['updated'] > 0) {
                $message .= $result['updated'] . ' data karyawan berhasil diperbarui. ';
            }

            if (!empty($result['failures'])) {
                return redirect()->route('karyawans.index')
                    ->with('warning', $message . 'Namun ada ' . count($result['failures']) . ' baris yang gagal.')
                    ->with('import_failures', $result['failures']);
            }

            return redirect()->route('karyawans.index')
                ->with('success', trim($message));
        } else {
            // Menampilkan pesan error lengkap untuk debugging
            return redirect()->route('karyawans.index')
                ->with('error', $result['message'])
                ->with('error_detail', $result['error_detail'] ?? 'No detailed error available');
        }
    }

    /**
     * API untuk pencarian karyawan (untuk form cuti)
     */
    public function search(Request $request)
    {
        try {
            // Detailed logging for debugging
            Log::info('API Search Request Debug', [
                'query' => $request->all(),
                'headers' => $request->header(),
                'url' => $request->fullUrl(),
                'client_ip' => $request->ip(),
                'time' => now()->toDateTimeString()
            ]);

            $query = Karyawan::query();

            // Pencarian berdasarkan ID
            if ($request->has('id') && $request->id) {
                Log::info('Searching by ID', ['id' => $request->id]);
                $result = Karyawan::where('id', $request->id)
                          ->get(['id', 'nama', 'nik', 'departemen']);

                Log::info('Search by ID result', ['count' => $result->count(), 'data' => $result]);

                // Return an empty array if no results, not null
                return response()->json($result, 200, [
                    'Content-Type' => 'application/json'
                ]);
            }

            // Pencarian berdasarkan NIK (exact match)
            if ($request->has('nik') && $request->nik) {
                $searchNik = $request->nik;
                Log::info('Searching by NIK', ['nik' => $searchNik, 'nik_length' => strlen($searchNik)]);

                // First try exact match - include poh, doh, and status fields
                $exactMatch = Karyawan::where('nik', $searchNik)->get(['id', 'nama', 'nik', 'departemen', 'jabatan', 'poh', 'doh', 'status']);

                // If no exact match, try LIKE search - include poh, doh, and status fields
                if ($exactMatch->isEmpty()) {
                    Log::info('No exact match, trying LIKE search');
                    $result = Karyawan::where('nik', 'like', $searchNik.'%')
                              ->limit(10)
                              ->get(['id', 'nama', 'nik', 'departemen', 'jabatan', 'poh', 'doh', 'status']);
                } else {
                    $result = $exactMatch;
                }

                Log::info('Search by NIK result', [
                    'count' => $result->count(),
                    'data' => $result,
                    'first_char_ascii' => $searchNik ? ord($searchNik[0]) : null
                ]);

                // Return JSON with proper Content-Type header
                return response()->json($result, 200, [
                    'Content-Type' => 'application/json',
                    'X-Search-Type' => 'nik',
                    'X-Result-Count' => $result->count()
                ]);
            }

            // Filter berdasarkan departemen
            if ($request->has('departemen') && $request->departemen) {
                $query->where('departemen', $request->departemen);
            }

            // Pencarian berdasarkan nama atau NIK
            if ($request->has('q')) {
                $searchTerm = $request->q;
                Log::info('Searching by term', ['term' => $searchTerm]);

                if (!empty($searchTerm)) {
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('nama', 'like', '%' . $searchTerm . '%')
                          ->orWhere('nik', 'like', '%' . $searchTerm . '%');
                    });
                }
            }

            // Default limit 10 records, but can be increased for specific requests
            $limit = $request->has('limit') ? min(intval($request->limit), 50) : 10;

            // Order by nama (prevents random ordering)
            $query->orderBy('nama', 'asc');

            $karyawans = $query->limit($limit)->get(['id', 'nama', 'nik', 'departemen', 'jabatan', 'poh', 'doh', 'status']);

            // Log hasil pencarian untuk debugging
            Log::info('General search result', ['count' => $karyawans->count()]);

            // Return JSON with proper Content-Type header
            return response()->json($karyawans, 200, [
                'Content-Type' => 'application/json',
                'X-Search-Type' => 'general',
                'X-Result-Count' => $karyawans->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in search API', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500, [
                'Content-Type' => 'application/json'
            ]);
        }
    }

    /**
     * Delete multiple employees at once
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function massDelete(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:karyawans,id'
        ]);

        // Check which employees have leave requests
        $employeesWithCuti = Karyawan::whereIn('id', $request->employee_ids)
            ->whereHas('cutis')
            ->get();

        if ($employeesWithCuti->count() > 0) {
            // Get the names of employees that can't be deleted
            $cantDeleteNames = $employeesWithCuti->pluck('nama')->toArray();
            $errorMessage = 'Tidak dapat menghapus karyawan yang memiliki data cuti: ' . implode(', ', $cantDeleteNames);

            return redirect()->route('karyawans.index')
                ->with('error', $errorMessage);
        }

        // Delete employees without leave requests
        $count = Karyawan::whereIn('id', $request->employee_ids)->delete();

        return redirect()->route('karyawans.index')
            ->with('success', $count . ' data karyawan berhasil dihapus.');
    }
}